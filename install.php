<?php
/**
 * Install script
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

// Environment setup
define('SED_CODE', TRUE);
define('SED_INSTALL', TRUE);
//define('SED_ADMIN', TRUE);
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
require_once 'system/debug.php';

if ($cfg['new_install'])
{
	require_once $cfg['system_dir'].'/database.'.$cfg['sqldb'].'.php';

	// A Few basics from common.php
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

	session_start();

	// Installer language selection support
	if (empty($_SESSION['cot_inst_lang']))
	{
		$lang = sed_import('lang', 'P', 'ALP');
		if (empty($lang))
		{
			$lang = 'en';
		}
	}
	else
	{
		$lang = $_SESSION['cot_inst_lang'];
	}

	require_once sed_langfile('main', 'core');
	require_once $cfg['system_dir'] . '/resources.php';
}
else
{
	require_once $cfg['system_dir'].'/common.php';
}

sed_require_api('forms');
sed_require_api('extensions');
sed_require_api('cotemplate');
sed_require_lang('install', 'module');
sed_require_lang('users', 'core');
sed_require_lang('admin', 'core');

sed_require_rc('install');

// Various Generic Vars needed to operate as Normal
$theme = $cfg['defaulttheme'];
$scheme = $cfg['defaultscheme'];
$out['meta_lastmod'] = gmdate('D, d M Y H:i:s');
$file['config'] = './datas/config.php';
$file['config_sample'] = './datas/config-sample.php';
$file['sql'] = './setup/install.sql';

if (!$cfg['new_install'])
{
	include sed_incfile($z, 'update');
}
else
{
	include sed_incfile($z, 'install');
}

?>