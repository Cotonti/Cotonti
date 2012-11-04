<?php
/**
 * @package install
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Modules and plugins checked by default
$default_modules = array('index', 'page', 'users', 'rss');
$default_plugins = array('ckeditor', 'cleaner', 'html', 'htmlpurifier', 'ipsearch', 'mcaptcha', 'news', 'search');

$step = empty($_SESSION['cot_inst_lang']) ? 0 : (int) $cfg['new_install'];

cot_sendheaders();

$mskin = cot_tplfile('install.install');
$t = new XTemplate($mskin);

$site_url = (strpos($_SERVER['SERVER_PROTOCOL'], 'HTTPS') === false && $_SERVER['HTTPS'] != 'on' && $_SERVER['SERVER_PORT'] != 443 && $_SERVER['HTTP_X_FORWARDED_PORT'] !== 443 ? 'http://' : 'https://')
	. $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
$site_url = str_replace('\\', '/', $site_url);
$site_url = preg_replace('#/$#', '', $site_url);
$sys['abs_url'] = $site_url . '/';
define('COT_ABSOLUTE_URL', $site_url . '/');

if ($step > 2)
{
	$dbc_port = empty($cfg['mysqlport']) ? '' : ';port='.$cfg['mysqlport'];
	$db = new CotDB('mysql:host='.$cfg['mysqlhost'].$dbc_port.';dbname='.$cfg['mysqldb'], $cfg['mysqluser'], $cfg['mysqlpassword']);
}

// Import section
switch ($step)
{
	case 2:
		$db_host = cot_import('db_host', 'P', 'TXT', 0, false, true);
		$db_port = cot_import('db_port', 'P', 'TXT', 0, false, true);
		$db_user = cot_import('db_user', 'P', 'TXT', 0, false, true);
		$db_pass = cot_import('db_pass', 'P', 'TXT', 0, false, true);
		$db_name = cot_import('db_name', 'P', 'TXT', 0, false, true);
		break;

	case 3:
		$cfg['mainurl'] = cot_import('mainurl', 'P', 'TXT', 0, false, true);
		$user['name'] = cot_import('user_name', 'P', 'TXT', 100, false, true);
		$user['pass'] = cot_import('user_pass', 'P', 'HTM', 32);
		$user['pass2'] = cot_import('user_pass2', 'P', 'TXT', 16);
		$user['email'] = cot_import('user_email', 'P', 'TXT', 64, false, true);
		$user['country'] = cot_import('user_country', 'P', 'TXT', 0, false, true);
		$rtheme = explode(':', cot_import('theme', 'P', 'TXT', 0, false, true));
		$rscheme = $rtheme[1];
		$rtheme = $rtheme[0];
		$rlang = cot_import('lang', 'P', 'TXT', 0, false, true);
		break;
	case 4:
		// Extension selection
		$install_modules = cot_import('install_modules', 'P', 'ARR', 0, false, true);
		$selected_modules = array();
		if (is_array($install_modules))
		{
			foreach ($install_modules as $key => $val)
			{
				if ($val)
				{
					$selected_modules[] = $key;
				}
			}
		}
		$install_plugins = cot_import('install_plugins', 'P', 'ARR', 0, false, true);
		$selected_plugins = array();
		if (is_array($install_plugins))
		{
			foreach ($install_plugins as $key => $val)
			{
				if ($val)
				{
					$selected_plugins[] = $key;
				}
			}
		}
		break;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Form submission handling
	switch ($step)
	{
		case 0:
			// Lang selection
			$_SESSION['cot_inst_lang'] = $lang;
			cot_redirect('install.php');
			break;
		case 1:
			// System info
			clearstatcache();
			if (!file_exists($file['sql']))
			{
				cot_error(cot_rc('install_error_missing_file', array('file' => $file['sql'])));
			}
			if (function_exists('version_compare') && !version_compare(PHP_VERSION, '5.2.3', '>='))
			{
				cot_error(cot_rc('install_error_php_ver', array('ver' => PHP_VERSION)));
			}
			if (!extension_loaded('mbstring'))
			{
				cot_error('install_error_mbstring');
			}
			if (!extension_loaded('pdo_mysql'))
			{
				cot_error('install_error_sql_ext');
			}

			if (!file_exists($file['config']))
			{
				if (!is_writable('datas') || !copy($file['config_sample'], $file['config']))
				{
					cot_error('install_error_config');
				}
			}
			break;
		case 2:
			// Database setup
			$db_x = cot_import('db_x', 'P', 'TXT', 0, false, true);

			try
			{
				$dbс_port = empty($db_port) ? '' : ';port='.$db_port;
				$db = new CotDB('mysql:host='.$db_host.$dbc_port.';dbname='.$db_name, $db_user, $db_pass);
			}
			catch (PDOException $e)
			{
				if ($e->getCode() == 1049 || mb_strpos($e->getMessage(), '[1049]') !== false)
				{
					// Attempt to create a new database
					try
					{
						$db = new CotDB('mysql:host='.$db_host.$dbc_port, $db_user, $db_pass);
						$db->query("CREATE DATABASE `$db_name`");
						$db->query("USE `$db_name`");
					}
					catch (PDOException $e)
					{
						cot_error('install_error_sql_db', 'db_name');
					}
				}
				else
				{
					cot_error('install_error_sql', 'db_host');
				}
			}

			if (!cot_error_found() && function_exists('version_compare')
				&& !version_compare($db->getAttribute(PDO::ATTR_SERVER_VERSION), '5.0.7', '>='))
			{
				cot_error(cot_rc('install_error_sql_ver', array('ver' => $db->getAttribute(PDO::ATTR_SERVER_VERSION))));
			}

			if (!cot_error_found())
			{
				$config_contents = file_get_contents($file['config']);
				cot_install_config_replace($config_contents, 'mysqlhost', $db_host);
				if (!empty($db_port))
				{
					cot_install_config_replace($config_contents, 'mysqlport', $db_port);
				}
				cot_install_config_replace($config_contents, 'mysqluser', $db_user);
				cot_install_config_replace($config_contents, 'mysqlpassword', $db_pass);
				cot_install_config_replace($config_contents, 'mysqldb', $db_name);
				$config_contents = preg_replace("#^\\\$db_x\s*=\s*'.*?';#m",
						"\$db_x				= '$db_x';", $config_contents);
				file_put_contents($file['config'], $config_contents);

				$sql_file = file_get_contents($file['sql']);
				$error = $db->runScript($sql_file);

				if ($error)
				{
					cot_error(cot_rc('install_error_sql_script', array('msg' => $error)));
				}
			}
			break;
		case 3:
			// Misc settings and admin account
			if (empty($cfg['mainurl']))
			{
				cot_error('install_error_mainurl', 'mainurl');
			}
			if ($user['pass'] != $user['pass2'])
			{
				cot_error('aut_passwordmismatch', 'user_pass');
			}
			if (mb_strlen($user['name']) < 2)
			{
				cot_error('aut_usernametooshort', 'user_name');
			}
			if (mb_strlen($user['pass']) < 4)
			{
				cot_error('aut_passwordtooshort', 'user_pass');
			}
			if (mb_strlen($user['email']) < 4
				|| !preg_match('#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$#i', $user['email']))
			{
				cot_error('aut_emailtooshort', 'user_email');
			}
			if (!file_exists($file['config_sample']))
			{
				cot_error(cot_rc('install_error_missing_file', array('file' => $file['config_sample'])));
			}

			if (!cot_error_found())
			{
				$config_contents = file_get_contents($file['config']);
				cot_install_config_replace($config_contents, 'defaultlang', $rlang);
				cot_install_config_replace($config_contents, 'defaulttheme', $rtheme);
				cot_install_config_replace($config_contents, 'defaultscheme', $rscheme);
				cot_install_config_replace($config_contents, 'mainurl', $cfg['mainurl']);

				$new_site_id = cot_unique(32);
				cot_install_config_replace($config_contents, 'site_id', $new_site_id);
				$new_secret_key = cot_unique(32);
				cot_install_config_replace($config_contents, 'secret_key', $new_secret_key);

				file_put_contents($file['config'], $config_contents);

				$ruserpass['user_passsalt'] = cot_unique(16);
				$ruserpass['user_passfunc'] = empty($cfg['hashfunc']) ? 'sha256' : $cfg['hashfunc'];
				$ruserpass['user_password'] = cot_hash($user['pass'], $ruserpass['user_passsalt'], $ruserpass['user_passfunc']);

				try
				{
					$db->insert($db_x . 'users', array(
						'user_name' => $user['name'],
						'user_password' => $ruserpass['user_password'],
						'user_passsalt' => $ruserpass['user_passsalt'],
						'user_passfunc' => $ruserpass['user_passfunc'],
						'user_maingrp' => COT_GROUP_SUPERADMINS,
						'user_country' => (string) $user['country'],
						'user_email' => $user['email'],
						'user_theme' => $rtheme,
						'user_scheme' => $rscheme,
						'user_lang' => $rlang,
						'user_regdate' => time(),
						'user_lastip' => $_SERVER['REMOTE_ADDR']
					));

					$user['id'] = $db->lastInsertId();

					$db->insert($db_x . 'groups_users', array(
						'gru_userid' => (int) $user['id'],
						'gru_groupid' => COT_GROUP_SUPERADMINS
					));
				}
				catch (PDOException $err)
				{
					cot_error(cot_rc('install_error_sql_script', array('msg' => $err->getMessage())));
				}
			}

			break;
		case 4:
			// Dependency check
			$install = true;
			foreach ($selected_modules as $ext)
			{
				$install &= cot_extension_dependencies_statisfied($ext, true, $selected_modules, $selected_plugins);
			}
			foreach ($selected_plugins as $ext)
			{
				$install &= cot_extension_dependencies_statisfied($ext, false, $selected_modules, $selected_plugins);
			}

			if ($install && !cot_error_found())
			{
				// Load groups
				$cot_groups = array();
				$res = $db->query("SELECT grp_id FROM $db_groups
					WHERE grp_disabled=0 ORDER BY grp_level DESC");
				while ($row = $res->fetch())
				{
					$cot_groups[$row['grp_id']] = array(
						'id' => $row['grp_id'],
						'alias' => $row['grp_alias'],
						'level' => $row['grp_level'],
						'disabled' => $row['grp_disabled'],
						'hidden' => $row['grp_hidden'],
						'state' => $row['grp_state'],
						'name' => htmlspecialchars($row['grp_name']),
						'title' => htmlspecialchars($row['grp_title'])
					);
				}
				$res->closeCursor();
				$usr['id'] = 1;
				// Install all at once
				// Note: installation statuses are ignored in this installer
				$selected_modules = cot_install_sort_extensions($selected_modules, true);
				foreach ($selected_modules as $ext)
				{
					if (!cot_extension_install($ext, true))
                    {
                        cot_error("Installing $ext module has failed");
                    }
				}
				$selected_plugins = cot_install_sort_extensions($selected_plugins, false);
				foreach ($selected_plugins as $ext)
				{
					if (!cot_extension_install($ext, false))
                    {
                        cot_error("Installing $ext plugin has failed");
                    }
				}
			}
			break;
		case 5:
			// End credits
			break;
		default:
			// Error
			cot_redirect(cot_url('index'));
			exit;
	}

	if (cot_error_found())
	{
		// One step back
		cot_redirect('install.php');
	}
	else
	{
		// Step++
		$step++;
		$config_contents = file_get_contents($file['config']);
		if ($step == 5)
		{
			$config_contents = preg_replace("#^\\\$cfg\['new_install'\]\s*=\s*.*?;#m", "\$cfg['new_install'] = false;", $config_contents);
		}
		else
		{
			$config_contents = preg_replace("#^\\\$cfg\['new_install'\]\s*=\s*.*?;#m", "\$cfg['new_install'] = $step;",
					$config_contents);
		}
		file_put_contents($file['config'], $config_contents);
	}
}

// Display
switch ($step)
{
	case 0:
		// Language selection
		$t->assign(array(
			'INSTALL_LANG' => cot_selectbox_lang($lang, 'lang')
		));
		break;
	case 1:
		// Create missing cache folders
		if (is_writable($cfg['cache_dir']))
		{
			$cache_subfolders = array('cot', 'static', 'system', 'templates');
			foreach ($cache_subfolders as $sub)
			{
				if (!file_exists($cfg['cache_dir'] . '/' . $sub))
				{
					mkdir($cfg['cache_dir'] . '/' . $sub, $cfg['dir_perms']);
				}
			}
		}

		// System info
		// Build CHMOD/Exists/Version data
		clearstatcache();

		if (is_dir($cfg['avatars_dir']))
		{
			$status['avatars_dir'] = is_writable($cfg['avatars_dir'])
				? $R['install_code_writable']
				: cot_rc('install_code_invalid', array('text' =>
					cot_rc('install_chmod_value', array('chmod' =>
						substr(decoct(fileperms($cfg['avatars_dir'])), -4)))));
		}
		else
		{
			$status['avatars_dir'] = $R['install_code_not_found'];
		}
		/* ------------------- */
		if (is_dir($cfg['cache_dir']))
		{
			$status['cache_dir'] = is_writable($cfg['cache_dir'])
				? $R['install_code_writable']
				: cot_rc('install_code_invalid', array('text' =>
					cot_rc('install_chmod_value', array('chmod' =>
						substr(decoct(fileperms($cfg['cache_dir'])), -4)))));
		}
		else
		{
			$status['cache_dir'] = $R['install_code_not_found'];
		}
		/* ------------------- */
		if (is_dir($cfg['pfs_dir']))
		{
			$status['pfs_dir'] = is_writable($cfg['pfs_dir'])
				? $R['install_code_writable']
				: cot_rc('install_code_invalid', array('text' =>
					cot_rc('install_chmod_value', array('chmod' =>
						substr(decoct(fileperms($cfg['pfs_dir'])), -4)))));
		}
		else
		{
			$status['pfs_dir'] = $R['install_code_not_found'];
		}
		/* ------------------- */
		if (is_dir($cfg['extrafield_files_dir']))
		{
			$status['exflds_dir'] = is_writable($cfg['extrafield_files_dir'])
				? $R['install_code_writable']
				: cot_rc('install_code_invalid', array('text' =>
					cot_rc('install_chmod_value', array('chmod' =>
						substr(decoct(fileperms($cfg['extrafield_files_dir'])), -4)))));
		}
		else
		{
			$status['exflds_dir'] = $R['install_code_not_found'];
		}
		/* ------------------- */
		if (is_dir($cfg['photos_dir']))
		{
			$status['photos_dir'] = is_writable($cfg['photos_dir'])
				? $R['install_code_writable']
				: cot_rc('install_code_invalid', array('text' =>
					cot_rc('install_chmod_value', array('chmod' =>
						substr(decoct(fileperms($cfg['photos_dir'])), -4)))));
		}
		else
		{
			$status['photos_dir'] = $R['install_code_not_found'];
		}
		/* ------------------- */
		if (is_dir($cfg['thumbs_dir']))
		{
			$status['thumbs_dir'] = is_writable($cfg['thumbs_dir'])
				? $R['install_code_writable']
				: cot_rc('install_code_invalid', array('text' =>
					cot_rc('install_chmod_value', array('chmod' =>
						substr(decoct(fileperms($cfg['thumbs_dir'])), -4)))));
		}
		else
		{
			$status['thumbs_dir'] = $R['install_code_not_found'];
		}
		/* ------------------- */
		if (file_exists($file['config']) || is_writable('datas'))
		{
			$status['config'] = is_writable($file['config']) || is_writable('datas')
				? $R['install_code_writable']
				: cot_rc('install_code_invalid', array('text' =>
					cot_rc('install_chmod_value', array('chmod' =>
						substr(decoct(fileperms($file['config'])), -4)))));
		}
		else
		{
			$status['config'] = $R['install_code_not_found'];
		}
		/* ------------------- */
		if (file_exists($file['config_sample']))
		{
			$status['config_sample'] = $R['install_code_found'];
		}
		else
		{
			$status['config_sample'] = $R['install_code_not_found'];
		}
		/* ------------------- */
		if (file_exists($file['sql']))
		{
			$status['sql_file'] = $R['install_code_found'];
		}
		else
		{
			$status['sql_file'] = $R['install_code_not_found'];
		}
		$status['php_ver'] = (function_exists('version_compare') && version_compare(PHP_VERSION, '5.2.3', '>='))
			? cot_rc('install_code_valid', array('text' =>
				cot_rc('install_ver_valid', array('ver' => PHP_VERSION))))
			: cot_rc('install_code_invalid', array('text' =>
				cot_rc('install_ver_invalid', array('ver' => PHP_VERSION))));
		$status['mbstring'] = (extension_loaded('mbstring'))
			? $R['install_code_available'] : $R['install_code_not_available'];
		$status['hash'] = (extension_loaded('hash') && function_exists('hash_hmac'))
			? $R['install_code_available'] : $R['install_code_not_available'];
		$status['mysql'] = (extension_loaded('pdo_mysql'))
			? $R['install_code_available'] : $R['install_code_not_available'];

		$t->assign(array(
			'INSTALL_AV_DIR' => $status['avatars_dir'],
			'INSTALL_CACHE_DIR' => $status['cache_dir'],
			'INSTALL_PFS_DIR' => $status['pfs_dir'],
			'INSTALL_EXFLDS_DIR' => $status['exflds_dir'],
			'INSTALL_PHOTOS_DIR' => $status['photos_dir'],
			'INSTALL_THUMBS_DIR' => $status['thumbs_dir'],
			'INSTALL_CONFIG' => $status['config'],
			'INSTALL_CONFIG_SAMPLE' => $status['config_sample'],
			'INSTALL_SQL_FILE' => $status['sql_file'],
			'INSTALL_PHP_VER' => $status['php_ver'],
			'INSTALL_MBSTRING' => $status['mbstring'],
			'INSTALL_HASH' => $status['hash'],
			'INSTALL_MYSQL' => $status['mysql']
		));
		break;
	case 2:
		// Database form
		$t->assign(array(
			'INSTALL_DB_HOST' => is_null($db_host) ? $cfg['mysqlhost'] : $db_host,
			'INSTALL_DB_PORT' => is_null($db_port) ? $cfg['mysqlport'] : $db_port,
			'INSTALL_DB_USER' => is_null($db_user) ? $cfg['mysqluser'] : $db_user,
			'INSTALL_DB_NAME' => is_null($db_name) ? $cfg['mysqldb'] : $db_name,
			'INSTALL_DB_X' => $db_x,
		));
		break;
	case 3:
		// Settings
		if ($_POST['step'] != 3 && !cot_check_messages())
		{
			$rtheme = $theme;
			$rscheme = $scheme;
			$rlang = $cfg['defaultlang'];
			$cfg['mainurl'] = $site_url;
		}

		$t->assign(array(
			'INSTALL_THEME_SELECT' => cot_selectbox_theme($rtheme, $rscheme, 'theme'),
			'INSTALL_LANG_SELECT' => cot_selectbox_lang($rlang, 'lang'),
			'INSTALL_COUNTRY_SELECT' => cot_selectbox_countries($user['country'], 'user_country')
		));
	case 4:
		// Extensions
		cot_install_parse_extensions('Module', $default_modules, $selected_modules);
		cot_install_parse_extensions('Plugin', $default_plugins, $selected_plugins);
		break;
	case 5:
		// End credits
		break;

}

$t->parse("MAIN.STEP_$step");

// Error & message display
cot_display_messages($t);

$t->assign(array(
	'INSTALL_STEP' => $step == 5 ? $L['Complete'] : cot_rc('install_step', array('step' => $step, 'total' => 4)),
	'INSTALL_LANG' => cot_selectbox_lang($lang, 'lang')
));


$t->parse('MAIN');
$t->out('MAIN');

/**
 * Replaces a sample config with its actual value
 *
 * @param string $file_contents Config file contents
 * @param string $config_name Config option name
 * @param string $config_value Config value to set
 * @return string Modified file contents
 */
function cot_install_config_replace(&$file_contents, $config_name, $config_value)
{
	$file_contents = preg_replace("#^\\\$cfg\['$config_name'\]\s*=\s*'.*?';#m",
		"\$cfg['$config_name'] = '$config_value';", $file_contents);
}

/**
 * Parses extensions selection section
 *
 * @param string $ext_type Extension type: 'Module' or 'Plugin'
 * @param array $default_list A list of recommended extensions (checked by default)
 * @param array $selected_list A list of previously selected extensions
 */
function cot_install_parse_extensions($ext_type, $default_list = array(), $selected_list = array())
{
	global $t, $cfg, $L;
	$ext_type_lc = strtolower($ext_type);
	$ext_type_uc = strtoupper($ext_type);

	$ext_list = cot_extension_list_info($cfg["{$ext_type_lc}s_dir"]);

	$ext_type_lc == 'plugin' ? usort($ext_list, 'cot_extension_catcmp') : ksort($ext_list);

	$prev_cat = '';
	$block_name = $ext_type_lc == 'plugin' ? "{$ext_type_uc}_CAT.{$ext_type_uc}_ROW" : "{$ext_type_uc}_ROW";
	foreach ($ext_list as $f => $info)
	{
		if (is_array($info))
		{
			$code = $ext_type_lc == 'plugin' ? $info['Code'] : $f;
			if ($ext_type_lc == 'plugin' && $prev_cat != $info['Category'])
			{
				if ($prev_cat != '')
				{
					// Render previous category
					$t->parse("MAIN.STEP_4.{$ext_type_uc}_CAT");
				}
				// Assign a new one
				$prev_cat = $info['Category'];
				$t->assign('PLUGIN_CAT_TITLE', $L['ext_cat'][$info['Category']]);
			}
			if (!empty($info['Requires_modules']) || !empty($info['Requires_plugins']))
			{
				$modules_list = empty($info['Requires_modules']) ? $L['None']
					: implode(', ', explode(',', $info['Requires_modules']));
				$plugins_list = empty($info['Requires_plugins']) ? $L['None']
					: implode(', ', explode(',', $info['Requires_plugins']));
				$requires = cot_rc('install_code_requires',
						array('modules_list' => $modules_list, 'plugins_list' => $plugins_list));
			}
			else
			{
				$requires = '';
			}
			if (!empty($info['Recommends_modules']) || !empty($info['Recommends_plugins']))
			{
				$modules_list = empty($info['Recommends_modules']) ? $L['None']
					: implode(', ', explode(',', $info['Recommends_modules']));
				$plugins_list = empty($info['Recommends_plugins']) ? $L['None']
					: implode(', ', explode(',', $info['Recommends_plugins']));
				$recommends = cot_rc('install_code_recommends',
						array('modules_list' => $modules_list, 'plugins_list' => $plugins_list));
			}
			else
			{
				$recommends = '';
			}
			if (count($selected_list) > 0)
			{
				$checked = in_array($code, $selected_list);
			}
			else
			{
				$checked = in_array($code, $default_list);
			}
			$type = $ext_type == 'Module' ? 'module' : 'plug';
			$L['info_desc'] = '';
			if (file_exists(cot_langfile($code, $type)))
			{
				include cot_langfile($code, $type);
			}
			$t->assign(array(
				"{$ext_type_uc}_ROW_CHECKBOX" => cot_checkbox($checked, "install_{$ext_type_lc}s[$code]"),
				"{$ext_type_uc}_ROW_TITLE" => empty($L['info_name']) ? $info['Name'] : $L['info_name'],
				"{$ext_type_uc}_ROW_DESCRIPTION" => empty($L['info_desc']) ? $info['Description'] : $L['info_desc'],
				"{$ext_type_uc}_ROW_REQUIRES" => $requires,
				"{$ext_type_uc}_ROW_RECOMMENDS" => $recommends
			));
			$t->parse("MAIN.STEP_4.$block_name");
		}
	}
	if ($ext_type_lc == 'plugin' && $prev_cat != '')
	{
		// Render last category
		$t->parse("MAIN.STEP_4.{$ext_type_uc}_CAT");
	}
}

/**
 * Sorts selected extensions by their setup order if present
 *
 * @global array $cfg
 * @param array $selected_extensions Unsorted list of extension names
 * @param bool $is_module TRUE if sorting modules, FALSE if sorting plugins
 * @return array Sorted list of extension names
 */
function cot_install_sort_extensions($selected_extensions, $is_module = FALSE)
{
	global $cfg;
	$path = $is_module ? $cfg['modules_dir'] : $cfg['plugins_dir'];
	$ret = array();

	// Split into groups by Order value
	$extensions = array();
	foreach ($selected_extensions as $name)
	{
		$info = cot_infoget("$path/$name/$name.setup.php", 'COT_EXT');
		$order = isset($info['Order']) ? (int) $info['Order'] : COT_PLUGIN_DEFAULT_ORDER;
		if ($info['Category'] == 'post-install' && $order < 999)
		{
			$order = 999;
		}
		$extensions[$order][] = $name;
	}

	// Merge back into a single array
	foreach ($extensions as $grp)
	{
		foreach ($grp as $name)
		{
			$ret[] = $name;
		}
	}

	return $ret;
}

?>