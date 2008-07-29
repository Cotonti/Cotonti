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

require('system/functions.php');
require('datas/config.php');
require('system/common.php');

switch($m)
	{
	default:
	require('system/core/view/view.inc.php');
	break;
	}

?>
