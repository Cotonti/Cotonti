<?php
/**
 * Administration panel - Poll editor
 *
 * @package Cotonti
 * @version 0.1.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('polls', 'a');
sed_block($usr['isadmin']);

require_once($cfg['system_dir'].'/core/polls/polls.functions.php');

$t = new XTemplate(sed_skinfile('admin.polls.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=polls'), $L['Polls']);
$adminhelp = $L['adm_help_polls'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;
$filter = sed_import('filter', 'G', 'TXT');

//$variant[key]=array("Caption", "filter", "page", "page_get", "sql", "sqlfield")
$variants[0] = array($L['All'], "");
$variants['index'] = array($L['Main'], "index");
$variants['forum'] = array($L['Forums'], "forum");

/* === Hook === */
$extp = sed_getextplugins('adim.polls.first');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

if($a == 'delete')
{
	sed_check_xg();
	sed_poll_delete($id);

	$adminwarnings = $L['adm_polls_msg916_deleted'];
}
elseif($a == 'reset')
{
	sed_check_xg();
	sed_poll_reset($id);

	$adminwarnings = $L['adm_polls_msg916_reset'];
}
elseif($a == 'lock')
{
	sed_check_xg();
	sed_poll_lock($id, 3);

	$adminwarnings = $L['adm_polls_msg916_reset'];
}
elseif($a == 'bump')
{
	sed_check_xg();
	$sql = sed_sql_query("UPDATE $db_polls SET poll_creationdate='".$sys['now_offset']."' WHERE poll_id='$id'");

	$adminwarnings = $L['adm_polls_msg916_bump'];
}

sed_poll_check();

if(empty($error_string))
{
	$number = sed_poll_save();

	if($poll_id == 'new')
	{
		$adminwarnings = $L['polls_created'];
	}
	elseif(!empty($poll_id))
	{
		$adminwarnings = $L['polls_updated'];
	}
}
else
{
	$adminwarnings = $error_string;
}

$is_adminwarnings = isset($adminwarnings);

if(!$filter)
{
    $poll_type = "1";
    $poll_filter = "";
}
else
{
    $poll_type = "poll_type='$filter'";
    $poll_filter = "&filter=$filter";
}

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_polls WHERE $poll_type");
$totalitems = sed_sql_result($sql, 0, "COUNT(*)");
if($cfg['jquery'] AND $cfg['turnajax'])
{
	$pagnav = sed_pagination(sed_url('admin','m=polls'.$poll_filter), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'ajaxSend', "url: '".sed_url('admin','m=polls&ajax=1'.$poll_filter)."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=polls'.$poll_filter), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'ajaxSend', "url: '".sed_url('admin','m=polls&ajax=1'.$poll_filter)."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
}
else
{
	$pagnav = sed_pagination(sed_url('admin','m=polls'.$poll_filter), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=polls'.$poll_filter), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
}

$sql = sed_sql_query("SELECT * FROM $db_polls
					WHERE $poll_type ORDER BY poll_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
$indexheader = false;
$forumheader = false;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.polls.loop');
/* ===== */

while($row = sed_sql_fetcharray($sql))
{
	$id = $row['poll_id'];
	$type = $row['poll_type'];

	if($type == 'index')
	{
		$admtypepoll = sed_url('polls', "id=".$row['poll_id']);
	}
	else
	{
		$admtypepoll = sed_url('forums', "m=posts&q=".$row['poll_id']);
	}

	$poll_state = ($row['poll_state']) ? "[-] " : "";

    $sql2 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$id'");
    $totalvotes = sed_sql_result($sql2, 0, "SUM(po_count)");

	$t -> assign(array(
		"ADMIN_POLLS_ROW_POLL_CREATIONDATE" => date($cfg['formatyearmonthday'], $row['poll_creationdate']),
		"ADMIN_POLLS_ROW_POLL_TYPE" => $variants[htmlspecialchars($type)][0],
		"ADMIN_POLLS_ROW_POLL_URL" => sed_url('admin', "m=polls".$poll_filter."&n=options&d=".$d."&id=".$row['poll_id']),
		"ADMIN_POLLS_ROW_POLL_TEXT" => htmlspecialchars($row['poll_text']),
		"ADMIN_POLLS_ROW_POLL_TOTALVOTES" => $totalvotes,
		"ADMIN_POLLS_ROW_POLL_CLOSED" => $poll_state,
		"ADMIN_POLLS_ROW_POLL_URL_DEL" => sed_url('admin', "m=polls".$poll_filter."&a=delete&id=".$id."&".sed_xg()),
		"ADMIN_POLLS_ROW_POLL_URL_LCK" => sed_url('admin', "m=polls".$poll_filter."&a=lock&id=".$id."&".sed_xg()),
		"ADMIN_POLLS_ROW_POLL_URL_RES" => sed_url('admin', "m=polls".$poll_filter."&a=reset&d=".$d."&id=".$id."&".sed_xg()),
		"ADMIN_POLLS_ROW_POLL_URL_BMP" => sed_url('admin', "m=polls".$poll_filter."&a=bump&id=".$id."&".sed_xg()),
		"ADMIN_POLLS_ROW_POLL_URL_OPN" => $admtypepoll,
		"ADMIN_POLLS_ROW_POLL_ODDEVEN" => sed_build_oddeven($ii)
	));

	/* === Hook - Part2 : Include === */
	if(is_array($extp))
	{
		foreach($extp as $k => $pl)
		{
			 include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
		}
	}
	/* ===== */

	$t -> parse("POLLS.POLLS_ROW");

	$ii++;
}

if($n == 'options')
{
	$poll_id = sed_import('id', 'G', 'TXT');
	$adminpath[] = array(sed_url('admin', 'm=polls'.$poll_filter.'&n=options&id='.$poll_id.'&d='.$d), $L['Options']." (#$id)");
	$formname = $L['editdeleteentries'];
	$send_button = $L['Update'];
}
elseif(!empty($error_string))
{
	if($poll_id != 'new')
	{
		$adminpath[] = array(sed_url('admin', 'm=polls'.$poll_filter.'&n=options&id='.$poll_id.'&d='.$d), $L['Options']." (#$id)");
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

foreach($variants as $val)
{
	$checked = ($filter == $val[1]) ? " selected='selected'" : "";
	if($val[1])
	{
		$val[1] = "&filter=".$val[1];
	}

	$t -> assign(array(
		"ADMIN_POLLS_ROW_FILTER_VALUE" => sed_url('admin', "m=polls".$val[1]),
		"ADMIN_POLLS_ROW_FILTER_CHECKED" => $checked,
		"ADMIN_POLLS_ROW_FILTER_NAME" => $val[0]
	));
	$t -> parse("POLLS.POLLS_ROW_FILTER");
}

sed_poll_edit_form($poll_id, $t, "POLLS");

$t -> assign(array(
	"ADMIN_POLLS_AJAX_OPENDIVID" => 'pagtab',
	"ADMIN_POLLS_CONF_URL" => sed_url('admin', "m=config&n=edit&o=core&p=polls"),
	"ADMIN_POLLS_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_POLLS_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_POLLS_PAGNAV" => $pagnav,
	"ADMIN_POLLS_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_POLLS_TOTALITEMS" => $totalitems,
	"ADMIN_POLLS_ON_PAGE" => $ii,
	"ADMIN_POLLS_FORMNAME" => $formname,
	"ADMIN_POLLS_FORM_URL" => ($poll_id != 'new') ? sed_url('admin', "m=polls".$poll_filter."&d=".$d) : sed_url('admin', "m=polls"),
	"ADMIN_POLLS_EDIT_FORM" => $poll_text,
	"ADMIN_POLLS_SEND_BUTTON" => $send_button
));

/* === Hook  === */
$extp = sed_getextplugins('admin.polls.tags');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

$t -> parse("POLLS");
$adminmain = $t -> text("POLLS");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>