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

require_once('./system/functions.php');
require_once('./datas/config.php');
require_once('./system/common.php');

switch($m)
	{
	default:
	require_once('./system/core/message/message.inc.php');
	break;
	}

?>
