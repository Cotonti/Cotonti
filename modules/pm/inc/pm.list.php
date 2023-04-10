<?php

/**
 * PM
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('pm', 'a');
cot_block(Cot::$usr['auth_read']);

$f = cot_import('f','G','ALP'); // Category inbox, sentbox, archive
list($pg, $d, $durl) = cot_import_pagenav('d', Cot::$cfg['pm']['maxpmperpage']); // pagination
$a = cot_import('a','G','TXT'); // Action
$filter = cot_import('filter','G','TXT');	// filter

/* === Hook === */
foreach (cot_getextplugins('pm.list.first') as $pl)
{
	include $pl;
}
/* ===== */

if (!empty($a)) {
	$id = cot_import('id','G','INT');		// Message id
	if ((int) $id > 0) {
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
		if (COT_AJAX && (int) $id > 0) {
			die();
		}
	}
}

list($totalsentbox, $totalinbox) = cot_message_count(Cot::$usr['id']);

$title[] = array(cot_url('pm'), Cot::$L['Private_Messages']);

if ($f == 'sentbox') {
	$sqlfilter = 'pm_fromuserid = ' . Cot::$usr['id'] . ' AND pm_fromstate <> ' . COT_PM_STATE_DELETED;
	$title[] = [cot_url('pm', 'f=sentbox'), Cot::$L['pm_sentbox']];
	$subtitle = Cot::$L['pm_sentboxsubtitle'];
	$totalcount = $totalsentbox;

} else {
	$f = 'inbox';
	$sqlfilter = 'pm_touserid = ' . Cot::$usr['id'] . ' AND pm_tostate <> ' . COT_PM_STATE_DELETED;
	$title[] = [cot_url('pm'), Cot::$L['pm_inbox']];
	$subtitle = Cot::$L['pm_inboxsubtitle'];
	$totalcount = $totalinbox;
}

if ($filter == 'unread') {
	$sqlfilter .= ' AND pm_tostate = ' . COT_PM_STATE_UNREAD;
	$title[] = Cot::$L['pm_unread'];

} elseif ($filter == 'starred') {
	$sqlfilter .= ($f == 'sentbox') ?
        ' AND pm_fromstate = ' . COT_PM_STATE_STARRED : ' AND pm_tostate = ' . COT_PM_STATE_STARRED;
	$title[] = Cot::$L['pm_starred'];
}

/* === Hook === */
foreach (cot_getextplugins('pm.list.main') as $pl) {
	include $pl;
}
/* ===== */

/* === Title === */
$title_params = array(
	'PM' => Cot::$L['Private_Messages'],
	'COUNT' => $totalcount,
	'BOX' => $subtitle
);
Cot::$out['subtitle'] = cot_title('{BOX} ({COUNT}) - {PM}', $title_params);
Cot::$out['head'] .= Cot::$R['code_noindex'];

Resources::linkFileFooter(Cot::$cfg['modules_dir'].'/pm/js/pm.js');

/* === Title === */
$totallines = Cot::$db->query("SELECT COUNT(*) FROM $db_pm WHERE $sqlfilter")->fetchColumn();
$elem = ($f == 'sentbox') ? 'pm_touserid' : 'pm_fromuserid';
$pm_sql = Cot::$db->query("SELECT p.*, u.* FROM $db_pm AS p
		LEFT JOIN $db_users AS u ON u.user_id = p.$elem
		WHERE $sqlfilter
		ORDER BY pm_date DESC LIMIT  $d,".Cot::$cfg['pm']['maxpmperpage']);

$pagenav = cot_pagenav('pm', 'f='.$f.'&filter='.$filter, $d, $totallines, Cot::$cfg['pm']['maxpmperpage'],
                       'd', '', Cot::$cfg['pm']['turnajax']);

require_once Cot::$cfg['system_dir'] . '/header.php';

if (!isset($pmalttpl)) {
    $pmalttpl = null;
}
$t = new XTemplate(cot_tplfile(array('pm', 'list', $pmalttpl)));

$jj = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('pm.list.loop');
/* ===== */

foreach ($pm_sql->fetchAll() as $row) {
	$jj++;
	$row['pm_icon_readstatus'] = ($row['pm_tostate'] == '0') ?
			cot_rc_link(cot_url('pm', 'm=message&id='.$row['pm_id']), Cot::$R['pm_icon_new'], array('title' => Cot::$L['pm_unread'], 'class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : ''))
			: cot_rc_link(cot_url('pm', 'm=message&id='.$row['pm_id']), Cot::$R['pm_icon'], array('title' => Cot::$L['pm_read'], 'class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : ''));

	$pm_data = cot_parse($row['pm_text'], Cot::$cfg['pm']['markup']);
	$pm_desc = cot_string_truncate($pm_data , 100 , true, false, '...');

	if ($f == 'sentbox') {
		$star_class = ($row['pm_fromstate'] == 2) ? 1 : 0;
	} else {
		$star_class = ($row['pm_tostate'] == 2) ? 1 : 0;
	}

    $url_star = cot_url('pm', 'f='.$f.'&filter='.$filter.'&a=star&id='.$row['pm_id'].'&d='.$durl);
    $url_edit = '';
    if ($row['pm_fromuserid'] == Cot::$usr['id'] && $row['pm_tostate'] == COT_PM_STATE_UNREAD) {
        $url_edit = cot_url('pm', ['m' => 'send', 'id' => $row['pm_id']]);
    }
	$url_delete = cot_url('pm', 'm=edit&a=delete&'.cot_xg().'&id='.$row['pm_id'].'&f='.$f.'&d='.$durl);

	$t->assign(array(
		'PM_ROW_ID' => $row['pm_id'],
		'PM_ROW_STATE' => $row['pm_tostate'],
		'PM_ROW_STAR' => cot_rc($star_class ? 'pm_icon_unstar' : 'pm_icon_star', array('link' => $url_star)),
        'PM_ROW_STARED' => $star_class,
		'PM_ROW_STAR_URL' => $url_star,
		'PM_ROW_DATE' => cot_date('datetime_medium', $row['pm_date']),
		'PM_ROW_DATE_STAMP' => $row['pm_date'],
		'PM_ROW_TITLE' => cot_rc_link(cot_url('pm', 'm=message&id='.$row['pm_id']), htmlspecialchars($row['pm_title']), array('class'=>Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
		'PM_ROW_URL' => cot_url('pm', 'm=message&id='.$row['pm_id']),
		'PM_ROW_TEXT' => $pm_data,
		'PM_ROW_ICON_STATUS' => $row['pm_icon_readstatus'],
		'PM_ROW_ICON_DELETE' => cot_rc_link($url_delete, Cot::$R['pm_icon_trashcan'], array('title' => Cot::$L['Delete'], 'class'=>Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
        // @todo confirmLink and ajax
		'PM_ROW_ICON_DELETE_CONFIRM' => cot_rc_link(
            cot_confirm_url($url_delete),
            Cot::$R['pm_icon_trashcan'],
            ['title' => Cot::$L['Delete'], 'class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : 'confirmLink',]
        ),
		'PM_ROW_DELETE_URL' => $url_delete,
		'PM_ROW_DELETE_CONFIRM_URL' => cot_confirm_url($url_delete),
		'PM_ROW_ICON_EDIT' => !empty($url_edit) ?
            cot_rc_link(
                $url_edit,
                Cot::$R['pm_icon_edit'],
                ['title' => Cot::$L['Edit'], 'class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : '',]
            )
            : '',
		'PM_ROW_EDIT_URL' => $url_edit,
		'PM_ROW_DESC' => $pm_desc,
		'PM_ROW_ODDEVEN' => cot_build_oddeven($jj),
		'PM_ROW_NUM' => $jj
	));
	$t->assign(cot_generate_usertags($row, 'PM_ROW_USER_'));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl) {
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.PM_ROW');
}

if ($jj == 0) {
	$t->parse('MAIN.PM_ROW_EMPTY');
}
if (!COT_AJAX) {
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
	'PM_PAGETITLE' => cot_breadcrumbs($title, Cot::$cfg['homebreadcrumb']),
	'PM_SUBTITLE' => $subtitle,
	'PM_FORM_UPDATE' => cot_url('pm', cot_xg().'&f='.$f.'&filter='.$filter.'&d='.$durl),
	'PM_SENDNEWPM' => (Cot::$usr['auth_write']) ? cot_rc_link($url_newpm, Cot::$L['pm_sendnew'], Cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : '') : '',
	'PM_SENDNEWPM_URL' => (Cot::$usr['auth_write']) ? $url_newpm : '',
	'PM_INBOX' => cot_rc_link($url_inbox, Cot::$L['pm_inbox'], Cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : ''),
	'PM_INBOX_URL' => $url_inbox,
	'PM_INBOX_COUNT' => $totalinbox,
	'PM_SENTBOX' => cot_rc_link($url_sentbox, Cot::$L['pm_sentbox'], Cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : ''),
	'PM_SENTBOX_URL' => $url_sentbox,
	'PM_SENTBOX_COUNT' => $totalsentbox,
	'PM_FILTER_ALL' => cot_rc_link($url_all, Cot::$L['pm_all'], Cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : ''),
	'PM_FILTER_ALL_URL' => $url_all,
	'PM_FILTER_UNREAD' => cot_rc_link($url_unread, Cot::$L['pm_unread'], Cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : ''),
	'PM_FILTER_UNREAD_URL' => $url_unread,
	'PM_FILTER_STARRED' => cot_rc_link($url_starred, Cot::$L['pm_starred'], Cot::$cfg['pm']['turnajax'] ? array('class'=>'ajax') : ''),
	'PM_FILTER_STARRED_URL' => $url_starred,
	'PM_PAGEPREV' => $pagenav['prev'],
	'PM_PAGENEXT' => $pagenav['next'],
	'PM_PAGES' => $pagenav['main'],
	'PM_CURRENTPAGE' => $pagenav['current'],
	'PM_TOTALPAGES' => $pagenav['total'],
	'PM_SENT_TYPE' => ($f == 'sentbox') ? Cot::$L['Recipient'] : Cot::$L['Sender']
));

/* === Hook === */
foreach (cot_getextplugins('pm.list.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
