<?php
/**
 * @package install
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');
define('COT_UPDATE', true);

$branch = 'siena';
$prev_branch = 'genoa';

sed_sendheaders();

if (!file_exists("./setup/$branch"))
{
	sed_diefatal($L['install_dir_not_found']);
}

include $file['config'];

$mskin = sed_skinfile('install.update');
if (!file_exists($mskin))
{
	sed_diefatal($L['install_update_template_not_found']);
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
                    elseif ($key == 'site_id')
                    {
                        $val = sed_unique(32);
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
					$val = str_replace("sed_", "\$db_x.'", $val);
					$delta .= "\${$key} = $val';\n";
				}
			}
		}
		if (!empty($delta))
		{
			$config_contents = file_get_contents($file['config']);
			$config_contents = str_replace('?>', $delta.'?>', $config_contents);
			file_put_contents($file['config'], $config_contents);
			sed_message('install_update_config_success');
			$updated_config = true;
			include $file['config'];
		}
	}
}
else
{
	// Display some warning
	sed_error('install_update_config_error');
}

$sed_dbc = sed_sql_connect($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword'], $cfg['mysqldb']);

$sql = @sed_sql_query("SELECT upd_value FROM $db_updates WHERE upd_param = 'revision'");
$sql2 = @sed_sql_query("SELECT upd_value FROM $db_updates WHERE upd_param = 'branch'");
$old_branch = @sed_sql_result($sql2, 0, 0);

if (sed_sql_errno() > 0 || sed_sql_numrows($sql) != 1)
{
	// Is Genoa, perform upgrade
	$script = file_get_contents("./setup/$branch/patch-$prev_branch.sql");
	$error = sed_sql_runscript($script);
	if (empty($error))
	{
		sed_message(sed_rc('install_update_patch_applied',
			array('f' => "setup/$branch/patch-$prev_branch.sql",
				'msg' => 'OK')));
	}
	else
	{
		sed_error(sed_rc('install_update_patch_error',
			array('f' => "setup/$branch/patch-$prev_branch.sql",
				'msg' => $error)));
	}
	if (file_exists("./setup/$branch/patch-$prev_branch.inc"))
	{
		$ret = include "./setup/$branch/patch-$prev_branch.inc";
		if ($ret !== false)
		{
			$msg = $ret == 1 ? 'OK' : $ret;
			sed_message('install_update_patch_applied',
				array('f' => "setup/$branch/patch-$prev_branch.inc",
					'msg' => $ret));
		}
		else
		{
			sed_error('install_update_patch_error',
				array('f' => "setup/$branch/patch-$prev_branch.inc",
					'msg' => $L['Error']));
		}
	}
	if (!$cot_error)
	{
		// Success
		sed_sql_query("UPDATE $db_updates SET upd_value = '$branch'
			WHERE upd_param = 'branch'");
		$t->assign(array(
			'SUCCESS_TITLE' => sed_rc('install_upgrade_success',
				array('ver' => $branch)),
			'SUCCESS_MSG' => sed_implode_messages()
		));
		$t->parse('MAIN.SUCCESS');
	}
	else
	{
		// Error
		$t->assign(array(
			'ERROR_TITLE' => sed_rc('install_upgrade_error',
				array('ver' => $branch)),
			'ERROR_MSG' => sed_implode_messages()
		));
		$t->parse('MAIN.ERROR');
	}

	$t->assign(array(
		'UPDATE_FROM' => $prev_branch,
		'UPDATE_TO' => $branch
	));
}
elseif ($old_branch != $branch)
{
	die("Upgrade from $old_branch is not supported");
}
else
{
	// Update the core
	$upd_rev = sed_sql_result($sql, 0, 0);
	preg_match('#\$Rev: (\d+) \$#', $upd_rev, $mt);
	$rev = (int) $mt[1];
	$new_rev = sed_apply_patches("./setup/$branch", 'r' . $rev);
	if (is_string($new_rev))
	{
		$new_rev = (int) substr($new_rev, 1);
	}

	// Update installed modules and plugins
	$updated_ext = false;
	foreach ($sed_modules as $code => $mod)
	{
		$ret = sed_extension_install($code, true, true);
		if ($ret === true)
		{
			$updated_ext = true;
		}
		elseif ($ret === false)
		{
			sed_error(sed_rc('ext_update_error', array(
				'type' => $L['Module'],
				'name' => $name
			)));
		}
	}
	$installed_plugs = array();
	$res = sed_sql_query("SELECT DISTINCT(pl_code) FROM $db_plugins
		WHERE pl_module = 0 AND pl_active = 1");
	while ($row = sed_sql_fetchrow($res))
	{
		$installed_plugs[] = $row[0];
	}
	sed_sql_freeresult($res);
	foreach ($installed_plugs as $code)
	{
		$ret = sed_extension_install($code, false, true);
		if ($ret === true)
		{
			$updated_ext = true;
		}
		elseif ($ret === false)
		{
			sed_error(sed_rc('ext_update_error', array(
				'type' => $L['Plugin'],
				'name' => $name
			)));
		}
	}
	
	if ($new_rev === false || $cot_error)
	{
		// Display error message
		$t->assign(array(
			'ERROR_TITLE' => $L['install_update_error'],
			'ERROR_MSG' => sed_implode_messages()
		));
		$t->parse('MAIN.ERROR');
	}
	elseif ($new_rev === true && !$updated_config && !$updated_ext)
	{
		$t->assign(array(
			'ERROR_TITLE' => $L['install_update_nothing'],
			'ERROR_MSG' => sed_implode_messages()
		));
		$t->parse('MAIN.ERROR');
	}
	else
	{
		if ($new_rev === true)
		{
			$new_rev = $rev;
		}
		else
		{
			sed_sql_query("UPDATE $db_updates SET upd_value = '\$Rev: $new_rev \$'
				WHERE upd_param = 'revision'");
		}
		$t->assign(array(
			'SUCCESS_TITLE' => sed_rc('install_update_success',
				array('rev' => $new_rev)),
			'SUCCESS_MSG' => sed_implode_messages()
		));
		$t->parse('MAIN.SUCCESS');
	}

	$t->assign(array(
		'UPDATE_FROM' => 'r' . $rev,
		'UPDATE_TO' => is_int($new_rev) ? 'r' . $new_rev : 'r' . $rev
	));
}

sed_clear_messages();

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

?>