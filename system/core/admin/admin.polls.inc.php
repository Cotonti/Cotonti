<?PHP
/**
 * Administration panel - Poll editor
 *
 * @package Cotonti
 * @version 0.0.3
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if(!defined('SED_CODE') || !defined('SED_ADMIN')){die('Wrong URL.');}

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('polls', 'a');
sed_block($usr['isadmin']);

require_once($cfg['system_dir'].'/core/polls/polls.functions.php');

$t = new XTemplate(sed_skinfile('admin.polls.inc', false, true));

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=polls'), $L['Polls']);
$adminhelp = $L['adm_help_polls'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

if($a == 'delete')
{
	sed_check_xg();
	$id2 = "v".$id;

	$sql = sed_sql_query("DELETE FROM $db_polls WHERE poll_id='$id'");
	$sql = sed_sql_query("DELETE FROM $db_polls_options WHERE po_pollid='$id'");
	$sql = sed_sql_query("DELETE FROM $db_polls_voters WHERE pv_pollid='$id'");
	$sql = sed_sql_query("DELETE FROM $db_com WHERE com_code='$id2'");
	$adminpollsmsg = $L['adm_polls_msg916_deleted'];
}
elseif($a == 'reset')
{
	sed_check_xg();
	$sql = sed_sql_query("DELETE FROM $db_polls_voters WHERE pv_pollid='$id'");
	$sql = sed_sql_query("UPDATE $db_polls_options SET po_count=0 WHERE po_pollid='$id'");
	$adminpollsmsg = $L['adm_polls_msg916_reset'];
}

if($a == 'bump')
{
	sed_check_xg();
	$sql = sed_sql_query("UPDATE $db_polls SET poll_creationdate='".$sys['now_offset']."' WHERE poll_id='$id'");
	$adminpollsmsg = $L['adm_polls_msg916_bump'];
}

sed_poll_check();

if(empty($error_string))
{
	$number=sed_poll_save();

	if($poll_id == 'new')
	{
		$adminpollsmsg = $L['polls_created'];
	}
	elseif(!empty($poll_id))
	{
		$adminpollsmsg = $L['polls_updated'];
	}
}
else
{
	$adminpollsmsg = $error_string;
}

if(!empty($adminpollsmsg))
{
	$t -> assign(array("ADMIN_POLLS_MESAGE" => $adminpollsmsg));
	$t -> parse("POLLS.MESAGE");
}

$totalitems = sed_sql_rowcount($db_polls);
$pagnav = sed_pagination(sed_url('admin','m=polls'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=polls'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$sql = sed_sql_query("SELECT p.*, t.ft_id FROM $db_polls AS p
					LEFT JOIN $db_forum_topics AS t ON t.ft_poll = p.poll_id
					WHERE 1 ORDER BY p.poll_type DESC, p.poll_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
$indexheader =false;
$forumheader =false;

while($row = sed_sql_fetcharray($sql))
{

	$id = $row['poll_id'];
	$type = $row['poll_type'];

	if($type == 'index' && !$indexheader)
	{
		$indexheader = true;
		$t -> assign(array("ADMIN_POLLS_TYPE_HEADER" => $L['adm_polls_indexpolls']));
		$t -> parse("POLLS.POLLS_ROW.POLLS_ROW_TYPE");
	}

	if($type == 'forum' && !$forumheader)
	{
		$forumheader = true;
		$t -> assign(array("ADMIN_POLLS_TYPE_HEADER" => $L['adm_polls_forumpolls']));
		$t -> parse("POLLS.POLLS_ROW.POLLS_ROW_TYPE");
	}

	$sql2 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$id'");
	$totalvotes = sed_sql_result($sql2,0,"SUM(po_count)");

	if($type == 'index')
	{
		$admtypepoll .= sed_url('polls', "id=".$row['poll_id']);
	}
	else
	{
		$admtypepoll .= sed_url('forums', "m=posts&q=".$row['ft_id']);
	}

	$t -> assign(array(
		"ADMIN_POLLS_ROW_POLL_CREATIONDATE" => date($cfg['formatyearmonthday'], $row['poll_creationdate']),
		"ADMIN_POLLS_ROW_POLL_TYPE" => sed_cc($type),
		"ADMIN_POLLS_ROW_POLL_URL" => sed_url('admin', "m=polls&n=options&id=".$row['poll_id']),
		"ADMIN_POLLS_ROW_POLL_TEXT" => sed_cc($row['poll_text']),
		"ADMIN_POLLS_ROW_POLL_TOTALVOTES" => $totalvotes,
		"ADMIN_POLLS_ROW_POLL_URL_DEL" => sed_url('admin', "m=polls&a=delete&id=".$id."&".sed_xg()),
		"ADMIN_POLLS_ROW_POLL_URL_RES" => sed_url('admin', "m=polls&a=reset&id=".$id."&".sed_xg()),
		"ADMIN_POLLS_ROW_POLL_URL_BMP" => sed_url('admin', "m=polls&a=bump&id=".$id."&".sed_xg()),
		"ADMIN_POLLS_ROW_POLL_URL_OPN" => $admtypepoll
	));
	$t -> parse("POLLS.POLLS_ROW");

	$ii++;
}

if($n == 'options')
{
	$poll_id = sed_import('id','G','TXT');
	$adminpath[] = array (sed_url('admin', 'm=polls&n=options&id=$poll_id'), $L['Options']." (#$id)");
	$adminmain .= $L['editdeleteentries'];
	$send_button = $L['Update'];
}
elseif(!empty($error_string))
{
	if($poll_id != 'new')
	{
		$adminpath[] = array (sed_url('admin', 'm=polls&n=options&id=$poll_id'), $L['Options']." (#$id)");
		$formname = $L['editdeleteentries'];
		$send_button = $L['Update'];
	}
	else
	{
		$formname = $L['addnewentry'];
		$send_button = $L['Create'];
	}
}
else
{
	$poll_id='new';
	$formname = $L['addnewentry'];
	$send_button = $L['Create'];
}

list($poll_text, $poll_options, $poll_date, $poll_settings)=sed_poll_edit_form($poll_id, 1);

$t -> assign(array(
	"ADMIN_POLLS_CONF_URL" => sed_url('admin', "m=config&n=edit&o=core&p=polls"),
	"ADMIN_POLLS_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_POLLS_PAGNAV" => $pagnav,
	"ADMIN_POLLS_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_POLLS_TOTALITEMS" => $totalitems,
	"ADMIN_POLLS_ON_PAGE" => $ii,
	"ADMIN_POLLS_FORMNAME" => $formname,
	"ADMIN_POLLS_FORM_URL" => sed_url('admin', "m=polls"),
	"ADMIN_POLLS_POLL_TEXT" => $poll_text,
	"ADMIN_POLLS_POLL_DATE" => $poll_date,
	"ADMIN_POLLS_POLL_OPTIONS" => $poll_options,
	"ADMIN_POLLS_POLL_SETTINGS" => $poll_settings,
	"ADMIN_POLLS_SEND_BUTTON" => $send_button
));
$t -> parse("POLLS");
$adminmain = $t -> text("POLLS");

?>