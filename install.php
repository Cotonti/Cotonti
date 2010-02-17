<?php
/**
 * Home page loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_INSTALL', TRUE);
define('SED_ADMIN', TRUE);
define('COT_MODULE', TRUE);
$location = 'Install';
$z = 'install';

if (file_exists('./datas/config.php'))
{
	require_once './datas/config.php';
}
else
{
	require_once './datas/config-sample.php';
}

if (empty($cfg['modules_dir']))
{
	$cfg['modules_dir'] = './modules';
}
if (empty($cfg['lang_dir']))
{
	$cfg['lang_dir'] = './lang';
}

require_once $cfg['system_dir'].'/functions.php';
require_once $cfg['system_dir'].'/database.'.$cfg['sqldb'].'.php';

//A Few basics from common.php
if (version_compare(PHP_VERSION, '6.0.0', '<='))
{
	if (get_magic_quotes_gpc())
	{
		function sed_disable_mqgpc(&$value, $key)
		{
			$value = stripslashes($value);
		}
		$gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		array_walk_recursive($gpc, 'sed_disable_mqgpc');
	}
}
define('MQGPC', FALSE);
error_reporting(E_ALL ^ E_NOTICE);

require_once sed_langfile('main', 'core');

require_once sed_incfile('xtemplate');
require_once sed_langfile('install', 'module');
require_once sed_incfile('functions', 'admin');

if ($_GET['m'] == 'update')
{
	require_once sed_incfile('update', 'install');
}
else
{
	if (!$cfg['new_install'])
	{
		header('Location: '.$cfg['mainurl']);
		exit;
	}
	require_once sed_incfile('main', 'install');
}

?>