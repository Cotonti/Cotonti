<?php

/**
 * PM
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('pm', 'a');
cot_block(cot::$usr['auth_read']);

$f = cot_import('f','G','ALP');				// Category inbox, sentbox, archive
list($pg, $d, $durl) = cot_import_pagenav('d', cot::$cfg['pm']['maxpmperpage']); // pagination
$a = cot_import('a','G','TXT');				// Action
$filter = cot_import('filter','G','TXT');	// filter

/*
 * PM States
 * 0 - new message
 * 1 - inbox message
 * 2 - starred message
 * 3 - deleted message
*/

/* === Hook === */
foreach (cot_getextplugins('pm.list.first') as $pl)
{
	include $pl;
}
/* ===== */

if (!empty($a)) {
	$id = cot_import('id','G','INT');		// Message id
	if ((int)$id > 0) {
		$msg[$id] = $id;
	}
} else {
	$msg = cot_import('msg', 'P', 'ARR');
	$a = cot_import('action', 'P', 'TXT');
}
if (count($msg) > 0) {
	if ($a == 'delete') {
		cot_check_xg();
		cot_remove_pm($msg);

	} elseif (!empty($a)) {
		cot_star_pm($msg);
		if (COT_AJAX && (int)$id > 0) {
			die();
		}
	}
}

list($totalsentbox, $totalinbox) = cot_message_count(cot::$usr['id']);

$title[] = array(cot_url('pm'), cot::$L['Private_Messages']);

if ($f == 'sentbox') {
	$sqlfilter = "pm_fromuserid = '".cot::$usr['id']."' AND pm_fromstate <> 3";
	$title[] = array(cot_url('pm', 'f=sentbox'), cot::$L['pm_sentbox']);
	$subtitle = cot::$L['pm_sentboxsubtitle'];
	$totalcount = $totalsentbox;

} else {
	$f = 'inbox';
	$sqlfilter = "pm_touserid = '".cot::$usr['id']."' AND pm_tostate <> 3";
	$title[] = array(cot_url('pm'), cot::$L['pm_inbox']);
	$subtitle = cot::$L['pm_inboxsubtitle'];
	$totalcount = $totalinbox;
}

if ($filter == 'unread') {
	$sqlfilter .= " AND pm_tostate = 0";
	$title[] = cot::$L['pm_unread'];

} elseif ($filter == 'starred') {
	$sqlfilter .= ($f == 'sentbox') ? " AND pm_fromstate = 2" : " AND pm_tostate = 2";
	$title[] = cot::$L['pm_starred'];
}

/* === Hook === */
foreach (cot_getextplugins('pm.list.main') as $pl)
{
	include $pl;
}
/* ===== */

/* === Title === */
$title_params = array(
	'PM' => cot::$L['Private_Messages'],
	'COUNT' => $totalcount,
	'BOX' => $subtitle
);
cot::$out['subtitle'] = cot_title('{BOX} ({COUNT}) - {PM}', $title_params);
cot::$out['head'] .= cot::$R['code_noindex'];

Resources::linkFileFooter(cot::$cfg['modules_dir'].'/pm/js/pm.js');

/* === Title === */
$totallines = cot::$db->query("SELECT COUNT(*) FROM $db_pm WHERE $sqlfilter")->fetchColumn();
$elem = ($f == 'sentbox') ? 'pm_touserid' : 'pm_fromuserid';
$pm_sql = cot::$db->query("SELECT p.*, u.* FROM $db_pm AS p
		LEFT JOIN $db_users AS u
		ON u.user_id = p.$elem
		WHERE $sqlfilter
		ORDER BY pm_date DESC LIMIT  $d,".cot::$cfg['pm']['maxpmperpage']);

$pagenav = cot_pagenav('pm', 'f='.$f.'&filter='.$filter, $d, $totallines, cot::$cfg['pm']['maxpmperpage'],
                       'd', '', cot::$cfg['pm']['turnajax']);

require_once cot::$cfg['system_dir'] . '/header.php';

if (!isset($pmalttpl)) $pmalttpl = null;
$t = new XTemplate(cot_tplfile(array('pm', 'list', $pmalttpl)));

$jj = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('pm.list.loop');
/* ===== */

foreach ($pm_sql->fetchAll() as $row)
{
	$jj++;
	$row['pm_icon_readstatus'] = ($row['pm_tostate'] == '0') ?
			cot_rc_link(cot_url('pm', 'm=message&id='.$row['pm_id']), cot::$R['pm_icon_new'], array('title' => cot::$L['pm_unread'], 'class'=> cot::$cfg['pm']['turnajax'] ? 'ajax' : ''))
			: cot_rc_link(cot_url('pm', 'm=message&id='.$row['pm_id']), cot::$R['pm_icon'], array('title' => cot::$L['pm_read'], 'class'=>'ajax'));

	$pm_data = cot_parse($row['pm_text'], cot::$cfg['pm']['markup']);
	$pm_desc = cot_string_truncate($pm_data , 100 , true, false, '...');

	if ($f == 'sentbox')
	{
		$star_class = ($row['pm_fromstate'] == 2) ? 1 : 0;
	}
	else
	{
		$star_class = ($row['pm_tostate'] == 2) ? 1 : 0;
	}

    $url_star = cot_url('pm', 'f='.$f.'&filter='.$filter.'&a=star&id='.$row['pm_id'].'&d='.$durl);
	$url_edit = cot_url('pm', 'm=send&id='.$row['pm_id']);
	$url_delete = cot_url('pm', 'm=edit&a=delete&'.cot_xg().'&id='.$row['pm_id'].'&f='.$f.'&d='.$durl);

	$t->assign(array(
		'PM_ROW_ID' => $row['pm_id'],
		'PM_ROW_STATE' => $row['pm_tostate'],
		'PM_ROW_STAR' => cot_rc($star_class ? 'pm_icon_unstar' : 'pm_icon_star', array('link' => $url_star)),
        'PM_ROW_STARED' => $star_class,
		'PM_ROW_STAR_URL' => $url_star,
		'PM_ROW_DATE' => cot_date('datetime_medium', $row['pm_date']),
		'PM_ROW_DATE_STAMP' => $row['pm_date'],
		'PM_ROW_TITLE' => cot_rc_link(cot_url('pm', 'm=message&id='.$row['pm_id']), htmlspecialchars($row['pm_title']), array('class'=>cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
		'PM_ROW_URL' => cot_url('pm', 'm=message&id='.$row['pm_id']),
		'PM_ROW_TEXT' => $pm_data,
		'PM_ROW_ICON_STATUS' => $row['pm_icon_readstatus'],
		'PM_ROW_ICON_DELETE' => cot_rc_link($url_delete, cot::$R['pm_icon_trashcan'], array('title' => cot::$L['Delete'], 'class'=>cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
		'PM_ROW_ICON_DELETE_CONFIRM' => cot_rc_link(cot_confirm_url($url_delete), cot::$R['pm_icon_trashcan'], array('title' => cot::$L['Delete'], 'class'=>cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
		'PM_ROW_DELETE_URL' => $url_delete,
		'PM_ROW_DELETE_CONFIRM_URL' => cot_confirm_url($url_delete),
		'PM_ROW_ICON_EDIT' => ($row['pm_tostate'] == 0) ? cot_rc_link($url_edit, cot::$R['pm_icon_edit'], array('title' => cot::$L['Edit'], 'class'=> cot::$cfg['pm']['turnajax'] ? 'ajax' : '')) : '',
		'PM_ROW_EDIT_URL' => ($row['pm_tostate'] == 0) ? $url_edit : '',
		'PM_ROW_DESC' => $pm_desc,
		'PM_ROW_ODDEVEN' => cot_build_oddeven($jj),
		'PM_ROW_NUM' => $jj
	));
	$t->assign(cot_generate_usertags($row, 'PM_ROW_USER_'));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.PM_ROW');
}

if ($jj == 0)
{
	$t->parse('MAIN.PM_ROW_EMPTY');
}
if (!COT_AJAX)
{
	$t->parse('MAIN.BEFORE_AJAX');
	$t->parse('MAIN.AFTER_AJAX');
}

$url_newpm = cot_url('pm', 'm=send');
$url_inbox = cot_url('pm');
$url_sentbox = cot_url('pm', 'f=sentbox');
$url_all = cot_url('pm', 'f='.$f);
$url_unread = cot_url('pm', 'f='.$f.'&filter=unread');
$url_starred = cot_url('pm', 'f='.$f.'&filter=starred');

$t->assign(array(
	'PM_PAGETITLE' => cot_breadcrumbs($title, cot::$cfg['homebreadcrumb']),
	'PM_SUBTITLE' => $subtitle,
	'PM_FORM_UPDATE' => cot_url('pm', cot_xg().'&f='.$f.'&filter='.$filter.'&d='.$durl),
	'PM_SENDNEWPM' => (cot::$usr['auth_write']) ? cot_rc_link($url_newpm, cot::$L['pm_sendnew'], cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : '') : '',
	'PM_SENDNEWPM_URL' => (cot::$usr['auth_write']) ? $url_newpm : '',
	'PM_INBOX' => cot_rc_link($url_inbox, cot::$L['pm_inbox'], cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : ''),
	'PM_INBOX_URL' => $url_inbox,
	'PM_INBOX_COUNT' => $totalinbox,
	'PM_SENTBOX' => cot_rc_link($url_sentbox, cot::$L['pm_sentbox'], cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : ''),
	'PM_SENTBOX_URL' => $url_sentbox,
	'PM_SENTBOX_COUNT' => $totalsentbox,
	'PM_FILTER_ALL' => cot_rc_link($url_all, cot::$L['pm_all'], cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : ''),
	'PM_FILTER_ALL_URL' => $url_all,
	'PM_FILTER_UNREAD' => cot_rc_link($url_unread, cot::$L['pm_unread'], cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : ''),
	'PM_FILTER_UNREAD_URL' => $url_unread,
	'PM_FILTER_STARRED' => cot_rc_link($url_starred, cot::$L['pm_starred'], cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : ''),
	'PM_FILTER_STARRED_URL' => $url_starred,
	'PM_PAGEPREV' => $pagenav['prev'],
	'PM_PAGENEXT' => $pagenav['next'],
	'PM_PAGES' => $pagenav['main'],
	'PM_CURRENTPAGE' => $pagenav['current'],
	'PM_TOTALPAGES' => $pagenav['total'],
	'PM_SENT_TYPE' => ($f == 'sentbox') ? cot::$L['Recipient'] : cot::$L['Sender']
));

/* === Hook === */
foreach (cot_getextplugins('pm.list.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once cot::$cfg['system_dir'] . '/footer.php';
