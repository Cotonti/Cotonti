<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin
[END_COT_EXT]
==================== */

/**
 * Administration panel - Poll editor
 *
 * @package polls
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('polls', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('polls', 'module');

$t = new XTemplate(cot_tplfile('polls.admin', 'module'));

$adminpath[] = array(cot_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(cot_url('admin', 'm=polls'), $L['Polls']);
$adminhelp = $L['adm_help_polls'];

list($pg, $d) = cot_import_pagenav('d', $cfg['maxrowsperpage']);
$filter = cot_import('filter', 'G', 'TXT');

//$variant[key]=array("Caption", "filter", "page", "page_get", "sql", "sqlfield")
$variants[0] = array($L['All'], "");
$variants['index'] = array($L['Main'], "index");
$variants['forum'] = array($L['Forums'], "forum");

/* === Hook === */
foreach (cot_getextplugins('adim.polls.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'delete')
{
	cot_check_xg();
	cot_poll_delete($id);

	$adminwarnings = $L['adm_polls_msg916_deleted'];
}
elseif($a == 'reset')
{
	cot_check_xg();
	cot_poll_reset($id);

	$adminwarnings = $L['adm_polls_msg916_reset'];
}
elseif($a == 'lock')
{
	cot_check_xg();
	cot_poll_lock($id, 3);

	$adminwarnings = $L['Locked'];
}
elseif($a == 'bump')
{
	cot_check_xg();
	$sql = $db->query("UPDATE $db_polls SET poll_creationdate='".$sys['now_offset']."' WHERE poll_id='$id'");

	$adminwarnings = $L['adm_polls_msg916_bump'];
}

cot_poll_check();

if (!$cot_error)
{
	$number = cot_poll_save();

	if ($poll_id == 'new')
	{
		$adminwarnings = $L['polls_created'];
	}
	elseif (!empty($poll_id))
	{
		$adminwarnings = $L['polls_updated'];
	}

	if ($cache && $cfg['cache_index'])
	{
		$cache->page->clear('index');
	}
}
else
{
	$adminwarnings = cot_implode_messages();
	cot_clear_messages();
}

$is_adminwarnings = isset($adminwarnings);

if(!$filter)
{
    $poll_type = '1';
    $poll_filter = '';
}
else
{
    $poll_type = 'poll_type='.$filter;
    $poll_filter = '"&filter='.$filter;
}

$sql = $db->query("SELECT COUNT(*) FROM $db_polls WHERE $poll_type");
$totalitems = $sql->fetchColumn();
$pagenav = cot_pagenav('admin', 'm=polls'.$poll_filter, $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql = $db->query("SELECT * FROM $db_polls
					WHERE $poll_type ORDER BY poll_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
$indexheader = false;
$forumheader = false;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('polls.admin.loop');
/* ===== */

while($row = $sql->fetch())
{
	$id = $row['poll_id'];
	$type = $row['poll_type'];

	if($type == 'index')
	{
		$admtypepoll = cot_url('polls', 'id='.$row['poll_id']);
	}
	else
	{
		$admtypepoll = cot_url('forums', 'm=posts&q='.$row['poll_id']);
	}

	$poll_state = ($row['poll_state']) ? '[-] ' : '';

    $sql2 = $db->query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$id'");
    $totalvotes = $sql2->fetchColumn();

	$t->assign(array(
		'ADMIN_POLLS_ROW_POLL_CREATIONDATE' => date($cfg['formatyearmonthday'], $row['poll_creationdate']),
		'ADMIN_POLLS_ROW_POLL_TYPE' => $variants[htmlspecialchars($type)][0],
		'ADMIN_POLLS_ROW_POLL_URL' => cot_url('admin', 'm=polls'.$poll_filter.'&n=options&d='.$d.'&id='.$row['poll_id']),
		'ADMIN_POLLS_ROW_POLL_TEXT' => htmlspecialchars($row['poll_text']),
		'ADMIN_POLLS_ROW_POLL_TOTALVOTES' => $totalvotes,
		'ADMIN_POLLS_ROW_POLL_CLOSED' => $poll_state,
		'ADMIN_POLLS_ROW_POLL_URL_DEL' => cot_url('admin', 'm=polls'.$poll_filter.'&a=delete&id='.$id.'&'.cot_xg()),
		'ADMIN_POLLS_ROW_POLL_URL_LCK' => cot_url('admin', 'm=polls'.$poll_filter.'&a=lock&id='.$id.'&'.cot_xg()),
		'ADMIN_POLLS_ROW_POLL_URL_RES' => cot_url('admin', 'm=polls'.$poll_filter.'&a=reset&d='.$d.'&id='.$id.'&'.cot_xg()),
		'ADMIN_POLLS_ROW_POLL_URL_BMP' => cot_url('admin', 'm=polls'.$poll_filter.'&a=bump&id='.$id.'&'.cot_xg()),
		'ADMIN_POLLS_ROW_POLL_URL_OPN' => $admtypepoll,
		'ADMIN_POLLS_ROW_POLL_ODDEVEN' => cot_build_oddeven($ii)
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.POLLS_ROW');

	$ii++;
}

if ($ii == 0)
{
	$t->parse('MAIN.POLLS_ROW_EMPTY');
}

if ($n == 'options')
{
	$poll_id = cot_import('id', 'G', 'TXT');
	$adminpath[] = array(cot_url('admin', 'm=polls'.$poll_filter.'&n=options&id='.$poll_id.'&d='.$d), $L['Options']." (#$id)");
	$formname = $L['editdeleteentries'];
	$send_button = $L['Update'];
}
elseif ($cot_error)
{
	if ($poll_id != 'new')
	{
		$adminpath[] = array(cot_url('admin', 'm=polls'.$poll_filter.'&n=options&id='.$poll_id.'&d='.$d), $L['Options']." (#$id)");
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
		$val[1] = '&filter='.$val[1];
	}

	$t->assign(array(
		'ADMIN_POLLS_ROW_FILTER_VALUE' => cot_url('admin', 'm=polls'.$val[1]),
		'ADMIN_POLLS_ROW_FILTER_CHECKED' => $checked,
		'ADMIN_POLLS_ROW_FILTER_NAME' => $val[0]
	));
	$t->parse('MAIN.POLLS_ROW_FILTER');
}

cot_poll_edit_form($poll_id, $t, 'MAIN');

$t->assign(array(
	'ADMIN_POLLS_CONF_URL' => cot_url('admin', 'm=config&n=edit&o=module&p=polls'),
	'ADMIN_POLLS_ADMINWARNINGS' => $adminwarnings,
	'ADMIN_POLLS_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_POLLS_PAGNAV' => $pagenav['main'],
	'ADMIN_POLLS_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_POLLS_TOTALITEMS' => $totalitems,
	'ADMIN_POLLS_ON_PAGE' => $ii,
	'ADMIN_POLLS_FORMNAME' => $formname,
	'ADMIN_POLLS_FORM_URL' => ($poll_id != 'new') ? cot_url('admin', 'm=polls'.$poll_filter.'&d='.$d) : cot_url('admin', 'm=polls'),
	'ADMIN_POLLS_EDIT_FORM' => $poll_text,
	'ADMIN_POLLS_SEND_BUTTON' => $send_button
));

/* === Hook  === */
foreach (cot_getextplugins('polls.admin.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (COT_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>