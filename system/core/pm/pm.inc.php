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

if(empty($id))
{
	sed_redirect(sed_url('pm'));
}

/* == Reading Messeges Count == */
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_tostate=2");
$totalarchives = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_fromuserid='".$usr['id']." AND pm_fromstate<>3'");
$totalsentbox = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_tostate<2");
$totalinbox = sed_sql_result($sql, 0, "COUNT(*)");
/* == Reading Messeges Count == */

/* === Hook === */
$extp = sed_getextplugins('pm.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$sql = sed_sql_query("SELECT *, u.user_name FROM $db_pm AS p LEFT JOIN $db_users AS u ON u.user_id=p.pm_touserid WHERE pm_id='".$id."'");
sed_die(sed_sql_numrows($sql)==0);
$row = sed_sql_fetcharray($sql);

$title = sed_rc_link(sed_url('pm'), $L['Private_Messages']) ." ".$cfg['separator'];

if ($row['pm_touserid']==$usr['id'])
{
	if ($row['pm_tostate']==0)
	{
		$sql = sed_sql_query("UPDATE $db_pm SET pm_tostate=1 WHERE pm_id='".$id."'");
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_tostate=0");
		$notread = sed_sql_result($sql,0,'COUNT(*)');
		if ($notread==0)
		{ $sql = sed_sql_query("UPDATE $db_users SET user_newpm=0 WHERE user_id='".$usr['id']."'"); }
	}

	if ($row['pm_tostate']==2)
	{
		$f = 'archives';
		$title .= ' '.sed_rc_link(sed_url('pm', 'f=archives'), $L['pm_archives']);
		$row['pm_icon_archive'] = sed_rc_link(sed_url('pm', 'a=archive&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f), $R['pm_icon_archive'], array('title' => $L['pm_deletefromarchives']));
	}
	else
	{
		$f = 'inbox';
		$title .= ' '.sed_rc_link(sed_url('pm', 'f=inbox'), $L['pm_inbox']);
		$row['pm_icon_archive'] = sed_rc_link(sed_url('pm', 'a=archive&'.sed_xg().'&id='.$row['pm_id']), $R['pm_icon_archive'], array('title' => $L['pm_putinarchives']));
	}

	$row['pm_icon_delete'] = sed_rc_link(sed_url('pm', 'a=delete&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f), $R['pm_icon_trashcan'], array('title' => $L['Delete']));
	$pm_fromuserid = $row['pm_fromuserid'];
	$pm_fromuser = htmlspecialchars($row['pm_fromuser']);
	$pm_touserid = $usr['id'];
	$pm_touser = htmlspecialchars($usr['name']);
	$pm_fromortouser = sed_build_user($pm_fromuserid, $pm_fromuser);
	$to = $row['pm_fromuserid'];
}
elseif ($row['pm_fromuserid']==$usr['id'])
{
	$f = 'sentbox';
	$title .= ' '.sed_rc_link(sed_url('pm', 'f=sentbox'), $L['pm_sentbox']);
	$pm_fromuserid = $usr['id'];
	$pm_fromuser = htmlspecialchars($usr['name']);
	$pm_touserid = $row['pm_touserid'];
	$pm_touser = htmlspecialchars($row['user_name']);
	$pm_fromortouser = sed_build_user($pm_touserid, $pm_touser);
	$row['pm_icon_edit'] = ($row['pm_tostate']==0) ? sed_rc_link(sed_url('pm', 'm=send&id='.$row['pm_id']), $R['pm_icon_edit'], array('title' => $L['Edit'])) : '';
	$row['pm_icon_delete'] .= ($row['pm_tostate']>0) ? ' ' . sed_rc_link(sed_url('pm', 'm=edit&a=delete&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f), $R['pm_icon_trashcan'], array('title' => $L['Delete'])) : '';
	$to = $row['pm_touserid'];
}
else
{
	sed_die();
}

$title_tags[] = array('{PM}', '{INBOX}', '{ARCHIVES}', '{SENTBOX}');
$title_tags[] = array('%1$s', '%2$s', '%3$s', '%4$s');
$title_data = array($L['Private_Messages'], $totalinbox, $totalarchives, $totalsentbox);
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
	$pm_data = sed_post_parse($row['pm_html']);
}
else
{
	$pm_data = sed_parse(htmlspecialchars($row['pm_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1);
	$pm_data = sed_post_parse($pm_data);
}

if (preg_match("/Re(\(\d+\))?\:(.+)/", $row['pm_title'], $matches))
{
	$matches[1] = empty($matches[1]) ? 2 : trim($matches[1], '()') + 1;
	$newpmtitle = 'Re(' . $matches[1] . '): ' . trim($matches[2]);
}
else
{
	$newpmtitle = 'Re: ' . $row['pm_title'];
}

if(!empty($q))
{
	$newpmtext= '[quote]'.htmlspecialchars($row['pm_text']).'[/quote]';
}

// TODO history with person

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('pm'));

if($history)
{
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE (pm_fromuserid='".$usr['id']."' AND pm_touserid='".$to."' AND pm_fromstate<>3)
						OR (pm_fromuserid='".$to."' AND pm_touserid='".$usr['id']."' AND pm_tostate<>3)");
	$totallines = sed_sql_result($sql, 0, "COUNT(*)");
	$sql = sed_sql_query("SELECT *, u.user_name FROM $db_pm AS p LEFT JOIN $db_users AS u ON u.user_id=p.pm_touserid
						WHERE (pm_fromuserid='".$usr['id']."' AND pm_touserid='".$to."' AND pm_fromstate<>3)
						OR (pm_fromuserid='".$to."' AND pm_touserid='".$usr['id']."' AND pm_tostate<>3)
						ORDER BY pm_date DESC LIMIT $d,".$cfg['maxpmperpage']);
	$jj=0;
	$pm_totalpages = ceil($totallines / $cfg['maxpmperpage']);
	$pm_currentpage = ceil ($d / $cfg['maxpmperpage'])+1;
	$pagenav = sed_pagenav('pm', 'm=message&id='.$id.'&history='.$history.'&q='.$q, $d, $totallines, $cfg['maxpmperpage']);
	$d=($d>$totallines) ? (floor($totallines / $cfg['maxpmperpage']))*$cfg['maxpmperpage'] : $d;

	/* === Hook - Part1 : Set === */
	$extp = sed_getextplugins('pm.history.loop');
	/* ===== */

	while ($row2 = sed_sql_fetcharray($sql))
	{
		$jj++;
		$row2['pm_icon_status'] = ($row2['pm_tostate']=='0' && $f!='sentbox') ? sed_rc_link(sed_url('pm', 'm=message&id='.$row2['pm_id']), $R['pm_icon_new']) : sed_rc_link(sed_url('pm', 'm=message&id='.$row2['pm_id']), $R['pm_icon']);
		$pm_fromuserid = $row2['pm_fromuserid'];

		if ($row2['pm_fromuserid']==$usr['id'])
		{
			$row2['pm_icon_delete'] = sed_rc_link(sed_url('pm', 'a=delete&'.sed_xg().'&id='.$row2['pm_id'].'&f='.$f.'&d='.$d), $R['pm_icon_trashcan'], array('title' => $L['Delete']));
			$row2['pm_icon_edit'] = ($row2['pm_tostate']==0) ? sed_rc_link(sed_url('pm', 'm=send&id='.$row2['pm_id']), $R['pm_icon_edit'], array('title' => $L['Edit'])) : '';
		}
		elseif($row2['pm_tostate']==2)
		{
			$row2['pm_icon_archive'] = sed_rc_link(sed_url('pm', 'a=archive&'.sed_xg().'&id='.$row2['pm_id'].'&f='.$f.'&d='.$d), $R['pm_icon_archive'], array('title' => $L['pm_deletefromarchives']));
			$row2['pm_icon_delete'] = sed_rc_link(sed_url('pm', 'a=delete&'.sed_xg().'&id='.$row2['pm_id'].'&f='.$f.'&d='.$d), $R['pm_icon_trashcan'], array('title' => $L['Delete']));
		}
		else
		{
			$row2['pm_icon_archive'] = sed_rc_link(sed_url('pm', 'a=archive&'.sed_xg().'&id='.$row2['pm_id'].'&d='.$d), $R['pm_icon_archive'], array('title' => $L['pm_putinarchives']));
			$row2['pm_icon_delete'] .= ($row2['pm_tostate']>0) ? ' ' . sed_rc_link(sed_url('pm', 'm=edit&a=delete&'.sed_xg().'&id='.$row2['pm_id'].'&f='.$f.'&d='.$d), $R['pm_icon_trashcan'], array('title' => $L['Delete'])) : '';
		}

		if($cfg['parser_cache'])
		{
			if(empty($row2['pm_html']) && !empty($row2['pm_text']))
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


		$t-> assign(array(
			"PM_ROW_ID" => $row2['pm_id'],
			"PM_ROW_STATE" => $row2['pm_tostate'],
			"PM_ROW_DATE" => @date($cfg['dateformat'], $row2['pm_date'] + $usr['timezone'] * 3600),
			"PM_ROW_FROMUSERID" => $pm_fromuserid,
			"PM_ROW_FROMUSER" => sed_build_user($row2['pm_fromuserid'], $row2['pm_fromuser']),
			"PM_ROW_TITLE" => sed_rc_link(sed_url('pm', 'm=message&id='.$row2['pm_id']), htmlspecialchars($row2['pm_title'])),
			"PM_ROW_TEXT" => $pm_data,
			"PM_ROW_ICON_STATUS" => $row2['pm_icon_status'],
			"PM_ROW_ICON_ARCHIVE" => $row2['pm_icon_archive'],
			"PM_ROW_ICON_DELETE" => $row2['pm_icon_delete'],
			"PM_ROW_ICON_EDIT" => $row2['pm_icon_edit'],
			"PM_ROW_DESC" => sed_cutpost($pm_data, 100, false),
			"PM_ROW_ODDEVEN" => sed_build_oddeven($jj),
			"PM_ROW_NUM" => $jj,
		));

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse("MAIN.HISTORY.PM_ROW");
	}

	if ($jj==0)
	{
		$t->parse("MAIN.HISTORY.PM_ROW_EMPTY");
	}
	$t-> assign(array(
	"PM_PAGEPREV" => $pagenav['prev'],
	"PM_PAGENEXT" => $pagenav['next'],
	'PM_PAGES' => $pagenav['main'],
	));
	$t->parse("MAIN.HISTORY");
}


if ($usr['auth_write'])
{
	$t-> assign(array(
		"PM_QUOTE" => sed_rc_link(sed_url('pm', 'm=message&id='.$id.'&q=quote&history='.$history.'&d='.$d), $L[Quote]),
		"PM_FORM_SEND" => sed_url('pm', 'm=send&a=send&to='.$to),
		"PM_FORM_TITLE" => htmlspecialchars($newpmtitle),
		"PM_FORM_TEXT" => $newpmtext,
		"PM_FORM_PFS" => $pfs,
	));
	$t->parse("MAIN.REPLY");
}

$t-> assign(array(
	"PM_PAGETITLE" => $title.' '.$cfg['separator'].' '.$pm_fromortouser.' '.$cfg['separator'].' '.sed_rc_link(sed_url('pm', 'id='.$id),htmlspecialchars($row['pm_title'])),
	"PM_SENDNEWPM" => ($usr['auth_write']) ? sed_rc_link(sed_url('pm', 'm=send'), $L['pm_sendnew']) : '',
	"PM_INBOX" => sed_rc_link(sed_url('pm'), $L['pm_inbox'].': '.$totalinbox),
	"PM_ARCHIVES" => sed_rc_link(sed_url('pm', 'f=archives'), $L['pm_archives'].': '.$totalarchives),
	"PM_SENTBOX" => sed_rc_link(sed_url('pm', 'f=sentbox'), $L['pm_sentbox'].': '.$totalsentbox),
	"PM_ID" => $row['pm_id'],
	"PM_STATE" => $row['pm_tostate'],
	"PM_DATE" => @date($cfg['dateformat'], $row['pm_date'] + $usr['timezone'] * 3600),
	"PM_FROMUSERID" => $pm_fromuserid,
	"PM_FROMUSER" => sed_build_user($pm_fromuserid, $pm_fromuser),
	"PM_TOUSERID" => $pm_touserid,
	"PM_TOUSER" => sed_build_user($pm_touserid, $pm_touser),
	"PM_TITLE" => sed_rc_link(sed_url('pm', 'id='.$row['pm_id']), htmlspecialchars($row['pm_title'])),
	"PM_TEXT" => $pm_data,
	"PM_FROMORTOUSER" => $pm_fromortouser,
	"PM_ICON_ARCHIVE" => $row['pm_icon_archive'],
	"PM_ICON_DELETE" => $row['pm_icon_delete'],
	"PM_ICON_EDIT" => $row['pm_icon_edit'],
	"PM_HISTORY" => sed_rc_link(sed_url('pm', 'm=message&id='.$id.'&q='.$q.'&history=1&d='.$d), $L['pm_messagehistory']),

));

/* === Hook === */
$extp = sed_getextplugins('pm.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>