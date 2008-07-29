<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.statistics.inc.php
Version=122
Updated=2007-sep-26
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
sed_block($usr['isadmin']);

$adminpath[] = array ("admin.php?m=page", $L['Pages']);
$adminhelp = $L['adm_help_page'];

$totaldbpages = sed_sql_rowcount($db_pages);
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1");
$sys['pagesqueued'] = sed_sql_result($sql,0,'COUNT(*)');

$adminmain .= "<ul>";
$adminmain .= "<li><a href=\"admin.php?m=config&amp;n=edit&amp;o=core&amp;p=page\">".$L['Configuration']." : <img src=\"system/img/admin/config.gif\" alt=\"\" /></a></li>";
$adminmain .= "<li>".sed_linkif("page.php?m=add", $L['addnewentry'], sed_auth('page', 'any', 'A'))."</li>";
$adminmain .= "<li>".sed_linkif("admin.php?m=page&amp;s=queue", $L['adm_valqueue']." : ".$sys['pagesqueued'], sed_auth('page', 'any', 'A'))."</li>";
$adminmain .= "<li>".sed_linkif("admin.php?m=page&amp;s=structure", $L['adm_structure'], sed_auth('admin', 'a', 'A'))."</li>";
$adminmain .= "<li>".sed_linkif("admin.php?m=page&amp;s=catorder", $L['adm_sortingorder'], sed_auth('admin', 'a', 'A'))."</li>";
$adminmain .= "<li>".$L['Pages']." : ".$totaldbpages." (<a href=\"list.php?c=all\">".$L['adm_showall']."</a>)</li>";
$adminmain .= "</ul>";

?>