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
sed_block($usr['auth_write']);

$to = sed_import('to', 'G', 'TXT');
$a = sed_import('a','G','TXT');
$id = sed_import('id','G','INT');

$totalrecipients = 0;
$touser_sql = array();
$touser_ids = array();
$touser_names = array();

/* === Hook === */
$extp = sed_getextplugins('pm.send.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */
if ($a == 'getusers')
{
	$q = strtolower(sed_import('q', 'G', 'TXT'));
	$q = sed_sql_prep(urldecode($q));
	if (!empty($q))
	{
		$res = array();
		$sql = sed_sql_query("SELECT `user_name` FROM $db_users WHERE `user_name` LIKE '$q%'");
		while($row = sed_sql_fetchassoc($sql))
		{
			$res[] = $row['user_name'];
		}
		$userlist = implode("\n", $res);
		sed_sendheaders();
	}
	echo $userlist;
	die();
}
elseif ($a == 'send')
{
	sed_shield_protect();
	$newpmtitle = sed_import('newpmtitle', 'P', 'TXT');
	$newpmtext = sed_import('newpmtext', 'P', 'HTM');
	$newpmrecipient = sed_import('newpmrecipient', 'P', 'TXT');
	$fromstate = (sed_import('fromstate', 'P', 'INT') == 0) ? 0 : 3;

	$error_string .= (mb_strlen($newpmtext) < 2) ? $L['pm_bodytooshort'].'<br />' : '';
	$error_string .= (mb_strlen($newpmtext) > $cfg['pm_maxsize']) ? $L['pm_bodytoolong'].'<br />' : '';
	$newpmtitle .= (mb_strlen($newpmtitle) < 2) ? ' . . . ' : '';
	$newpmhtml = ($cfg['parser_cache']) ? sed_sql_prep(sed_parse(htmlspecialchars($newpmtext))) : '';
	/* === Hook === */
	$extp = sed_getextplugins('pm.send.send.first');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */


	if (!empty($id))			// edit message

	{
		if (empty($error_string))
		{
			$sql = sed_sql_query("UPDATE $db_pm SET
				pm_title = '".sed_sql_prep($newpmtitle)."', pm_text = '".sed_sql_prep($newpmtext)."',
				pm_html = '$newpmhtml', pm_date = '".$sys['now_offset']."', pm_fromstate = '".$fromstate."' 
				WHERE pm_id = '$id' AND pm_fromuserid = '".$usr['id']."' AND pm_tostate = '0'");
		}
		/* === Hook === */
		$extp = sed_getextplugins('pm.send.update.done');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */
		sed_redirect(sed_url('pm', 'f=sentbox'));
	}
	else				//send message

	{
		if (!empty($newpmrecipient))
		{
			$touser_src = explode(",", $newpmrecipient);
			$touser_req = count($touser_src);
			foreach($touser_src as $k => $i)
			{
				$user_name=trim(sed_import($i, 'D', 'TXT'));
				if(!empty($user_name))
				{
					$touser_sql[] = "'".sed_sql_prep($user_name)."'";
				}
				else
				{
					$touser_req--;
				}
			}
			$touser_sql = '('.implode(',', $touser_sql).')';
			$sql = sed_sql_query("SELECT user_id, user_name FROM $db_users WHERE user_name IN $touser_sql");
			$totalrecipients = sed_sql_numrows($sql);
			while($row = sed_sql_fetcharray($sql))
			{
				$touser_ids[] = $row['user_id'];
				$touser_names[] = htmlspecialchars($row['user_name']);
			}
			$error_string .= ($totalrecipients < $touser_req ) ? $L['pm_wrongname']."<br />" : '';
			$error_string .= (!$usr['isadmin'] && $totalrecipients > 10) ? sprintf($L['pm_toomanyrecipients'], 10)."<br />" : '';
			$touser = ($totalrecipients > 0) ? implode(",", $touser_names) : '';
		}
		else
		{
			if (empty($to)) $error_string .= $L['pm_norecipient'].'<br />';
			$touser_ids[] = $to;
			$touser = $to;
			$totalrecipients = 1;
		}

		if (empty($error_string))
		{
			foreach ($touser_ids as $k => $userid)
			{
				$sql = sed_sql_query("INSERT into $db_pm
					(pm_date, pm_fromuserid, pm_fromuser,
					pm_touserid, pm_title, pm_text,
					pm_html, pm_fromstate, pm_tostate)
					VALUES
					(".(int)$sys['now_offset'].", ".(int)$usr['id'].", '".sed_sql_prep($usr['name'])."',
					".(int)$userid.", '".sed_sql_prep($newpmtitle)."', '".sed_sql_prep($newpmtext)."',
					'$newpmhtml', '".(int)$fromstate."', 0)");

				$sql = sed_sql_query("UPDATE $db_users SET user_newpm = 1 WHERE user_id = '".$userid."'");

				if ($cfg['pm_allownotifications'])
				{
					$sql = sed_sql_query("SELECT user_email, user_name, user_lang
						FROM $db_users
						WHERE user_id = '$userid' AND user_pmnotify = 1 AND user_maingrp > 3");

					if ($row = sed_sql_fetcharray($sql))
					{
						sed_send_translated_mail($row['user_lang'], $row['user_email'], htmlspecialchars($row['user_name']));
						sed_stat_inc('totalmailpmnot');
					}
				}
			}

			/* === Hook === */
			$extp = sed_getextplugins('pm.send.send.done');
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			sed_stat_inc('totalpms');
			sed_shield_update(30, "New private message (".$totalrecipients.")");
			sed_redirect(sed_url('pm', 'f=sentbox'));
		}
	}
}

if (!empty($to))
{
	if (mb_substr(mb_strtolower($to), 0, 1) == 'g' && $usr['maingrp'] == 5)
	{
		$group = sed_import(mb_substr($to, 1, 8), 'D', 'INT');
		if ($group > 1)
		{
			$sql = sed_sql_query("SELECT user_id, user_name FROM $db_users WHERE user_maingrp = '$group' ORDER BY user_name ASC");
		}
	}
	else
	{
		$touser_src = explode('-', $to);
		$touser_req = count($touser_src);
		foreach ($touser_src as $k => $i)
		{
			$userid = sed_import($i, 'D', 'INT');
			if ($userid > 0)
			{
				$touser_sql[] = "'".$userid."'";
			}
		}
		if (count($touser_sql) > 0)
		{
			$touser_sql = implode(',', $touser_sql);
			$touser_sql = '('.$touser_sql.')';
			$sql = sed_sql_query("SELECT user_id, user_name FROM $db_users WHERE user_id IN $touser_sql");
		}
	}
	$totalrecipients = sed_sql_numrows($sql);
	if ($totalrecipients > 0)
	{
		while ($row = sed_sql_fetcharray($sql))
		{
			$touser_ids[] = $row['user_id'];
			$touser_names[] = htmlspecialchars($row['user_name']);
		}
		$touser = implode(", ", $touser_names);
		$error_string .= ($totalrecipients < $touser_req) ? $L['pm_wrongname']."<br />" : '';
		$error_string .= (!$usr['isadmin'] && $totalrecipients>10) ? sprintf($L['pm_toomanyrecipients'], 10)."<br />" : '';
	}
}

$pfs = sed_build_pfs($usr['id'], 'newlink', 'newpmtext', $L['Mypfs']);
$pfs .= (sed_auth('pfs', 'a', 'A')) ? ' &nbsp; '.sed_build_pfs(0, 'newlink', 'newpmtext', $L['SFS']) : '';

list($totalsentbox, $totalinbox) = sed_message_count($usr['id']);

$title_params = array(
	'PM' => $L['Private_Messages'],
	'SEND_NEW' => $L['pm_sendnew']
);
$out['subtitle'] = sed_title('title_pm_send', $title_params);
$out['head'] .= $R['code_noindex'];

/* === Hook === */
$extp = sed_getextplugins('pm.send.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */
if ($id)
{
	$sql = sed_sql_query("SELECT *, u.user_name FROM $db_pm AS p LEFT JOIN $db_users AS u ON u.user_id=p.pm_touserid WHERE pm_id='".$id."' AND pm_tostate=0 LIMIT 1");
	if (sed_sql_numrows($sql)!=0)
	{
		$row = sed_sql_fetcharray($sql);
		$newpmtitle = (!empty($newpmtitle)) ? $newpmtitle : $row['pm_title'];
		$newpmtext = (!empty($newpmtitle)) ? $newpmtext : $row['pm_text'];
		$idurl = '&id='.$id;
	}
	else
	{
		sed_die();
	}
}

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('pm.send'));

if (!SED_AJAX)
{
	$t->parse("MAIN.BEFORE_AJAX");
	$t->parse("MAIN.AFTER_AJAX");
}

if (!empty($error_string))
{
	$t->assign("PMSEND_ERROR_BODY",$error_string);
	$t->parse("MAIN.PMSEND_ERROR");
}

$bhome = $cfg['homebreadcrumb'] ? sed_rc_link($cfg['mainurl'], htmlspecialchars($cfg['maintitle'])).' '.$cfg['separator'].' ' : '';
$title = $bhome . sed_rc_link(sed_url('pm'), $L['Private_Messages']).' '.$cfg['separator'].' ';
$title .= (!$id) ? $L['pmsend_title'] : $L['Edit'].' #'.$id;

if (!$id)
{
	$t->assign(array(
			"PMSEND_FORM_TOUSER" => $touser,
	));
	$t->parse("MAIN.PMSEND_USERLIST");
}

$t->assign(array(
		"PMSEND_TITLE" => $title,
		"PMSEND_SUBTITLE" => $L['pmsend_subtitle'],
		"PMSEND_SENDNEWPM" => ($usr['auth_write']) ? sed_rc_link(sed_url('pm', 'm=send'), $L['pm_sendnew'], array('class'=>'ajax')) : '',
		"PMSEND_INBOX" => sed_rc_link(sed_url('pm'), $L['pm_inbox'], array('class'=>'ajax')),
		"PMSEND_INBOX_COUNT" => $totalinbox,
		"PMSEND_SENTBOX" => sed_rc_link(sed_url('pm', 'f=sentbox'), $L['pm_sentbox'], array('class'=>'ajax')),
		"PMSEND_SENTBOX_COUNT" => $totalsentbox,
		"PMSEND_FORM_SEND" => sed_url('pm', 'm=send&a=send'.$idurl),
		"PMSEND_FORM_TITLE" => htmlspecialchars($newpmtitle),
		"PMSEND_FORM_TEXT" => htmlspecialchars($newpmtext),
		"PMSEND_FORM_PFS" => $pfs,
		"PMSEND_FORM_TOUSER" => $touser,
		"PMSEND_AJAX_MARKITUP" => (SED_AJAX && count($cfg['plugin']['markitup'])>0 && $cfg['jquery'] && $cfg['turnajax'])
));

/* === Hook === */
$extp = sed_getextplugins('pm.send.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';
?>