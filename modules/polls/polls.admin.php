<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin
[END_COT_EXT]
==================== */

/**
 * Administration panel - Poll editor
 *
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('polls', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('polls', 'module');
require_once cot_incfile('polls', 'module', 'resources');

$t = new XTemplate(cot_tplfile('polls.admin', 'module', true));

$adminpath[] = array(cot_url('admin', 'm=extensions'), $L['Extensions']);
$adminpath[] = array(cot_url('admin', 'm=extensions&a=details&mod='.$m), $cot_modules[$m]['title']);
$adminpath[] = array(cot_url('admin', 'm='.$m), $L['Administration']);
$adminhelp = $L['adm_help_polls'];
$adminsubtitle = $L['Polls'];

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['maxrowsperpage']);
$filter = cot_import('filter', 'G', 'TXT');

//$variant[key]=array("Caption", "filter", "page", "page_get", "sql", "sqlfield")
$variants[0] = array($L['All'], "");
$variants['index'] = array($L['Main'], "index");
$variants['forum'] = array($L['Forums'], "forum");

$id = cot_import('id', 'G', 'INT');

/* === Hook === */
foreach (cot_getextplugins('polls.admin.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'delete' && $id > 0)
{
	cot_check_xg();
	cot_poll_delete($id);

	cot_message('adm_polls_msg916_deleted');
}
elseif($a == 'reset' && $id > 0)
{
	cot_check_xg();
	cot_poll_reset($id);

	cot_message('adm_polls_msg916_reset');
}
elseif($a == 'lock' && $id > 0)
{
	cot_check_xg();
	cot_poll_lock($id, 3);

	cot_message('Locked');
}
elseif($a == 'bump' && $id > 0)
{
	cot_check_xg();
	$sql_polls = $db->update($db_polls, array('poll_creationdate' => $sys['now']),  "poll_id=$id");

	cot_message('adm_polls_msg916_bump');
}

cot_poll_check();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !cot_error_found())
{
	$number = cot_poll_save();

	if ($poll_id == 0)
	{
		cot_message('polls_created');
	}
	elseif (!empty($poll_id))
	{
		cot_message('polls_updated');
	}

	($cache && $cfg['cache_index']) && $cache->page->clear('index');
}

if(!$filter)
{
    $poll_type = '1';
    $poll_filter = '';
}
else
{
    $poll_type = 'poll_type="'.$filter.'"';
    $poll_filter = '"&filter='.$filter;
}

$totalitems = $db->query("SELECT COUNT(*) FROM $db_polls WHERE $poll_type")->fetchColumn();
$pagenav = cot_pagenav('admin', 'm=polls'.$poll_filter, $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql_polls = $db->query("SELECT * FROM $db_polls
					WHERE $poll_type ORDER BY poll_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('polls.admin.loop');
/* ===== */

foreach ($sql_polls->fetchAll() as $row)
{
	$ii++;
	$id = $row['poll_id'];
    $totalvotes = $db->query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid=$id")->fetchColumn();

	$t->assign(array(
		'ADMIN_POLLS_ROW_POLL_CREATIONDATE' => cot_date('date_full', $row['poll_creationdate']),
		'ADMIN_POLLS_ROW_POLL_CREATIONDATE_STAMP' => $row['poll_creationdate'],
		'ADMIN_POLLS_ROW_POLL_TYPE' => $variants[htmlspecialchars($row['poll_type'])][0],
		'ADMIN_POLLS_ROW_POLL_URL' => cot_url('admin', 'm=polls'.$poll_filter.'&n=options&d='.$durl.'&id='.$row['poll_id']),
		'ADMIN_POLLS_ROW_POLL_TEXT' => htmlspecialchars($row['poll_text']),
		'ADMIN_POLLS_ROW_POLL_TOTALVOTES' => $totalvotes,
		'ADMIN_POLLS_ROW_POLL_LOCKED' => ($row['poll_state']) ? '[-] ' : '',
		'ADMIN_POLLS_ROW_POLL_URL_DEL' => cot_url('admin', 'm=polls'.$poll_filter.'&a=delete&id='.$id.'&'.cot_xg()),
		'ADMIN_POLLS_ROW_POLL_URL_LCK' => cot_url('admin', 'm=polls'.$poll_filter.'&a=lock&id='.$id.'&'.cot_xg()),
		'ADMIN_POLLS_ROW_POLL_URL_RES' => cot_url('admin', 'm=polls'.$poll_filter.'&a=reset&d='.$durl.'&id='.$id.'&'.cot_xg()),
		'ADMIN_POLLS_ROW_POLL_URL_BMP' => cot_url('admin', 'm=polls'.$poll_filter.'&a=bump&id='.$id.'&'.cot_xg()),
		'ADMIN_POLLS_ROW_POLL_URL_OPN' => ($row['poll_type'] == 'index') ? cot_url('polls', 'id='.$id) : cot_url('forums', 'm=posts&q='.$id),
		'ADMIN_POLLS_ROW_POLL_ODDEVEN' => cot_build_oddeven($ii)
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.POLLS_ROW');
}

if ($ii == 0)
{
	$t->parse('MAIN.POLLS_ROW_EMPTY');
}

if ($n == 'options')
{
	$poll_id = cot_import('id', 'G', 'INT');
	$adminpath[] = array(cot_url('admin', 'm=polls'.$poll_filter.'&n=options&id='.(int)$poll_id.'&d='.$durl), $L['Options']." (#$poll_id)");
	$formname = $L['editdeleteentries'];
	$send_button = $L['Update'];
}
elseif (cot_error_found())
{
	if ((int)$poll_id > 0)
	{
		$adminpath[] = array(cot_url('admin', 'm=polls'.$poll_filter.'&n=options&id='.(int)$poll_id.'&d='.$durl), $L['Options']." (#$poll_id)");
		$formname = $L['editdeleteentries'];
		$send_button = $L['Update'];
	}
	else
	{
		$formname = $L['Add'];
		$send_button = $L['Create'];
	}
}
else
{
	$poll_id = 0;
	$formname = $L['Add'];
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
	'ADMIN_POLLS_FORM_URL' => ((int)$poll_id > 0) ? cot_url('admin', 'm=polls'.$poll_filter.'&d='.$durl) : cot_url('admin', 'm=polls'),
	'ADMIN_POLLS_EDIT_FORM' => $poll_text,
	'ADMIN_POLLS_SEND_BUTTON' => $send_button
));

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('polls.admin.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');
