<?php
/**
 * @package Cotonti
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
	sed_diefatal('Setup directory not found'); // TODO: Need translate
}

include $file['config'];

$mskin = sed_skinfile('update');
if (!file_exists($mskin))
{
	sed_diefatal('Update template file not found'); // TODO: Need translate
}
$t = new XTemplate($mskin);

// Check for new config options
if (is_writable($file['config']) && file_exists($file['config_sample']))
{
	list($old_cfg, $old_db) = cot_get_config($file['config']);
	list($new_cfg, $new_db) = cot_get_config($file['config_sample']);
	if (count($new_cfg) > count($old_cfg) || count($new_db) > count($old_db))
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
		if (count($new_db) > count($old_db))
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
		$msg_string .= $L['install_update_config_success'].'<br />';
		include $file['config'];
	}
}
else
{
	// Display some warning
	$msg_string .= $L['install_update_config_error'].'<br />';
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
		// Success
		sed_sql_query("UPDATE $db_updates SET upd_value = '$branch' WHERE upd_param = 'branch'");
		$t->assign(array(
			'SUCCESS_TITLE' => $L['install_upgrade_success'].$branch,
			'SUCCESS_MSG' => "setup/$branch/patch-$prev_branch.sql<br/>".$msg_string
		));
		$t->parse('MAIN.SUCCESS');
	}
	else
	{
		// Error
		$t->assign(array(
			'ERROR_TITLE' => $L['install_upgrade_error'].$branch,
			'ERROR_MSG' => $error.'<br />'.$msg_string
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
	// Check for new Siena patches
	$dp = opendir("./setup/$branch");
	$delta = array();
	while ($f = readdir($dp))
	{
		if (preg_match('#^sql_r(\d+).sql$#', $f, $mt))
		{
			$r = (int) $mt[1];
			if ($r > $rev)
			{
				$delta[$r]['sql'] = './setup/'.$branch.'/'.$mt[0];
			}
		}
		elseif (preg_match('#^php_r(\d+).inc$#', $f, $mt))
		{
			$r = (int) $mt[1];
			if ($r > $rev)
			{
				$delta[$r]['php'] = './setup/'.$branch.'/'.$mt[0];
			}
		}
	}
	closedir($dp);
	if (count($delta) > 0)
	{
		ksort($delta);
		$max_r = $rev;
		$applied = '';
		$error = '';
		foreach ($delta as $key => $val)
		{
			if (isset($val['sql']))
			{
				$error = sed_sql_runscript(file_get_contents($val['sql']));
				if (empty($error))
				{
					$applied .= $val['sql'].'<br />';
				}
				else
				{
					$error .= $val['sql'].'<br />';
					break;
				}
			}
			if (isset($val['php']))
			{
				include $val['php'];
				$applied .= $val['php'].'<br />';
			}
			$max_r = $key;
		}
		if (!empty($error))
		{
			// Display error message
			$t->assign(array(
				'ERROR_TITLE' => $L['install_update_error'],
				'ERROR_MSG' => $error.'<br />'.$msg_string
			));
			$t->parse('MAIN.ERROR');
		}
		else
		{
			sed_sql_query("UPDATE $db_updates SET upd_value = '\$Rev: $max_r \$' WHERE upd_param = 'revision'");
			$t->assign(array(
				'SUCCESS_TITLE' => $L['install_update_success'].$max_r,
				'SUCCESS_MSG' => $applied.$msg_string
			));
			$t->parse('MAIN.SUCCESS');
		}
	}
	else
	{
		$t->assign(array(
			'ERROR_TITLE' => $L['install_update_nothing'],
			'ERROR_MSG' => $msg_string
		));
		$t->parse('MAIN.ERROR');
	}
}

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