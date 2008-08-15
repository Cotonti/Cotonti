<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=Administration panel loader
[END_SED]
==================== */

define('SED_CODE', TRUE);
define('SED_ADMIN', TRUE);
$location = 'Administration';
$z = 'admin';

require_once('./system/functions.php');
require_once('./system/functions.admin.php');
require_once('./datas/config.php');
require_once('./system/common.php');
require_once("system/lang/".$usr['lang']."/admin.lang.php");
require_once("system/core/admin/admin.inc.php");

?>
