<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=polls.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=Polls
[END_SED]
==================== */

define('SED_CODE', TRUE);
define('SED_POLLS', TRUE);
$location = 'Polls';
$z = 'polls';

require('system/functions.php');
require('datas/config.php');
require('system/common.php');

sed_dieifdisabled($cfg['disable_polls']);

switch($m)
	{
	default:
	require('system/core/polls/polls.inc.php');
	break;
	}

?>
