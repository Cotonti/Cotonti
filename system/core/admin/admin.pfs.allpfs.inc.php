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

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=pfs'), $L['PFS']);
$adminpath[] = array (sed_url('admin', 'm=pfs&s=allpfs'), $L['adm_allpfs']);
$adminhelp = $L['adm_help_allpfs'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

unset ($disp_list);

$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(DISTINCT pfs_userid) FROM $db_pfs WHERE pfs_folderid>=0"), 0, "COUNT(DISTINCT pfs_userid)");
$pagnav = sed_pagination(sed_url('admin','m=pfs&s=allpfs'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=pfs&s=allpfs'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$sql = sed_sql_query("SELECT DISTINCT p.pfs_userid, u.user_name, u.user_id, COUNT(*) FROM $db_pfs AS p
	LEFT JOIN $db_users AS u ON p.pfs_userid=u.user_id
	WHERE pfs_folderid>=0 GROUP BY p.pfs_userid ORDER BY u.user_name ASC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;

while ($row = sed_sql_fetcharray($sql))
	{
	$row['user_name'] = ($row['user_id']==0) ? $L['SFS'] : $row['user_name'];
	$row['user_id'] = ($row['user_id']==0) ? "0" : $row['user_id'];

	$disp_list .= "<tr>";
	$disp_list .= "<td>[<a href=\"".sed_url('pfs', "userid=".$row['user_id'])."\">e</a>]</td>";
	$disp_list .= "<td>".sed_build_user($row['user_id'], sed_cc($row['user_name']))."</td>";
 	$disp_list .= "<td>".$row['COUNT(*)']."</td>";
	$disp_list .= "</tr>";

	$ii++;
	}

$adminmain .= "<div class=\"pagnav\">".$pagination_prev." ".$pagnav." ".$pagination_next."</div>";
$adminmain .= "<table class=\"cells\">";
$adminmain .= "<tr><td class=\"coltop\">".$L['Edit']."</td><td class=\"coltop\">".$L['User']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Files']."</td></tr>".$disp_list."<tr><td colspan=\"3\">".$L['Total']." : ".$totalitems.", ".$L['comm_on_page'].": ".$ii."</td></tr></table>";

?>