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

$f = sed_import('f','G','ALP');			// Category^ inbox, sentbox, archive
$d = sed_import('d','G','INT');			// Page number
$d = empty($d) ? 0 : (int) $d;
$a = sed_import('a','G','TXT');			// Action



/*
 * PM States
 * 0 - new message
 * 1 - inbox message
 * 2 - archived message
 * 3 - deleted message
*/

/* === Hook === */
$extp = sed_getextplugins('pm.list.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if(!empty($a))
{
	$msg = array();
	sed_check_xg();
	$id = sed_import('id','G','INT');		// Message id
	if (!empty($id))
	{
		$msg[] = $id;
	}
	else
	{
		$msg = sed_import('msg', 'P', 'ARR');
		$a=(sed_import('delete', 'P', 'TXT')) ? 'delete' : 'archive';
		foreach($msg as $k => $v)
		{
			$msgt[] = sed_import($k, 'D', 'INT');
		}
		unset($msg);
		$msg=$msgt;
	}
	if (count($msg)>0)
	{
		$msg= '('.implode(',', $msg).')';
		$sql = sed_sql_query("SELECT * FROM $db_pm WHERE pm_id IN $msg");
		while($row = sed_sql_fetcharray($sql))
		{
			$id=$row['pm_id'];
			if ($a=='delete')
			{
				if (($row['pm_fromuserid']==$usr['id'] && ($row['pm_tostate']==3 || $row['pm_tostate']==0)) ||
					($row['pm_touserid']==$usr['id'] && $row['pm_fromstate']==3) ||
					($row['pm_fromuserid']==$usr['id'] && $row['pm_touserid']==$usr['id']))
				{
					if ($cfg['trash_pm'])
					{
						sed_trash_put('pm', $L['Private_Messages']." #".$id." ".$row['pm_title']." (".$row['pm_fromuser'].")", $id, $row);
					}
					$sql2 = sed_sql_query("DELETE FROM $db_pm WHERE pm_id='$id'");
				}
				elseif($row['pm_fromuserid']==$usr['id'] && ($row['pm_tostate']!=3 || $row['pm_tostate']!=0))
				{
					$sql2 = sed_sql_query("UPDATE $db_pm SET pm_fromstate=3 WHERE pm_id='$id'");
				}
				elseif($row['pm_touserid']==$usr['id'] && $row['pm_fromstate']!=3)
				{
					$sql2 = sed_sql_query("UPDATE $db_pm SET pm_tostate=3 WHERE pm_id='$id'");
				}
			}
			else
			{
				if ($row['pm_touserid']==$usr['id'])
				{
					echo "Удалено у получателя";
					$tostate=($row['pm_tostate']==2) ?  1 : 2;
					$sql2 = sed_sql_query("UPDATE $db_pm SET pm_tostate=".(int)$tostate." WHERE pm_id='$id'");
				}
			}

		}
	}
}
/* == Reading Messeges Count == */
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_tostate=2");
$totalarchives = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_fromuserid='".$usr['id']."' AND pm_fromstate <> '3'");
$totalsentbox = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_tostate<2");
$totalinbox = sed_sql_result($sql, 0, "COUNT(*)");
/* == Reading Messeges Count == */

$bhome = $cfg['homebreadcrumb'] ? sed_rc_link($cfg['mainurl'], htmlspecialchars($cfg['maintitle'])).$cfg['separator'].' ' : '';
$title = $bhome . sed_rc_link(sed_url('pm'), $L['Private_Messages']).' '.$cfg['separator'];

if ($f=='archives')
{
	$totallines = $totalarchives;
	$d=($d>$totallines) ? (floor($totallines / $cfg['maxpmperpage']))*$cfg['maxpmperpage'] : $d;
	$sql = sed_sql_query("SELECT * FROM $db_pm
        WHERE pm_touserid='".$usr['id']."' AND pm_tostate=2
        ORDER BY pm_date DESC LIMIT $d,".$cfg['maxpmperpage']);
	$title .= ' '.sed_rc_link(sed_url('pm', 'f=archives'), $L['pm_archives']);
	$subtitle = $L['pm_arcsubtitle'];
	$archive = ($totallines) ? '<input type="submit" name="archive" value="'.$L['pm_deletefromarchives'].'" />' : '';
}

elseif ($f=='sentbox')
{
	$totallines = $totalsentbox;
	$d=($d>$totallines) ? (floor($totallines / $cfg['maxpmperpage']))*$cfg['maxpmperpage'] : $d;
	$sql = sed_sql_query("SELECT p.*, u.user_name FROM $db_pm p, $db_users u
        WHERE p.pm_fromuserid='".$usr['id']."' AND u.user_id=p.pm_touserid  AND pm_fromstate<>3
        ORDER BY pm_date DESC LIMIT $d,".$cfg['maxpmperpage']);
	$title .= ' '.sed_rc_link(sed_url('pm', 'f=sentbox'), $L['pm_sentbox']);
	$subtitle = $L['pm_sentboxsubtitle'];
}

else
{
	$f = 'inbox';
	$totallines = $totalinbox;
	$d=($d>$totallines) ? (floor($totallines / $cfg['maxpmperpage']))*$cfg['maxpmperpage'] : $d;
	$sql = sed_sql_query("SELECT * FROM $db_pm
        WHERE pm_touserid='".$usr['id']."' AND pm_tostate < 2
        ORDER BY pm_date DESC LIMIT  $d,".$cfg['maxpmperpage']);
	$title .= ' '.sed_rc_link(sed_url('pm'),$L['pm_inbox']);
	$subtitle = $L['pm_inboxsubtitle'];
	$archive = ($totallines) ? '<input type="submit" name="archive" value="'.$L['pm_putinarchives'].'" />' : '';
}
$delete = ($totallines) ? '<input type="submit" name="delete" value="'.$L['Delete'].'" />' : '';

$pm_totalpages = ceil($totallines / $cfg['maxpmperpage']);
$pm_currentpage = ceil ($d / $cfg['maxpmperpage'])+1;

$pagenav = sed_pagenav('pm', "f=$f", $d, $totallines, $cfg['maxpmperpage']);

$title_tags[] = array('{PM}', '{INBOX}', '{ARCHIVES}', '{SENTBOX}');
$title_tags[] = array('%1$s', '%2$s', '%3$s', '%4$s');
$title_data = array($L['Private_Messages'], $totalinbox, $totalarchives, $totalsentbox);
$out['subtitle'] = sed_title('title_pm_main', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('pm.list.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('pm.list'));

$jj=0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('pm.list.loop');
/* ===== */

while ($row = sed_sql_fetcharray($sql))
{
	$jj++;
	$row['pm_icon_status'] = ($row['pm_tostate']=='0' && $f!='sentbox') ? sed_rc_link(sed_url('pm', 'm=message&id='.$row['pm_id']), $R['pm_icon_new']) : sed_rc_link(sed_url('pm', 'm=message&id='.$row['pm_id']), $R['pm_icon']);

	if ($f=='sentbox')
	{
		$pm_fromuserid = $usr['id'];
		$pm_fromuser = htmlspecialchars($usr['name']);
		$pm_touserid = $row['pm_touserid'];
		$pm_touser = htmlspecialchars($row['user_name']);
		$pm_fromortouser = sed_build_user($pm_touserid, $pm_touser);
		$row['pm_icon_delete'] = sed_rc_link(sed_url('pm', 'a=delete&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f.'&d='.$d), $R['pm_icon_trashcan'], array('title' => $L['Delete']));
		$row['pm_icon_edit'] = ($row['pm_tostate']==0) ? sed_rc_link(sed_url('pm', 'm=send&id='.$row['pm_id']), $R['pm_icon_edit'], array('title' => $L['Edit'])) : '';

	}
	elseif ($f=='archives')
	{
		$pm_fromuserid = $row['pm_fromuserid'];
		$pm_fromuser = htmlspecialchars($row['pm_fromuser']);
		$pm_touserid = $usr['id'];
		$pm_touser = htmlspecialchars($usr['name']);
		$pm_fromortouser = sed_build_user($pm_fromuserid, $pm_fromuser);
		$row['pm_icon_archive'] = sed_rc_link(sed_url('pm', 'a=archive&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f.'&d='.$d), $R['pm_icon_archive'], array('title' => $L['pm_deletefromarchives']));
		$row['pm_icon_delete'] = sed_rc_link(sed_url('pm', 'a=delete&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f.'&d='.$d), $R['pm_icon_trashcan'], array('title' => $L['Delete']));
	}
	else
	{
		$pm_fromuserid = $row['pm_fromuserid'];
		$pm_fromuser = htmlspecialchars($row['pm_fromuser']);
		$pm_touserid = $usr['id'];
		$pm_touser = htmlspecialchars($usr['name']);
		$pm_fromortouser = sed_build_user($pm_fromuserid, $pm_fromuser);
		$row['pm_icon_archive'] = sed_rc_link(sed_url('pm', 'a=archive&'.sed_xg().'&id='.$row['pm_id'].'&d='.$d), $R['pm_icon_archive'], array('title' => $L['pm_putinarchives']));
		$row['pm_icon_delete'] .= ($row['pm_tostate']>0) ? ' ' . sed_rc_link(sed_url('pm', 'm=edit&a=delete&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f.'&d='.$d), $R['pm_icon_trashcan'], array('title' => $L['Delete'])) : '';
	}

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

	$t-> assign(array(
		"PM_ROW_ID" => $row['pm_id'],
		"PM_ROW_STATE" => $row['pm_tostate'],
		"PM_ROW_DATE" => @date($cfg['dateformat'], $row['pm_date'] + $usr['timezone'] * 3600),
		"PM_ROW_FROMUSERID" => $pm_fromuserid,
		"PM_ROW_FROMUSER" => sed_build_user($pm_fromuserid, $pm_fromuser),
		"PM_ROW_TOUSERID" => $pm_touserid,
		"PM_ROW_TOUSER" => sed_build_user($pm_touserid, $pm_touser),
		"PM_ROW_TITLE" => sed_rc_link(sed_url('pm', 'm=message&id='.$row['pm_id']), htmlspecialchars($row['pm_title'])),
		"PM_ROW_TEXT" => $pm_data,
		"PM_ROW_FROMORTOUSER" => $pm_fromortouser,
		"PM_ROW_ICON_STATUS" => $row['pm_icon_status'],
		"PM_ROW_ICON_ARCHIVE" => $row['pm_icon_archive'],
		"PM_ROW_ICON_DELETE" => $row['pm_icon_delete'],
		"PM_ROW_ICON_EDIT" => $row['pm_icon_edit'],
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

	$t->parse("MAIN.PM_ROW");


}

if ($jj==0)
{ $t->parse("MAIN.PM_ROW_EMPTY"); }

$t-> assign(array(
	"PM_PAGETITLE" => $title,
	"PM_SUBTITLE" => $subtitle,
	"PM_FORM_UPDATE" => sed_url('pm', 'a=op&'.sed_xg().'&f='.$f.'&d='.$d),
	"PM_SENDNEWPM" => ($usr['auth_write']) ? sed_rc_link(sed_url('pm', 'm=send'), $L['pm_sendnew']) : '',
	"PM_INBOX" => sed_rc_link(sed_url('pm'), $L['pm_inbox'].': '.$totalinbox),
	"PM_ARCHIVES" => sed_rc_link(sed_url('pm', 'f=archives'), $L['pm_archives'].': '.$totalarchives),
	"PM_SENTBOX" => sed_rc_link(sed_url('pm', 'f=sentbox'), $L['pm_sentbox'].': '.$totalsentbox),
	"PM_DELETE" => $delete,
	"PM_ARCHIVE" => $archive,
	"PM_PAGEPREV" => $pagenav['prev'],
	"PM_PAGENEXT" => $pagenav['next'],
	'PM_PAGES' => $pagenav['main'],
	"PM_CURRENTPAGE" => $pm_currentpage,
	"PM_TOTALPAGES" => ($pm_totalpages== 0 )? "1" : $pm_totalpages,
	"PM_SENT_TYPE" => ($f=='sentbox') ? $L['Recipient'] : $L['Sender'],
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