<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.trashcan.inc.php
Version=121
Updated=2007-mar-20
Type=Core.admin
Author=Neocrome
Description=Trash can
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array ("admin.php?m=trashcan", $L['Trashcan']);
$adminhelp = $L['adm_help_trashcan'];

$id = sed_import('id','G','INT');

if ($a=='wipe')
	{
	sed_check_xg();
	$sql = sed_sql_query("DELETE FROM $db_trash WHERE tr_id='$id'");
	}
elseif ($a=='wipeall')
	{
	sed_check_xg();
	$sql = sed_sql_query("TRUNCATE $db_trash");
	}
elseif ($a=='restore')
	{
	sed_check_xg();
	if (sed_trash_restore($id))
		{ sed_trash_delete($id); }
	}

$sql = sed_sql_query("SELECT t.*, u.user_name FROM $db_trash AS t
	LEFT JOIN $db_users AS u ON t.tr_trashedby=u.user_id
	WHERE 1 ORDER by tr_id DESC");

$adminmain .= "<ul><li><a href=\"admin.php?m=config&amp;n=edit&amp;o=core&amp;p=trash\">".$L['Configuration']." : <img src=\"images/admin/config.gif\" alt=\"\" /></a></li><li>";
$adminmain .= $L['Wipeall'].": [<a href=\"admin.php?m=trashcan&amp;a=wipeall&amp;".sed_xg()."\">x</a>]</li></ul>";
$adminmain .= "<table class=\"cells\"><tr>";
$adminmain .= "<td class=\"coltop\" style=\"width:144px;\">".$L['Date']."</td>";
$adminmain .= "<td class=\"coltop\" style=\"width:144px;\">".$L['Type']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Title']."</td>";
$adminmain .= "<td class=\"coltop\" style=\"width:96px;\">".$L['adm_setby']."</td>";
$adminmain .= "<td class=\"coltop\" style=\"width:56px;\">".$L['Wipe']."</td>";
$adminmain .= "<td class=\"coltop\" style=\"width:56px;\">".$L['Restore']."</td></tr>";

$ii = 0;

while ($row = sed_sql_fetcharray($sql))
	{
	switch ($row['tr_type'])
		{
		case 'comment':
		$icon = "comments.gif";
		$typestr = $L['Comment'];
		break;

		case 'forumpost':
		$icon = "forums.gif";
		$typestr = $L['Post'];
		break;

		case 'forumtopic':
		$icon = "forums.gif";
		$typestr = $L['Topic'];
		break;

		case 'page':
		$icon = "page.gif";
		$typestr = $L['Page'];
		break;

		case 'pm':
		$icon = "pm.gif";
		$typestr = $L['Private_Messages'];
		break;

		case 'user':
		$icon = "user.gif";
		$typestr = $L['User'];
		break;

		default:
		$icon = "tools.gif";
		$typestr = $row['tr_type'];
		break;
		}

	$adminmain .= "<tr>";
	$adminmain .= "<td style=\"text-align:center;\">".date($cfg['dateformat'], $row['tr_date']+$usr['timezone']*3600)."</td>";
	$adminmain .= "<td><img src=\"images/admin/".$icon."\" alt=\"".$typestr."\" /> ".$typestr."</td>";
	$adminmain .= "<td>".sed_cc($row['tr_title'])."</td>";
	$adminmain .= "<td style=\"text-align:center;\">";
	$adminmain .= ($row['tr_trashedby']==0) ? $L['System'] : sed_build_user($row['tr_trashedby'], sed_cc($row['user_name']));
	$adminmain .= "</td><td style=\"text-align:center;\">";
	$adminmain .= "[<a href=\"admin.php?m=trashcan&amp;a=wipe&amp;id=".$row['tr_id']."&amp;".sed_xg()."\">-</a>]</td>";
	$adminmain .= "<td style=\"text-align:center;\">";
	$adminmain .= "[<a href=\"admin.php?m=trashcan&amp;a=restore&amp;id=".$row['tr_id']."&amp;".sed_xg()."\">+</a>]</td></tr>";
	$ii++;
	}
$adminmain .= "<tr><td colspan=\"6\">".$L['Total']." : ".$ii."</td></tr></table>";

?>
