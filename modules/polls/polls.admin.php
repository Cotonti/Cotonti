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

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('polls', 'a');
cot_block(Cot::$usr['isadmin']);

require_once cot_incfile('polls', 'module');
require_once cot_incfile('polls', 'module', 'resources');

$t = new XTemplate(cot_tplfile('polls.admin', 'module', true));

$adminPath[] = array(cot_url('admin', 'm=extensions'), Cot::$L['Extensions']);
$adminPath[] = array(cot_url('admin', 'm=extensions&a=details&mod='.$m), $cot_modules[$m]['title']);
$adminPath[] = array(cot_url('admin', 'm='.$m), Cot::$L['Administration']);
$adminHelp = Cot::$L['adm_help_polls'];
$adminTitle = Cot::$L['Polls'];

list($pg, $d, $durl) = cot_import_pagenav('d', Cot::$cfg['maxrowsperpage']);
$filter = cot_import('filter', 'G', 'TXT');

//$variant[key]=array("Caption", "filter", "page", "page_get", "sql", "sqlfield")
$variants[0] = array(Cot::$L['All'], "");
$variants['index'] = array(Cot::$L['Main'], "index");
$variants['forum'] = array(Cot::$L['Forums'], "forum");

$id = cot_import('id', 'G', 'INT');

/* === Hook === */
foreach (cot_getextplugins('polls.admin.first') as $pl) {
	include $pl;
}
/* ===== */

$urlParams = ['m' => 'polls'];
if (!empty($filter)) {
    $urlParams['filter'] = $filter;
}
if (!empty($durl)) {
    $urlParams['d'] = $durl;
}

if ($id > 0) {
    if ($a == 'delete') {
        cot_check_xg();
        cot_poll_delete($id);
        cot_message('adm_polls_msg916_deleted');
        cot_redirect(cot_url('admin', $urlParams, '', true));

    } elseif ($a == 'reset') {
        cot_check_xg();
        cot_poll_reset($id);
        cot_message('adm_polls_msg916_reset');
        cot_redirect(cot_url('admin', $urlParams, '', true));

    } elseif ($a == 'lock') {
        cot_check_xg();
        cot_poll_lock($id, 3);
        cot_message('Locked');
        cot_redirect(cot_url('admin', $urlParams, '', true));

    } elseif ($a == 'bump') {
        cot_check_xg();
        $sql_polls = Cot::$db->update(Cot::$db->polls, ['poll_creationdate' => Cot::$sys['now']], "poll_id=$id");
        cot_message('adm_polls_msg916_bump');
        cot_redirect(cot_url('admin', $urlParams, '', true));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    cot_poll_check();

    if (!cot_error_found()) {
        $number = cot_poll_save();

        if ($poll_id == 0) {
            cot_message('polls_created');

        } elseif (!empty($poll_id)) {
            cot_message('polls_updated');
        }

        if (Cot::$cache && Cot::$cfg['cache_index']) {
            Cot::$cache->static->clear('index');
        }
    }
}

if (!$filter) {
    $poll_type = '1';
    $poll_filter = '';
} else {
    $poll_type = 'poll_type="'.$filter.'"';
    $poll_filter = '"&filter='.$filter;
}

$totalitems = Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->polls . " WHERE $poll_type")->fetchColumn();
if ($totalitems <= Cot::$cfg['maxrowsperpage'] && !empty($d)) {
    unset($urlParams['d']);
    cot_redirect(cot_url('admin', $urlParams, '', true));
}

$pagenav = cot_pagenav(
    'admin',
    'm=polls' . $poll_filter,
    $d,
    $totalitems,
    Cot::$cfg['maxrowsperpage'],
    'd',
    '',
    Cot::$cfg['jquery'] && Cot::$cfg['turnajax']
);
if ($pagenav['current'] > $pagenav['total']) {
    $urlParams['d'] = Cot::$cfg['easypagenav']
        ? $pagenav['total']
        : ($pagenav['total'] - 1) * Cot::$cfg['maxrowsperpage'];
    cot_redirect(cot_url('admin', $urlParams, '', true));
}

$sql_polls = Cot::$db->query(
    "SELECT * FROM $db_polls WHERE $poll_type ORDER BY poll_id DESC LIMIT $d, " . Cot::$cfg['maxrowsperpage']
);

$ii = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('polls.admin.loop');
/* ===== */

while ($row = $sql_polls->fetch()) {
	$ii++;
	$id = $row['poll_id'];
    $totalvotes = Cot::$db->query('SELECT SUM(po_count) FROM ' . Cot::$db->polls_options . " WHERE po_pollid=$id")
        ->fetchColumn();

    $actionUrlParams = $urlParams + ['id' => $id, 'x' => Cot::$sys['xk']];

    $deleteUrl = cot_url('admin', $actionUrlParams + ['a' => 'delete']);
    $deleteConfirmUrl = cot_confirm_url($deleteUrl, 'admin');

	$t->assign([
		'ADMIN_POLLS_ROW_POLL_CREATIONDATE' => cot_date('date_full', $row['poll_creationdate']),
		'ADMIN_POLLS_ROW_POLL_CREATIONDATE_STAMP' => $row['poll_creationdate'],
		'ADMIN_POLLS_ROW_POLL_TYPE' => $variants[htmlspecialchars($row['poll_type'])][0],
		'ADMIN_POLLS_ROW_POLL_URL' => cot_url('admin', 'm=polls'.$poll_filter.'&n=options&d='.$durl.'&id='.$row['poll_id']),
		'ADMIN_POLLS_ROW_POLL_TEXT' => htmlspecialchars($row['poll_text']),
		'ADMIN_POLLS_ROW_POLL_TOTALVOTES' => $totalvotes,
		'ADMIN_POLLS_ROW_POLL_LOCKED' => ($row['poll_state']) ? Cot::$R['polls_icon_locked'] : '',

        'ADMIN_POLLS_ROW_POLL_DELETE_URL' => $deleteUrl,
        'ADMIN_POLLS_ROW_POLL_DELETE_CONFIRM_URL' => $deleteConfirmUrl,
        'ADMIN_POLLS_ROW_POLL_URL_LCK' => cot_url('admin', $actionUrlParams + ['a' => 'lock']),
        'ADMIN_POLLS_ROW_POLL_URL_RES' => cot_url('admin', $actionUrlParams + ['a' => 'reset']),
		'ADMIN_POLLS_ROW_POLL_URL_BMP' => cot_url('admin', $actionUrlParams + ['a' => 'bump']),
		'ADMIN_POLLS_ROW_POLL_URL_OPN' => ($row['poll_type'] == 'index')
            ? cot_url('polls', ['id' => $id])
            : cot_url('forums', ['m' => 'posts', 'q' => $row['poll_code']]),
		'ADMIN_POLLS_ROW_POLL_ODDEVEN' => cot_build_oddeven($ii),
	]);

    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        // @deprecated in 0.9.24
        $t->assign([
            'ADMIN_POLLS_ROW_POLL_URL_DEL' => $deleteUrl,
        ]);
    }

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl) {
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.POLLS_ROW');
}
$sql_polls->closeCursor();

if ($ii == 0) {
	$t->parse('MAIN.POLLS_ROW_EMPTY');
}

if ($n == 'options') {
	$poll_id = cot_import('id', 'G', 'INT');
	$adminPath[] = array(cot_url('admin', 'm=polls'.$poll_filter.'&n=options&id='.(int)$poll_id.'&d='.$durl), $L['Options']." (#$poll_id)");
	$formname = $L['editdeleteentries'];
	$send_button = $L['Update'];
} elseif (cot_error_found()) {
	if ((int)$poll_id > 0)
	{
		$adminPath[] = array(cot_url('admin', 'm=polls'.$poll_filter.'&n=options&id='.(int)$poll_id.'&d='.$durl), $L['Options']." (#$poll_id)");
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
	'ADMIN_POLLS_ADMINWARNINGS' => !empty($adminwarnings) ? $adminwarnings : '',//TODO: May by need deprecate adminwarnings ?
	'ADMIN_POLLS_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_POLLS_PAGNAV' => $pagenav['main'],
	'ADMIN_POLLS_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_POLLS_TOTALITEMS' => $totalitems,
	'ADMIN_POLLS_ON_PAGE' => $ii,
	'ADMIN_POLLS_FORMNAME' => $formname,
	'ADMIN_POLLS_FORM_URL' => ((int) $poll_id > 0) ? cot_url('admin', 'm=polls'.$poll_filter.'&d='.$durl) : cot_url('admin', 'm=polls'),
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
$adminMain = $t->text('MAIN');
