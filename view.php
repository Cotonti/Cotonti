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

require_once('./system/functions.php');
require_once('./datas/config.php');
require_once('./system/common.php');

switch($m)
	{
	default:
	require_once('./system/core/view/view.inc.php');
	break;
	}

?>
