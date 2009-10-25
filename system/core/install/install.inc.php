<?PHP

/**
 * @package Cotonti
 * @version 0.7.0
 * @author Kilandor
 * @copyright Copyright (c) Cotonti Team 2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

//Various Generic Vars needed to operate as Normal
$skin = $cfg['defaultskin'];
$theme = $cfg['defaulttheme'];
$out['meta_lastmod'] = gmdate("D, d M Y H:i:s");
$file['config'] = './datas/config.php';
$file['config_sample'] = './datas/config-sample.php';
$file['sql'] = './sql/cotonti-'.$cfg['dbversion'].'.sql';

$mskin = sed_skinfile('install');
$t = new XTemplate($mskin);

if($_POST['submit'])
{
	$cfg['mysqlhost'] = sed_import('db_host', 'P', 'TXT');
	$cfg['mysqluser'] = sed_import('db_user', 'P', 'TXT');
	$cfg['mysqlpassword'] = sed_import('db_pass', 'P', 'TXT');
	$cfg['mysqldb'] = sed_import('db_name', 'P', 'TXT');
	$db_x = sed_import('db_x', 'P', 'TXT');
	$rskin = sed_import('skin', 'P', 'TXT');
	$rtheme = sed_import('theme', 'P', 'TXT');
	$rtheme = ($skin == $rtheme && $rskin != $rtheme) ? $rskin : $rtheme;
	$rlang = sed_import('lang', 'P', 'TXT');

	$connection = sed_sql_connect($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword'], $cfg['mysqldb']);
	$error .= ($connection == 1) ? $L['install_error_sql'].'<br />' : '';
	$error .= ($connection == 2) ? $L['install_error_sql_db'].'<br />' : '';

	if(!$error)
	{
		$sql_file = file_get_contents($file['sql']);
		$sql_queries = preg_split('/;\r?\n/', $sql_file);
		foreach($sql_queries as $sql_query)
		{
			$sql_query = str_replace('`sed_', '`'.$db_x, $sql_query);
		}
	}
}
else
{
	$rskin = $skin;
	$rtheme = $theme;
	$rlang = $cfg['defaultlang'];
}

//Build CHMOD/Exists/Version data
clearstatcache();

if(is_dir($cfg['av_dir']))
	$status['av_dir'] = (substr(decoct(fileperms($cfg['av_dir'])), -4) >= $cfg['dir_perms']) ? '<span class="install_valid">'.$L['install_writable'].'</span>' : '<span class="install_invalid">'.sprintf($L['install_chmod_value'], substr(decoct(fileperms($cfg['av_dir'])), -4)).'</span>';
else
	$status['av_dir'] = '<span class="install_invalid">'.$L['nf'].'</span>';
/* ------------------- */
if(is_dir($cfg['cache_dir']))
	$status['cache_dir'] = (substr(decoct(fileperms($cfg['cache_dir'])), -4) >= $cfg['dir_perms']) ? '<span class="install_valid">'.$L['install_writable'].'</span>' : '<span class="install_invalid">'.sprintf($L['install_chmod_value'], substr(decoct(fileperms($cfg['cache_dir'])), -4)).'</span>';
else
	$status['cache_dir'] = '<span class="install_invalid">'.$L['nf'].'</span>';
/* ------------------- */
if(is_dir($cfg['pfs_dir']))
	$status['pfs_dir'] = (substr(decoct(fileperms($cfg['pfs_dir'])), -4) >= $cfg['dir_perms']) ? '<span class="install_valid">'.$L['install_writable'].'</span>' : '<span class="install_invalid">'.sprintf($L['install_chmod_value'], substr(decoct(fileperms($cfg['pfs_dir'])), -4)).'</span>';
else
	$status['pfs_dir'] = '<span class="install_invalid">'.$L['nf'].'</span>';
/* ------------------- */
if(is_dir($cfg['photos_dir']))
	$status['photos_dir'] = (substr(decoct(fileperms($cfg['photos_dir'])), -4) >= $cfg['dir_perms']) ? '<span class="install_valid">'.$L['install_writable'].'</span>' : '<span class="install_invalid">'.sprintf($L['install_chmod_value'], substr(decoct(fileperms($cfg['photos_dir'])), -4)).'</span>';
else
	$status['photos_dir'] = '<span class="install_invalid">'.$L['nf'].'</span>';
/* ------------------- */
if(is_dir($cfg['sig_dir']))
	$status['sig_dir'] = (substr(decoct(fileperms($cfg['sig_dir'])), -4) >= $cfg['dir_perms']) ? '<span class="install_valid">'.$L['install_writable'].'</span>' : '<span class="install_invalid">'.sprintf($L['install_chmod_value'], substr(decoct(fileperms($cfg['sig_dir'])), -4)).'</span>';
else
	$status['sig_dir'] = '<span class="install_invalid">'.$L['nf'].'</span>';
/* ------------------- */
if(is_dir($cfg['th_dir']))
	$status['th_dir'] = (substr(decoct(fileperms($cfg['th_dir'])), -4) >= $cfg['dir_perms']) ? '<span class="install_valid">'.$L['install_writable'].'</span>' : '<span class="install_invalid">'.sprintf($L['install_chmod_value'], substr(decoct(fileperms($cfg['th_dir'])), -4)).'</span>';
else
	$status['th_dir'] = '<span class="install_invalid">'.$L['nf'].'</span>';
/* ------------------- */
if(file_exists($file['config']))
	$status['config'] = (substr(decoct(fileperms($file['config'])), -4) >= $cfg['file_perms']) ? '<span class="install_valid">'.$L['install_writable'].'</span>' : '<span class="install_invalid">'.sprintf($L['install_chmod_value'], substr(decoct(fileperms($file['config'])), -4)).'</span>';
else
	$status['config'] = '<span class="install_invalid">'.$L['nf'].'</span>';
/* ------------------- */
if(file_exists($file['config_sample']))
	$status['config_sample'] = '<span class="install_valid">'.$L['Found'].'</span>';
else
	$status['config_sample'] = '<span class="install_invalid">'.$L['nf'].'</span>';
/* ------------------- */
if(file_exists($file['sql']))
	$status['sql_file'] = '<span class="install_valid">'.$L['Found'].'</span>';
else
	$status['sql_file'] = '<span class="install_invalid">'.$L['nf'].'</span>';

$status['php_ver'] = (function_exists('version_compare') && version_compare(PHP_VERSION, '5.1.0', '>=')) ? '<span class="install_valid">'.sprintf($L['install_ver_valid'],  PHP_VERSION).'</span>' : '<span class="install_invalid">'.sprintf($L['install_ver_invalid'],  PHP_VERSION).'</span>';
$status['mbstring'] = (extension_loaded('mbstring')) ? '<span class="install_valid">'.$L['Available'].'</span>' : '<span class="install_invalid">'.$L['na'].'</span>';
$status['mysql'] = (extension_loaded('mysql')) ? '<span class="install_valid">'.$L['Available'].'</span>' : '<span class="install_invalid">'.$L['na'].'</span>';

if($_POST['submit'])
{
	$status['mysql_ver'] = ($connection && function_exists('version_compare') && version_compare(@mysql_get_server_info($connection), '4.1.0', '>=')) ? '<span class="install_valid">'.sprintf($L['install_ver_valid'],  mysql_get_server_info($connection)).'</span>' : '<span class="install_invalid">'.$L['na'].'</span>';
}
else
{
	$status['mysql_ver'] = '<span class="install_invalid">'.$L['na'].'</span>';
}
if($error)
{
	$t->assign(array(
		'INSTALL_ERROR' => $error
		));
	$t->parse('MAIN.ERROR');
}

$t->assign(array(
	'INSTALL_AV_DIR' => $status['av_dir'],
	'INSTALL_CACHE_DIR' => $status['cache_dir'],
	'INSTALL_PFS_DIR' => $status['pfs_dir'],
	'INSTALL_PHOTOS_DIR' => $status['photos_dir'],
	'INSTALL_SIG_DIR' => $status['sig_dir'],
	'INSTALL_TH_DIR' => $status['th_dir'],
	'INSTALL_CONFIG' => $status['config'],
	'INSTALL_CONFIG_SAMPLE' => $status['config_sample'],
	'INSTALL_SQL_FILE' => $status['sql_file'],
	'INSTALL_PHP_VER' => $status['php_ver'],
	'INSTALL_MBSTRING' => $status['mbstring'],
	'INSTALL_MYSQL' => $status['mysql'], 
	'INSTALL_MYSQL_VER' => $status['mysql_ver'],
	'INSTALL_DB_HOST' => $cfg['mysqlhost'],
	'INSTALL_DB_USER' => $cfg['mysqluser'],
	'INSTALL_DB_NAME' => $cfg['mysqldb'],
	'INSTALL_DB_X' => $db_x,
	'INSTALL_SKIN_SELECT' => sed_selectbox_skin($rskin, 'skin'),
	'INSTALL_THEME_SELECT' => sed_selectbox_theme($rskin, 'theme', $theme),
	'INSTALL_LANG_SELECT' => sed_selectbox_lang($rlang, 'lang'),
	));

if($a == "1")
{
	$file_contents = file_get_contents('config-sample.php', NULL, NULL, 5, filesize('config-sample.php'));

	$file_contents = eregi_replace('\$cfg[\'defaultlang\'] = \'en\';', '$cfg[\'defaultlang\'] = \''.$value.'\';', $file_contents);
	$file_contents = eregi_replace('\$cfg[\'defaultskin\'] = \'en\';', '$cfg[\'defaultskin\'] = \''.$value.'\';', $file_contents);
	$file_contents = eregi_replace('\$cfg[\'defaulttheme\'] = \'en\';', '$cfg[\'defaulttheme\'] = \''.$value.'\';', $file_contents);
	$file_contents = eregi_replace('\$cfg\[\'mysqlhost\'\] = \'localhost\';', '$cfg[\'mysqlhost\'] = \''.$value[0].'\';', $file_contents);
	$file_contents = eregi_replace('\$cfg\[\'mysqluser\'\] = \'root\';', '$cfg[\'mysqluser\'] = \''.$value[1].'\';', $file_contents);
	$file_contents = eregi_replace('\$cfg\[\'mysqlpassword\'\] = \'\';', '$cfg[\'mysqlpassword\'] = \''.$value[2].'\';', $file_contents);
	$file_contents = eregi_replace('\$cfg\[\'mysqldb\'\] = \'cotonti\';', '$cfg[\'mysqldb\'] = \''.$value[3].'\';', $file_contents);
	$file_contents = eregi_replace('\$db_x\ = \'sed_\';', '$db_x				= \''.$value[4].'\';', $file_contents);

	//echo"<pre>".$file_contents."</pre>";
	file_put_contents('config.php', "<?PHP".$file_contents);
}

$t->parse("MAIN");
$t->out("MAIN");

?>
