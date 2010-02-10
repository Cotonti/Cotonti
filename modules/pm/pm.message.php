<?php

/**
 * PM
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pm', 'a');
sed_block($usr['auth_read']);

$id = sed_import('id','G','INT');				// Message ID
$q = sed_import('q','G','TXT');					// Quote
$history = sed_import('history','G','BOL');		// Turn on history
$d = sed_import('d','G','INT');					// Page number of history
$d = empty($d) ? 0 : (int) $d;

if (empty($id))
{
	sed_redirect(sed_url('pm'));
}

/* === Hook === */
$extp = sed_getextplugins('pm.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

list($totalsentbox, $totalinbox) = sed_message_count($usr['id']);
$sql = sed_sql_query("SELECT * FROM $db_pm WHERE pm_id = '".$id."' LIMIT 1");
sed_die(sed_sql_numrows($sql) == 0);
$row = sed_sql_fetcharray($sql);

$title = sed_rc_link(sed_url('pm'), $L['Private_Messages']) ." ".$cfg['separator'];

if ($row['pm_touserid'] == $usr['id'])
{	
	if ($row['pm_tostate'] == 0)
	{
		$sql = sed_sql_query("UPDATE $db_pm SET pm_tostate = 1 WHERE pm_id = '".$id."'");
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid = '".$usr['id']."' AND pm_tostate = 0");
		if (sed_sql_result($sql,0,'COUNT(*)') == 0)
		{
			$sql = sed_sql_query("UPDATE $db_users SET user_newpm = 0 WHERE user_id = '".$usr['id']."'");
		}
	}
	$f = 'inbox';
	$title .= ' '.sed_rc_link(sed_url('pm', 'f=inbox'), $L['pm_inbox']);
	$to = $row['pm_fromuserid'];
	$titstar = ($row2['pm_tostate'] == 2) ? $L['pm_deletefromstarred'] : $L['pm_putinstarred'];
	$star_class = ($row['pm_tostate'] == 2) ? 'star-rating star-rating-on' : 'star-rating';
}
elseif ($row['pm_fromuserid'] == $usr['id'])
{
	$f = 'sentbox';
	$title .= ' '.sed_rc_link(sed_url('pm', 'f=sentbox'), $L['pm_sentbox']);
	$row['pm_icon_edit'] = ($row['pm_tostate'] == 0) ? sed_rc_link(sed_url('pm', 'm=send&id='.$row['pm_id']), $L['Edit']) : '';
	$to = $row['pm_touserid'];
	$titstar = ($row2['pm_fromstate'] == 2) ? $L['pm_deletefromstarred'] : $L['pm_putinstarred'];
	$star_class = ($row['pm_fromstate'] == 2) ? 'star-rating star-rating-on' : 'star-rating';
}
else
{
	sed_die();
}
$sql_user = sed_sql_query("SELECT * FROM $db_users WHERE user_id = '".$to."' LIMIT 1");
$row_user = sed_sql_fetcharray($sql_user);


$star = '<div class="'.$star_class.'">'.$row['pm_icon_starred'].'</div>';
$row['pm_icon_starred'] = sed_rc_link(sed_url('pm', 'a=star&&id='.$row['pm_id']), $R['pm_icon_archive'], array('title' => $titstar));

$title_tags[] = array('{PM}', '{INBOX}', '{SENTBOX}');
$title_tags[] = array('%1$s', '%2$s', '%3$s');
$title_data = array($L['Private_Messages'], $totalinbox, $totalsentbox);
$out['subtitle'] = sed_title('title_pm_main', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('pm.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if($cfg['parser_cache'])
{
	if(empty($row['pm_html']) && !empty($row['pm_text']))
	{
		$row['pm_html'] = sed_parse(htmlspecialchars($row['pm_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1);
		sed_sql_query("UPDATE $db_pm SET pm_html = '".sed_sql_prep($row['pm_html'])."' WHERE pm_id = " . $row['pm_id']);
	}
	$pm_maindata = sed_post_parse($row['pm_html']);
}
else
{
	$pm_maindata = sed_parse(htmlspecialchars($row['pm_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1);
	$pm_maindata = sed_post_parse($pm_maindata);
}

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('pm'));

if ($history)
{	
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE (pm_fromuserid = '".$usr['id']."' AND pm_touserid = '".$to."' AND pm_fromstate <> 3)
						OR (pm_fromuserid = '".$to."' AND pm_touserid = '".$usr['id']."' AND pm_tostate <> 3)");
	$totallines = sed_sql_result($sql, 0, "COUNT(*)");
	$d = ($d >= $totallines) ? (floor($totallines / $cfg['maxpmperpage']))*$cfg['maxpmperpage'] : $d;
	$sql = sed_sql_query("SELECT *, u.user_name FROM $db_pm AS p LEFT JOIN $db_users AS u ON u.user_id = p.pm_touserid
						WHERE (pm_fromuserid = '".$usr['id']."' AND pm_touserid = '".$to."' AND pm_fromstate <> 3)
						OR (pm_fromuserid = '".$to."' AND pm_touserid = '".$usr['id']."' AND pm_tostate <> 3)
						ORDER BY pm_date DESC LIMIT $d,".$cfg['maxpmperpage']);

	$pm_totalpages = ceil($totallines / $cfg['maxpmperpage']);
	$pm_currentpage = ceil ($d / $cfg['maxpmperpage'])+1;
	$pagenav = sed_pagenav('pm', 'm=message&id='.$id.'&history='.$history.'&q='.$q, $d, $totallines, $cfg['maxpmperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax'], 'ajaxHistory');

	/* === Hook - Part1 : Set === */
	$extp = sed_getextplugins('pm.history.loop');
	/* ===== */
	$jj = 0;
	while ($row2 = sed_sql_fetcharray($sql))
	{
		$jj++;
		$row2['pm_icon_readstatus'] = ($row2['pm_tostate'] == '0') ?
				sed_rc_link(sed_url('pm', 'm=message&id='.$row2['pm_id']), $R['pm_icon_new'], array('title' => $L['pm_unread'], 'class'=>'ajax'))
				: sed_rc_link(sed_url('pm', 'm=message&id='.$row2['pm_id']), $R['pm_icon'], array('title' => $L['pm_read'], 'class'=>'ajax'));

		if ($row2['pm_fromuserid']==$usr['id'])
		{// sentbox
			$row2['pm_icon_edit'] = ($row2['pm_tostate'] == 0) ? sed_rc_link(sed_url('pm', 'm=send&id='.$row2['pm_id']), $R['pm_icon_edit'], array('title' => $L['Edit'], 'class'=>'ajax')) : '';
			$pm_user = sed_generate_usertags($usr['profile'], "PM_ROW_USER");
			$titstar = ($row2['pm_fromstate'] == 2) ? $L['pm_deletefromstarred'] : $L['pm_putinstarred'];
			$star_class = ($row2['pm_fromstate'] == 2) ? 'star-rating star-rating-on' : 'star-rating';
		}
		else
		{//inbox
			$pm_user = sed_generate_usertags($row_user, "PM_ROW_USER");
			$titstar = ($row2['pm_tostate'] == 2) ? $L['pm_deletefromstarred'] : $L['pm_putinstarred'];
			$star_class = ($row2['pm_tostate'] == 2) ? 'star-rating star-rating-on' : 'star-rating';
		}

		if ($cfg['parser_cache'])
		{
			if (empty($row2['pm_html']) && !empty($row2['pm_text']))
			{
				$row2['pm_html'] = sed_parse(htmlspecialchars($row2['pm_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1);
				sed_sql_query("UPDATE $db_pm SET pm_html = '".sed_sql_prep($row2['pm_html'])."' WHERE pm_id = " . $row2['pm_id']);
			}
			$pm_data = sed_post_parse($row2['pm_html']);
		}
		else
		{
			$pm_data = sed_parse(htmlspecialchars($row2['pm_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1);
			$pm_data = sed_post_parse($pm_data);
		}
		$row2['pm_icon_delete'] = sed_rc_link(sed_url('pm', 'a=delete&'.sed_xg().'&id='.$row2['pm_id'].'&f='.$f.'&d='.$d),
				$R['pm_icon_trashcan'], array('title' => $L['Delete'], 'class'=>'ajax'));
		$row2['pm_icon_starred'] = sed_rc_link(sed_url('pm', '&a=star&id='.$row2['pm_id']),
				$R['pm_icon_archive'], array('title' => $arch_lab));
		$star2 = '<div class="'.$star_class.'">'.$row2['pm_icon_starred'].'</div>';

		$t->assign(array(
				"PM_ROW_ID" => $row2['pm_id'],
				"PM_ROW_STATE" => $row2['pm_tostate'],
				"PM_ROW_STAR" => $star2,
				"PM_ROW_DATE" => @date($cfg['dateformat'], $row2['pm_date'] + $usr['timezone'] * 3600),
				"PM_ROW_TITLE" => sed_rc_link(sed_url('pm', 'm=message&id='.$row2['pm_id']), htmlspecialchars($row2['pm_title']), array('class'=>'ajax')),
				"PM_ROW_TEXT" => $pm_data,
				"PM_ROW_ICON_STATUS" => $row2['pm_icon_readstatus'],
				"PM_ROW_ICON_STARRED" => $row2['pm_icon_starred'],
				"PM_ROW_ICON_DELETE" => sed_rc_link(sed_url('pm', 'm=edit&a=delete&'.sed_xg().'&id='.$row2['pm_id'].'&f='.$f.'&d='.$d), $R['pm_icon_trashcan'], array('title' => $L['Delete'], 'class'=>'ajax')),
				"PM_ROW_ICON_EDIT" => $row2['pm_icon_edit'],
				"PM_ROW_ODDEVEN" => sed_build_oddeven($jj),
				"PM_ROW_NUM" => $jj
		));
		$t->assign($pm_user);
		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse("MAIN.HISTORY.PM_ROW");
	}

	if ($jj == 0)
	{
		$t->parse("MAIN.HISTORY.PM_ROW_EMPTY");
	}
	$t->assign(array(
			"PM_FORM_UPDATE" => sed_url('pm', sed_xg()),
			"PM_PAGEPREV" => $pagenav['prev'],
			"PM_PAGENEXT" => $pagenav['next'],
			'PM_PAGES' => $pagenav['main'],
	));
	$t->parse("MAIN.HISTORY");
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
	$onclick = "insertText(document, 'newlink', 'newpmtext', '[quote]'+$('#pm_text').text()+'[/quote]'); return false;";
	$pfs = sed_build_pfs($usr['id'], 'newlink', 'newpmtext', $L['Mypfs']);
	$pfs .= (sed_auth('pfs', 'a', 'A')) ? ' &nbsp; '.sed_build_pfs(0, 'newlink', 'newpmtext', $L['SFS']) : '';
	$t->assign(array(
			"PM_QUOTE" => sed_rc_link(sed_url('pm', 'm=message&id='.$id.'&q=quote&history='.$history.'&d='.$d), $L['Quote'], array('onclick' => $onclick)),
			"PM_FORM_SEND" => sed_url('pm', 'm=send&a=send&to='.$to),
			"PM_FORM_TITLE" => htmlspecialchars($newpmtitle),
			"PM_FORM_TEXT" => $newpmtext,
			"PM_FORM_PFS" => $pfs,
			"PM_AJAX_MARKITUP" => (SED_AJAX && count($cfg['plugin']['markitup'])>0 && $cfg['jquery'] && $cfg['turnajax'])
	));
	$t->parse("MAIN.REPLY");
}
if (!SED_AJAX)
{
	$t->parse("MAIN.BEFORE_AJAX");
	$t->parse("MAIN.AFTER_AJAX");
}
$pm_user = sed_generate_usertags($row_user, "PM_ROW_USER");
$t->assign(array(
		"PM_PAGETITLE" => $title.' '.$cfg['separator'].' '.$pm_fromortouser.' '.$cfg['separator'].' '.sed_rc_link(sed_url('pm', 'id='.$id),htmlspecialchars($row['pm_title'])),
		"PM_SENDNEWPM" => ($usr['auth_write']) ? sed_rc_link(sed_url('pm', 'm=send'), $L['pm_sendnew'], array('class'=>'ajax')) : '',
		"PM_INBOX" => sed_rc_link(sed_url('pm'), $L['pm_inbox'], array('class'=>'ajax')),
		"PM_INBOX_COUNT" => $totalinbox,
		"PM_SENTBOX" => sed_rc_link(sed_url('pm', 'f=sentbox'), $L['pm_sentbox'], array('class'=>'ajax')),
		"PM_SENTBOX_COUNT" => $totalsentbox,
		"PM_ID" => $row['pm_id'],
		"PM_STATE" => $row['pm_tostate'],
		"PM_STAR" => $star,
		"PM_DATE" => @date($cfg['dateformat'], $row['pm_date'] + $usr['timezone'] * 3600),
		"PM_TITLE" => sed_rc_link(sed_url('pm', 'id='.$row['pm_id']), htmlspecialchars($row['pm_title'])),
		"PM_TEXT" => '<div id="pm_text">'.$pm_maindata.'</div>',
		"PM_ICON_STARRED" => $row['pm_icon_starred'],
		"PM_ICON_DELETE" => sed_rc_link(sed_url('pm', 'm=edit&a=delete&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f), $L['Delete'], array('class'=>'ajax')),
		"PM_ICON_EDIT" => $row['pm_icon_edit'],
		"PM_HISTORY" => sed_rc_link(sed_url('pm', 'm=message&id='.$id.'&q='.$q.'&history=1&d='.$d), $L['pm_messagehistory'], array("rel" => "get-ajaxHistory", 'class'=>'ajax')),
		"PM_SENT_TYPE" => ($f == 'sentbox') ? $L['Recipient'] : $L['Sender']
		));
$t->assign(sed_generate_usertags($pm_user, "PM_USER"));

/* === Hook === */
$extp = sed_getextplugins('pm.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");

if (SED_AJAX && $history)
{
	$t->out("MAIN.HISTORY");
}
else
{
	$t->out("MAIN");
}
require_once $cfg['system_dir'] . '/footer.php';

?>