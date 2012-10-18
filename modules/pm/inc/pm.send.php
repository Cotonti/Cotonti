<?php

/**
 * PM
 *
 * @package pm
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forms');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('pm', 'a');
cot_block($usr['auth_write']);

$to = cot_import('to', 'G', 'TXT');
$a = cot_import('a','G','TXT');
$id = cot_import('id','G','INT');

$totalrecipients = 0;
$touser_sql = array();
$touser_ids = array();
$touser_names = array();

/* === Hook === */
foreach (cot_getextplugins('pm.send.first') as $pl)
{
	include $pl;
}
/* ===== */
if ($a == 'send')
{
	cot_shield_protect();
	$newpmtitle = cot_import('newpmtitle', 'P', 'TXT');
	$newpmtext = cot_import('newpmtext', 'P', 'HTM');
	$newpmrecipient = cot_import('newpmrecipient', 'P', 'TXT');
	$fromstate = (cot_import('fromstate', 'P', 'INT') == 0) ? 0 : 3;

	if (mb_strlen($newpmtext) < 2)
	{
		cot_error('pm_bodytooshort', 'newpmtext');
	}
	if (mb_strlen($newpmtext) > $cfg['pm']['maxsize'])
	{
		cot_error('pm_bodytoolong', 'newpmtext');
	}
	$newpmtitle .= (mb_strlen($newpmtitle) < 2) ? ' . . . ' : '';
	/* === Hook === */
	foreach (cot_getextplugins('pm.send.send.first') as $pl)
	{
		include $pl;
	}
	/* ===== */


	if (!empty($id))			// edit message

	{
		if (!cot_error_found())
		{
			$pm['pm_title'] = $newpmtitle;
			$pm['pm_date'] = (int)$sys['now'];
			$pm['pm_text'] = $newpmtext;
			$pm['pm_fromstate'] = $fromstate;

			$sql_pm_update = $db->update($db_pm, $pm, "pm_id = $id AND pm_fromuserid = ".$usr['id']." AND pm_tostate = '0'");
		}
		/* === Hook === */
		foreach (cot_getextplugins('pm.send.update.done') as $pl)
		{
			include $pl;
		}
		/* ===== */
		cot_redirect(cot_url('pm', 'f=sentbox'));
	}
	else				//send message

	{
		if (!empty($newpmrecipient))
		{
			$touser_src = explode(",", $newpmrecipient);
			$touser_req = count($touser_src);
			foreach($touser_src as $k => $i)
			{
				$user_name=trim(cot_import($i, 'D', 'TXT'));
				if(!empty($user_name))
				{
					$touser_sql[] = "'".$db->prep($user_name)."'";
				}
				else
				{
					$touser_req--;
				}
			}
			$touser_sql = '('.implode(',', $touser_sql).')';
			$sql_pm_users = $db->query("SELECT user_id, user_name FROM $db_users WHERE user_name IN $touser_sql");
			$totalrecipients = $sql_pm_users->rowCount();
			while($row = $sql_pm_users->fetch())
			{
				$touser_ids[] = (int) $row['user_id'];
				$touser_names[] = htmlspecialchars($row['user_name']);
			}
			$sql_pm_users->closeCursor();
			if ($totalrecipients < $touser_req )
			{
				cot_error('pm_wrongname', 'newpmrecipient');
			}
			if (!$usr['isadmin'] && $totalrecipients > 10)
			{
				cot_error(sprintf($L['pm_toomanyrecipients'], 10), 'newpmrecipient');
			}
			$touser = ($totalrecipients > 0) ? implode(",", $touser_names) : '';
		}
		else
		{
			if (empty($to))
			{
				cot_error('pm_norecipient', 'newpmrecipient');
			}
			$touser_ids[] = (int) $to;
			$touser = (int) $to;
			$totalrecipients = 1;
		}

		if (!cot_error_found())
		{
			$stats_enabled = function_exists('cot_stat_inc');
			foreach ($touser_ids as $k => $userid)
			{
				$pm['pm_title'] = $newpmtitle;
				$pm['pm_date'] = (int)$sys['now'];
				$pm['pm_text'] = $newpmtext;
				$pm['pm_fromstate'] = $fromstate;
				$pm['pm_fromuserid'] = (int)$usr['id'];
				$pm['pm_fromuser'] = $usr['name'];
				$pm['pm_touserid'] = $userid;
				$pm['pm_tostate'] = 0;
				$pmsql = $db->insert($db_pm, $pm);
				$pmsql = $db->update($db_users, array('user_newpm' => '1'), "user_id = $userid");

				if ($cfg['pm']['allownotifications'])
				{
					$pmsql = $db->query("SELECT user_email, user_name, user_lang
						FROM $db_users WHERE user_id = $userid AND user_pmnotify = 1 AND user_maingrp > 3");

					if ($row = $pmsql->fetch())
					{
						cot_send_translated_mail($row['user_lang'], $row['user_email'], htmlspecialchars($row['user_name']));
						if($stats_enabled) { cot_stat_inc('totalmailpmnot'); }
					}
				}
			}

			/* === Hook === */
			foreach (cot_getextplugins('pm.send.send.done') as $pl)
			{
				include $pl;
			}
			/* ===== */

			if($stats_enabled) { cot_stat_inc('totalpms'); }
			cot_shield_update(30, "New private message (".$totalrecipients.")");
			cot_redirect(cot_url('pm', 'f=sentbox'));
		}
	}
}

if (!empty($to))
{
	if (mb_substr(mb_strtolower($to), 0, 1) == 'g' && $usr['maingrp'] == 5)
	{
		$group = cot_import(mb_substr($to, 1, 8), 'D', 'INT');
		if ($group > 1)
		{
			$sql_pm_users = $db->query("SELECT user_id, user_name FROM $db_users WHERE user_maingrp = $group ORDER BY user_name ASC");
		}
	}
	else
	{
		$touser_src = explode('-', $to);
		$touser_req = count($touser_src);
		foreach ($touser_src as $k => $i)
		{
			$userid = cot_import($i, 'D', 'INT');
			if ($userid > 0)
			{
				$touser_sql[] = $userid;
			}
		}
		if (count($touser_sql) > 0)
		{
			$touser_sql = implode(',', $touser_sql);
			$touser_sql = '('.$touser_sql.')';
			$sql_pm_users = $db->query("SELECT user_id, user_name FROM $db_users WHERE user_id IN $touser_sql");
		}
	}
	$totalrecipients = $sql_pm_users->rowCount();
	if ($totalrecipients > 0)
	{
		while ($row = $sql_pm_users->fetch())
		{
			$touser_ids[] = $row['user_id'];
			$touser_names[] = htmlspecialchars($row['user_name']);
		}
		$sql_pm_users->closeCursor();
		$touser = implode(", ", $touser_names);
		if ($totalrecipients < $touser_req)
		{
			cot_error('pm_wrongname', 'newpmrecipient');
		}
		if (!$usr['isadmin'] && $totalrecipients > 10)
		{
			cot_error(sprintf($L['pm_toomanyrecipients'], 10), 'newpmrecipient');
		}
	}
}

list($totalsentbox, $totalinbox) = cot_message_count($usr['id']);

$title_params = array(
	'PM' => $L['Private_Messages'],
	'SEND_NEW' => $L['pm_sendnew']
);
$out['subtitle'] = cot_title('{SEND_NEW} - {PM}', $title_params);
$out['head'] .= $R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('pm.send.main') as $pl)
{
	include $pl;
}
/* ===== */
if ($id)
{
	$pmsql = $db->query("SELECT *, u.user_name FROM $db_pm AS p LEFT JOIN $db_users AS u ON u.user_id=p.pm_touserid WHERE pm_id=$id AND pm_tostate=0 LIMIT 1");
	if ($pmsql->rowCount()!=0)
	{
		$row = $pmsql->fetch();
		$newpmtitle = (!empty($newpmtitle)) ? $newpmtitle : $row['pm_title'];
		$newpmtext = (!empty($newpmtext)) ? $newpmtext : $row['pm_text'];
		$idurl = '&id='.$id;
	}
	else
	{
		cot_die();
	}
}

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(cot_tplfile(array('pm', 'send', $pmalttpl)));

if (!COT_AJAX)
{
	$t->parse('MAIN.BEFORE_AJAX');
	$t->parse('MAIN.AFTER_AJAX');
}

cot_display_messages($t);

$title[] = array(cot_url('pm'), $L['Private_Messages']);
$title[] = (!$id) ? $L['pmsend_title'] : $L['Edit'].' #'.$id;

$url_newpm = cot_url('pm', 'm=send');
$url_inbox = cot_url('pm');
$url_sentbox = cot_url('pm', 'f=sentbox');

$t->assign(array(
	'PMSEND_TITLE' => cot_breadcrumbs($title, $cfg['homebreadcrumb']),
	'PMSEND_SENDNEWPM' => ($usr['auth_write']) ? cot_rc_link($url_newpm, $L['pm_sendnew'], array('class'=>$cfg['pm']['turnajax'] ? 'ajax' : '')) : '',
	'PMSEND_SENDNEWPM_URL' => ($usr['auth_write']) ? $url_newpm : '',
	'PMSEND_INBOX' => cot_rc_link($url_inbox, $L['pm_inbox'], array('class'=>$cfg['pm']['turnajax'] ? 'ajax' : '')),
	'PMSEND_INBOX_URL' => $url_inbox,
	'PMSEND_INBOX_COUNT' => $totalinbox,
	'PMSEND_SENTBOX' => cot_rc_link($url_sentbox, $L['pm_sentbox'], array('class'=>$cfg['pm']['turnajax'] ? 'ajax' : '')),
	'PMSEND_SENTBOX_URL' => $url_sentbox,
	'PMSEND_SENTBOX_COUNT' => $totalsentbox,
	'PMSEND_FORM_SEND' => cot_url('pm', 'm=send&a=send'.$idurl),
	'PMSEND_FORM_TITLE' => cot_inputbox('text', 'newpmtitle', htmlspecialchars($newpmtitle), 'size="56" maxlength="255"'),
	'PMSEND_FORM_TEXT' => cot_textarea('newpmtext', $newpmtext, 8, 56, '', 'input_textarea_editor'),
	'PMSEND_FORM_TOUSER' => cot_textarea('newpmrecipient', $touser, 3, 56, 'class="userinput"'),
	'PMSEND_AJAX_MARKITUP' => (COT_AJAX && cot_plugin_active('markitup') && $cfg['pm']['turnajax'])
));

if (!$id)
{
	$t->parse('MAIN.PMSEND_USERLIST');
}

/* === Hook === */
foreach (cot_getextplugins('pm.send.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';
?>