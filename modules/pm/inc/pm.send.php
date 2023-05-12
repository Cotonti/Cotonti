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
cot_block(Cot::$usr['auth_write']);

$to = cot_import('to', 'G', 'TXT');
$a = cot_import('a','G','TXT');
$id = cot_import('id','G','INT');

$totalrecipients = 0;
$touser_sql = array();
$touser_ids = array();
$touser_names = array();

/* === Hook === */
foreach (cot_getextplugins('pm.send.first') as $pl) {
	include $pl;
}
/* ===== */

$newpmtitle = '';
$newpmtext = '';
$newpmrecipient = '';
$fromstate = 0;
$touser = '';
if ($a == 'send') {
	cot_shield_protect();
	$newpmtitle = cot_import('newpmtitle', 'P', 'TXT') ?: '';
	$newpmtext = cot_import('newpmtext', 'P', 'HTM') ?: '';
	$newpmrecipient = cot_import('newpmrecipient', 'P', 'TXT');
	$fromstate = (cot_import('fromstate', 'P', 'INT') == 0) ? 0 : 3;

	if (mb_strlen($newpmtext) < 2) {
		cot_error('pm_bodytooshort', 'newpmtext');
	}
	if (mb_strlen($newpmtext) > Cot::$cfg['pm']['maxsize']) {
		cot_error(cot_rc('pm_bodytoolong', array('size' => Cot::$cfg['pm']['maxsize'])), 'newpmtext');
	}
	$newpmtitle .= (mb_strlen($newpmtitle) < 2) ? ' . . . ' : '';

	/* === Hook === */
	foreach (cot_getextplugins('pm.send.send.first') as $pl) {
		include $pl;
	}
	/* ===== */

	if (!empty($id)) {
        // edit message
		if (!cot_error_found()) {
			$pm['pm_title'] = $newpmtitle;
			$pm['pm_date'] = Cot::$sys['now'];
			$pm['pm_text'] = $newpmtext;
			$pm['pm_fromstate'] = $fromstate;

			$result = Cot::$db->update(
                Cot::$db->pm,
                $pm,
                'pm_id = :pmId AND pm_fromuserid = :userId AND pm_tostate = ' . COT_PM_STATE_UNREAD,
                ['pmId' => $id, 'userId' => Cot::$usr['id'],]
            );
		}

		/* === Hook === */
		foreach (cot_getextplugins('pm.send.update.done') as $pl) {
			include $pl;
		}
		/* ===== */

		cot_redirect(cot_url('pm', 'f=sentbox', '', true));
	} else {
        //send message
		if (!empty($newpmrecipient)) {
			$touser_src = explode(",", $newpmrecipient);
			$touser_req = count($touser_src);
			foreach($touser_src as $k => $i) {
				$user_name=trim(cot_import($i, 'D', 'TXT'));
				if (!empty($user_name)) {
					$touser_sql[] = "'".Cot::$db->prep($user_name)."'";
				} else {
					$touser_req--;
				}
			}
			$touser_sql = '('.implode(',', $touser_sql).')';
			$sql_pm_users = Cot::$db->query("SELECT user_id, user_name FROM $db_users WHERE user_name IN $touser_sql");
			$totalrecipients = $sql_pm_users->rowCount();
			while($row = $sql_pm_users->fetch()) {
				$touser_ids[] = (int) $row['user_id'];
				$touser_names[] = htmlspecialchars($row['user_name']);
			}
			$sql_pm_users->closeCursor();
			if ($totalrecipients < $touser_req ) {
				cot_error('pm_wrongname', 'newpmrecipient');
			}
			if (!Cot::$usr['isadmin'] && $totalrecipients > 10) {
				cot_error(sprintf(Cot::$L['pm_toomanyrecipients'], 10), 'newpmrecipient');
			}

			$touser = ($totalrecipients > 0) ? implode(",", $touser_names) : '';
		} else {
			if (empty($to)) {
				cot_error('pm_norecipient', 'newpmrecipient');
			}
			$touser_ids[] = (int) $to;
			$touser = (int) $to;
			$totalrecipients = 1;
		}

		if (!cot_error_found()) {
			foreach ($touser_ids as $k => $userid) {
                $pmId = cot_send_pm($userid, $newpmtitle, $newpmtext, Cot::$usr['id'], $fromstate);
			}

			/* === Hook === */
			foreach (cot_getextplugins('pm.send.send.done') as $pl) {
				include $pl;
			}
			/* ===== */

			cot_shield_update(30, "New private message (".$totalrecipients.")");
			cot_redirect(cot_url('pm', 'f=sentbox', '', true));
		}
	}
}

if (!empty($to)) {
	$totalrecipients = 0;
	if (mb_substr(mb_strtolower($to), 0, 1) == 'g' && $usr['maingrp'] == 5) {
		$group = cot_import(mb_substr($to, 1, 8), 'D', 'INT');
		if ($group > 1) {
			$sql_pm_users = $db->query("SELECT user_id, user_name FROM $db_users WHERE user_maingrp = $group ORDER BY user_name ASC");
		}
	} else {
		$touser_src = explode('-', $to);
		$touser_req = count($touser_src);
		foreach ($touser_src as $k => $i) {
			$userid = cot_import($i, 'D', 'INT');
			if ($userid > 0) {
				$touser_sql[] = $userid;
			}
		}
		if (count($touser_sql) > 0) {
			$touser_sql = implode(',', $touser_sql);
			$touser_sql = '('.$touser_sql.')';
			$sql_pm_users = $db->query("SELECT user_id, user_name FROM $db_users WHERE user_id IN $touser_sql");
		}
	}
	$sql_pm_users && $totalrecipients = $sql_pm_users->rowCount();
	if ($totalrecipients > 0) {
		while ($row = $sql_pm_users->fetch()) {
			$touser_ids[] = $row['user_id'];
			$touser_names[] = htmlspecialchars($row['user_name']);
		}
		$sql_pm_users->closeCursor();
		$touser = implode(", ", $touser_names);
		if ($totalrecipients < $touser_req) {
			cot_error('pm_wrongname', 'newpmrecipient');
		}
		if (!$usr['isadmin'] && $totalrecipients > 10) {
			cot_error(sprintf($L['pm_toomanyrecipients'], 10), 'newpmrecipient');
		}
	}
}

list($totalsentbox, $totalinbox) = cot_message_count($usr['id']);

$title_params = array(
	'PM' => $L['Private_Messages'],
	'SEND_NEW' => $L['pm_sendnew']
);
Cot::$out['subtitle'] = cot_title('{SEND_NEW} - {PM}', $title_params);
Cot::$out['head'] .= Cot::$R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('pm.send.main') as $pl) {
	include $pl;
}
/* ===== */

$idurl = '';
if ($id > 0) {
    // It is possible to edit only user's own message, which was not read by the recipient
    $message = Cot::$db->query(
        'SELECT * FROM ' . Cot::$db->pm .
        ' WHERE pm_id = :pmId AND pm_fromuserid = :userId AND pm_tostate = ' . COT_PM_STATE_UNREAD .
        ' LIMIT 1',
        ['pmId' => $id, 'userId' => Cot::$usr['id'],]
    )->fetch();

    if (!$message) {
        cot_die_message(404);
    }

    $newpmtitle = !empty($newpmtitle) ? $newpmtitle : $message['pm_title'];
    $newpmtext = (!empty($newpmtext)) ? $newpmtext : $message['pm_text'];
    $idurl = '&id=' . $id;
}

require_once Cot::$cfg['system_dir'] . '/header.php';

if (!isset($pmalttpl)) {
    $pmalttpl = null;
}
$t = new XTemplate(cot_tplfile(array('pm', 'send', $pmalttpl)));

if (!COT_AJAX) {
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
	'PMSEND_TITLE' => cot_breadcrumbs($title, Cot::$cfg['homebreadcrumb']),
	'PMSEND_SENDNEWPM' => (Cot::$usr['auth_write']) ?
        cot_rc_link($url_newpm, Cot::$L['pm_sendnew'], array('class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')) : '',
	'PMSEND_SENDNEWPM_URL' => (Cot::$usr['auth_write']) ? $url_newpm : '',
	'PMSEND_INBOX' => cot_rc_link($url_inbox, Cot::$L['pm_inbox'], array('class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
	'PMSEND_INBOX_URL' => $url_inbox,
	'PMSEND_INBOX_COUNT' => $totalinbox,
	'PMSEND_SENTBOX' => cot_rc_link($url_sentbox, Cot::$L['pm_sentbox'], array('class' => Cot::$cfg['pm']['turnajax'] ? 'ajax' : '')),
	'PMSEND_SENTBOX_URL' => $url_sentbox,
	'PMSEND_SENTBOX_COUNT' => $totalsentbox,
	'PMSEND_FORM_SEND' => cot_url('pm', 'm=send&a=send'.$idurl),
	'PMSEND_FORM_TITLE' => cot_inputbox('text', 'newpmtitle', htmlspecialchars($newpmtitle), 'size="56" maxlength="255"'),
	'PMSEND_FORM_TEXT' => cot_textarea('newpmtext', $newpmtext, 8, 56, '', 'input_textarea_editor'),
	'PMSEND_FORM_TOUSER' => cot_textarea('newpmrecipient', $touser, 3, 56, 'class="userinput"'),
	'PMSEND_FORM_NOT_TO_SENTBOX' => cot_checkbox(false, 'fromstate', Cot::$L['pm_notmovetosentbox'], '', '3')
));

/* === Hook === */
foreach (cot_getextplugins('pm.send.tags') as $pl) {
	include $pl;
}
/* ===== */

if (!$id) {
    $t->parse('MAIN.PMSEND_USERLIST');
}

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
