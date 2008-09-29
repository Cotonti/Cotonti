<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.pfs.allpfs.php
Version=110
Updated=2006-may-29
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=pfs'), $L['PFS']);
$adminpath[] = array (sed_url('admin', 'm=pfs&amp;s=allpfs'), $L['adm_allpfs']);
$adminhelp = $L['adm_help_allpfs'];

unset ($disp_list);

$sql = sed_sql_query("SELECT DISTINCT p.pfs_userid, u.user_name, u.user_id, COUNT(*) FROM $db_pfs AS p 
	LEFT JOIN $db_users AS u ON p.pfs_userid=u.user_id
	WHERE pfs_folderid>=0 GROUP BY p.pfs_userid ORDER BY u.user_name ASC");

while ($row = sed_sql_fetcharray($sql))
	{
	$row['user_name'] = ($row['user_id']==0) ? $L['SFS'] : $row['user_name'];
	$row['user_id'] = ($row['user_id']==0) ? "0" : $row['user_id'];
	
	$disp_list .= "<tr>";
	$disp_list .= "<td>[<a href=\"".sed_url('pfs', "userid=".$row['user_id'])."\">e</a>]</td>";
	$disp_list .= "<td>".sed_build_user($row['user_id'], sed_cc($row['user_name']))."</td>";
 	$disp_list .= "<td>".$row['COUNT(*)']."</td>";
	$disp_list .= "</tr>";
	}

$adminmain .= "<table class=\"cells\">";
$adminmain .= "<tr><td class=\"coltop\">".$L['Edit']."</td><td class=\"coltop\">".$L['User']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Files']."</td></tr>".$disp_list."</table>";

?>
