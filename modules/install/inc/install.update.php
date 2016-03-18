<?php
/**
 * @package Install
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');
define('COT_UPDATE', true);

cot_sendheaders();

if (!file_exists("./setup/$branch"))
{
	cot_diefatal($L['install_dir_not_found']);
}

// include $file['config'];

$mskin = cot_tplfile('install.update');
if (!file_exists($mskin))
{
	cot_diefatal($L['install_update_template_not_found']);
}
$t = new XTemplate($mskin);

// Check for new config options
if (is_writable($file['config']) && file_exists($file['config_sample']))
{
	list($old_cfg, $old_db) = cot_get_config($file['config']);
	list($new_cfg, $new_db) = cot_get_config($file['config_sample']);
	if (count(array_diff($new_cfg, $old_cfg)) > 0
		|| count(array_diff($new_db, $old_db)) > 0)
	{
		// Add new config options
		$delta = '';
		if (count(array_diff($new_cfg, $old_cfg)) > 0)
		{
			foreach ($new_cfg as $key => $val)
			{
				if (!isset($old_cfg[$key]))
				{
					if ($key == 'new_install')
					{
						$val = false;
					}
					elseif ($key == 'site_id' || $key == 'secret_key')
					{
						$val = cot_unique(32);
					}

					if (is_bool($val))
					{
						$val = $val ? 'TRUE' : 'FALSE';
					}
					elseif (is_int($val) || is_float($val))
					{
						$val = (string) $val;
					}
					else
					{
						$val = "'$val'";
					}
					$delta .= "\$cfg['$key'] = $val;\n";
				}
			}
		}
		if (count(array_diff($new_db, $old_db)) > 0)
		{
			foreach ($new_db as $key => $val)
			{
				if (!isset($old_db[$key]))
				{
					$val = str_replace("cot_", "\$db_x.'", $val);
					$delta .= "\${$key} = $val';\n";
				}
			}
		}
		if (!empty($delta))
		{
			$config_contents = file_get_contents($file['config']);
			// strip PHP closing tag if exists
			if (substr($config_contents, -2) == '?>') $config_contents = substr($config_contents, 0, -2);
			$config_contents .= $delta;
			file_put_contents($file['config'], $config_contents);
			cot_message('install_update_config_success');
			$updated_config = true;
			include $file['config'];
		}
	}
}
else
{
	// Display some warning
	cot_error('install_update_config_error');
}

// Force config options
$cfg['display_errors'] = true;
$cfg['debug_mode'] = true;
$cfg['customfuncs'] = false;

if (defined('COT_UPGRADE') && !cot_error_found())
{
	// Is Genoa, perform upgrade

	// Create missing cache folders
	$cache_subfolders = array('cot', 'static', 'system', 'templates');
	foreach ($cache_subfolders as $sub)
	{
		if (!file_exists($cfg['cache_dir'] . '/' . $sub))
		{
			mkdir($cfg['cache_dir'] . '/' . $sub, $cfg['dir_perms']);
		}
	}

	// Run SQL patches for core
	$script = file_get_contents("./setup/$branch/patch-$prev_branch.sql");
	$error = $db->runScript($script);
	if (empty($error))
	{
		cot_message(cot_rc('install_update_patch_applied',
			array('f' => "setup/$branch/patch-$prev_branch.sql",
				'msg' => 'OK')));
	}
	else
	{
		cot_error(cot_rc('install_update_patch_error',
			array('f' => "setup/$branch/patch-$prev_branch.sql",
				'msg' => $error)));
	}

	// Run PHP patches
	$ret = include "./setup/$branch/patch-$prev_branch.inc";
	if ($ret !== false)
	{
		$msg = $ret == 1 ? 'OK' : $ret;
		cot_message('install_update_patch_applied',
			array('f' => "setup/$branch/patch-$prev_branch.inc",
				'msg' => $ret));
	}
	else
	{
		cot_error('install_update_patch_error',
			array('f' => "setup/$branch/patch-$prev_branch.inc",
				'msg' => $L['Error']));
	}

	// Unregister modules which have no registration anymore
	$db->delete($db_core, "ct_code IN ('comments', 'ratings', 'trash')");

	// Set Module versions to Genoa version before upgrade
	$db->update($db_core, array('ct_version' => '0.8.99'), '1');

	// Update modules
	foreach (array('forums', 'index', 'page', 'pfs', 'pm', 'polls', 'users') as $code)
	{
		$ret = cot_extension_install($code, true, true);
		if ($ret === false)
		{
			cot_error(cot_rc('ext_update_error', array(
				'type' => $L['Module'],
				'name' => $code
			)));
		}
	}

	// Update installed Siena plugins and uninstall Genoa plugins
	$res = $db->query("SELECT DISTINCT(pl_code) FROM $db_plugins
		WHERE pl_module = 0");
	while ($row = $res->fetch(PDO::FETCH_NUM))
	{
		$code = $row[0];
		$setup_file = $cfg['plugins_dir'] . '/' . $code . '/' . $code . '.setup.php';
		if (file_exists($setup_file) && $info = cot_infoget($setup_file))
		{
			// Update
			cot_extension_add($code, $info['Name'], '0.0.0', true);
			cot_extension_install($code, false, true);
		}
		else
		{
			// Uninstall
			$qcode = $db->quote($code);
			$db->delete($db_auth, "auth_option = $qcode");
			$db->delete($db_config, "config_cat = $qcode");
			$db->delete($db_plugins, "pl_code = $qcode");
		}
	}
	$res->closeCursor();

	// Install bbcode and html parsers
	cot_extension_install('bbcode');
	cot_extension_install('html');
	// Import old Seditio/LDU bbcodes
	$db->runScript(file_get_contents('./setup/siena/seditio_bbcodes.sql'));

	// Install URLEditor if urltrans.dat is non-standard
	if (file_exists('datas/urltrans.dat'))
	{
		$urltrans_dat = trim(file_get_contents('datas/urltrans.dat'));
		if ($urltrans_dat != '*	*	{$_area}.php')
		{
			cot_extension_install('urleditor');
		}
	}

	// Install userimages plugin
	cot_extension_install('userimages');

	// Update config theme and scheme
	$config_contents = file_get_contents($file['config']);
	$config_contents = preg_replace('#^\$cfg\[\'defaultscheme\'\]\s*=\s*\'.*?\';\n?#m', '', $config_contents);
	$config_contents = preg_replace('#^\$cfg\[\'defaulttheme\'\]\s*=.*?;#m',
		"\$cfg['defaultscheme'] = 'default';", $config_contents);
	$config_contents = preg_replace('#^\$cfg\[\'defaultskin\'\]\s*=.*?;#m',
		"\$cfg['defaulttheme'] = 'nemesis';", $config_contents);
	file_put_contents($file['config'], $config_contents);

	// Display results
	if (!cot_error_found())
	{
		// Success
		$t->assign('UPDATE_COMPLETED_NOTE', $L['install_upgrade_success_note']);
		$t->parse('MAIN.COMPLETED');
		$db->update($db_updates,  array('upd_value' => $branch), "upd_param = 'branch'");
		$t->assign('UPDATE_TITLE', cot_rc('install_upgrade_success', array('ver' => $branch)));
	}
	else
	{
		// Error
		$t->assign('UPDATE_TITLE', cot_rc('install_upgrade_error', array('ver' => $branch)));
	}

	$t->assign(array(
		'UPDATE_FROM' => $prev_branch,
		'UPDATE_TO' => $branch
	));
}
elseif (!cot_error_found())
{
	// Update the core
	$sql_install = $db->query("SELECT upd_value FROM $db_updates WHERE upd_param = 'revision'");
	$upd_rev = $sql_install->fetchColumn();
	if (preg_match('#\$Rev: (\d+) \$#', $upd_rev, $mt))
	{
		// Old SVN revision format
		if ($mt[1] > 2099)
		{
			$rev = '0.9.3';
		}
		elseif ($mt[1] > 2033)
		{
			$rev = '0.9.2';
		}
		elseif ($mt[1] > 1972)
		{
			$rev = '0.9.1';
		}
		else
		{
			$rev = '0.9.0';
		}
		$rev .=  '-r' . $mt[1];
	}
	else
	{
		// New revision format
		$rev = $upd_rev;
	}
	$new_rev = cot_apply_patches("./setup/$branch", $rev);

	// Update installed modules and plugins
	$updated_ext = false;
	if(count($cot_modules)>0)
	{
		foreach ($cot_modules as $code => $mod)
		{
			$ret = cot_extension_install($code, true, true);
			if ($ret === true)
			{
				$updated_ext = true;
			}
			elseif ($ret === false)
			{
				cot_error(cot_rc('ext_update_error', array(
					'type' => $L['Module'],
					'name' => $code
				)));
			}
		}
	}
	if(count($cot_plugins_enabled)>0)
	{
		foreach ($cot_plugins_enabled as $code => $plug)
		{
			$ret = cot_extension_install($code, false, true);
			if ($ret === true)
			{
				$updated_ext = true;
			}
			elseif ($ret === false)
			{
				cot_error(cot_rc('ext_update_error', array(
					'type' => $L['Plugin'],
					'name' => $code
				)));
			}
		}
	}

	if ($new_rev === false || cot_error_found())
	{
		// Display error message
		$t->assign('UPDATE_TITLE', $L['install_update_error']);
	}
	elseif ($new_rev === true && !$updated_config && !$updated_ext)
	{
		$t->assign('UPDATE_TITLE', $L['install_update_nothing']);
		$t->assign('UPDATE_COMPLETED_NOTE', '');
		$t->parse('MAIN.COMPLETED');
	}
	else
	{
		if ($new_rev === true)
		{
			$new_rev = $rev;
		}
		else
		{
			$db->update($db_updates, array('upd_value' => $new_rev), "upd_param = 'revision'");
		}
		$t->assign('UPDATE_TITLE', cot_rc('install_update_success', array('rev' => $new_rev)));
		$t->assign('UPDATE_COMPLETED_NOTE', $L['install_complete_note']);
		$t->parse('MAIN.COMPLETED');
	}

	$t->assign(array(
		'UPDATE_FROM' => $rev,
		'UPDATE_TO' => is_string($new_rev) ? $new_rev : $rev
	));

	// Clear cache
	$db->query("TRUNCATE TABLE $db_cache");
}

cot_display_messages($t);

$t->parse('MAIN');
$t->out('MAIN');

function cot_get_config($file)
{
	include $file;
	$db_vars = array();
	$vars = get_defined_vars();
	foreach ($vars as $key => $val)
	{
		if (preg_match('#^db_#', $key))
		{
			$db_vars[$key] = $val;
		}
	}
	return array($cfg, $db_vars);
}
