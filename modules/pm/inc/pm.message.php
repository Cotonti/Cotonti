<?php

/**
 * PM
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forms');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('pm', 'a');
cot_block(Cot::$usr['auth_read']);

$id = cot_import('id','G','INT');				// Message ID
$q = cot_import('q','G','TXT');					// Quote
$history = cot_import('history','G','BOL');		// Turn on history
list($pg, $d, $durl) = cot_import_pagenav('d', Cot::$cfg['pm']['maxpmperpage']); //pagination history

if (empty($id)) {
	cot_redirect(cot_url('pm'));
}

/* === Hook === */
foreach (cot_getextplugins('pm.first') as $pl)
{
	include $pl;
}
/* ===== */

list($totalsentbox, $totalinbox) = cot_message_count(Cot::$usr['id']);
$pmsql = Cot::$db->query("SELECT * FROM $db_pm WHERE pm_id = $id LIMIT 1");
cot_die($pmsql->rowCount() == 0);
$row = $pmsql->fetch();

$title[] = array(cot_url('pm'), Cot::$L['Private_Messages']);

$row['pm_icon_edit'] = $row['pm_edit_url'] = '';

if ($row['pm_touserid'] == Cot::$usr['id']) {
	if ($row['pm_tostate'] == COT_PM_STATE_UNREAD) {
		Cot::$db->update(Cot::$db->pm, ['pm_tostate' => COT_PM_STATE_READ], 'pm_id = ?', $id);
		if (Cot::$db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid = '".Cot::$usr['id']."' AND pm_tostate = 0")->fetchColumn() == 0) {
			Cot::$db->update($db_users, array('user_newpm' => '0'), "user_id = '".Cot::$usr['id']."'");
		}
	}
	$f = 'inbox';
	$title[] = array(cot_url('pm', 'f=inbox'), Cot::$L['pm_inbox']);
	$to = $row['pm_fromuserid'];
	$star_class = ($row['pm_tostate'] == COT_PM_STATE_STARRED) ?  1 : 0;
	$totalcount = $totalinbox;
	$subtitle = Cot::$L['pm_inbox'];

} elseif ($row['pm_fromuserid'] == Cot::$usr['id']) {
	$f = 'sentbox';
	$title[] = array(cot_url('pm', 'f=sentbox'), Cot::$L['pm_sentbox']);

    if ($row['pm_tostate'] == 0) {
        $row['pm_edit_url']  = cot_url('pm', 'm=send&id='.$row['pm_id']);
        $row['pm_icon_edit'] = cot_rc_link($row['pm_edit_url'], Cot::$L['Edit']);
    }

	$to = $row['pm_touserid'];
	$star_class = ($row['pm_fromstate'] == 2) ?  1 : 0;
	$totalcount = $totalsentbox;
	$subtitle = Cot::$L['pm_sentbox'];

} else {
	cot_die();
}

$row_user = Cot::$db->query("SELECT * FROM $db_users WHERE user_id = $to LIMIT 1")->fetch();

$title_params = array(
	'PM' => Cot::$L['Private_Messages'],
	'COUNT' => $totalcount,
	'BOX' => $subtitle
);
Cot::$out['subtitle'] = cot_title('{BOX} ({COUNT}) - {PM}', $title_params);
Cot::$out['head'] .= Cot::$R['code_noindex'];

Resources::linkFileFooter(Cot::$cfg['modules_dir'].'/pm/js/pm.js');

/* === Hook === */
foreach (cot_getextplugins('pm.main') as $pl)
{
	include $pl;
}
/* ===== */

$pm_maindata = cot_parse($row['pm_text'], Cot::$cfg['pm']['markup']);

require_once Cot::$cfg['system_dir'] . '/header.php';

if (!isset($pmalttpl)) $pmalttpl = null;
$t = new XTemplate(cot_tplfile(array('pm', 'message', $pmalttpl)));

if ($history)
{
	$totallines = Cot::$db->query("SELECT COUNT(*) FROM $db_pm WHERE (pm_fromuserid = '".Cot::$usr['id']."' AND pm_touserid = $to AND pm_fromstate <> 3)
						OR (pm_fromuserid = $to AND pm_touserid = '".Cot::$usr['id']."' AND pm_tostate <> 3)")->fetchColumn();
	$sql_pm_history = $db->query("SELECT *, u.user_name FROM $db_pm AS p LEFT JOIN $db_users AS u ON u.user_id = p.pm_touserid
						WHERE (pm_fromuserid = '".Cot::$usr['id']."' AND pm_touserid = $to AND pm_fromstate <> 3)
						OR (pm_fromuserid = $to AND pm_touserid = '".Cot::$usr['id']."' AND pm_tostate <> 3)
						ORDER BY pm_date DESC LIMIT $d,".Cot::$cfg['pm']['maxpmperpage']);

	$pagenav = cot_pagenav('pm', 'm=message&id='.$id.'&history='.(int)$history.'&q='.$q, $d, $totallines, Cot::$cfg['pm']['maxpmperpage'], 'd', '', Cot::$cfg['pm']['turnajax'], 'ajaxHistory');

	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('pm.history.loop');
	/* ===== */
	$jj = 0;
	foreach ($sql_pm_history->fetchAll() as $row2)
	{
		$jj++;
		$row2['pm_icon_readstatus'] = ($row2['pm_tostate'] == '0') ?
				cot_rc_link(cot_url('pm', 'm=message&id='.$row2['pm_id']), Cot::$R['pm_icon_new'], array('title' => Cot::$L['pm_unread'], 'class'=> Cot::$cfg['pm']['turnajax'] ? 'ajax' : ''))
				: cot_rc_link(cot_url('pm', 'm=message&id='.$row2['pm_id']), Cot::$R['pm_icon'], array('title' => Cot::$L['pm_read'], 'class'=> Cot::$cfg['pm']['turnajax'] ? 'ajax' : ''));

		if ($row2['pm_fromuserid'] == Cot::$usr['id'])
		{// sentbox
			$pm_user = cot_generate_usertags(Cot::$usr['profile'], 'PM_ROW_USER_');
			$star_class2 = ($row2['pm_fromstate'] == 2) ? 1 : 0;
		}
		else
		{//inbox
			$pm_user = cot_generate_usertags($row_user, 'PM_ROW_USER_');
			$star_class2 = ($row2['pm_tostate'] == 2) ? 1 : 0;
		}

		$pm_data = cot_parse($row2['pm_text'], Cot::$cfg['pm']['markup']);

		$url_star = cot_url('pm', 'f='.$f.'&a=star&id='.$row['pm_id'].'&d='.$durl);
		$url_pm = cot_url('pm', 'm=message&id='.$row2['pm_id']);
		$url_delete = cot_url('pm', 'm=edit&a=delete&'.cot_xg().'&id='.$row2['pm_id'].'&f='.$f.'&d='.$durl);
		$url_edit = cot_url('pm', 'm=send&id='.$row2['pm_id']);

		$t->assign(array(
			'PM_ROW_ID' => $row2['pm_id'],
			'PM_ROW_STATE' => $row2['pm_tostate'],
			'PM_ROW_STAR' => cot_rc($star_class2 ? 'pm_icon_unstar' : 'pm_icon_star', array('link' => $url_star)),
            'PM_ROW_STARED' => $star_class2,
			'PM_ROW_STAR_URL' => $url_star,
			'PM_ROW_DATE' => cot_date('datetime_medium', $row2['pm_date']),
			'PM_ROW_DATE_STAMP' => $row2['pm_date'],
			'PM_ROW_TITLE' => cot_rc_link($url_pm, htmlspecialchars($row2['pm_title']), array('class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
			'PM_ROW_URL' => $url_pm,
			'PM_ROW_TEXT' => $pm_data,
			'PM_ROW_ICON_STATUS' => $row2['pm_icon_readstatus'],
			'PM_ROW_ICON_DELETE' => cot_rc_link(
                $url_delete,
                Cot::$R['pm_icon_trashcan'],
                ['title' => Cot::$L['Delete'], 'class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : 'confirmLink',]
            ),
			'PM_ROW_DELETE_URL' => $url_delete,
			'PM_ROW_DELETE_CONFIRM_URL' => cot_confirm_url($url_delete),
			'PM_ROW_ICON_EDIT' => ($row2['pm_tostate'] == 0) ? cot_rc_link($url_edit, Cot::$R['pm_icon_edit'], array('title' => Cot::$L['Edit'], 'class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')) : '',
			'PM_ROW_EDIT_URL' => ($row2['pm_tostate'] == 0) ? $url_edit : '',
			'PM_ROW_ODDEVEN' => cot_build_oddeven($jj),
			'PM_ROW_NUM' => $jj
		));
		$t->assign($pm_user);

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse('MAIN.HISTORY.PM_ROW');
	}

	if ($jj == 0)
	{
		$t->parse('MAIN.HISTORY.PM_ROW_EMPTY');
	}
	$t->assign(array(
		'PM_FORM_UPDATE' => cot_url('pm', cot_xg()),
		'PM_PAGEPREV' => $pagenav['prev'],
		'PM_PAGENEXT' => $pagenav['next'],
		'PM_PAGES' => $pagenav['main']
	));
	$t->parse('MAIN.HISTORY');
}

if (Cot::$usr['auth_write']) {
	if (preg_match("/Re(\(\d+\))?\:(.+)/", $row['pm_title'], $matches)) {
		$matches[1] = empty($matches[1]) ? 2 : trim($matches[1], '()') + 1;
		$newpmtitle = 'Re(' . $matches[1] . '): ' . trim($matches[2]);
	} else {
		$newpmtitle = 'Re: ' . $row['pm_title'];
	}

	switch ($editor) {
		case 'markitup':
			$newpmtext = !empty($q) ? '[quote]' . htmlspecialchars($row['pm_text']) . '[/quote]' : '';
			if (Cot::$cfg['jquery']) {
                $onclick = "insertText(document, 'newpmtext', '[quote]' + $('#pm_text').text() + '[/quote]'); return false;";
            }
			break;

		case 'ckeditor':
			if (Cot::$cfg['jquery']) {
                $onclick = "CKEDITOR.instances.newpmtext.insertHtml('<blockquote>' + $('#pm_text').text() + '</blockquote><br />'); return false;";
            }

		default:
			$newpmtext = !empty($q) ? '<blockquote>' . $row['pm_text'] . '</blockquote>' : '';
	}

	$t->assign(array(
		'PM_QUOTE' => cot_rc_link(cot_url('pm', 'm=message&id='.$id.'&q=quote&history='.(int)$history.'&d='.$durl), Cot::$L['Quote'], array('onclick' => $onclick)),
        'PM_QUOTE_URL' => cot_url('pm', 'm=message&id='.$id.'&q=quote&history='.(int)$history.'&d='.$durl),
        'PM_QUOTE_ONCLICK' => $onclick,
		'PM_FORM_SEND' => cot_url('pm', 'm=send&a=send&to='.$to),
		'PM_FORM_TITLE' => cot_inputbox('text', 'newpmtitle', htmlspecialchars($newpmtitle), 'size="56" maxlength="255"'),
		'PM_FORM_TEXT' => cot_textarea('newpmtext', $newpmtext, 8, 56, '', 'input_textarea_editor'),
        'PM_FORM_NOT_TO_SENTBOX' => cot_checkbox(false, 'fromstate', Cot::$L['pm_notmovetosentbox'], '', '3')
	));

	/* === Hook === */
	foreach (cot_getextplugins('pm.reply.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.REPLY');
}
if (!COT_AJAX)
{
	$t->parse('MAIN.BEFORE_AJAX');
	$t->parse('MAIN.AFTER_AJAX');
}

$pm_username = cot_build_user($row_user['user_id'], $row_user['user_name']);
$title[] = array(cot_url('users', 'm=details&id='.$row_user['user_id']), $row_user['user_name']);
$title[] = array(cot_url('pm', 'm=message&id='.$id), $row['pm_title']);

$url_delete = cot_url('pm', array('m'=>'edit', 'a'=>'delete', 'id'=>$row['pm_id'],'x'=>Cot::$sys['xk'], 'f'=>$f));

$t->assign(array(
    'PM_ID' => $row['pm_id'],
	'PM_PAGETITLE' => cot_breadcrumbs($title, Cot::$cfg['homebreadcrumb']),
	'PM_SENDNEWPM' => (Cot::$usr['auth_write']) ? cot_rc_link(cot_url('pm', 'm=send'), Cot::$L['pm_sendnew'], array('class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')) : '',
    'PM_SENDNEWPM_URL' => cot_url('pm', 'm=send'),
	'PM_INBOX' => cot_rc_link(cot_url('pm'), Cot::$L['pm_inbox'], array('class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
    'PM_INBOX_URL' => cot_url('pm'),
	'PM_INBOX_COUNT' => $totalinbox,
	'PM_SENTBOX' => cot_rc_link(cot_url('pm', 'f=sentbox'), Cot::$L['pm_sentbox'], array('class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
    'PM_SENTBOX_URL' => cot_url('pm', 'f=sentbox'),
	'PM_SENTBOX_COUNT' => $totalsentbox,
	'PM_STATE' => $row['pm_tostate'],
	'PM_STAR' => cot_rc($star_class ? 'pm_icon_unstar' : 'pm_icon_star', array('link' => cot_url('pm', 'f='.$f.'&a=star&id='.$row['pm_id'].'&d='.$durl))),
    'PM_STARED' => $star_class,
	'PM_DATE' => cot_date('datetime_medium', $row['pm_date']),
	'PM_DATE_STAMP' => $row['pm_date'],
	'PM_TITLE' => htmlspecialchars($row['pm_title']),
	'PM_TEXT' => '<div id="pm_text">'.$pm_maindata.'</div>',
	'PM_DELETE' => cot_rc_link($url_delete , Cot::$L['Delete'], array('class'=> Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
	'PM_DELETE_CONFIRM' => cot_rc_link(cot_confirm_url($url_delete ), Cot::$L['Delete'], array('class'=> 'confirmLink')),
    'PM_DELETE_CONFIRM_URL' => cot_confirm_url($url_delete),
	'PM_DELETE_URL' => $url_delete ,
	'PM_EDIT' => $row['pm_icon_edit'],
    'PM_EDIT_URL' => (!empty($row['pm_edit_url'])) ? $row['pm_edit_url'] : '',
	'PM_HISTORY' => cot_rc_link(cot_url('pm', 'm=message&id='.$id.'&q='.$q.'&history=1&d='.$durl), Cot::$L['pm_messagehistory'], array("rel" => "get-ajaxHistory", 'class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
    'PM_HISTORY_URL' => cot_url('pm', 'm=message&id='.$id.'&q='.$q.'&history=1&d='.$durl),
	'PM_SENT_TYPE' => ($f == 'sentbox') ? Cot::$L['Recipient'] : Cot::$L['Sender']
));
$t->assign(cot_generate_usertags($row_user, 'PM_USER_'));

/* === Hook === */
foreach (cot_getextplugins('pm.tags') as $pl) {
	include $pl;
}
/* ===== */

if (COT_AJAX && $history) {
	$t->out('MAIN.HISTORY');
} else {
	$t->parse('MAIN');
	$t->out('MAIN');
}

require_once Cot::$cfg['system_dir'] . '/footer.php';
