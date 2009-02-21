<?PHP
/**
 * Administration panel
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=page'), $L['Pages']);
$adminhelp = $L['adm_help_page'];

$totaldbpages = sed_sql_rowcount($db_pages);
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1");
$sys['pagesqueued'] = sed_sql_result($sql,0,'COUNT(*)');

$adminmain .= "<ul>";
$adminmain .= "<li><a href=\"".sed_url('admin', "m=config&n=edit&o=core&p=page")."\">".$L['Configuration']." : <img src=\"images/admin/config.gif\" alt=\"\" /></a></li>";
$adminmain .= "<li>".sed_linkif(sed_url('page', 'm=add'), $L['addnewentry'], sed_auth('page', 'any', 'A'))."</li>";
$adminmain .= "<li>".sed_linkif(sed_url('admin', 'm=page&s=queue'), $L['adm_valqueue']." : ".$sys['pagesqueued'], sed_auth('page', 'any', 'A'))."</li>";
$adminmain .= "<li>".sed_linkif(sed_url('admin', 'm=page&s=structure'), $L['adm_structure'], sed_auth('admin', 'a', 'A'))."</li>";
$adminmain .= "<li>".sed_linkif(sed_url('admin', 'm=page&s=extrafields'), $L['adm_extrafields_desc'], sed_auth('admin', 'a', 'A'))."</li>";
$adminmain .= "<li>".sed_linkif(sed_url('admin', 'm=page&s=catorder'), $L['adm_sortingorder'], sed_auth('admin', 'a', 'A'))."</li>";
$adminmain .= "<li>".$L['Pages']." : ".$totaldbpages." (<a href=\"".sed_url('list', 'c=all')."\">".$L['adm_showall']."</a>)</li>";
$adminmain .= "</ul>";

?>