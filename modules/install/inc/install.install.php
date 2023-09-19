<?php
/**
 * @package Install
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Modules and plugins checked by default
$default_modules = array('index', 'page', 'users', 'rss');
$default_plugins = array('ckeditor', 'cleaner', 'html', 'htmlpurifier', 'ipsearch', 'mcaptcha', 'indexnews', 'search');

$step = empty($_SESSION['cot_inst_lang']) ? 0 : (int) $cfg['new_install'];

$cfg['msg_separate'] = true;

$mskin = cot_tplfile('install.install');

if (!empty($_SESSION['cot_inst_script']) && file_exists($_SESSION['cot_inst_script'])) {
	require_once $_SESSION['cot_inst_script'];
}

cot_sendheaders();

$t = new XTemplate($mskin);
$server_SERVER_PROTOCOL = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : null;
$server_HTTPS = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : null;
$server_HTTP_X_FORWARDED_PORT = isset($_SERVER['HTTP_X_FORWARDED_PORT']) ? $_SERVER['HTTP_X_FORWARDED_PORT'] : null;
$server_REQUEST_URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$site_url = (strpos($server_SERVER_PROTOCOL, 'HTTPS') === false && $server_HTTPS != 'on' && $_SERVER['SERVER_PORT'] != 443 && $server_HTTP_X_FORWARDED_PORT !== 443 ? 'http://' : 'https://')
	. $_SERVER['HTTP_HOST'] . dirname($server_REQUEST_URI);
$site_url = str_replace('\\', '/', $site_url);
$site_url = preg_replace('#/$#', '', $site_url);
$sys['abs_url'] = $site_url . '/';
define('COT_ABSOLUTE_URL', $site_url . '/');

if ($step > 2) {
    $db = new CotDB([
        'host' => $cfg['mysqlhost'],
        'port' => $cfg['mysqlport'],
        'tablePrefix' => $db_x,
        'user' => $cfg['mysqluser'],
        'password' => $cfg['mysqlpassword'],
        'dbName' => $cfg['mysqldb'],
        'charset' => !empty($cfg['mysqlcharset']) ? $cfg['mysqlcharset'] : null,
        'collate' => !empty($cfg['mysqlcollate']) ? $cfg['mysqlcollate'] : null,
    ]);

    // Need to register DB tables
	Cot::init();
}

// Import section
switch ($step)
{
    // Step 2. $step will be increased later
	case 2:
		$db_host = cot_import('db_host', 'P', 'TXT', 0, false, true);
		$db_port = cot_import('db_port', 'P', 'TXT', 0, false, true);
		$db_user = cot_import('db_user', 'P', 'TXT', 0, false, true);
		$db_pass = cot_import('db_pass', 'P', 'TXT', 0, false, true);
		$db_name = cot_import('db_name', 'P', 'TXT', 0, false, true);
		break;

	case 3:
        $rurl = cot_import('mainurl', 'P', 'TXT', 0, false, true);
        $rurl = $rurl ? $rurl : '';
        $rurl = rtrim($rurl, '/');
		$user['name'] = cot_import('user_name', 'P', 'TXT', 100, false, true);
		$user['pass']  = (string) cot_import('user_pass', 'P', 'NOC', 32);
		$user['pass2'] = (string) cot_import('user_pass2', 'P', 'NOC', 32);
		$user['email'] = cot_import('user_email', 'P', 'TXT', 64, false, true);
		$user['country'] = cot_import('user_country', 'P', 'TXT', 0, false, true);
        $rtheme = cot_import('theme', 'P', 'TXT', 0, false, true);
        $rtheme = $rtheme ? $rtheme : '';
		$rtheme = explode(':', $rtheme);
		$rscheme = isset($rtheme[1]) ? $rtheme[1] : $cfg['defaultscheme'];
		$rtheme = $rtheme[0];
		$rlang = cot_import('lang', 'P', 'TXT', 0, false, true);
		break;

    case 4:
		// Extension selection
		$install_modules = cot_import('install_modules', 'P', 'ARR', 0, false, true);
		$selected_modules = array();
		if (is_array($install_modules)) {
			foreach ($install_modules as $key => $val) {
				if ($val) {
					$selected_modules[] = $key;
				}
			}
		}
		$install_plugins = cot_import('install_plugins', 'P', 'ARR', 0, false, true);
		$selected_plugins = [];
		if (is_array($install_plugins)) {
			foreach ($install_plugins as $key => $val) {
				if ($val) {
					$selected_plugins[] = $key;
				}
			}
		}
		break;
}
$inst_func_name = "cot_install_step".$step."_import";
function_exists($inst_func_name) && $inst_func_name();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Form submission handling
	switch ($step) {
		case 0:
			// Lang selection
			$_SESSION['cot_inst_lang'] = $lang;
			$_SESSION['cot_inst_script'] = cot_import('script', 'P', 'TXT');
            cot_installRedirect('install.php');
			break;

		case 1:
			// System info
			clearstatcache();
			if (!file_exists($file['sql'])) {
				cot_error(cot_rc('install_error_missing_file', array('file' => $file['sql'])));
			}
			if (function_exists('version_compare') && !version_compare(PHP_VERSION, '5.6.0', '>=')) {
				cot_error(cot_rc('install_error_php_ver', array('ver' => PHP_VERSION)));
			}
			if (!extension_loaded('mbstring')) {
				cot_error('install_error_mbstring');
			}
			if (!extension_loaded('pdo_mysql')) {
				cot_error('install_error_sql_ext');
			}
			if (!file_exists($file['config'])) {
				if (!is_writable('datas') || !copy($file['config_sample'], $file['config'])) {
					cot_error('install_error_config');
                }
			}
			break;

		case 2:
			// Database setup
			$db_x = cot_import('db_x', 'P', 'TXT', 0, false, true);

            if (empty($db_host)) {
                cot_error('install_error_sql_host', 'db_host');
            }
            if (empty($db_user)) {
                cot_error('install_error_sql_user', 'db_user');
            }
            if (empty($db_name)) {
                cot_error('install_error_sql_db_name', 'db_name');
            }

            $mySqlVersion = null;
            if (!cot_error_found()) {
                $mySqlCharset = $cfg['mysqlcharset'];
                $mySqlCollate = isset($cfg['mysqlcollate']) ? $cfg['mysqlcollate'] : null;
                // Disable the collation queries until we determine the charset
                $cfg['mysqlcharset'] = $cfg['mysqlcollate'] = null;

                try {
                    $db = new CotDB([
                        'host' => $db_host,
                        'port' => !empty($db_port) ? $db_port : null,
                        'tablePrefix' => $db_x,
                        'user' => $db_user,
                        'password' => $db_pass,
                        'dbName' => $db_name,
                    ]);
                } catch (PDOException $e) {
                    if ($e->getCode() == 1049 || mb_strpos($e->getMessage(), '[1049]') !== false) {
                        // Attempt to create a new database
                        try {
                            $db = new CotDB([
                                'host' => $db_host,
                                'port' => !empty($db_port) ? $db_port : null,
                                'tablePrefix' => $db_x,
                                'user' => $db_user,
                                'password' => $db_pass,
                            ]);
                            $db->query('CREATE DATABASE ' . $db->quoteTableName($db_name));
                            $db->query('USE ' . $db->quoteTableName($db_name));
                        } catch (PDOException $e) {
                            cot_error($L['install_error_sql_db'] . $e->getMessage(), 'db_name');
                        }
                    } else {
                        cot_error($L['install_error_sql'] . $e->getMessage(), 'db_host');
                    }
                }

                if (!cot_error_found()) {
                    if (empty($mySqlCharset)) {
                        $mySqlCharset = 'utf8mb4';
                        $mySqlCollate = 'utf8mb4_unicode_ci';
                    }

                    $mySqlVersion = $db->getConnection()->getAttribute(PDO::ATTR_SERVER_VERSION);
                    $setUseUtf8 = false;
                    if (
                        $mySqlCharset == 'utf8mb4' &&
                        (
                            version_compare($mySqlVersion, '5.7', '<') ||
                            version_compare(PHP_VERSION, '5.5', '<')
                        )
                    ) {
                        $setUseUtf8 = true;
                        $mySqlCharset = 'utf8';
                        $mySqlCollate = 'utf8_unicode_ci';
                    }

                    $cfg['mysqlcharset'] = $mySqlCharset;
                    $cfg['mysqlcollate'] = $mySqlCollate;

                    $collationQuery = 'ALTER DATABASE ' . $db->quoteTableName($db_name) . ' CHARACTER SET ' .
                        $cfg['mysqlcharset'];
                    if (!empty($cfg['mysqlcollate'])) {
                        $collationQuery .= ' COLLATE ' . $cfg['mysqlcollate'];
                    }
                    $collationQuery .= ';';
                    $collationQuery .= " SET NAMES '{$cfg['mysqlcharset']}'";
                    if (!empty($cfg['mysqlcollate']) ) {
                        $collationQuery .= " COLLATE '{$cfg['mysqlcollate']}'";
                    }

                    $db->query($collationQuery);
                }
            }

			if (!cot_error_found() && !version_compare($mySqlVersion, '5.0.7', '>=')) {
				cot_error(cot_rc(
                    'install_error_sql_ver',
                    array('ver' => $db->getConnection()->getAttribute(PDO::ATTR_SERVER_VERSION))
                ));
			}

			if (!cot_error_found()) {
				Cot::init();

				$config_contents = file_get_contents($file['config']);
				cot_installConfigReplace($config_contents, 'mysqlhost', $db_host);
				if (!empty($db_port)) {
					cot_installConfigReplace($config_contents, 'mysqlport', $db_port);
				}
				cot_installConfigReplace($config_contents, 'mysqluser', $db_user);
				cot_installConfigReplace($config_contents, 'mysqlpassword', $db_pass);
				cot_installConfigReplace($config_contents, 'mysqldb', $db_name);
                if ($setUseUtf8) {
                    cot_installConfigReplace($config_contents, 'mysqlcharset', $cfg['mysqlcharset']);
                    cot_installConfigReplace($config_contents, 'mysqlcollate', $cfg['mysqlcollate']);
                }

				$config_contents = preg_replace("#^\\\$db_x\s*=\s*'.*?';#m", "\$db_x = '$db_x';", $config_contents);
				file_put_contents($file['config'], $config_contents);

				$sql_file = file_get_contents($file['sql']);
				$error = $db->runScript($sql_file);

				if ($error) {
					cot_error(cot_rc('install_error_sql_script', array('msg' => $error)));
				}
			}
			break;

		case 3:
			// Misc settings and admin account
			if (empty($rurl)) {
				cot_error('install_error_mainurl', 'mainurl');
			}
			if ($user['pass'] != $user['pass2']) {
				cot_error('aut_passwordmismatch', 'user_pass');
			}
			if (mb_strlen($user['name']) < 2) {
				cot_error('aut_usernametooshort', 'user_name');
			}
			if (mb_strlen($user['pass']) < 4) {
				cot_error('aut_passwordtooshort', 'user_pass');
			}
			if (mb_strlen($user['email']) < 4 || !cot_check_email($user['email'])) {
				cot_error('aut_emailtooshort', 'user_email');
			}
			if (!file_exists($file['config_sample'])) {
				cot_error(cot_rc('install_error_missing_file', array('file' => $file['config_sample'])));
			}

			if (!cot_error_found()) {
				$config_contents = file_get_contents($file['config']);
				cot_installConfigReplace($config_contents, 'defaultlang', $rlang);
				cot_installConfigReplace($config_contents, 'defaulttheme', $rtheme);
				cot_installConfigReplace($config_contents, 'defaultscheme', $rscheme);

                $rurl = rtrim($rurl, '/');
				cot_installConfigReplace($config_contents, 'mainurl', $rurl);
                $cfg['mainurl'] = $rurl;

				$new_site_id = cot_unique(32);
				cot_installConfigReplace($config_contents, 'site_id', $new_site_id);
				$new_secret_key = cot_unique(32);
				cot_installConfigReplace($config_contents, 'secret_key', $new_secret_key);

                $url = parse_url($rurl);
                $domain = preg_replace('#^www\.#', '', $url['host']);
                $config_contents = str_replace('mail_sender@localhost', 'mail_sender@'.$domain, $config_contents);

				file_put_contents($file['config'], $config_contents);

				$ruserpass['user_passsalt'] = cot_unique(16);
				$ruserpass['user_passfunc'] = empty($cfg['hashfunc']) ? 'sha256' : $cfg['hashfunc'];
				$ruserpass['user_password'] = cot_hash($user['pass'], $ruserpass['user_passsalt'], $ruserpass['user_passfunc']);

				try {
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

					$db->update($db_x . 'config', array('config_value' => $user['email']), "config_owner = 'core' AND config_name = 'adminemail'");
				} catch (PDOException $err) {
					cot_error(cot_rc('install_error_sql_script', array('msg' => $err->getMessage())));
				}

                // robots.txt
                $robotsTxtFilePath = './robots.txt';
                if (file_exists($robotsTxtFilePath) && is_writable($robotsTxtFilePath)) {
                    $robotsTxtFile = file_get_contents($robotsTxtFilePath);
                    $tmp = 'Host: '.$domain;
                    $robotsTxtFile = str_replace('# Host: https://your-domain.com', $tmp, $robotsTxtFile);
                    file_put_contents($robotsTxtFilePath, $robotsTxtFile);
                }
			}
			break;

		case 4:
			// Dependency check
			$install = true;
			foreach ($selected_modules as $ext) {
				$install &= cot_extension_dependencies_statisfied($ext, true, $selected_modules, $selected_plugins);
			}
			foreach ($selected_plugins as $ext) {
				$install &= cot_extension_dependencies_statisfied($ext, false, $selected_modules, $selected_plugins);
			}

			if ($install && !cot_error_found()) {
				// Load groups
				$cot_groups = array();
				$res = $db->query("SELECT * FROM $db_groups WHERE grp_disabled=0 ORDER BY grp_level DESC");
				while ($row = $res->fetch()) {
					$cot_groups[$row['grp_id']] = array(
						'id' => $row['grp_id'],
						'alias' => $row['grp_alias'],
						'level' => $row['grp_level'],
						'disabled' => $row['grp_disabled'],
						'name' => htmlspecialchars($row['grp_name']),
						'title' => htmlspecialchars($row['grp_title'])
					);
				}
				$res->closeCursor();
				$usr['id'] = 1;
				// Install all at once
				// Note: installation statuses are ignored in this installer
				$selected_modules = cot_installSortExtensions($selected_modules, true);
				foreach ($selected_modules as $ext) {
					if (!cot_extension_install($ext, true)) {
						cot_error("Installing $ext module has failed");
					}
				}
				$selected_plugins = cot_installSortExtensions($selected_plugins, false);
				foreach ($selected_plugins as $ext) {
					if (!cot_extension_install($ext, false)) {
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
            cot_installRedirect(cot_url('index'));
	}

	$inst_func_name = "cot_install_step".$step."_setup";
	function_exists($inst_func_name) && $inst_func_name();

	if (cot_error_found()) {
		// One step back
        cot_installRedirect('install.php');
	}

    // Step++
    $step++;
    $config_contents = file_get_contents($file['config']);
    if ($step == 5) {
        $config_contents = preg_replace(
            "#^\\\$cfg\['new_install'\]\s*=\s*.*?;#m",
            "\$cfg['new_install'] = false;",
            $config_contents
        );
    } else {
        $config_contents = preg_replace(
            "#^\\\$cfg\['new_install'\]\s*=\s*.*?;#m",
            "\$cfg['new_install'] = $step;",
            $config_contents
        );
    }

    function_exists("cot_install_stepplusplus") && cot_install_stepplusplus();

    file_put_contents($file['config'], $config_contents);
}

// Display
switch ($step) {
	case 0:
		// Language selection
		$t->assign(array(
			'INSTALL_LANG' => cot_selectbox_lang($lang, 'lang')
		));

		$install_files = glob("*.install.php");

		if (!empty($install_files)) {
			$install_scripts = array();
			foreach ($install_files as $filename) {
				preg_match("#(.*?)\/?(.+)\.install\.php#i", $filename, $mtch);
				$install_scripts[$filename] = $mtch[2];
			}
			$t->assign(array(
				'INSTALL_SCRIPT' => cot_selectbox('', 'script', array_keys($install_scripts), array_values($install_scripts))
			));
			$t->parse("MAIN.STEP_$step.SCRIPT");
		}
		break;

	case 1:
		// Create missing cache folders
		if (is_writable($cfg['cache_dir'])) {
			$cache_subfolders = array('cot', 'static', 'system', 'templates');
			foreach ($cache_subfolders as $sub) {
				if (!file_exists($cfg['cache_dir'] . '/' . $sub)) {
					mkdir($cfg['cache_dir'] . '/' . $sub, $cfg['dir_perms']);
				}
			}
		}

		// System info
		// Build CHMOD/Exists/Version data
		clearstatcache();

		if (is_dir($cfg['avatars_dir'])) {
			$status['avatars_dir'] = is_writable($cfg['avatars_dir'])
				? $R['install_code_writable']
				: cot_rc('install_code_invalid', array('text' =>
					cot_rc('install_chmod_value', array('chmod' =>
						substr(decoct(fileperms($cfg['avatars_dir'])), -4)))));
		} else {
			$status['avatars_dir'] = $R['install_code_not_found'];
		}
		/* ------------------- */
		if (is_dir($cfg['cache_dir'])) {
			$status['cache_dir'] = is_writable($cfg['cache_dir'])
				? $R['install_code_writable']
				: cot_rc('install_code_invalid', array('text' =>
					cot_rc('install_chmod_value', array('chmod' =>
						substr(decoct(fileperms($cfg['cache_dir'])), -4)))));
		} else {
			$status['cache_dir'] = $R['install_code_not_found'];
		}
		/* ------------------- */
		if (is_dir($cfg['pfs_dir'])) {
			$status['pfs_dir'] = is_writable($cfg['pfs_dir'])
				? $R['install_code_writable']
				: cot_rc('install_code_invalid', array('text' =>
					cot_rc('install_chmod_value', array('chmod' =>
						substr(decoct(fileperms($cfg['pfs_dir'])), -4)))));
		} else {
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
		$status['php_ver'] = (function_exists('version_compare')
            && version_compare(PHP_VERSION, '5.6.0', '>='))
			? cot_rc(
                'install_code_valid',
                ['text' => cot_rc('install_ver_valid', ['ver' => PHP_VERSION])]
            )
			: cot_rc(
                'install_code_invalid',
                ['text' => cot_rc('install_ver_invalid', ['ver' => PHP_VERSION])]
            );
		$status['mbstring'] = (extension_loaded('mbstring'))
			? $R['install_code_available'] : $R['install_code_not_available'];
		$status['hash'] = (extension_loaded('hash') && function_exists('hash_hmac'))
			? $R['install_code_available'] : $R['install_code_not_available'];
		$status['mysql'] = (extension_loaded('pdo_mysql'))
			? $R['install_code_available'] : $R['install_code_not_available'];

        // TODO проверить PDO json bcmath

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
        $db_host = !isset($db_host) ? $cfg['mysqlhost'] : $db_host;
        $db_port = !isset($db_port) ? $cfg['mysqlport'] : $db_port;
		$db_user = !isset($db_user) ? $cfg['mysqluser'] : $db_user;
		$db_name = !isset($db_name) ? $cfg['mysqldb'] : $db_name;

		$t->assign(array(
			'INSTALL_DB_HOST' => $db_host,
			'INSTALL_DB_PORT' => $db_port,
			'INSTALL_DB_USER' => $db_user,
			'INSTALL_DB_NAME' => $db_name,
			'INSTALL_DB_X' => $db_x,
			'INSTALL_DB_HOST_INPUT' => cot_inputbox('text', 'db_host', $db_host, 'size="32"'),
			'INSTALL_DB_PORT_INPUT' => cot_inputbox('text', 'db_port', $db_port, 'size="32"'),
			'INSTALL_DB_USER_INPUT' => cot_inputbox('text', 'db_user',  $db_user, 'size="32"'),
			'INSTALL_DB_NAME_INPUT' => cot_inputbox('text', 'db_name',  $db_name, 'size="32"'),
			'INSTALL_DB_PASS_INPUT' => cot_inputbox('password', 'db_pass', '', 'size="32"'),
			'INSTALL_DB_X_INPUT' => cot_inputbox('text', 'db_x',  $db_x, 'size="32"'),
		));
		break;

	case 3:
		// Settings
        $user['name'] = isset($user['name']) ? $user['name'] : '';
        $user['email'] = isset($user['email']) ? $user['email'] : '';
        $user['country'] = isset($user['country']) ? $user['country'] : '';
        $rtheme = isset($rtheme) ? $rtheme : $theme;
        $rscheme = isset($rscheme) ? $rscheme : $scheme;
        $rlang = isset($rlang) ? $rlang : $lang;
        $rurl = (isset($rurl) && $rurl !== '') ? $rurl : $site_url;

		$t->assign(array(
			'INSTALL_THEME_SELECT' => cot_selectbox_theme($rtheme, $rscheme, 'theme'),
			'INSTALL_LANG_SELECT' => cot_selectbox_lang($rlang, 'lang'),
			'INSTALL_COUNTRY_SELECT' => cot_selectbox_countries($user['country'], 'user_country'),
			'INSTALL_MAINURL' => cot_inputbox('text', 'mainurl', $rurl, 'size="32"'),
			'INSTALL_USERNAME' => cot_inputbox('text', 'user_name', $user['name'], 'size="32"'),
			'INSTALL_PASS1' => cot_inputbox('password', 'user_pass', '', 'size="32"'),
			'INSTALL_PASS2' => cot_inputbox('password', 'user_pass2', '', 'size="32"'),
			'INSTALL_EMAIL' => cot_inputbox('text', 'user_email', $user['email'], 'size="32"'),
		));
        break;

	case 4:
		// Extensions
        $selected_modules = isset($selected_modules) ? $selected_modules : '';
        $selected_plugins = isset($selected_plugins) ? $selected_plugins : '';
		cot_installParseExtensions('Module', $default_modules, $selected_modules);
		cot_installParseExtensions('Plugin', $default_plugins, $selected_plugins);
		break;

	case 5:
		// End credits
		break;

}

$inst_func_name = "cot_install_step".$step."_tags";
function_exists($inst_func_name) && $inst_func_name();

$t->parse("MAIN.STEP_$step");

// Error & message display
cot_display_messages($t);

$t->assign(array(
	'INSTALL_STEP' => $step == 5 ? $L['Complete'] : cot_rc('install_step', array('step' => $step, 'total' => 4)),
	'INSTALL_LANG' => cot_selectbox_lang($lang, 'lang')
));


$t->parse('MAIN');
$t->out('MAIN');
