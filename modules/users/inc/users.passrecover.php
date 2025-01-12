<?php
/**
 * Sends emails to users so they can recovery their passwords
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $a
 */

use cot\users\UsersHelper;

defined('COT_CODE') or die('Wrong URL');

$v = cot_import('v', 'G', 'TXT');
$email = cot_import('email', 'P', 'TXT');

/* === Hook === */
foreach (cot_getextplugins('users.passrecover.first') as $pl) {
	include $pl;
}
/* ===== */

cot_block(Cot::$usr['id'] === 0);

$msg = '';

if ($a === 'request' && $email !== '') {
	cot_shield_protect();
	$data = Cot::$db->query(
        'SELECT * FROM ' . Cot::$db->users . ' WHERE user_email = ? ORDER BY user_id ASC',
        $email
    )->fetchAll();

    if (empty($data)) {
        cot_shield_update(10, "Password recovery requested");
        $env['status'] = '403 Forbidden';
        cot_log('Pass recovery failed, user email: ' . $email, 'users', 'passrecover', 'error');
        cot_redirect(cot_url('message', 'msg=154', '', true));
    }

    $users = [];
    $userIds = [];

    foreach ($data as $row) {
        $users[$row['user_id']] = $row;
        $users[$row['user_id']]['groups'] = [];
        $userIds[] = $row['user_id'];
    }
    unset($data);

    $groups = Cot::$db->query(
        'SELECT gru_userid, gru_groupid FROM ' . Cot::$db->groups_users
        . ' WHERE gru_userid IN (' . implode(',', $userIds) . ')')
        ->fetchAll();

    if (!empty($groups)) {
        foreach ($groups as $group) {
            $users[$group['gru_userid']]['groups'][] = (int) $group['gru_groupid'];
        }
    }

    $emailFound = false;
    foreach ($users as $user) {
        cot_fillGroupsForUser($user);

        if (in_array(COT_GROUP_INACTIVE, $user['groups'], true)) {
            cot_log(
                'Password recovery failed, user inactive: ' . $user['user_name'],
                'users',
                'passrecover', 'error'
            );
            continue;
        }

        if (in_array(COT_GROUP_BANNED, $user['groups'], true)) {
            cot_log(
                'Password recovery failed, user banned: ' . $user['user_name'],
                'users',
                'passrecover',
                'error'
            );
            continue;
        }

		$validationKey = $user['user_lostpass'];
		if (empty($validationkey) || $validationkey == '0') {
            $validationKey = md5(microtime());
			Cot::$db->update(
                Cot::$db->users,
                ['user_lostpass' => $validationKey, 'user_lastip' => Cot::$usr['ip']],
                'user_id = ?',
                $user['user_id']
            );
		}

		$changePasswordUrl = Cot::$cfg['mainurl'] . '/'
            . cot_url('users', ['m' => 'passrecover', 'a' => 'auth', 'v' => $validationKey], '', true);

        $mailBody = sprintf(
            Cot::$L['pasrec_email1'],
            UsersHelper::getInstance()->getFullName($user),
            $changePasswordUrl,
            Cot::$usr['ip'],
            cot_date('datetime_medium')
        );
        $mailBody .= "\n\n " . Cot::$L['aut_contactadmin'];

        cot_mail($email, Cot::$L['pasrec_title'], $mailBody);

        $emailFound = true;
		if (!Cot::$cfg['useremailduplicate']) {
            break;
        }
	}

    if (!$emailFound) {
        cot_shield_update(10, "Password recovery requested");
        $env['status'] = '403 Forbidden';
        cot_log('Pass recovery failed, user email: ' . $email, 'users', 'passrecover', 'error');
        cot_redirect(cot_url('message', 'msg=154', '', true));
    }

    $msg = 'request';

	cot_shield_update(60, "Password recovery email sent");

} elseif ($a === 'auth' && mb_strlen($v) === 32) {
	cot_shield_protect();

	$user = Cot::$db->query('SELECT * FROM ' . Cot::$db->users . ' WHERE user_lostpass = ? LIMIT 1', $v)->fetch();
    if (!$user) {
        $env['status'] = '403 Forbidden';
        cot_shield_update(7, "Log in");
        cot_log("Pass recovery failed, user lostPass " . $v, 'users', 'passrecover', 'error');
        cot_redirect(cot_url('message', ['msg' => 151], '', true));
    }

    $user['user_id'] = (int) $user['user_id'];
    $user['user_maingrp'] = (int) $user['user_maingrp'];
    $user['user_banexpire'] = (int) $user['user_banexpire'];

    cot_fillGroupsForUser($user);

    if (in_array(COT_GROUP_INACTIVE, $user['groups'], true)) {
        cot_log(
            'Password recovery failed, user inactive: ' . $user['user_name'],
            'users',
            'passrecover', 'error'
        );
        $env['status'] = '403 Forbidden';
        cot_redirect(cot_url('message',  ['msg' => 152], '', true));

    }

    if (in_array(COT_GROUP_BANNED, $user['groups'], true)) {
        cot_log(
            'Password recovery failed, user banned: ' . $user['user_name'],
            'users',
            'passrecover',
            'error'
        );
        $env['status'] = '403 Forbidden';
        cot_redirect(cot_url('message', ['msg' => 153, 'num' => $user['user_banexpire']], '', true));
    }

    $validationKey = md5(microtime());
    $newPassword = cot_randomstring();
    $updateData = [
        'user_passsalt' => cot_unique(16),
        'user_passfunc' => empty(Cot::$cfg['hashfunc']) ? 'sha256' : Cot::$cfg['hashfunc'],
        'user_lostpass' => $validationKey,
    ];
    $updateData['user_password'] = cot_hash($newPassword, $updateData['user_passsalt'], $updateData['user_passfunc']);

    Cot::$db->update(Cot::$db->users, $updateData, 'user_id = ?', $user['user_id']);

    $mailBody = Cot::$L['Hi'] . ' ' . UsersHelper::getInstance()->getFullName($user) . ",\n\n"
        . Cot::$L['pasrec_email2'] . "\n\n" . $newPassword . "\n\n" . Cot::$L['aut_contactadmin'];

    cot_mail($user['user_email'], Cot::$L['pasrec_title'], $mailBody);

    Cot::$sys['uri_redir'] = base64_encode(cot_url('index'));

	$msg = 'auth';
}

Cot::$out['subtitle'] = Cot::$L['pasrec_title'];
Cot::$out['head'] .= Cot::$R['code_noindex'];

$mskin = cot_tplfile('users.passrecover', 'module');

/* === Hook === */
foreach (cot_getextplugins('users.passrecover.main') as $pl) {
	include $pl;
}
/* ===== */

require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate($mskin);

$breadCrumbs = [
    [cot_url('users', ['m' => 'profile']), Cot::$L['pasrec_title']],
];

$t->assign([
	'PASSRECOVER_TITLE' => Cot::$L['pasrec_title'],
    'PASSRECOVER_BREADCRUMBS' => cot_breadcrumbs($breadCrumbs, Cot::$cfg['homebreadcrumb']),
	'PASSRECOVER_URL_FORM' => cot_url('users', ['m' => 'passrecover', 'a' => 'request']),
]);

/* === Hook === */
foreach (cot_getextplugins('users.passrecover.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
