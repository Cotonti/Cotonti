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

//Various Generic Vars needed to operate as Normal
$skin = $cfg['defaultskin'];
$theme = $cfg['defaulttheme'];
$out['meta_lastmod'] = gmdate("D, d M Y H:i:s");
$file['config'] = './datas/config.php';
$file['config_sample'] = './datas/config-sample.php';
$branch = 'siena';
$prev_branch = 'genoa';
$msg_string = '';

sed_sendheaders();

if (!file_exists("./setup/$branch"))
{
	sed_diefatal($L['install_dir_not_found']);
}

include $file['config'];

$mskin = sed_skinfile('update');
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
	if (count($new_cfg) > count($old_cfg)
		|| count(array_diff($new_db, $old_db)) > 0)
	{
		// Add new config options
		$delta = '';
		if (count($new_cfg) > count($old_cfg))
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
		$config_contents = file_get_contents($file['config']);
		$config_contents = str_replace('?>', $delta.'?>', $config_contents);
		file_put_contents($file['config'], $config_contents);
		sed_message('install_update_config_success');
		include $file['config'];
	}
}
else
{
	// Display some warning
	sed_error('install_update_config_error');
}

$sed_dbc = sed_sql_connect($cfg['mysqlhost'], $cfg['mysqluser'],
	$cfg['mysqlpassword'], $cfg['mysqldb']);
$sql = @sed_sql_query("SELECT upd_value FROM $db_updates
	WHERE upd_param = 'revision'");
$sql2 = @sed_sql_query("SELECT upd_value FROM $db_updates
	WHERE upd_param = 'branch'");
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
			'SUCCESS_MSG' => $applied . $msg_string
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
}
elseif ($old_branch != $branch)
{
	// TODO Other branch upgrade
	sed_print($old_branch);
}
else
{
	$upd_rev = sed_sql_result($sql, 0, 0);
	preg_match('#\$Rev: (\d+) \$#', $upd_rev, $mt);
	$rev = (int) $mt[1];
	$new_rev = sed_apply_patches("./setup/$branch", 'r' . $rev);
	
	if ($new_rev === false)
	{
		// Display error message
		$t->assign(array(
			'ERROR_TITLE' => $L['install_update_error'],
			'ERROR_MSG' => sed_implode_messages()
		));
		$t->parse('MAIN.ERROR');
	}
	elseif ($new_rev === true)
	{
		$t->assign(array(
			'ERROR_TITLE' => $L['install_update_nothing'],
			'ERROR_MSG' => sed_implode_messages()
		));
		$t->parse('MAIN.ERROR');
	}
	else
	{
		sed_sql_query("UPDATE $db_updates SET upd_value = '\$Rev: $new_rev \$'
			WHERE upd_param = 'revision'");
		$t->assign(array(
			'SUCCESS_TITLE' => sed_rc('install_update_success',
				array('rev' => $new_rev)),
			'SUCCESS_MSG' => sed_implode_messages()
		));
		$t->parse('MAIN.SUCCESS');
	}
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