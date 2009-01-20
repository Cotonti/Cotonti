<?PHP

/* ====================
[BEGIN_SED]
File=admin.polls.inc.php
Version=0.0.2
Updated=2009-jan-21
Type=Core.admin
Author=Neocrome & Cotonti Team
Description=polls (Cotonti - Website engine http://www.cotonti.com Copyright (c) Cotonti Team 2009 BSD License)
[END_SED]
==================== */

/**
 * Poll editor
 *
 * @package Seditio-N
 * @version 0.0.2
 * @author esclkm
 * @copyright Copyright (c) 2008 Cotonti Team
 * @license BSD License
 */


if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('polls', 'a');
sed_block($usr['isadmin']);

require_once($cfg['system_dir'].'/core/polls/polls.functions.php');

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=polls'), $L['Polls']);
$adminhelp = $L['adm_help_polls'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

$adminmain .= "<ul><li><a href=\"".sed_url('admin', "m=config&n=edit&o=core&p=polls")."\">".$L['Configuration']." : <img src=\"images/admin/config.gif\" alt=\"\" /></a></li></ul>";

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

sed_poll_check();

	if (empty($error_string)){
		$number=sed_poll_save();

		if($poll_id=='new'){$adminmain .= "<h4>".$L['polls_created']."</h4>";}
		elseif(!empty($poll_id)) {$adminmain .= "<h4>".$L['polls_updated']."</h4>";}
	}
	else
	{$adminmain .="<div class=\"error\">".$error_string."</div>";}


$totalitems = sed_sql_rowcount($db_polls);
$pagnav = sed_pagination(sed_url('admin','m=polls'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=polls'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$sql = sed_sql_query("SELECT p.*, t.ft_id FROM $db_polls AS p
LEFT JOIN $db_forum_topics AS t ON t.ft_poll = p.poll_id
WHERE 1 ORDER BY p.poll_type ASC, p.poll_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
$adminmain .= "<div class=\"pagnav\">".$pagination_prev." ".$pagnav." ".$pagination_next."</div>";
$adminmain .= "<table class=\"cells\">";

$ii = 0;
$prev = -1;

while ($row = sed_sql_fetcharray($sql))
{
	$id = $row['poll_id'];
	$type = $row['poll_type'];

	if ($type=='index' && $prev==-1)
	{
		$prev = 0;
		$adminmain .= "<tr><td colspan=\"8\">".$L['adm_polls_indexpolls']."</td></tr>";
		$adminmain .= "<tr><td class=\"coltop\" style=\"width:128px;\">".$L['Date']."</td>";
		$adminmain .= "<td class=\"coltop\">".$L['Poll']." ".$L['adm_clicktoedit']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Votes']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Delete']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Reset']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Bump']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Open']."</td></tr>";
	}

	if ($type=='forum' && $prev==0)
	{
		$prev = 1;
		$adminmain .= "<tr><td colspan=\"8\">".$L['adm_polls_forumpolls']."</td></tr>";
		$adminmain .= "<tr><td class=\"coltop\" style=\"width:128px;\">".$L['Date']."</td>";
		$adminmain .= "<td class=\"coltop\">".$L['Topic']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Votes']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Delete']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Reset']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Bump']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Open']."</td></tr>";
	}

	$sql2 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$id'");
	$totalvotes = sed_sql_result($sql2,0,"SUM(po_count)");
	$adminmain .= "<tr><td style=\"text-align:center;\">".date($cfg['formatyearmonthday'], $row['poll_creationdate'])."</td>";


	$adminmain .= "<td><a href=\"".sed_url('admin', "m=polls&n=options&id=".$row['poll_id'])."\">".sed_cc($row['poll_text'])."</a></td>";
	$adminmain .= "<td style=\"text-align:center;\">".$totalvotes."</td>";

	$adminmain .= "<td style=\"text-align:center;\">[<a href=\"".sed_url('admin', "m=polls&a=delete&id=".$id."&".sed_xg())."\">x</a>]</td>";
	$adminmain .= "<td style=\"text-align:center;\">[<a href=\"".sed_url('admin', "m=polls&a=reset&id=".$id."&".sed_xg())."\">R</a>]</td>";
	$adminmain .= "<td style=\"text-align:center;\">[<a href=\"".sed_url('admin', "m=polls&a=bump&id=".$id."&".sed_xg())."\">B</a>]</td>";
	$adminmain .= "<td style=\"text-align:center;\">";

	if ($type=='index')
	{ $adminmain .= "<a href=\"".sed_url('polls', "id=".$row['poll_id'])."\"><img src=\"images/admin/jumpto.gif\" alt=\"\" /></a>"; }
	else
	{ $adminmain .= "<a href=\"".sed_url('forums', "m=posts&q=".$row['ft_id'])."\"><img src=\"images/admin/jumpto.gif\" alt=\"\" /></a>"; }

	$adminmain .= "</td></tr>";
	$ii++;
}
$adminmain .= "<tr><td colspan=\"8\">".$L['Total']." : ".$totalitems.", ".$L['adm_polls_on_page'].": ".$ii."</td></tr></table>";

if ($n=='options')
{
	$poll_id = sed_import('id','G','TXT');
	$adminpath[] = array (sed_url('admin', 'm=polls&n=options&id=$poll_id'), $L['Options']." (#$id)");
	$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
	$send_button=$L['Update'];
}
elseif(!empty($error_string))
{
	if ($poll_id!='new')
	{$adminpath[] = array (sed_url('admin', 'm=polls&n=options&id=$poll_id'), $L['Options']." (#$id)");
	$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
	$send_button=$L['Update'];}
	else
	{$adminmain .= "<h4>".$L['addnewentry']." :</h4>";
	$send_button=$L['Create'];}
}
else
{
	$poll_id='new';
	$adminmain .= "<h4>".$L['addnewentry']." :</h4>";
	$send_button=$L['Create'];
}
list($poll_text, $poll_options, $poll_date, $poll_settings)=sed_poll_edit_form($poll_id, 1);

$adminmain .= "<form id=\"addpoll\" action=\"".sed_url('admin', "m=polls")."\" method=\"post\">";
$adminmain .= "<table class=\"cells\">";
$adminmain .= "<tr><td>".$L['adm_polls_polltopic']."</td><td>".$poll_text."</td></tr>";
$adminmain .= "<tr><td>".$L['Date']." : </td><td>".$poll_date."</td></tr>";
$adminmain .= "<tr><td>".$L['Options']."</td><td>";
$adminmain .= $poll_options."</td></tr>";
$adminmain .= "<tr><td></td><td>".$poll_settings."
</td></tr>";
$adminmain .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$send_button."\" /></td></tr></table></form>";

?>