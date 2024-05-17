<?php

/**
 * User Functions
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
require_once cot_incfile('auth');
require_once cot_langfile('users', 'core');
require_once cot_incfile('users', 'module', 'resources');

require_once cot_incfile('extrafields');

// Extafield globals
cot_extrafields_register_table('users');

/**
 * Adds new user
 *
 * @param array $ruser User data array
 * @param string $email Email address
 * @param string $name User name; defaults to $email if omitted
 * @param string $password Password; randomly generated if omitted
 * @param int $maingrp Custom main grp
 * @param float $sendemail Send email if need activation
 * @return int New user ID or false
 * @global CotDB $db
 */
function cot_add_user($ruser, $email = null, $name = null, $password = null, $maingrp = null, $sendemail = true)
{
	global $db, $db_users, $db_groups_users, $L;

	$ruser['user_email'] = (!empty($email)) ? $email : $ruser['user_email'];
	$ruser['user_name'] = (!empty($name)) ? $name : $ruser['user_name'];
	$ruser['user_password'] = (!empty($password)) ? $password : $ruser['user_password'];

	(empty($ruser['user_password'])) && $ruser['user_password'] = cot_randomstring();
	(empty($ruser['user_name'])) && $ruser['user_name'] = $ruser['user_email'];
	$password = $ruser['user_password'];

	$user_exists = (bool) $db->query("SELECT user_id FROM $db_users WHERE user_name = ? LIMIT 1", array($ruser['user_name']))->fetch();
	$email_exists = (bool) $db->query("SELECT user_id FROM $db_users WHERE user_email = ? LIMIT 1", array($ruser['user_email']))->fetch();
	if (!cot_check_email($ruser['user_email']) || $user_exists || (!Cot::$cfg['useremailduplicate'] && $email_exists)) {
		return false;
	}

	$ruser['user_gender'] = (in_array($ruser['user_gender'], array('M', 'F'))) ? $ruser['user_gender'] : 'U';
    $ruser['user_country'] = (!empty($ruser['user_country']) && mb_strlen($ruser['user_country']) < 4) ?
        $ruser['user_country'] : '';

    $defaultTimeZone = !empty(Cot::$cfg['defaulttimezone']) ? Cot::$cfg['defaulttimezone'] : 'UTC';
    if (!empty($ruser['user_timezone'])) {
        try {
            $tmp = new \DateTimeZone($ruser['user_timezone']);
        } catch (\Exception $e) {
            $ruser['user_timezone'] = $defaultTimeZone;
        }
    } else {
        $ruser['user_timezone'] = $defaultTimeZone;
    }

    $maingrp = (int) $maingrp;
    if ($maingrp > 0) {
        $tmp2 = $maingrp;

    } elseif (Cot::$db->countRows($db_users) == 0) {
        // There is no users in DB.
        $tmp2 = COT_GROUP_SUPERADMINS;

    } elseif(Cot::$cfg['users']['regnoactivation']) {
        $tmp2 = COT_GROUP_MEMBERS;

    } else {
        $tmp2 = COT_GROUP_INACTIVE;
    }
    $ruser['user_maingrp'] = $tmp2;

	$ruser['user_passsalt'] = cot_unique(16);
	$ruser['user_passfunc'] = empty(Cot::$cfg['hashfunc']) ? 'sha256' : Cot::$cfg['hashfunc'];
	$ruser['user_password'] = cot_hash($ruser['user_password'], $ruser['user_passsalt'], $ruser['user_passfunc']);

	if (isset($ruser['user_birthdate'])) {
		if (is_null($ruser['user_birthdate']) || $ruser['user_birthdate'] > Cot::$sys['now']) {
			$ruser['user_birthdate'] = 'NULL';
		} else {
			$ruser['user_birthdate'] = cot_stamp2date($ruser['user_birthdate']);
		}
	}

	$ruser['user_lostpass'] = md5(microtime());
	cot_shield_update(20, "Registration");

	$ruser['user_hideemail'] = 1;
	$ruser['user_theme'] = Cot::$cfg['defaulttheme'];
	$ruser['user_scheme'] = Cot::$cfg['defaultscheme'];
	$ruser['user_lang'] = empty($ruser['user_lang']) ? Cot::$cfg['defaultlang'] : $ruser['user_lang'];
	$ruser['user_regdate'] = (int)Cot::$sys['now'];
	$ruser['user_logcount'] = 0;
	$ruser['user_lastip'] = empty($ruser['user_lastip']) ? Cot::$usr['ip'] : $ruser['user_lastip'];
	$ruser['user_token'] = cot_unique(16);

	if (!$db->insert($db_users, $ruser)) return false;

	$userid = $db->lastInsertId();

	$db->insert($db_groups_users, array('gru_userid' => (int)$userid, 'gru_groupid' => (int)$ruser['user_maingrp']));
	cot_extrafield_movefiles();

	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.adduser.done') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($ruser['user_maingrp'] == 2 && $sendemail)
	{
		if (Cot::$cfg['users']['regrequireadmin'])
		{
			$subject = $L['aut_regrequesttitle'];
			$body = sprintf($L['aut_regrequest'], $ruser['user_name']);
			$body .= "\n\n".$L['aut_contactadmin'];
			cot_mail($ruser['user_email'], $subject, $body);

			$subject = $L['aut_regreqnoticetitle'];
			$inactive = Cot::$cfg['mainurl'].'/'.cot_url('users', 'gm=2&s=regdate&w=desc', '', true);
			$body = sprintf($L['aut_regreqnotice'], $ruser['user_name'], $inactive);
			cot_mail(Cot::$cfg['adminemail'], $subject, $body);
		}
		else
		{
			$subject = $L['Registration'];
			$activate = Cot::$cfg['mainurl'].'/'.cot_url('users', 'm=register&a=validate&token='.$ruser['user_token'].'&v='.$ruser['user_lostpass'].'&y=1', '', true);
			$deactivate = Cot::$cfg['mainurl'].'/'.cot_url('users', 'm=register&a=validate&token='.$ruser['user_token'].'&v='.$ruser['user_lostpass'].'&y=0', '', true);
			$body = sprintf($L['aut_emailreg'], $ruser['user_name'], $activate, $deactivate);
			$body .= "\n\n".$L['aut_contactadmin'];
			cot_mail($ruser['user_email'], $subject, $body);
		}
	}
	return $userid;
}

/**
 * Builds list of user's groups, editable or not
 *
 * @param int $userid Edited user ID
 * @param bool $edit Permission
 * @param int $maingrp User main group
 * @return string
 */
function cot_build_groupsms($userid, $edit = false, $maingrp = 0)
{
	global $cot_groups;

	$memberships = Cot::$db->query(
        'SELECT gru_groupid FROM ' . Cot::$db->groups_users . ' WHERE gru_userid = ?',
        [$userid]
    )->fetchAll();
	foreach ($memberships as $row) {
		$member[(int) $row['gru_groupid']] = true;
	}

	$res = Cot::$R['users_code_grplist_begin'];
	foreach ($cot_groups as $groupId => $i) {
        if (!isset($member[$groupId])) {
            $member[$groupId] = false;
        }

		if ($edit) {
			$checked = ($member[$groupId]) ? ' checked="checked"' : '';
			$checked_maingrp = ($maingrp == $groupId) ? ' checked="checked"' : '';
			$readonly = (
                in_array($groupId, [COT_GROUP_GUESTS, COT_GROUP_INACTIVE], true)
				|| ($groupId === COT_GROUP_SUPERADMINS && $userid == 1)
            )
                ? ' disabled="disabled"'
                : '';
			$readonly_maingrp = (
                $groupId === COT_GROUP_GUESTS
                || ($groupId === COT_GROUP_INACTIVE && $userid == 1)
				|| ($groupId === COT_GROUP_BANNED && $userid == 1)
            )
                ? ' disabled="disabled"'
                : '';
		}

		if ($member[$groupId] || $edit) {
			if (!$cot_groups[$groupId]['hidden'] || cot_auth('users', 'a', 'A')) {
				$item = '';
				if ($edit) {
					$item .= cot_rc(
                        'users_input_grplist_radio',
                        [
                            'value' => $groupId,
                            'name' => 'rusermaingrp',
                            'checked' => $checked_maingrp,
                            'title' => '',
                            'attrs' => $readonly_maingrp,
                        ]
                    );
					$item .= cot_rc(
                       'users_input_grplist_checkbox',
                        [
                            'value' => '1',
                            'name' => "rusergroupsms[$groupId]",
                            'checked' => $checked,
                            'title' => '',
                            'attrs' => $readonly,
                        ]
                    );
				}
				$item .= ( $groupId == COT_GROUP_GUESTS)
                    ? $cot_groups[$groupId]['name']
                    : cot_rc_link(cot_url('users', 'gm=' . $groupId), $cot_groups[$groupId]['name']);
				$item .= ( $cot_groups[$groupId]['hidden']) ? ' (' . Cot::$L['Hidden'] . ')' : '';
				$rc = ($maingrp == $groupId) ? 'users_code_grplist_item_main' : 'users_code_grplist_item';
				$res .= cot_rc($rc, ['item' => $item]);
			}
		}
	}

	$res .= Cot::$R['users_code_grplist_end'];

	return $res;
}

/**
 * Renders user signature text
 *
 * @param string $text Signature text
 * @return string
 */
function cot_build_usertext($text)
{
	global $cfg;
	return cot_parse($text, $cfg['users']['usertextimg']);
}

/**
 * Generates gender dropdown
 *
 * @param string $check Checked gender
 * @param string $name Input name
 * @return string
 */
function cot_selectbox_gender($check, $name, $custom_rc = '')
{
	global $L;

	$genlist = ['U', 'M', 'F'];
	$titlelist = [];
	foreach ($genlist as $i) {
		$titlelist[] = $L['Gender_' . $i];
	}
	return cot_selectbox($check, $name, $genlist, $titlelist, false, '', $custom_rc);
}
