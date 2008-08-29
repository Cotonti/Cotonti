<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=view.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=View loader
[END_SED]
==================== */

define('SED_CODE', TRUE);
define('SED_VIEW', TRUE);
$location = 'Views';
$z = 'view';

require_once './datas/config.php';
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/common.php');

switch($m)
	{
	default:
	require_once($cfg['system_dir'].'/core/view/view.inc.php');
	break;
	}

?>
