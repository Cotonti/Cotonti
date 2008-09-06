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

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/common.php');

sed_dieifdisabled($cfg['disable_plug']);

switch($m)
	{
	default:
	require_once($cfg['system_dir'].'/core/plug/plug.inc.php');
	break;
	}

?>
