<?php
/**
 * User Authentication
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

// Environment
define('COT_CODE', TRUE);
define('COT_CORE', TRUE);
define('COT_AUTH', TRUE);
$env['location'] = 'users';
$env['ext'] = 'users';

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/cotemplate.php';
require_once $cfg['system_dir'] . '/common.php';

require_once cot_langfile('users', 'core');

$logout = cot_import('out', 'G', 'BOL');

if ($logout)
{
	// Perform logout
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('users.logout') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($usr['id'] > 0)
	{
		cot_uriredir_apply($cfg['redirbkonlogout']);
	}

	if(!empty($_COOKIE[$sys['site_id']]))
	{
		cot_setcookie($sys['site_id'], '', time()-63072000, $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
	}

	session_unset();
	session_destroy();

	if ($usr['id'] > 0)
	{
		$db->update($db_users, array('user_lastvisit' => $sys['now']), "user_id = " . $usr['id']);

		$all = cot_import('all', 'G', 'BOL');
		if ($all)
		{
			// Log out on all devices
			$db->update($db_users, array('user_sid' => ''), "user_id = " . $usr['id']);
		}

		cot_uriredir_redirect(empty($redirect) ? cot_url('index') : base64_decode($redirect));
	}
	else
	{
		cot_redirect(cot_url('index'));
	}

	exit;
}

/* === Hook === */
foreach (cot_getextplugins('users.auth.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'check')
{
	cot_shield_protect();

	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.auth.check') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$rusername = cot_import('rusername','P','TXT', 100, TRUE);
	$rpassword = cot_import('rpassword','P','HTM', 32, TRUE);
	$rcookiettl = cot_import('rcookiettl', 'P', 'INT');
	$rremember = cot_import('rremember', 'P', 'BOL');
	if(empty($rremember) && $rcookiettl > 0 || $cfg['forcerememberme'])
	{
		$rremember = true;
	}

	$login_param = !$cfg['useremailduplicate'] && cot_check_email($rusername) ?
		'user_email' : 'user_name';

	// Load salt and algo from db
	$sql = $db->query("SELECT user_passsalt, user_passfunc FROM $db_users WHERE $login_param=".$db->quote($rusername));
	if ($sql->rowCount() == 1)
	{
		$hash_params = $sql->fetch();
		$rmdpass = cot_hash($rpassword, $hash_params['user_passsalt'], $hash_params['user_passfunc']);
		unset($hash_params);
	}

	/**
	 * Sets user selection criteria for authentication. Override this string in your plugin
	 * hooking into users.auth.check.query to provide other authentication methods.
	 */
	$user_select_condition = "user_password=".$db->quote($rmdpass)." AND $login_param=".$db->quote($rusername);

	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.auth.check.query') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql = $db->query("SELECT user_id, user_name, user_maingrp, user_banexpire, user_theme, user_scheme, user_lang, user_sid, user_sidtime FROM $db_users WHERE $user_select_condition");

	if ($row = $sql->fetch())
	{
		$rusername = $row['user_name'];
		if ($row['user_maingrp']==-1)
		{
			$env['status'] = '403 Forbidden';
			cot_log("Log in attempt, user inactive : ".$rusername, 'usr');
			cot_redirect(cot_url('message', 'msg=152', '', true));
		}
		if ($row['user_maingrp']==2)
		{
			$env['status'] = '403 Forbidden';
			cot_log("Log in attempt, user inactive : ".$rusername, 'usr');
			cot_redirect(cot_url('message', 'msg=152', '', true));
		}
		elseif ($row['user_maingrp']==3)
		{
			if ($sys['now'] > $row['user_banexpire'] && $row['user_banexpire']>0)
			{
				$sql = $db->update($db_users, array('user_maingrp' => '4'),  "user_id={$row['user_id']}");
			}
			else
			{
				$env['status'] = '403 Forbidden';
				cot_log("Log in attempt, user banned : ".$rusername, 'usr');
				cot_redirect(cot_url('message', 'msg=153&num='.$row['user_banexpire'], '', true));
			}
		}

		$ruserid = $row['user_id'];
		$rdeftheme = $row['user_theme'];
		$rdefscheme = $row['user_scheme'];

		$token = cot_unique(16);

		$sid = hash_hmac('sha256', $rmdpass . $row['user_sidtime'], $cfg['secret_key']);

		if (empty($row['user_sid']) || $row['user_sid'] != $sid
			|| $row['user_sidtime'] + $cfg['cookielifetime'] < $sys['now'])
		{
			// Generate new session identifier
			$sid = hash_hmac('sha256', $rmdpass . $sys['now'], $cfg['secret_key']);
			$update_sid = ", user_sid = " . $db->quote($sid) . ", user_sidtime = " . $sys['now'];
		}
		else
		{
			$update_sid = '';
		}

		$db->query("UPDATE $db_users SET user_lastip='{$usr['ip']}', user_lastlog = {$sys['now']}, user_logcount = user_logcount + 1, user_token = '$token' $update_sid WHERE user_id={$row['user_id']}");

		// Hash the sid once more so it can't be faked even if you  know user_sid
		$sid = hash_hmac('sha1', $sid, $cfg['secret_key']);

		$u = base64_encode($ruserid.':'.$sid);

		if($rremember)
		{
			cot_setcookie($sys['site_id'], $u, time()+$cfg['cookielifetime'], $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
			unset($_SESSION[$sys['site_id']]);
		}
		else
		{
			$_SESSION[$sys['site_id']] = $u;
		}

		/* === Hook === */
		foreach (cot_getextplugins('users.auth.check.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		cot_uriredir_apply($cfg['redirbkonlogin']);
		cot_uriredir_redirect(empty($redirect) ? cot_url('index') : base64_decode($redirect));
	}
	else
	{
		$env['status'] = '401 Unauthorized';
		cot_shield_update(7, "Log in");
		cot_log("Log in failed, user : ".$rusername,'usr');

		/* === Hook === */
		foreach (cot_getextplugins('users.auth.check.fail') as $pl)
		{
			include $pl;
		}
		/* ===== */

		cot_redirect(cot_url('message', 'msg=151', '', true));
	}
}

/* === Hook === */
foreach (cot_getextplugins('users.auth.main') as $pl)
{
	include $pl;
}
/* ===== */

$out['subtitle'] = $L['aut_logintitle'];
$out['head'] .= $R['code_noindex'];
require_once $cfg['system_dir'] . '/header.php';
$mskin = file_exists(cot_tplfile('login', 'core')) ? cot_tplfile('login', 'core') : cot_tplfile('users.auth', 'module');
$t = new XTemplate($mskin);

require_once cot_incfile('forms');

if ($cfg['maintenance'])
{
	$t->assign(array('USERS_AUTH_MAINTENANCERES' => $cfg['maintenancereason']));
	$t->parse('MAIN.USERS_AUTH_MAINTENANCE');
}

$t->assign(array(
	'USERS_AUTH_TITLE' => $L['aut_logintitle'],
	'USERS_AUTH_SEND' => cot_url('login', 'a=check' . (empty($redirect) ? '' : "&redirect=$redirect")),
	'USERS_AUTH_USER' => cot_inputbox('text', 'rusername', $rusername, array('size' => '12', 'maxlength' => '100')),
	'USERS_AUTH_PASSWORD' => cot_inputbox('password', 'rpassword', '', array('size' => '12', 'maxlength' => '32')),
	'USERS_AUTH_REGISTER' => cot_url('users', 'm=register'),
	'USERS_AUTH_REMEMBER' => $cfg['forcerememberme'] ? $R['form_guest_remember_forced'] : $R['form_guest_remember']
));

/* === Hook === */
foreach (cot_getextplugins('users.auth.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';
?>