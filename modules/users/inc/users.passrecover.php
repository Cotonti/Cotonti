<?php
/**
 * Sends emails to users so they can recovery their passwords
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$v = cot_import('v', 'G', 'TXT');
$email = cot_import('email', 'P', 'TXT');

/* === Hook === */
foreach (cot_getextplugins('users.passrecover.first') as $pl)
{
	include $pl;
}
/* ===== */

cot_block($usr['id'] == 0);

$msg = '';

if ($a == 'request' && $email != '')
{
	cot_shield_protect();
	$sql = $db->query("SELECT user_id, user_name, user_lostpass FROM $db_users WHERE user_email='".$db->prep($email)."' ORDER BY user_id ASC");
    $email_found= FALSE;
	while ($row = $sql->fetch())
	{
		$rusername = $row['user_name'];
		$ruserid = $row['user_id'];
		$validationkey = $row['user_lostpass'];

		if (empty($validationkey) || $validationkey == "0")
		{
			$validationkey = md5(microtime());
			$sql = $db->update($db_users, array('user_lostpass' => $validationkey, 'user_lastip' => $usr['ip']), "user_id=$ruserid");
		}

		$rsubject = $L['pasrec_title'];
		$ractivate = $cfg['mainurl'].'/'.cot_url('users', 'm=passrecover&a=auth&v='.$validationkey, '', true);
		$rbody = sprintf($L['pasrec_email1'], $rusername, $ractivate, $usr['ip'], cot_date('datetime_medium'));
		$rbody .= "\n\n ".$L['aut_contactadmin'];
		cot_mail($email, $rsubject, $rbody);
		$email_found = TRUE;
		if (!$cfg['useremailduplicate']) break;
	}
	if ($email_found)
	{
		cot_shield_update(60, "Password recovery email sent");
		$msg = 'request';
    }
	else
	{
		cot_shield_update(10, "Password recovery requested");
		$env['status'] = '403 Forbidden';
		cot_log("Pass recovery failed, user : ".$rusername);
		cot_redirect(cot_url('message', 'msg=154', '', true));
	}
}
elseif ($a == 'auth' && mb_strlen($v) == 32)
{
	cot_shield_protect();

	$sql = $db->query("SELECT user_name, user_id, user_email, user_password, user_maingrp, user_banexpire FROM $db_users WHERE user_lostpass='".$db->prep($v)."'");

	if ($row = $sql->fetch())
	{
		$sql->closeCursor();
		$rmdpass  = $row['user_password'];
		$rusername = $row['user_name'];
		$ruserid = $row['user_id'];
		$rusermail = $row['user_email'];

		if ($row['user_maingrp'] == 2)
		{
			$env['status'] = '403 Forbidden';
			cot_log("Password recovery failed, user inactive : ".$rusername);
			cot_redirect(cot_url('message', 'msg=152', '', true));
		}

		if ($row['user_maingrp'] == 3)
		{
			$env['status'] = '403 Forbidden';
			cot_log("Password recovery failed, user banned : ".$rusername);
			cot_redirect(cot_url('message', 'msg=153&num='.$row['user_banexpire'], '', true));
		}

		$validationkey = md5(microtime());
		$newpass = cot_randomstring();
		$ruserpass = array();
		$ruserpass['user_passsalt'] = cot_unique(16);
		$ruserpass['user_passfunc'] = empty($cfg['hashfunc']) ? 'sha256' : $cfg['hashfunc'];
		$ruserpass['user_password'] = cot_hash($newpass, $ruserpass['user_passsalt'], $ruserpass['user_passfunc']);
		$ruserpass['user_lostpass'] = $validationkey;
		$sql = $db->update($db_users, $ruserpass, "user_id=$ruserid");

		$rsubject = $L['pasrec_title'];
		$rbody = $L['Hi']." ".$rusername.",\n\n".$L['pasrec_email2']."\n\n".$newpass."\n\n".$L['aut_contactadmin'];
		cot_mail($rusermail, $rsubject, $rbody);

		$msg = 'auth';
	}
	else
	{
		$env['status'] = '403 Forbidden';
		cot_shield_update(7, "Log in");
		cot_log("Pass recovery failed, user : ".$rusername);
		cot_redirect(cot_url('message', 'msg=151', '', true));
	}
}

$out['subtitle'] = $L['pasrec_title'];
$out['head'] .= $R['code_noindex'];

$title[] = $L['pasrec_title'];
$mskin = cot_tplfile('users.passrecover', 'module');

/* === Hook === */
foreach (cot_getextplugins('users.passrecover.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'].'/header.php';
$t = new XTemplate($mskin);

$t->assign(array(
	'PASSRECOVER_TITLE' => cot_breadcrumbs($title, $cfg['homebreadcrumb']),
	'PASSRECOVER_URL_FORM' => cot_url('users', 'm=passrecover&a=request')
));

/* === Hook === */
foreach (cot_getextplugins('users.passrecover.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'].'/footer.php';
