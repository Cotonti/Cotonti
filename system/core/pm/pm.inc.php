<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=pm.inc.php
Version=120
Updated=2007-jan-16
Type=Core
Author=Neocrome
Description=Private messages
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pm', 'a');
sed_block($usr['auth_read']);

$id = sed_import('id','G','INT');
$f = sed_import('f','G','ALP');
$to = sed_import('to','G','TXT');
$q = sed_import('q','G','INT');
$d = sed_import('d','G','INT');

unset ($touser, $pm_editbox);
$totalrecipients = 0;
$touser_all =array();
$touser_sql = array();
$touser_ids = array();
$touser_names = array();

/* === Hook === */
$extp = sed_getextplugins('pm.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state=2");
$totalarchives = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_fromuserid='".$usr['id']."' AND (pm_state=0 OR pm_state=3)");
$totalsentbox = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state<2");
$totalinbox = sed_sql_result($sql, 0, "COUNT(*)");

if (empty($d)) { $d = '0'; }
unset($pageprev, $pagenext);

if (!empty($id)) // -------------- Single mode
{
	unset($mode);
	$sql1 = sed_sql_query("SELECT * FROM $db_pm WHERE pm_id='".$id."'");
	sed_die(sed_sql_numrows($sql1)==0);
	$row1 = sed_sql_fetcharray($sql1);

	$title = "<a href=\"".sed_url('pm')."\">".$L['Private_Messages']."</a> ".$cfg['separator'];

	if ($row1['pm_touserid']==$usr['id'] && $row1['pm_state']==2)
	{
		$f = 'archives';
		$title .= " <a href=\"".sed_url('pm', 'f=archives')."\">".$L['pm_archives']."</a>";
		$subtitle = '';
	}
	elseif ($row1['pm_touserid']==$usr['id'] && $row1['pm_state']<2)
	{
		$f = 'inbox';
		$title .= " <a href=\"".sed_url('pm', 'f=inbox')."\">".$L['pm_inbox']."</a>";
		$subtitle = '';

		if ($row1['pm_state']==0)
		{
			$sql1 = sed_sql_query("UPDATE $db_pm SET pm_state=1 WHERE pm_touserid='".$usr['id']."' AND pm_id='".$id."'");
			$sql1 = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state=0");
			$notread = sed_sql_result($sql1,0,'COUNT(*)');
			if ($notread==0)
			{ $sql = sed_sql_query("UPDATE $db_users SET user_newpm=0 WHERE user_id='".$usr['id']."'"); }
			// Leave a copy in sentbox
			$row1['pm_fromuser'] = sed_sql_prep($row1['pm_fromuser']);
			$row1['pm_title'] = sed_sql_prep($row1['pm_title']);
			$row1['pm_text'] = sed_sql_prep($row1['pm_text']);
			sed_sql_query("INSERT INTO $db_pm (pm_state, pm_date, pm_fromuserid, pm_fromuser, pm_touserid, pm_title, pm_text)
				VALUES(3, {$row1['pm_date']}, {$row1['pm_fromuserid']}, '{$row1['pm_fromuser']}', {$row1['pm_touserid']}, '{$row1['pm_title']}', '{$row1['pm_text']}')");
		}
	}
	elseif ($row1['pm_fromuserid']==$usr['id'] && ($row1['pm_state']==0 || $row1['pm_state']==3))
	{
		$f = 'sentbox';
		$title .= " <a href=\"".sed_url('pm', 'f=sentbox')."\">".$L['pm_sentbox']."</a>";
		$subtitle = '';
	}
	else
	{
		sed_die();
	}

	$title .= ' '.$cfg['separator']." <a href=\"".sed_url('pm', 'id='.$id)."\">#".$id."</a>";
	$sql = sed_sql_query("SELECT *, u.user_name FROM $db_pm AS p LEFT JOIN $db_users AS u ON u.user_id=p.pm_touserid WHERE pm_id='".$id."'");
}

else // --------------- List mode

{
	unset($id);

	$bhome = $cfg['homebreadcrumb'] ? '<a href="'.$cfg['mainurl'].'">'.sed_cc($cfg['maintitle']).'</a> '.$cfg['separator'].' ' : '';

	$title = $bhome . "<a href=\"".sed_url('pm')."\">".$L['Private_Messages']."</a> ".$cfg['separator'];

	if ($f=='archives')
	{
		$totallines = $totalarchives;
		$sql = sed_sql_query("SELECT * FROM $db_pm
		WHERE pm_touserid='".$usr['id']."' AND pm_state=2
		ORDER BY pm_date DESC LIMIT $d,".$cfg['maxrowsperpage']);
		$title .= " <a href=\"".sed_url('pm', 'f=archives')."\">".$L['pm_archives']."</a>";
		$subtitle = $L['pm_arcsubtitle'];
		$delete = "<input type=\"submit\" name=\"delete\" value=\"".$L['Delete']."\" />";
	}
	elseif ($f=='sentbox')
	{
		$totallines = $totalsentbox;
		$sql = sed_sql_query("SELECT p.*, u.user_name FROM $db_pm p, $db_users u
		WHERE p.pm_fromuserid='".$usr['id']."' AND (p.pm_state=0 OR p.pm_state=3) AND u.user_id=p.pm_touserid
		ORDER BY pm_date DESC LIMIT $d,".$cfg['maxrowsperpage']);
		$title .= " <a href=\"".sed_url('pm', 'f=sentbox')."\">".$L['pm_sentbox']."</a>";
		$subtitle = $L['pm_sentboxsubtitle'];
		$delete = "<input type=\"submit\" name=\"delete\" value=\"".$L['Delete']."\" />";
	}
	else
	{
		$f = 'inbox';
		$totallines = $totalinbox;
		$sql = sed_sql_query("SELECT * FROM $db_pm
		WHERE pm_touserid='".$usr['id']."' AND pm_state<2
		ORDER BY pm_date DESC LIMIT  $d,".$cfg['maxrowsperpage']);
		$title .= " <a href=\"".sed_url('pm')."\">".$L['pm_inbox']."</a>";
		$subtitle = $L['pm_inboxsubtitle'];
		$delete = "<input type=\"submit\" name=\"delete\" value=\"".$L['Delete']."\" />";
		$archive = "<input type=\"submit\" name=\"move\" value=\"".$L['pm_putinarchives']."\" />";
	}

	$pm_totalpages = ceil($totallines / $cfg['maxrowsperpage']);
	$pm_currentpage = ceil ($d / $cfg['maxrowsperpage'])+1;

	$pm_pagination = sed_pagination(sed_url('pm', "f=$f"), $d, $totallines, $cfg['maxrowsperpage'], 'd');
	list($pm_pageprev, $pm_pagenext) = sed_pagination_pn(sed_url('pm', "f=$f"), $d, $totallines, $cfg['maxrowsperpage'], TRUE, 'd');
}

$title_tags[] = array('{PM}', '{INBOX}', '{ARCHIVES}', '{SENTBOX}');
$title_tags[] = array('%1$s', '%2$s', '%3$s', '%4$s');
$title_data = array($L['Private_Messages'], $totalinbox, $totalarchives, $totalsentbox);
$out['subtitle'] = sed_title('title_pm_main', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('pm.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$pm_sendlink = ($usr['auth_write']) ? "<a href=\"".sed_url('pm', 'm=send')."\">".$L['pm_sendnew']."</a>" : '';

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('pm'));

if ($pm_totalpages=='0') {$pm_totalpages = '1'; }

$t-> assign(array(
	"PM_PAGETITLE" => $title,
	"PM_SUBTITLE" => $subtitle,
	"PM_FORM_UPDATE" => sed_url('pm', "m=edit&a=op&".sed_xg()."&f=".$f),
	"PM_SENDNEWPM" => $pm_sendlink,
	"PM_INBOX" => "<a href=\"".sed_url('pm')."\">".$L['pm_inbox']."</a>:".$totalinbox,
	"PM_ARCHIVES" => "<a href=\"".sed_url('pm', 'f=archives')."\">".$L['pm_archives']."</a>:".$totalarchives,
	"PM_SENTBOX" => "<a href=\"".sed_url('pm', 'f=sentbox')."\">".$L['pm_sentbox']."</a>:".$totalsentbox,
	"PM_DELETE" => $delete,
	"PM_ARCHIVE" => $archive,
	"PM_TOP_PAGEPREV" => $pm_pageprev,
	"PM_TOP_PAGENEXT" => $pm_pagenext,
	'PM_TOP_PAGES' => $pm_pagination,
	"PM_TOP_CURRENTPAGE" => $pm_currentpage,
	"PM_TOP_TOTALPAGES" => $pm_totalpages,
));

$jj=0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('pm.loop');
/* ===== */

while ($row = sed_sql_fetcharray($sql) and ($jj<$cfg['maxrowsperpage']))
{
	$jj++;
	$row['pm_icon_status'] = ($row['pm_state']=='0' && $f!='sentbox') ? "<a href=\"".sed_url('pm', 'id='.$row['pm_id'])."\"><img src=\"skins/".$skin."/img/system/icon-pm-new.gif\" alt=\"\" /></a>" : "<a href=\"".sed_url('pm', 'id='.$row['pm_id'])."\"><img src=\"skins/".$skin."/img/system/icon-pm.gif\" alt=\"\" /></a>";

	if ($f=='sentbox')
	{
		$pm_fromuserid = $usr['id'];
		$pm_fromuser = sed_cc($usr['name']);
		$pm_touserid = $row['pm_touserid'];
		$pm_touser = sed_cc($row['user_name']);
		$pm_fromortouser = sed_build_user($pm_touserid, $pm_touser);
		$row['pm_icon_action'] = "<a href=\"".sed_url('pm', "m=edit&a=delete&".sed_xg()."&id=".$row['pm_id']."&f=".$f)."\" title=\"".$L['Delete']."\"><img src=\"skins/".$skin."/img/system/icon-pm-trashcan.gif\" alt=\"".$L['Delete']."\" /></a>";

		if (!empty($id) && $row['pm_state'] == 0)
		{
			$pm_editbox = "<h4>".$L['Edit']." :</h4>";
			$pm_editbox .= "<form id=\"newlink\" action=\"".sed_url('pm', "m=edit&a=update&".sed_xg()."&id=".$id)."\" method=\"post\">";
			$pm_editbox .= "<textarea class=\"editor\" name=\"newpmtext\" rows=\"8\" cols=\"56\">".sed_cc($row['pm_text'])."</textarea>";
			$pm_editbox .= "<br />&nbsp;<br /><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></form>";
		}
	}
	elseif ($f=='archives')
	{
		$pm_fromuserid = $row['pm_fromuserid'];
		$pm_fromuser = sed_cc($row['pm_fromuser']);
		$pm_touserid = $usr['id'];
		$pm_touser = sed_cc($usr['name']);
		$pm_fromortouser = sed_build_user($pm_fromuserid, $pm_fromuser);
		$row['pm_icon_action'] = "<a href=\"".sed_url('pm', "m=send&to=".$row['pm_fromuserid']."&q=".$row['pm_id'])."\" title=\"".$L['pm_replyto']."\"><img src=\"skins/".$skin."/img/system/icon-pm-reply.gif\" alt=\"".$L['pm_replyto']."\" /></a> <a href=\"".sed_url('pm', "m=edit&a=delete&".sed_xg()."&id=".$row['pm_id']."&f=".$f)."\" title=\"".$L['Delete']."\"><img src=\"skins/".$skin."/img/system/icon-pm-trashcan.gif\" alt=\"".$L['Delete']."\" /></a>";
	}
	else
	{
		$pm_fromuserid = $row['pm_fromuserid'];
		$pm_fromuser = sed_cc($row['pm_fromuser']);
		$pm_touserid = $usr['id'];
		$pm_touser = sed_cc($usr['name']);
		$pm_fromortouser = sed_build_user($pm_fromuserid, $pm_fromuser);
		$row['pm_icon_action'] = "<a href=\"".sed_url('pm', "m=send&to=".$row['pm_fromuserid']."&q=".$row['pm_id'])."\" title=\"".$L['pm_replyto']."\"><img src=\"skins/".$skin."/img/system/icon-pm-reply.gif\" alt=\"".$L['pm_replyto']."\" /></a> <a href=\"".sed_url('pm', "m=edit&a=archive&".sed_xg()."&id=".$row['pm_id'])."\" title=\"".$L['pm_putinarchives']."\"><img src=\"skins/".$skin."/img/system/icon-pm-archive.gif\" alt=\"".$L['pm_putinarchives']."\" /></a>";
		$row['pm_icon_action'] .= ($row['pm_state']>0) ? " <a href=\"".sed_url('pm', "m=edit&a=delete&".sed_xg()."&id=".$row['pm_id']."&f=".$f)."\" title=\"".$L['Delete']."\"><img src=\"skins/".$skin."/img/system/icon-pm-trashcan.gif\" alt=\"".$L['Delete']."\" /></a>" : '';
	}

	if($cfg['parser_cache'])
	{
		if(empty($row['pm_html']) && !empty($row['pm_text']))
		{
			$row['pm_html'] = sed_parse(sed_cc($row['pm_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1);
			sed_sql_query("UPDATE $db_pm SET pm_html = '".sed_sql_prep($row['pm_html'])."' WHERE pm_id = " . $row['pm_id']);
		}
		$pm_data = sed_post_parse($row['pm_html']);
	}
	else
	{
		$pm_data = sed_parse(sed_cc($row['pm_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1);
		$pm_data = sed_post_parse($pm_data);
	}

	$t-> assign(array(
		"PM_ROW_ID" => $row['pm_id'],
		"PM_ROW_STATE" => $row['pm_state'],
		"PM_ROW_SELECT" => "<input type=\"checkbox\" class=\"checkbox\"  name=\"msg[".$row['pm_id']."]\" />",
		"PM_ROW_DATE" => @date($cfg['dateformat'], $row['pm_date'] + $usr['timezone'] * 3600),
		"PM_ROW_FROMUSERID" => $pm_fromuserid,
		"PM_ROW_FROMUSER" => sed_build_user($pm_fromuserid, $pm_fromuser),
		"PM_ROW_TOUSERID" => $pm_touserid,
		"PM_ROW_TOUSER" => sed_build_user($pm_touserid, $pm_touser),
		"PM_ROW_TITLE" => "<a href=\"".sed_url('pm', 'id='.$row['pm_id'])."\">".sed_cc($row['pm_title'])."</a>",
		"PM_ROW_TEXT" => $pm_data.$pm_editbox,
		"PM_ROW_TEXTBOXER" => $pm_data.$pm_editbox,
		"PM_ROW_FROMORTOUSER" => $pm_fromortouser,
		"PM_ROW_ICON_STATUS" => $row['pm_icon_status'],
		"PM_ROW_ICON_ACTION" => $row['pm_icon_action'],
		"PM_ROW_ODDEVEN" => sed_build_oddeven($jj)
	));

	/* === Hook - Part2 : Include === */
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	if (empty($id))
	{ $t->parse("MAIN.PM_ROW"); }
	else
	{ $t->parse("MAIN.PM_DETAILS"); }

}

if (empty($id))
{
	if ($f=='sentbox')
	{ $t->parse("MAIN.PM_TITLE_SENTBOX"); }
	else
	{ $t->parse("MAIN.PM_TITLE"); }

	if ($jj==0)
	{ $t->parse("MAIN.PM_ROW_EMPTY"); }

	$t->parse("MAIN.PM_FOOTER");
}

/* === Hook === */
$extp = sed_getextplugins('pm.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>