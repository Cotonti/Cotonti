<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.versions.inc.php
Version=101
Updated=2006-mar-15
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['auth_read']);

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=infos'), $L['adm_infos']);
$adminhelp = $L['adm_help_versions'];

@error_reporting(0);

$adminmain .= (function_exists('phpversion')) ? $L['adm_phpver']." : ".phpversion()."<br />" : '' ;
$adminmain .= (function_exists('zend_version')) ? $L['adm_zendver']." : ".zend_version()."<br />" : '';
$adminmain .= (function_exists('php_sapi_name')) ? $L['adm_interface']." : ".php_sapi_name()."<br />" : '';
$adminmain .= (function_exists('php_uname')) ? $L['adm_os']." : ".php_uname() : '';

$adminmain .= "<h4>".$L['adm_clocks']." :</h4>";
$adminmain .= "<table class=\"cells\">";
$adminmain .= "<tr><td>".$L['adm_time1']."</td><td> ".date("Y-m-d H:i")." </td></tr>";
$adminmain .= "<tr><td>".$L['adm_time2']."</td><td> ".gmdate("Y-m-d H:i")." GMT </td></tr>";
$adminmain .= "<tr><td>".$L['adm_time3']."</td>";
$adminmain .= "<td>".$usr['gmttime']." </td></tr>";
$adminmain .= "<tr><td>".$L['adm_time4']." : </td>";
$adminmain .= "<td>".date($cfg['dateformat'], $sys['now_offset'] + $usr['timezone'] * 3600)." ".$usr['timetext']." </td></tr>";
$adminmain .= "</table>";

@error_reporting(7);

?>
