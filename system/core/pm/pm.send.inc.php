<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=pm.send.inc.php
Version=125
Updated=2008-mar-20
Type=Core
Author=Neocrome
Description=Private messages
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pm', 'a');
sed_block($usr['auth_write']);

$id = sed_import('id','G','INT');
$f = sed_import('f','G','ALP');
$to = sed_import('to','G','TXT');
$q = sed_import('q','G','INT');
$d = sed_import('d','G','INT');

unset ($touser);
$totalrecipients = 0;
$touser_all = array();
$touser_sql = array();
$touser_ids = array();
$touser_names = array();

/* === Hook === */
$extp = sed_getextplugins('pm.send.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state=2");
$totalarchives = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_fromuserid='".$usr['id']."' AND pm_state=0");
$totalsentbox = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state<2");
$totalinbox = sed_sql_result($sql, 0, "COUNT(*)");

if ($a=='send')
{
	/* === Hook === */
	$extp = sed_getextplugins('pm.send.send.first');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	sed_shield_protect();
	$newpmtitle = sed_import('newpmtitle','P','TXT');
	$newpmtext = sed_import('newpmtext','P','HTM');
	$newpmrecipient = sed_import('newpmrecipient','P','TXT');
	$userid = sed_import('userid','P','INT');
	$touser_src = explode (",", $newpmrecipient);
	$touser_req = count($touser_src);
	foreach($touser_src as $k => $i)
	{ $touser_sql[] = "'".sed_sql_prep(trim(sed_import($i, 'D', 'TXT')))."'";	}
	$touser_sql = implode (',', $touser_sql);
	$touser_sql = '('.$touser_sql.')';
	$sql = sed_sql_query("SELECT user_id, user_name FROM $db_users WHERE user_name IN $touser_sql");
	$totalrecipients = sed_sql_numrows($sql);
	while ($row = sed_sql_fetcharray($sql))
	{
		$touser_ids[] = $row['user_id'];
		$row['user_name'] = sed_cc($row['user_name']);
		$touser_names[] = $row['user_name'];
		$touser_usrlnk[] .= ($cfg['parsebbcodecom']) ? "[user=".$row['user_id']."]".$row['user_name']."[/user]" : $row['user_name'];
	}

	$touser = ($totalrecipients>0) ? implode (",", $touser_names) : '';
	$error_string .= (mb_strlen($newpmtitle)<2) ? $L['pm_titletooshort']."<br />" : '';
	$error_string .= (mb_strlen($newpmtext)<2) ? $L['pm_bodytooshort']."<br />" : '';
	$error_string .= (mb_strlen($newpmtext)>$cfg['pm_maxsize']) ? $L['pm_bodytoolong']."<br />" : '';
	$error_string .= ($totalrecipients<$touser_req ) ? $L['pm_wrongname']."<br />" : '';
	$error_string .= ($totalrecipients>10) ? sprintf($L['pm_toomanyrecipients'], 10)."<br />" : '';

	if (empty($error_string))
	{
		$newpmtext .= ($totalrecipients>1) ? "\n\n".sprintf($L['pm_multiplerecipients'], $totalrecipients-1)."\n".implode(', ', $touser_usrlnk)  : '';

		if($cfg['parser_cache'])
		{
			$newpmhtml = sed_sql_prep(sed_parse(sed_cc($newpmtext)));
		}
		else
		{
				$newpmhtml = '';
		}

		foreach($touser_ids as $k => $userid)
		{
			$sql = sed_sql_query("INSERT into $db_pm
			(pm_state,
			pm_date,
			pm_fromuserid,
			pm_fromuser,
			pm_touserid,
			pm_title,
			pm_text,
			pm_html)
			VALUES
			(0,
				".(int)$sys['now_offset'].",
				".(int)$usr['id'].",
				'".sed_sql_prep($usr['name'])."',
				".(int)$userid.",
				'".sed_sql_prep($newpmtitle)."',
				'".sed_sql_prep($newpmtext)."',
				'$newpmhtml')");

			$sql = sed_sql_query("UPDATE $db_users SET user_newpm=1 WHERE user_id='".$userid."'");

			if ($cfg['pm_allownotifications'])
			{
				$sql = sed_sql_query("SELECT user_email, user_name
				FROM $db_users
				WHERE user_id='$userid' AND user_pmnotify=1 AND user_maingrp>3");

				if ($row = sed_sql_fetcharray($sql))
				{
					$rusername = sed_cc($row['user_name']);
					$remail = $row['user_email'];
					$rsubject = $cfg['maintitle']." - ".$L['pm_notifytitle'];
					$rbody = sprintf($L['pm_notify'], $rusername, sed_cc($usr['name']), $cfg['mainurl']."/pm.php");
					sed_mail($remail, $rsubject, $rbody);
					sed_stat_inc('totalmailpmnot');
				}
			}
		}

		/* === Hook === */
		$extp = sed_getextplugins('pm.send.send.done');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		sed_stat_inc('totalpms');
		sed_shield_update(30, "New private message (".$totalrecipients.")");
		header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=502", '', true));
		exit;
	}
}
elseif (!empty($to))
{
	if (mb_substr(mb_strtolower($to),0,1)=='g' && $usr['maingrp']==5)
	{
		$group = sed_import(mb_substr($to, 1, 8), 'D', 'INT');
		if ($group>1)
		{
			$sql = sed_sql_query("SELECT user_id, user_name FROM $db_users WHERE user_maingrp='$group' ORDER BY user_name ASC");
			$totalrecipients = sed_sql_numrows($sql);
		}
	}
	else
	{
		$touser_src = explode ('-', $to);
		$touser_req = count($touser_src);

		foreach($touser_src as $k => $i)
		{
			$userid = sed_import($i, 'D', 'INT');
			if ($userid > 0)
			{ $touser_sql[] = "'".$userid."'"; }
		}
		if (count($touser_sql)>0)
		{
			$touser_sql = implode (',', $touser_sql);
			$touser_sql = '('.$touser_sql.')';
			$sql = sed_sql_query("SELECT user_id, user_name FROM $db_users WHERE user_id IN $touser_sql");
			$totalrecipients = sed_sql_numrows($sql);
		}
	}

	if ($totalrecipients>0)
	{
		while ($row = sed_sql_fetcharray($sql))
		{
			$touser_ids[] = $row['user_id'];
			$touser_names[] = sed_cc($row['user_name']);
		}
		$touser = implode (", ", $touser_names);
		$error_string .= ($totalrecipients<$touser_req) ? $L['pm_wrongname']."<br />" : '';
		$error_string .= ($totalrecipients>10) ? sprintf($L['pm_toomanyrecipients'], 10)."<br />" : '';
	}
}

if (!empty($q) && empty($newpmtext))
{
	$sql = sed_sql_query("SELECT pm_date,pm_title,pm_text FROM $db_pm WHERE pm_id='$q' AND pm_touserid='".$usr['id']."' AND pm_state<3 ");

	if ($row=sed_sql_fetcharray($sql))
	{
		$pm_date = @date($cfg['dateformat'], $row['pm_date']).' GMT';
		$newpmtext = "\n\n\n-------- ".$L['Originalmessage']." --------\n".$L['Date']." : ".$pm_date."\n".$L['Title']." : ".$row['pm_title']."\n".$row['pm_text']."\n-------------\n";
		$newpmtitle = "Re: ".$row['pm_title'];
	}
}

$bbcodes = ($cfg['parsebbcodecom']) ? sed_build_bbcodes('newlink', 'newpmtext', $L['BBcodes']) : '';
$smilies = ($cfg['parsesmiliescom']) ? sed_build_smilies('newlink', 'newpmtext', $L['Smilies']) : '';
$pfs = sed_build_pfs($usr['id'], 'newlink', 'newpmtext', $L['Mypfs']);
$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, 'newlink', 'newpmtext', $L['SFS']) : '';
$pm_sendlink = ($usr['auth_write']) ? "<a href=\"".sed_url('pm', 'm=send')."\">".$L['pm_sendnew']."</a>" : '';

$title_tags[] = array('{PM}', '{SEND_NEW}');
$title_tags[] = array('%1$s', '%2$s');
$title_data = array($L['Private_Messages'], $L['pm_sendnew']);
$out['subtitle'] = sed_title('title_pm_send', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('pm.send.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('pm.send'));

if (!empty($error_string))
{
	$t->assign("PMSEND_ERROR_BODY",$error_string);
	$t->parse("MAIN.PMSEND_ERROR");
}

$t->assign(array(
	"PMSEND_TITLE" => "<a href=\"".sed_url('pm')."\">".$L['Private_Messages']."</a> ".$cfg['separator']." ".$L['pmsend_title'],
	"PMSEND_SUBTITLE" => $L['pmsend_subtitle'],
	"PMSEND_SENDNEWPM" => $pm_sendlink,
	"PMSEND_INBOX" => "<a href=\"".sed_url('pm')."\">".$L['pm_inbox']."</a>:".$totalinbox,
	"PMSEND_ARCHIVES" => "<a href=\"".sed_url('pm', 'f=archives')."\">".$L['pm_archives']."</a>:".$totalarchives,
	"PMSEND_SENTBOX" => "<a href=\"".sed_url('pm', 'f=sentbox')."\">".$L['pm_sentbox']."</a>:".$totalsentbox,
	"PMSEND_FORM_SEND" => sed_url('pm', 'pm.php?m=send&amp;a=send&amp;to='.$to),
	"PMSEND_FORM_TITLE" => "<input type=\"text\" class=\"text\" name=\"newpmtitle\" value=\"".sed_cc($newpmtitle)."\" size=\"56\" maxlength=\"64\" />",
	"PMSEND_FORM_TEXT" =>  "<textarea class=\"editor\" name=\"newpmtext\" rows=\"16\" cols=\"56\">".sed_cc($newpmtext)."</textarea><br />".$bbcodes." ".$smilies." ".$pfs,
	"PMSEND_FORM_TEXTBOXER" => "<textarea class=\"editor\" name=\"newpmtext\" rows=\"16\" cols=\"56\">".sed_cc($newpmtext)."</textarea><br />".$bbcodes." ".$smilies." ".$pfs,
	"PMSEND_FORM_BBCODES" => $bbcodes,
	"PMSEND_FORM_SMILIES" => $smilies,
	"PMSEND_FORM_MYPFS" => $pfs,
	"PMSEND_FORM_TOUSER" => "<textarea name=\"newpmrecipient\" rows=\"3\" cols=\"56\">".$touser."</textarea>"
	));

	/* === Hook === */
	$extp = sed_getextplugins('pm.send.tags');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t->parse("MAIN");
	$t->out("MAIN");

	require_once $cfg['system_dir'] . '/footer.php';

	?>
