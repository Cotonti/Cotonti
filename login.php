<?php
/**
 * User Authentication
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

// Environment
const COT_CODE = true;
const COT_CORE = true;
const COT_AUTH = true;

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';

require_once cot_langfile('users', 'core');

Cot::$env['ext'] = 'users';
Cot::$env['location'] = 'login';
Cot::$sys['sublocation'] = Cot::$L['Login'];

$logout = cot_import('out', 'G', 'BOL');
if ($logout) {
	// Perform logout
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('users.logout') as $pl) {
		include $pl;
	}
	/* ===== */

	if (Cot::$usr['id'] > 0) {
		cot_uriredir_apply(Cot::$cfg['redirbkonlogout']);
	}

	if (cot_import(Cot::$sys['site_id'], 'COOKIE', 'TXT')) {
		cot_setcookie(Cot::$sys['site_id'], '', time()-63072000, Cot::$cfg['cookiepath'],
            Cot::$cfg['cookiedomain'], Cot::$sys['secure'], true);
	}

	session_unset();
	session_destroy();

	if (Cot::$usr['id'] > 0) {
        Cot::$db->update(Cot::$db->users, array('user_lastvisit' => Cot::$sys['now']), "user_id = " . Cot::$usr['id']);

		$all = cot_import('all', 'G', 'BOL');
		if ($all) {
			// Log out on all devices
            Cot::$db->update(Cot::$db->users, array('user_sid' => ''), "user_id = " . Cot::$usr['id']);
		}

		cot_uriredir_redirect(empty($redirect) ? cot_url('index') : base64_decode($redirect));
	} else {
		cot_redirect(cot_url('index'));
	}

	cot_log("Logout user : " . $rusername, 'users', 'logout', 'done');

	exit;
}

/* === Hook === */
foreach (cot_getextplugins('users.auth.first') as $pl) {
	include $pl;
}
/* ===== */

$rusername = '';

if ($a == 'check') {
	cot_shield_protect();

	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.auth.check') as $pl) {
		include $pl;
	}
	/* ===== */

	$rusername = cot_import('rusername','P','TXT', 100);
	$rpassword = (string) cot_import('rpassword','P','NOC', 32);
	$rcookiettl = cot_import('rcookiettl', 'P', 'INT');
	$rremember = cot_import('rremember', 'P', 'BOL');
	$token = cot_import('token', 'G', 'ALP');
	$v = cot_import('v', 'G', 'ALP');
	$validating = FALSE;

	if (empty($rremember) && $rcookiettl > 0 || Cot::$cfg['forcerememberme']) {
		$rremember = true;
	}

	$login_param = !Cot::$cfg['useremailduplicate'] && cot_check_email($rusername) ?
		'user_email' : 'user_name';

    // Todo Obsolete? Is it really using? @see modules/users/inc/users.register.php:140
	if (!empty($v) && mb_strlen($v) == 32) {
		$validating = TRUE;
		$login_param = 'user_lostpass';
	}

	// Load salt and algo from db
    $rmdpass = '';
    if (!empty($rusername) && !empty($rpassword)) {
        $sql = Cot::$db->query(
            'SELECT user_passsalt, user_passfunc FROM ' . Cot::$db->users . " WHERE {$login_param}=?",
            $rusername
        );
        if ($sql->rowCount() == 0) {
            // If login has e-mail format, try to find it as user_name
            $sql = Cot::$db->query(
                'SELECT user_passsalt, user_passfunc FROM ' . Cot::$db->users . ' WHERE user_name=?',
                $rusername
            );
        }

        if ($sql->rowCount() == 1) {
            $hash_params = $sql->fetch();
            $rmdpass = cot_hash($rpassword, $hash_params['user_passsalt'], $hash_params['user_passfunc']);
            unset($hash_params);
        }
    }

	/**
	 * Sets user selection criteria for authentication. Override this string in your plugin
	 * hooking into users.auth.check.query to provide other authentication methods.
	 */
    $userSelectCondition = null;
    $userSelectParams = [];

    /** @deprecated Will be removed in 1.0.0 */
    $user_select_condition = null;

    if (!$validating) {
        if (!empty($rusername) && !empty($rmdpass)) {
            $userSelectCondition = "$login_param = :login_param AND user_password = :password";
            $userSelectParams = ['login_param' => $rusername, 'password' => $rmdpass];
        }
    } else {
        if (!empty($v)) {
            $userSelectCondition = 'user_lostpass = :lost_pass';
            $userSelectParams = ['lost_pass' => $v];
        }
    }

	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.auth.check.query') as $pl) {
		include $pl;
	}
	/* ===== */

    // For backwards compatibility. Will be removed in 1.0.0
    if (!empty($user_select_condition)) {
        $userSelectCondition = $user_select_condition;
    }

    if (empty($userSelectCondition)) {
        Cot::$env['status'] = '401 Unauthorized';
        cot_log("Log in failed, user : " . $rusername, 'users', 'login', 'error');
        cot_redirect(cot_url('message', 'msg=151', '', true));
    }

	$sql = Cot::$db->query('SELECT user_id, user_name, user_token, user_regdate, user_maingrp, user_banexpire, ' .
        'user_theme, user_scheme, user_lang, user_sid, user_sidtime ' .
         'FROM ' . Cot::$db->users . " WHERE $userSelectCondition", $userSelectParams);

	/* 	Checking if we got any entries with the current login conditions,
		only may fail when user name has e-mail format or user is not registered,
		added for compatibility, because disallowed using e-mail as login on registration
	*/
	if ($sql->rowCount() == 0) {
		// If login has e-mail format, try to find it as user_name
		$user_select_condition = "user_password=".Cot::$db->quote($rmdpass)." AND user_name=".Cot::$db->quote($rusername);

		// Query the database
		$sql = Cot::$db->query('SELECT user_id, user_name, user_token, user_regdate, user_maingrp, ' .
            'user_banexpire, user_theme, user_scheme, user_lang, user_sid, user_sidtime ' .
            'FROM ' . Cot::$db->users .
            " WHERE $user_select_condition");
	}

	if ($row = $sql->fetch()) {
		$rusername = $row['user_name'];

		// Checking to make sure user doesn't game the free login from
		if (
            $validating &&
            (
                $row['user_maingrp'] != COT_GROUP_MEMBERS ||
                Cot::$sys['now'] > ($row['user_regdate'] + 172800) ||
                $token != $row['user_token']
            )
        ) {
            Cot::$env['status'] = '403 Forbidden';
			cot_log('Failed user validation login attempt : ' . $rusername, 'users', 'login', 'error');
			cot_redirect(cot_url('message', 'msg=157', '', true));
		}
		if ($row['user_maingrp'] < 1) {
            Cot::$env['status'] = '403 Forbidden';
			cot_log("Log in attempt, user inactive : " . $rusername, 'users', 'login', 'error');
			cot_redirect(cot_url('message', 'msg=152', '', true));
		}
		if ($row['user_maingrp'] == COT_GROUP_INACTIVE) {
		    if(!isset(Cot::$cfg['users']['inactive_login']) || !Cot::$cfg['users']['inactive_login']) {
                Cot::$env['status'] = '403 Forbidden';
                cot_log("Log in attempt, user inactive : " . $rusername, 'users', 'login', 'error');
                cot_redirect(cot_url('message', 'msg=152', '', true));
            }
		} elseif ($row['user_maingrp'] == COT_GROUP_BANNED) {
			if (Cot::$sys['now'] > $row['user_banexpire'] && $row['user_banexpire'] > 0) {
				$sql = Cot::$db->update(Cot::$db->users, array('user_maingrp' => '4'),  "user_id={$row['user_id']}");
			} else {
                Cot::$env['status'] = '403 Forbidden';
				cot_log("Log in attempt, user banned : " . $rusername, 'users', 'login', 'error');
				cot_redirect(cot_url('message', 'msg=153&num='.$row['user_banexpire'], '', true));
			}
		}

		$ruserid = $row['user_id'];
		$rdeftheme = $row['user_theme'];
		$rdefscheme = $row['user_scheme'];

		$token = cot_unique(16);

		$sid = hash_hmac('sha256', $rmdpass . $row['user_sidtime'], Cot::$cfg['secret_key']);

		if (
            empty($row['user_sid']) ||
            $row['user_sid'] != $sid ||
            $row['user_sidtime'] + Cot::$cfg['cookielifetime'] < Cot::$sys['now']
        ) {
			// Generate new session identifier
			$sid = hash_hmac('sha256', $rmdpass . Cot::$sys['now'], Cot::$cfg['secret_key']);
			$update_sid = ", user_sid = " . Cot::$db->quote($sid) . ", user_sidtime = " . Cot::$sys['now'];
		} else {
			$update_sid = '';
		}

		if ($validating) {
			$update_lostpass = ', user_lostpass=' . Cot::$db->quote(md5(microtime()));
		} else {
			$update_lostpass = '';
		}

        Cot::$db->query('UPDATE ' . Cot::$db->users . " SET user_lastip='" . Cot::$usr['ip'] .
            "', user_lastlog = " . Cot::$sys['now'] . ", user_logcount = user_logcount + 1, user_token = '$token' " .
            "$update_lostpass $update_sid WHERE user_id={$row['user_id']}");

		// Hash the sid once more so it can't be faked even if you  know user_sid
		$sid = hash_hmac('sha1', $sid, Cot::$cfg['secret_key']);

		$u = base64_encode($ruserid . ':' . $sid);

		if ($rremember) {
			cot_setcookie(Cot::$sys['site_id'], $u, time() + Cot::$cfg['cookielifetime'], Cot::$cfg['cookiepath'],
                Cot::$cfg['cookiedomain'], Cot::$sys['secure'], true);
			unset($_SESSION[Cot::$sys['site_id']]);
		} else {
			$_SESSION[Cot::$sys['site_id']] = $u;
		}

		/* === Hook === */
		foreach (cot_getextplugins('users.auth.check.done') as $pl) {
			include $pl;
		}
		/* ===== */

		cot_uriredir_apply(Cot::$cfg['redirbkonlogin']);
		cot_uriredir_redirect(empty($redirect) ? cot_url('index') : base64_decode($redirect));

	} else {
        Cot::$env['status'] = '401 Unauthorized';
		cot_shield_update(7, "Log in");
		cot_log("Log in failed, user : " . $rusername, 'users', 'login', 'error');

		/* === Hook === */
		foreach (cot_getextplugins('users.auth.check.fail') as $pl) {
			include $pl;
		}
		/* ===== */

		cot_redirect(cot_url('message', 'msg=151', '', true));
	}
}

/* === Hook === */
foreach (cot_getextplugins('users.auth.main') as $pl) {
	include $pl;
}
/* ===== */

Cot::$out['subtitle'] = Cot::$L['aut_logintitle'];
if (!isset(Cot::$out['head'])) {
    Cot::$out['head'] = '';
}
Cot::$out['head'] .= Cot::$R['code_noindex'];
require_once Cot::$cfg['system_dir'] . '/header.php';
$mskin = file_exists(cot_tplfile('login', 'core')) ?
    cot_tplfile('login', 'core') : cot_tplfile('users.auth', 'module');
$t = new XTemplate($mskin);

require_once cot_incfile('forms');

if (Cot::$cfg['maintenance']) {
	$t->assign(array('USERS_AUTH_MAINTENANCERES' => Cot::$cfg['maintenancereason']));
	$t->parse('MAIN.USERS_AUTH_MAINTENANCE');
}

$t->assign(array(
	'USERS_AUTH_TITLE' => Cot::$L['aut_logintitle'],
	'USERS_AUTH_SEND' => cot_url('login', 'a=check' . (empty($redirect) ? '' : "&redirect=$redirect")),
	'USERS_AUTH_USER' => cot_inputbox('text', 'rusername', $rusername, array('size' => '12', 'maxlength' => '100')),
	'USERS_AUTH_PASSWORD' => cot_inputbox('password', 'rpassword', '', array('size' => '12', 'maxlength' => '32')),
	'USERS_AUTH_REGISTER' => cot_url('users', 'm=register'),
	'USERS_AUTH_REMEMBER' => Cot::$cfg['forcerememberme'] ? Cot::$R['form_guest_remember_forced'] : Cot::$R['form_guest_remember']
));

/* === Hook === */
foreach (cot_getextplugins('users.auth.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
