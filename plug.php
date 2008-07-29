<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=plug.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=Plugin loader
[END_SED]
==================== */

define('SED_CODE', TRUE);
define('SED_PLUG', TRUE);
$location = 'Plugins';
$z = 'plug';

require('system/functions.php');
require('datas/config.php');
require('system/common.php');

sed_dieifdisabled($cfg['disable_plug']);

switch($m)
	{
	default:
	require('system/core/plug/plug.inc.php');
	break;
	}

?>
