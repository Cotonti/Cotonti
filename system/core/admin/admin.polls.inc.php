<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.polls.inc.php
Version=101
Updated=2006-mar-15
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('polls', 'a');
sed_block($usr['isadmin']);

$id = sed_import('id','G','TXT');
$po = sed_import('po','G','TXT');

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=polls'), $L['Polls']);
$adminhelp = $L['adm_help_polls'];

$ntext = sed_import('ntext','P','HTM');

$adminmain .= "<ul><li><a href=\"".sed_url('admin', "m=config&n=edit&o=core&p=polls")."\">".$L['Configuration']." : <img src=\"images/admin/config.gif\" alt=\"\" /></a></li></ul>";

if ($n=='options')
{
	if ($a=='update' && !empty($id) && !empty($po))
	{
		$rtext = sed_import('rtext','P','HTM');
		$sql = (!empty($rtext)) ? sed_sql_query("UPDATE $db_polls_options SET po_text='".sed_sql_prep($rtext)."' WHERE po_id='$po' AND po_pollid='$id'") : '';
	}
	elseif ($a=='updatetitle' && !empty($id))
	{
		$rtitle = sed_import('rtitle','P','HTM');
		$sql = (!empty($rtitle)) ? sed_sql_query("UPDATE $db_polls SET poll_text='".sed_sql_prep($rtitle)."' WHERE poll_id='$id'") : '';
	}
	elseif ($a=='add' && !empty($id) && !empty($ntext))
	{
		$sql = sed_sql_query("INSERT INTO $db_polls_options (po_pollid, po_text) VALUES (".(int)$id.",'".sed_sql_prep($ntext)."')");
	}

	elseif ($a=='delete')
	{
		sed_check_xg();
		$sql = sed_sql_query("DELETE FROM $db_polls_options WHERE po_id='$po' AND po_pollid='$id'");
	}

	$sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_id='$id' ");
	$sql1 = sed_sql_query("SELECT * FROM $db_polls_options WHERE po_pollid='$id' ORDER by po_id ASC");

	$row = sed_sql_fetcharray($sql);

	$adminpath[] = array (sed_url('admin', 'm=polls&n=options&id=$id'), $L['Options']." (#$id)");
	$adminmain .= $L['editdeleteentries']." :<br />&nbsp;<br />";
	$adminmain .= "<a href=\"javascript:polls('".$row["poll_id"]."')\">".$L['Poll']." #".$row["poll_id"]."</a><br />";
	$adminmain .= "<form id=\"pollchgtitle\" action=\"".sed_url('admin', "m=polls&n=options&a=updatetitle&id=".$id)."\" method=\"post\">";
	$adminmain .= $L['Title']." : <input type=\"text\" class=\"text\" name=\"rtitle\" value=\"".sed_cc($row["poll_text"])."\" size=\"56\" maxlength=\"255\">";
	$adminmain .= " <input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\"></form><br />";
	$adminmain .= $L['Date']." : ".date($cfg['dateformat'], $row["poll_creationdate"])." GMT<br />";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td>".$L['Delete']."</td><td>#</td><td>".$L['Option']."</td><td>&nbsp;</td></tr>";

	while ($row1 = sed_sql_fetcharray($sql1))
	{
		$adminmain .= "<form id=\"savepollopt\" action=\"".sed_url('admin', "m=polls&n=options&a=update&id=".$row1['po_pollid']."&po=".$row1['po_id'])."\" method=\"post\">";
		$adminmain .= "<tr><td>[<a href=\"".sed_url('admin', "m=polls&n=options&a=delete&id=".$row1['po_pollid']."&po=".$row1['po_id']."&".sed_xg())."\">x</a>]";
		$adminmain .= "<td>".$row1['po_id']."</td><td> <input type=\"text\" class=\"text\" name=\"rtext\" value=\"".sed_cc($row1['po_text'])."\" size=\"32\" maxlength=\"128\"> </td>";
		$adminmain .= "<td><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\"></td></tr></form>";
	}

	$adminmain .= "</table><br />&nbsp;<br />".$L['addnewentry']." :<br />&nbsp;<br /><table class=\"cells\"><tr>";
	$adminmain .= "<td>".$L['Option']."</td><td>&nbsp;</td><tr>";
	$adminmain .= "<form id=\"addpollopt\" action=\"".sed_url('admin', "m=polls&n=options&a=add&id=".$row["poll_id"])."\" method=\"post\"><tr>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"ntext\" value=\"\" size=\"32\" maxlength=\"128\"> </td>";
	$adminmain .= "<td><input type=\"submit\" class=\"submit\" value=\"".$L['Add']."\"></td></tr></form></table>";
}
else
{
	if ($a=='delete')
	{
		sed_check_xg();
		$id2 = "v".$id;

		$sql = sed_sql_query("DELETE FROM $db_polls WHERE poll_id='$id'");
		$num = sed_sql_affectedrows();
		$sql = sed_sql_query("DELETE FROM $db_polls_options WHERE po_pollid='$id'");
		$num = $num + sed_sql_affectedrows();
		$sql = sed_sql_query("DELETE FROM $db_polls_voters WHERE pv_pollid='$id'");
		$num = $num + sed_sql_affectedrows();
		$sql = sed_sql_query("DELETE FROM $db_com WHERE com_code='$id2'");
		$num = $num + sed_sql_affectedrows();
		header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=916&rc=102&num=".$num, '', true));
		exit;
	}

	elseif ($a=='reset')
	{
		sed_check_xg();
		$sql = sed_sql_query("DELETE FROM $db_polls_voters WHERE pv_pollid='$id'");
		$num = sed_sql_affectedrows();
		$sql = sed_sql_query("UPDATE $db_polls_options SET po_count=0 WHERE po_pollid='$id'");
		$num = $num + sed_sql_affectedrows();
		header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=916&rc=102&num=".$num, '', true));
		exit;
	}

	if ($a=='bump')
	{
		sed_check_xg();
		$sql = sed_sql_query("UPDATE $db_polls SET poll_creationdate='".$sys['now_offset']."' WHERE poll_id='$id'");
		header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=916&rc=102&num=1", '', true));
		exit;
	}

	if ($a=='add' && !empty($ntext))
	{
		$sql = sed_sql_query("INSERT INTO $db_polls (poll_state, poll_creationdate, poll_text) valueS (0, ".(int)$sys['now_offset'].", '".sed_sql_prep($ntext)."')");
	}

	$sql = sed_sql_query("SELECT p.*, t.ft_id FROM $db_polls AS p
	LEFT JOIN $db_forum_topics AS t ON t.ft_poll = p.poll_id
	WHERE 1 ORDER BY p.poll_type ASC, p.poll_id DESC LIMIT 20");

	$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
	$adminmain .= "<table class=\"cells\">";

	$ii = 0;
	$prev = -1;

	while ($row = sed_sql_fetcharray($sql))
	{
		$id = $row['poll_id'];
		$type = $row['poll_type'];

		if ($type==0 && $prev==-1)
		{
			$prev = 0;
			$adminmain .= "<tr><td colspan=\"8\">".$L['adm_polls_indexpolls']."</td></tr>";
			$adminmain .= "<tr><td class=\"coltop\" style=\"width:40px;\">".$L['Delete']."</td>";
			$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Reset']."</td>";
			$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Bump']."</td>";
			$adminmain .= "<td class=\"coltop\" style=\"width:128px;\">".$L['Date']."</td>";
			$adminmain .= "<td class=\"coltop\">".$L['Poll']." ".$L['adm_clicktoedit']."</td>";
			$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Votes']."</td>";
			$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Open']."</td></tr>";
		}

		if ($type==1 && $prev==0)
		{
			$prev = 1;
			$adminmain .= "<tr><td colspan=\"8\">".$L['adm_polls_forumpolls']."</td></tr>";
			$adminmain .= "<tr><td class=\"coltop\" style=\"width:40px;\">".$L['Delete']."</td>";
			$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Reset']."</td>";
			$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Bump']."</td>";
			$adminmain .= "<td class=\"coltop\" style=\"width:128px;\">".$L['Date']."</td>";
			$adminmain .= "<td class=\"coltop\">".$L['Topic']."</td>";
			$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Votes']."</td>";
			$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Open']."</td></tr>";
		}

		$sql2 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$id'");
		$totalvotes = sed_sql_result($sql2,0,"SUM(po_count)");

		$adminmain .= "<tr><td style=\"text-align:center;\">[<a href=\"".sed_url('admin', "m=polls&a=delete&id=".$id."&".sed_xg())."\">x</a>]</td>";
		$adminmain .= "<td style=\"text-align:center;\">[<a href=\"".sed_url('admin', "m=polls&a=reset&id=".$id."&".sed_xg())."\">R</a>]</td>";
		$adminmain .= "<td style=\"text-align:center;\">[<a href=\"".sed_url('admin', "m=polls&a=bump&id=".$id."&".sed_xg())."\">B</a>]</td>";
		$adminmain .= "<td style=\"text-align:center;\">".date($cfg['formatyearmonthday'], $row['poll_creationdate'])."</td>";


		$adminmain .= "<td><a href=\"".sed_url('admin', "?m=polls&n=options&id=".$row['poll_id'])."\">".sed_cc($row['poll_text'])."</a></td>";
		$adminmain .= "<td style=\"text-align:center;\">".$totalvotes."</td>";
		$adminmain .= "<td style=\"text-align:center;\">";

		if ($type==0)
		{ $adminmain .= "<a href=\"".sed_url('polls', "id=".$row['poll_id'])."\"><img src=\"images/admin/jumpto.gif\" alt=\"\"></a>"; }
		else
		{ $adminmain .= "<a href=\"".sed_url('forums', "m=posts&q=".$row['ft_id'])."\"><img src=\"images/admin/jumpto.gif\" alt=\"\"></a>"; }

		$adminmain .= "</td></tr>";
		$ii++;
	}
	$adminmain .= "<tr><td colspan=\"8\">".$L['Total']." : ".$ii."</td></tr></table>";
	$adminmain .= "<h4>".$L['addnewentry']." :</h4>";
	$adminmain .= "<form id=\"addpoll\" action=\"".sed_url('admin', "m=polls&a=add")."\" method=\"post\">";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td>".$L['adm_polls_polltopic']."</td><td><input type=\"text\" class=\"text\" name=\"ntext\" value=\"\" size=\"64\" maxlength=\"255\"></tr>";
	$adminmain .= "<td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$L['Add']."\"> </td></tr></table></form>";
}

?>
