<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=message.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=Messages loader
[END_SED]
==================== */

define('SED_CODE', TRUE);
define('SED_MESSAGE', TRUE);
$location = 'Messages';
$z = 'message';

require('system/functions.php');
require('datas/config.php');
require('system/common.php');

switch($m)
	{
	default:
	require('system/core/message/message.inc.php');
	break;
	}

?>
