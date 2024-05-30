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

if ($a === 'check') {
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

	if (empty($rremember) && $rcookiettl > 0 || Cot::$cfg['forcerememberme']) {
		$rremember = true;
	}

	$loginParam = !Cot::$cfg['useremailduplicate'] && cot_check_email($rusername)
        ? 'user_email'
        : 'user_name';

	// Load salt and algo from db
    $rmdpass = '';
    if (!empty($rusername) && !empty($rpassword)) {
        $sql = Cot::$db->query(
            'SELECT user_passsalt, user_passfunc FROM ' . Cot::$db->users . " WHERE {$loginParam}=?",
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

    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        /** @deprecated Will be removed in 1.0.0 */
        $user_select_condition = null;
    }

    if (!empty($rusername) && !empty($rmdpass)) {
        $userSelectCondition = "$loginParam = :login_param AND user_password = :password";
        $userSelectParams = ['login_param' => $rusername, 'password' => $rmdpass];
    }

	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.auth.check.query') as $pl) {
		include $pl;
	}
	/* ===== */

    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        // For backwards compatibility. Will be removed in 1.0.0
        if (!empty($user_select_condition)) {
            $userSelectCondition = $user_select_condition;
        }
    }

    if (empty($userSelectCondition)) {
        Cot::$env['status'] = '401 Unauthorized';
        cot_log("Log in failed, user : " . $rusername, 'users', 'login', 'error');
        cot_redirect(cot_url('message', 'msg=151', '', true));
    }

    /** @see cot_user_authorize() */
    $query = 'SELECT user_id,user_name, user_password, user_maingrp, user_banexpire, user_sid, user_sidtime, '
        . 'user_passsalt, user_passfunc FROM ' . Cot::$db->users . ' WHERE ';

	$sql = Cot::$db->query($query . $userSelectCondition, $userSelectParams);

	/*
	    Checking if we got any entries with the current login conditions,
		only may fail when user name has e-mail format or user is not registered,
		added for compatibility, because disallowed using e-mail as login on registration
	*/
	if ($sql->rowCount() == 0) {
		// If login has e-mail format, try to find it as user_name
		$userSelectCondition2 = "user_password = " . Cot::$db->quote($rmdpass)
            . " AND user_name = " . Cot::$db->quote($rusername);

		// Query the database
		$sql = Cot::$db->query($query . $userSelectCondition2);
	}

    $row = $sql->fetch();

    if (!$row) {
        Cot::$env['status'] = '401 Unauthorized';
        cot_shield_update(7, "Log in");
        cot_log('Log in failed, user: ' . $rusername, 'users', 'login', 'error');

        /* === Hook === */
        foreach (cot_getextplugins('users.auth.check.fail') as $pl) {
            include $pl;
        }
        /* ===== */

        cot_redirect(cot_url('message', 'msg=151', '', true));
    }

    $row['user_id'] = (int) $row['user_id'];
    $row['user_maingrp'] = (int) $row['user_maingrp'];

    $authResult = cot_user_authorize($row, $rremember);
    if (!empty($authResult['error'])) {
        switch ($authResult['error']) {
            case COT_USER_AUTH_ERROR_FORBIDDEN:
                Cot::$env['status'] = '403 Forbidden';
                cot_log(
                    'Log in attempt, user inactive: ' . $row['user_name'],
                    'users',
                    'login',
                    'error'
                );
                cot_redirect(cot_url('message', 'msg=152', '', true));
                break;

            case COT_USER_AUTH_ERROR_BANNED:
                Cot::$env['status'] = '403 Forbidden';
				cot_log(
                    'Log in attempt, user banned: ' . $row['user_name'],
                    'users',
                    'login',
                    'error'
                );
				cot_redirect(cot_url('message', 'msg=153&num=' . $row['user_banexpire'], '', true));
                break;

            case COT_USER_AUTH_ERROR_NOT_FOUND:
            default:
                Cot::$env['status'] = '401 Unauthorized';
                cot_shield_update(7, "Log in");
                cot_log("Log in failed, user: " . $row['user_name'], 'users', 'login', 'error');

                /* === Hook === */
                foreach (cot_getextplugins('users.auth.check.fail') as $pl) {
                    include $pl;
                }
                /* ===== */

                cot_redirect(cot_url('message', 'msg=151', '', true));
                break;
        }
    }

    /* === Hook === */
    foreach (cot_getextplugins('users.auth.check.done') as $pl) {
        include $pl;
    }
    /* ===== */

    cot_uriredir_apply(Cot::$cfg['redirbkonlogin']);
    cot_uriredir_redirect(empty($redirect) ? cot_url('index') : base64_decode($redirect));
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
$mskin = file_exists(cot_tplfile('login', 'core'))
    ? cot_tplfile('login', 'core')
    : cot_tplfile('users.auth', 'module');
$t = new XTemplate($mskin);

require_once cot_incfile('forms');

if (Cot::$cfg['maintenance']) {
	$t->assign(array('USERS_AUTH_MAINTENANCERES' => Cot::$cfg['maintenancereason']));
	$t->parse('MAIN.USERS_AUTH_MAINTENANCE');
}

$t->assign([
	'USERS_AUTH_TITLE' => Cot::$L['aut_logintitle'],
	'USERS_AUTH_SEND' => cot_url('login', 'a=check' . (empty($redirect) ? '' : "&redirect=$redirect")),
	'USERS_AUTH_USER' => cot_inputbox('text', 'rusername', $rusername, array('size' => '12', 'maxlength' => '100')),
	'USERS_AUTH_PASSWORD' => cot_inputbox('password', 'rpassword', '', array('size' => '12', 'maxlength' => '32')),
	'USERS_AUTH_REGISTER' => cot_url('users', 'm=register'),
	'USERS_AUTH_REMEMBER' => Cot::$cfg['forcerememberme']
        ? Cot::$R['form_guest_remember_forced']
        : Cot::$R['form_guest_remember'],
]);

/* === Hook === */
foreach (cot_getextplugins('users.auth.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
