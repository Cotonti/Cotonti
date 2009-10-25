<?php
/**
 * Home page loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_INSTALL', TRUE);
$location = 'Install';
$z = 'install';

if(file_exists('./datas/config.php'))
{
	require_once('./datas/config.php');
}
else
{
	require_once('./datas/config-sample.php');
}

if(!$cfg['new_install'])
{
	die('Cotonti configuration is not set to do a new install.');
}

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

$mlang = $cfg['system_dir'].'/lang/'.$cfg['defaultlang'].'/main.lang.php';
if(file_exists($mlang))
{
	require_once($mlang);
}
else
{
	sed_diefatal('Main language file not found.');
}

require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/database.'.$cfg['sqldb'].'.php');
require_once($cfg['system_dir'].'/core/install/install.inc.php');

?>