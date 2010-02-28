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

$f = sed_import('f','G','ALP');				// Category inbox, sentbox, archive
$d = sed_import('d','G','INT');				// Page number
$d = empty($d) ? 0 : (int) $d;
$a = sed_import('a','G','TXT');				// Action
$filter = sed_import('filter','G','TXT');	// filter

/*
 * PM States
 * 0 - new message
 * 1 - inbox message
 * 2 - starred message
 * 3 - deleted message
*/

/* === Hook === */
$extp = sed_getextplugins('pm.list.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if (!empty($a))
{
	$id = sed_import('id','G','INT');		// Message id
	if ((int)$id > 0)
	{
		$msg[$id] = $id;
	}
}
else
{
	$msg = sed_import('msg', 'P', 'ARR');
	$a = sed_import('action', 'P', 'TXT');
}
if (count($msg) > 0)
{
	if ($a == 'delete')
	{
		sed_check_xg();
		sed_remove_pm($msg);
	}
	elseif (!empty($a))
	{
		sed_star_pm($msg);
		if (SED_AJAX && (int)$id > 0)
		{
			die();
		}
	}
}

list($totalsentbox, $totalinbox) = sed_message_count($usr['id']);

$bhome = $cfg['homebreadcrumb'] ? sed_rc_link($cfg['mainurl'], htmlspecialchars($cfg['maintitle'])).$cfg['separator'].' ' : '';
$title = $bhome . sed_rc_link(sed_url('pm'), $L['Private_Messages']).' '.$cfg['separator'];

if ($f == 'sentbox')
{
	$sqlfilter = "pm_fromuserid = '".$usr['id']."' AND pm_fromstate <> 3";
	$title .= ' '.sed_rc_link(sed_url('pm', 'f=sentbox'), $L['pm_sentbox']);
	$subtitle = $L['pm_sentboxsubtitle'];
}
else
{
	$f = 'inbox';
	$sqlfilter = "pm_touserid = '".$usr['id']."' AND pm_tostate <> 3";
	$title .= ' '.sed_rc_link(sed_url('pm'),$L['pm_inbox']);
	$subtitle = $L['pm_inboxsubtitle'];
}

if ($filter == 'unread')
{
	$sqlfilter .= " AND pm_tostate = 0";
	$title .= ' ('.$L['pm_unread'].')';
}
elseif ($filter == 'starred')
{
	$sqlfilter .= ($f == 'sentbox') ? " AND pm_fromstate = 2" : " AND pm_tostate = 2";
	$title .= ' ('.$L['pm_starred'].')';
}

/* === Hook === */
$extp = sed_getextplugins('pm.list.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

/* === Title === */
$title_params = array(
	'PM' => $L['Private_Messages'],
	'INBOX' => $totalinbox,
	'SENTBOX' => $totalsentbox
);
$out['subtitle'] = sed_title('title_pm_main', $title_params);
$out['head'] .= $R['code_noindex'];

sed_online_update();
/* === Title === */

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE $sqlfilter");
$totallines = sed_sql_result($sql, 0, "COUNT(*)");
$d = ($d >= $totallines) ? (floor($totallines / $cfg['maxpmperpage'])) * $cfg['maxpmperpage'] : $d;
$elem = ($f == 'sentbox') ? 'pm_touserid' : 'pm_fromuserid';
$sql = sed_sql_query("SELECT p.*, u.* FROM $db_pm AS p
		LEFT JOIN $db_users AS u
		ON u.user_id = p.$elem
		WHERE $sqlfilter
		ORDER BY pm_date DESC LIMIT  $d,".$cfg['maxpmperpage']);

$totalpages = ceil($totallines / $cfg['maxpmperpage']);
$currentpage = ceil($d / $cfg['maxpmperpage'])+1;
$pagenav = sed_pagenav('pm', 'f='.$f.'&filter='.$filter, $d, $totallines, $cfg['maxpmperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

/* === Hook === */
$extp = sed_getextplugins('pm.list.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate(sed_skinfile('pm.list'));

$jj = 0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('pm.list.loop');
/* ===== */

while ($row = sed_sql_fetcharray($sql))
{
	$jj++;
	$row['pm_icon_readstatus'] = ($row['pm_tostate'] == '0') ?
			sed_rc_link(sed_url('pm', 'm=message&id='.$row['pm_id']), $R['pm_icon_new'], array('title' => $L['pm_unread'], 'class'=>'ajax'))
			: sed_rc_link(sed_url('pm', 'm=message&id='.$row['pm_id']), $R['pm_icon'], array('title' => $L['pm_read'], 'class'=>'ajax'));

	if ($cfg['parser_cache'])
	{
		if (empty($row['pm_html']) && !empty($row['pm_text']))
		{
			$row['pm_html'] = sed_parse(htmlspecialchars($row['pm_text']), $cfg['parsebbcodepm'], $cfg['parsesmiliespm'], 1);
			sed_sql_query("UPDATE $db_pm SET pm_html = '".sed_sql_prep($row['pm_html'])."' WHERE pm_id = " . $row['pm_id']);
		}
		$pm_data = sed_post_parse($row['pm_html']);
	}
	else
	{
		$pm_data = sed_parse(htmlspecialchars($row['pm_text']), $cfg['parsebbcodepm'], $cfg['parsesmiliespm'], 1);
		$pm_data = sed_post_parse($pm_data);
	}
	$pm_desc = $pm_data;
	$pm_desc .= (sed_string_truncate($pm_desc)) ? "..." : "";

	if ($f == 'sentbox')
	{
		$row['pm_icon_edit'] = ($row['pm_tostate'] == 0) ? sed_rc_link(sed_url('pm', 'm=send&id='.$row['pm_id']), $R['pm_icon_edit'], array('title' => $L['Edit'], 'class'=>'ajax')) : '';
		$star_title = ($row['pm_fromstate'] == 2) ? $L['pm_deletefromstarred'] : $L['pm_putinstarred'];
		$star_class = ($row['pm_fromstate'] == 2) ? 'star-rating star-rating-on' : 'star-rating';
	}
	else
	{
		$star_title = ($row['pm_tostate'] == 2) ? $L['pm_deletefromstarred'] : $L['pm_putinstarred'];
		$star_class = ($row['pm_tostate'] == 2) ? 'star-rating star-rating-on' : 'star-rating';
	}
	$pm_user = sed_generate_usertags($row, "PM_ROW_USER_");

	$row['pm_icon_starred'] = sed_rc_link(sed_url('pm', 'f='.$f.'&filter='.$filter.'&a=star&id='.$row['pm_id'].'&d='.$d),
			$R['pm_icon_archive'], array('title' => $star_title));
	$star = '<div class="'.$star_class.'">'.$row['pm_icon_starred'].'</div>';

	$t->assign(array(
		"PM_ROW_ID" => $row['pm_id'],
		"PM_ROW_STATE" => $row['pm_tostate'],
		"PM_ROW_STAR" => $star,
		"PM_ROW_DATE" => @date($cfg['dateformat'], $row['pm_date'] + $usr['timezone'] * 3600),
		"PM_ROW_TITLE" => sed_rc_link(sed_url('pm', 'm=message&id='.$row['pm_id']), htmlspecialchars($row['pm_title']), array('class'=>'ajax')),
		"PM_ROW_TEXT" => $pm_data,
		"PM_ROW_ICON_STATUS" => $row['pm_icon_readstatus'],
		"PM_ROW_ICON_STARRED" => $row['pm_icon_starred'],
		"PM_ROW_ICON_DELETE" => sed_rc_link(sed_url('pm', 'm=edit&a=delete&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f.'&d='.$d), $R['pm_icon_trashcan'], array('title' => $L['Delete'], 'class'=>'ajax')),
		"PM_ROW_ICON_EDIT" => $row['pm_icon_edit'],
		"PM_ROW_DESC" => $pm_desc,
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

	$t->parse("MAIN.PM_ROW");
}

if ($jj == 0)
{
	$t->parse("MAIN.PM_ROW_EMPTY");
}
if (!SED_AJAX)
{
	$t->parse("MAIN.BEFORE_AJAX");
	$t->parse("MAIN.AFTER_AJAX");
}

$t->assign(array(
	"PM_PAGETITLE" => $title,
	"PM_SUBTITLE" => $subtitle,
	"PM_FORM_UPDATE" => sed_url('pm', sed_xg().'&f='.$f.'&filter='.$filter.'&d='.$d),
	"PM_SENDNEWPM" => ($usr['auth_write']) ? sed_rc_link(sed_url('pm', 'm=send'), $L['pm_sendnew'], array('class'=>'ajax')) : '',
	"PM_INBOX" => sed_rc_link(sed_url('pm'), $L['pm_inbox'], array('class'=>'ajax')),
	"PM_INBOX_COUNT" => $totalinbox,
	"PM_SENTBOX" => sed_rc_link(sed_url('pm', 'f=sentbox'), $L['pm_sentbox'], array('class'=>'ajax')),
	"PM_SENTBOX_COUNT" => $totalsentbox,
	"PM_FILTER_ALL" => sed_rc_link(sed_url('pm', 'f='.$f), $L['pm_all'], array('class'=>'ajax')),
	"PM_FILTER_UNREAD" => sed_rc_link(sed_url('pm', 'f='.$f.'&filter=unread'), $L['pm_unread'], array('class'=>'ajax')),
	"PM_FILTER_STARRED" => sed_rc_link(sed_url('pm', 'f='.$f.'&filter=starred'), $L['pm_starred'], array('class'=>'ajax')),
	"PM_PAGEPREV" => $pagenav['prev'],
	"PM_PAGENEXT" => $pagenav['next'],
	'PM_PAGES' => $pagenav['main'],
	"PM_CURRENTPAGE" => $currentpage,
	"PM_TOTALPAGES" => ($totalpages == 0 )? "1" : $totalpages,
	"PM_SENT_TYPE" => ($f == 'sentbox') ? $L['Recipient'] : $L['Sender']
));

/* === Hook === */
$extp = sed_getextplugins('pm.list.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>