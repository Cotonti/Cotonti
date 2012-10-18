<?php

/**
 * PM
 *
 * @package pm
 * @version 0.9.6
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forms');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('pm', 'a');
cot_block($usr['auth_read']);

$id = cot_import('id','G','INT');				// Message ID
$q = cot_import('q','G','TXT');					// Quote
$history = cot_import('history','G','BOL');		// Turn on history
list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['pm']['maxpmperpage']); //pagination history

if (empty($id))
{
	cot_redirect(cot_url('pm'));
}

/* === Hook === */
foreach (cot_getextplugins('pm.first') as $pl)
{
	include $pl;
}
/* ===== */

list($totalsentbox, $totalinbox) = cot_message_count($usr['id']);
$pmsql = $db->query("SELECT * FROM $db_pm WHERE pm_id = $id LIMIT 1");
cot_die($pmsql->rowCount() == 0);
$row = $pmsql->fetch();

$title[] = array(cot_url('pm'), $L['Private_Messages']);

if ($row['pm_touserid'] == $usr['id'])
{
	if ($row['pm_tostate'] == 0)
	{
		$db->update($db_pm, array('pm_tostate' => '1'), "pm_id = $id");
		if ($db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid = '".$usr['id']."' AND pm_tostate = 0")->fetchColumn() == 0)
		{
			$db->update($db_users, array('user_newpm' => '0'), "user_id = '".$usr['id']."'");
		}
	}
	$f = 'inbox';
	$title[] = array(cot_url('pm', 'f=inbox'), $L['pm_inbox']);
	$to = $row['pm_fromuserid'];
	$star_class = ($row['pm_tostate'] == 2) ?  1 : 0;
	$totalcount = $totalinbox;
	$subtitle = $L['pm_inbox'];
}
elseif ($row['pm_fromuserid'] == $usr['id'])
{
	$f = 'sentbox';
	$title[] = array(cot_url('pm', 'f=sentbox'), $L['pm_sentbox']);
	$row['pm_icon_edit'] = ($row['pm_tostate'] == 0) ? cot_rc_link(cot_url('pm', 'm=send&id='.$row['pm_id']), $L['Edit']) : '';
	$to = $row['pm_touserid'];
	$star_class = ($row['pm_fromstate'] == 2) ?  1 : 0;
	$totalcount = $totalsentbox;
	$subtitle = $L['pm_sentbox'];
}
else
{
	cot_die();
}
$row_user = $db->query("SELECT * FROM $db_users WHERE user_id = $to LIMIT 1")->fetch();

$title_params = array(
	'PM' => $L['Private_Messages'],
	'COUNT' => $totalcount,
	'BOX' => $subtitle
);
$out['subtitle'] = cot_title('{BOX} ({COUNT}) - {PM}', $title_params);
$out['head'] .= $R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('pm.main') as $pl)
{
	include $pl;
}
/* ===== */

$pm_maindata = cot_parse($row['pm_text'], $cfg['pm']['markup']);

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(cot_tplfile(array('pm', 'message', $pmalttpl)));

if ($history)
{
	$totallines = $db->query("SELECT COUNT(*) FROM $db_pm WHERE (pm_fromuserid = '".$usr['id']."' AND pm_touserid = $to AND pm_fromstate <> 3)
						OR (pm_fromuserid = $to AND pm_touserid = '".$usr['id']."' AND pm_tostate <> 3)")->fetchColumn();
	$sql_pm_history = $db->query("SELECT *, u.user_name FROM $db_pm AS p LEFT JOIN $db_users AS u ON u.user_id = p.pm_touserid
						WHERE (pm_fromuserid = '".$usr['id']."' AND pm_touserid = $to AND pm_fromstate <> 3)
						OR (pm_fromuserid = $to AND pm_touserid = '".$usr['id']."' AND pm_tostate <> 3)
						ORDER BY pm_date DESC LIMIT $d,".$cfg['pm']['maxpmperpage']);

	$pagenav = cot_pagenav('pm', 'm=message&id='.$id.'&history='.(int)$history.'&q='.$q, $d, $totallines, $cfg['pm']['maxpmperpage'], 'd', '', $cfg['pm']['turnajax'], 'ajaxHistory');

	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('pm.history.loop');
	/* ===== */
	$jj = 0;
	foreach ($sql_pm_history->fetchAll() as $row2)
	{
		$jj++;
		$row2['pm_icon_readstatus'] = ($row2['pm_tostate'] == '0') ?
				cot_rc_link(cot_url('pm', 'm=message&id='.$row2['pm_id']), $R['pm_icon_new'], array('title' => $L['pm_unread'], 'class'=> $cfg['pm']['turnajax'] ? 'ajax' : ''))
				: cot_rc_link(cot_url('pm', 'm=message&id='.$row2['pm_id']), $R['pm_icon'], array('title' => $L['pm_read'], 'class'=> $cfg['pm']['turnajax'] ? 'ajax' : ''));

		if ($row2['pm_fromuserid'] == $usr['id'])
		{// sentbox
			$pm_user = cot_generate_usertags($usr['profile'], 'PM_ROW_USER_');
			$star_class2 = ($row2['pm_fromstate'] == 2) ? 1 : 0;
		}
		else
		{//inbox
			$pm_user = cot_generate_usertags($row_user, 'PM_ROW_USER_');
			$star_class2 = ($row2['pm_tostate'] == 2) ? 1 : 0;
		}

		$pm_data = cot_parse($row2['pm_text'], $cfg['pm']['markup']);

		$url_star = cot_url('pm', 'f='.$f.'&filter='.$filter.'&a=star&id='.$row['pm_id'].'&d='.$durl);
		$url_pm = cot_url('pm', 'm=message&id='.$row2['pm_id']);
		$url_delete = cot_url('pm', 'm=edit&a=delete&'.cot_xg().'&id='.$row2['pm_id'].'&f='.$f.'&d='.$durl);
		$url_edit = cot_url('pm', 'm=send&id='.$row2['pm_id']);

		$t->assign(array(
			'PM_ROW_ID' => $row2['pm_id'],
			'PM_ROW_STATE' => $row2['pm_tostate'],
			'PM_ROW_STAR' => cot_rc($star_class2 ? 'pm_icon_unstar' : 'pm_icon_star', array('link' => $url_star)),
			'PM_ROW_STAR_URL' => $url_star,
			'PM_ROW_DATE' => cot_date('datetime_medium', $row2['pm_date']),
			'PM_ROW_DATE_STAMP' => $row2['pm_date'],
			'PM_ROW_TITLE' => cot_rc_link($url_pm, htmlspecialchars($row2['pm_title']), array('class' => $cfg['pm']['turnajax'] ? 'ajax' : '')),
			'PM_ROW_URL' => $url_pm,
			'PM_ROW_TEXT' => $pm_data,
			'PM_ROW_ICON_STATUS' => $row2['pm_icon_readstatus'],
			'PM_ROW_ICON_DELETE' => cot_rc_link($url_delete, $R['pm_icon_trashcan'], array('title' => $L['Delete'], 'class' => $cfg['pm']['turnajax'] ? 'ajax' : '')),
			'PM_ROW_DELETE_URL' => $url_delete,
			'PM_ROW_ICON_EDIT' => ($row2['pm_tostate'] == 0) ? cot_rc_link($url_edit, $R['pm_icon_edit'], array('title' => $L['Edit'], 'class' => $cfg['pm']['turnajax'] ? 'ajax' : '')) : '',
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

if ($usr['auth_write'])
{
	if (preg_match("/Re(\(\d+\))?\:(.+)/", $row['pm_title'], $matches))
	{
		$matches[1] = empty($matches[1]) ? 2 : trim($matches[1], '()') + 1;
		$newpmtitle = 'Re(' . $matches[1] . '): ' . trim($matches[2]);
	}
	else
	{
		$newpmtitle = 'Re: ' . $row['pm_title'];
	}
	$newpmtext = (!empty($q)) ? '[quote]'.htmlspecialchars($row['pm_text']).'[/quote]' : '';
	$onclick = "insertText(document, 'newpmtext', '[quote]'+$('#pm_text').text()+'[/quote]'); return false;";

	$t->assign(array(
		'PM_QUOTE' => cot_rc_link(cot_url('pm', 'm=message&id='.$id.'&q=quote&history='.(int)$history.'&d='.$durl), $L['Quote'], array('onclick' => $onclick)),
		'PM_FORM_SEND' => cot_url('pm', 'm=send&a=send&to='.$to),
		'PM_FORM_TITLE' => cot_inputbox('text', 'newpmtitle', htmlspecialchars($newpmtitle), 'size="56" maxlength="255"'),
		'PM_FORM_TEXT' => cot_textarea('newpmtext', htmlspecialchars($newpmtext), 8, 56, '', 'input_textarea_editor'),
		'PM_AJAX_MARKITUP' => (COT_AJAX && cot_plugin_active('markitup') && $cfg['pm']['turnajax'])
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

$pm_username = cot_build_user($row_user['user_id'], htmlspecialchars($row_user['user_name']));
$title[] =  array(cot_url('users', 'm=details&id='.$row_user['user_id']), $row_user['user_name']);
$title[] = array(cot_url('pm', 'm=message&id='.$id), $row['pm_title']);
$t->assign(array(
	'PM_PAGETITLE' => cot_breadcrumbs($title, $cfg['homebreadcrumb']),
	'PM_SENDNEWPM' => ($usr['auth_write']) ? cot_rc_link(cot_url('pm', 'm=send'), $L['pm_sendnew'], array('class' => $cfg['pm']['turnajax'] ? 'ajax' : '')) : '',
	'PM_INBOX' => cot_rc_link(cot_url('pm'), $L['pm_inbox'], array('class' => $cfg['pm']['turnajax'] ? 'ajax' : '')),
	'PM_INBOX_COUNT' => $totalinbox,
	'PM_SENTBOX' => cot_rc_link(cot_url('pm', 'f=sentbox'), $L['pm_sentbox'], array('class' => $cfg['pm']['turnajax'] ? 'ajax' : '')),
	'PM_SENTBOX_COUNT' => $totalsentbox,
	'PM_ID' => $row['pm_id'],
	'PM_STATE' => $row['pm_tostate'],
	'PM_STAR' => cot_rc($star_class ? 'pm_icon_unstar' : 'pm_icon_star', array('link' => cot_url('pm', 'f='.$f.'&filter='.$filter.'&a=star&id='.$row['pm_id'].'&d='.$durl))),
	'PM_DATE' => cot_date('datetime_medium', $row['pm_date']),
	'PM_DATE_STAMP' => $row['pm_date'],
	'PM_TITLE' => htmlspecialchars($row['pm_title']),
	'PM_TEXT' => '<div id="pm_text">'.$pm_maindata.'</div>',
	'PM_DELETE' => cot_rc_link(cot_url('pm', 'm=edit&a=delete&'.cot_xg().'&id='.$row['pm_id'].'&f='.$f), $L['Delete'], array('class'=> $cfg['pm']['turnajax'] ? 'ajax' : '')),
	'PM_EDIT' => $row['pm_icon_edit'],
	'PM_HISTORY' => cot_rc_link(cot_url('pm', 'm=message&id='.$id.'&q='.$q.'&history=1&d='.$durl), $L['pm_messagehistory'], array("rel" => "get-ajaxHistory", 'class' => $cfg['pm']['turnajax'] ? 'ajax' : '')),
	'PM_SENT_TYPE' => ($f == 'sentbox') ? $L['Recipient'] : $L['Sender']
));
$t->assign(cot_generate_usertags($row_user, 'PM_USER_'));

/* === Hook === */
foreach (cot_getextplugins('pm.tags') as $pl)
{
	include $pl;
}
/* ===== */

if (COT_AJAX && $history)
{
	$t->out('MAIN.HISTORY');
}
else
{
	$t->parse('MAIN');
	$t->out('MAIN');
}
require_once $cfg['system_dir'] . '/footer.php';

?>