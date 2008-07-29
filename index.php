<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=index.php
Version=102
Updated=2006-apr-19
Type=Core
Author=Neocrome
Description=Home page loader
[END_SED]
==================== */

define('SED_CODE', TRUE);
define('SED_INDEX', TRUE);
$location = 'Home';
$z = 'index';

require('system/functions.php');
require('datas/config.php');
require('system/common.php');
require('system/core/index/index.inc.php');

?>
