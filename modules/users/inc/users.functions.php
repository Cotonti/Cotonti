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
 * @param string $maingrp Custom main grp
 * @param float $sendemail Send email if need activation
 * @return int New user ID or false
 * @global CotDB $db
 */
function cot_add_user($ruser, $email = null, $name = null, $password = null, $maingrp = null, $sendemail = true)
{
	global $db, $db_users, $db_groups_users, $db_x, $L, $R, $uploadfiles;

	$ruser['user_email'] = (!empty($email)) ? $email : $ruser['user_email'];
	$ruser['user_name'] = (!empty($name)) ? $name : $ruser['user_name'];
	$ruser['user_password'] = (!empty($password)) ? $password : $ruser['user_password'];

	(empty($ruser['user_password'])) && $ruser['user_password'] = cot_randomstring();
	(empty($ruser['user_name'])) && $ruser['user_name'] = $ruser['user_email'];
	$password = $ruser['user_password'];

	$user_exists = (bool)$db->query("SELECT user_id FROM $db_users WHERE user_name = ? LIMIT 1", array($ruser['user_name']))->fetch();
	$email_exists = (bool)$db->query("SELECT user_id FROM $db_users WHERE user_email = ? LIMIT 1", array($ruser['user_email']))->fetch();
	if(!cot_check_email($ruser['user_email']) || $user_exists || (!cot::$cfg['useremailduplicate'] && $email_exists))
	{
		return false;
	}

	$ruser['user_gender'] = (in_array($ruser['user_gender'], array('M', 'F'))) ? $ruser['user_gender'] : 'U';
	$ruser['user_country'] = (mb_strlen($ruser['user_country']) < 4) ? $ruser['user_country'] : '';
	$ruser['user_timezone'] = (!$ruser['user_timezone']) ? 'GMT' : $ruser['user_timezone'];

	$ruser['user_maingrp'] = ($db->countRows($db_users) == 0) ? 5 : (cot::$cfg['users']['regnoactivation']) ? 4 : 2;
	$ruser['user_maingrp'] = (int)$maingrp > 0 ? $maingrp : $ruser['user_maingrp'];

	$ruser['user_passsalt'] = cot_unique(16);
	$ruser['user_passfunc'] = empty(cot::$cfg['hashfunc']) ? 'sha256' : cot::$cfg['hashfunc'];
	$ruser['user_password'] = cot_hash($ruser['user_password'], $ruser['user_passsalt'], $ruser['user_passfunc']);

	if(isset($ruser['user_birthdate']))
	{
		if(is_null($ruser['user_birthdate']) || $ruser['user_birthdate'] > cot::$sys['now'])
		{
			$ruser['user_birthdate'] = 'NULL';
		
		} else {
			$ruser['user_birthdate'] = cot_stamp2date($ruser['user_birthdate']);
		}
	}
		
	$ruser['user_lostpass'] = md5(microtime());
	cot_shield_update(20, "Registration");

	$ruser['user_hideemail'] = 1;
	$ruser['user_theme'] = cot::$cfg['defaulttheme'];
	$ruser['user_scheme'] = cot::$cfg['defaultscheme'];
	$ruser['user_lang'] = empty($ruser['user_lang']) ? cot::$cfg['defaultlang'] : $ruser['user_lang'];
	$ruser['user_regdate'] = (int)cot::$sys['now'];
	$ruser['user_logcount'] = 0;
	$ruser['user_lastip'] = empty($ruser['user_lastip']) ? cot::$usr['ip'] : $ruser['user_lastip'];
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
		if (cot::$cfg['users']['regrequireadmin'])
		{
			$subject = $L['aut_regrequesttitle'];
			$body = sprintf($L['aut_regrequest'], $ruser['user_name']);
			$body .= "\n\n".$L['aut_contactadmin'];
			cot_mail($ruser['user_email'], $subject, $body);

			$subject = $L['aut_regreqnoticetitle'];
			$inactive = cot::$cfg['mainurl'].'/'.cot_url('users', 'gm=2&s=regdate&w=desc', '', true);
			$body = sprintf($L['aut_regreqnotice'], $ruser['user_name'], $inactive);
			cot_mail(cot::$cfg['adminemail'], $subject, $body);
		}
		else
		{
			$subject = $L['Registration'];
			$activate = cot::$cfg['mainurl'].'/'.cot_url('users', 'm=register&a=validate&token='.$ruser['user_token'].'&v='.$ruser['user_lostpass'].'&y=1', '', true);
			$deactivate = cot::$cfg['mainurl'].'/'.cot_url('users', 'm=register&a=validate&token='.$ruser['user_token'].'&v='.$ruser['user_lostpass'].'&y=0', '', true);
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
 * @global CotDB $db
 */
function cot_build_groupsms($userid, $edit = FALSE, $maingrp = 0)
{
	global $db, $db_groups, $db_groups_users, $cot_groups, $L, $usr, $R;

	$memberships = $db->query("SELECT gru_groupid FROM $db_groups_users	WHERE gru_userid = ?", array($userid))->fetchAll();
	foreach ($memberships as $row)
	{
		$member[$row['gru_groupid']] = TRUE;
	}

	$res = $R['users_code_grplist_begin'];
	foreach ($cot_groups as $k => $i)
	{
		if ($edit)
		{
			$checked = ($member[$k]) ? ' checked="checked"' : '';
			$checked_maingrp = ($maingrp == $k) ? ' checked="checked"' : '';
			$readonly = ($k == COT_GROUP_GUESTS || $k == COT_GROUP_INACTIVE || $k == COT_GROUP_BANNED
				|| ($k == COT_GROUP_SUPERADMINS && $userid == 1)) ? ' disabled="disabled"' : '';
			$readonly_maingrp = ( $k == COT_GROUP_GUESTS || ($k == COT_GROUP_INACTIVE && $userid == 1)
				|| ($k == COT_GROUP_BANNED && $userid == 1)) ? ' disabled="disabled"' : '';
		}
		if ($member[$k] || $edit)
		{
			if (!$cot_groups[$k]['hidden'] || cot_auth('users', 'a', 'A'))
			{
				$item = '';
				if ($edit)
				{
					$item .= cot_rc('users_input_grplist_radio', array(
						'value' => $k,
						'name' => 'rusermaingrp',
						'checked' => $checked_maingrp,
						'title' => '',
						'attrs' => $readonly_maingrp
					));
					$item .= cot_rc('users_input_grplist_checkbox', array(
						'value' => '1',
						'name' => "rusergroupsms[$k]",
						'checked' => $checked,
						'title' => '',
						'attrs' => $readonly
					));
				}
				$item .= ( $k == COT_GROUP_GUESTS) ? $cot_groups[$k]['name'] : cot_rc_link(cot_url('users', 'gm=' . $k), $cot_groups[$k]['name']);
				$item .= ( $cot_groups[$k]['hidden']) ? ' (' . $L['Hidden'] . ')' : '';
				$rc = ($maingrp == $k) ? 'users_code_grplist_item_main' : 'users_code_grplist_item';
				$res .= cot_rc($rc, array('item' => $item));
			}
		}
	}
	$res .= $R['users_code_grplist_end'];
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
function cot_selectbox_gender($check, $name)
{
	global $L;

	$genlist = array('U', 'M', 'F');
	$titlelist = array();
	foreach ($genlist as $i)
	{
		$titlelist[] = $L['Gender_' . $i];
	}
	return cot_selectbox($check, $name, $genlist, $titlelist, false);
}

/**
 * Fetches user entry from DB
 *
 * @param int $uid   User ID
 * @param bool $cacheitem Use one time session cache
 * @return array
 */
function cot_user_data($uid = 0, $cacheitem = true)
{
	$user = false;

	if (! $uid && cot::$usr['id'] > 0)
	{
		$uid = cot::$usr['id'];
		$user = cot::$usr['profile'];
	}
	if (! $uid) return null;

	static $u_cache = array();

	if ($cacheitem && isset($u_cache[$uid]))
	{
		return $u_cache[$uid];
	}

	if (! $user)
	{
		if (is_array($uid))
		{
			$user = $uid;
			$uid = $user['user_id'];
		}
		else
		{
			if ($uid > 0 && $uid == cot::$usr['id'])
			{
				$user = cot::$usr['profile'];
			}
			else
			{
				$uid = (int) $uid;
				if (! $uid) return null;
				$sql = cot::$db->query("SELECT * FROM ".cot::$db->users." WHERE user_id = ? LIMIT 1", $uid);
				$user = $sql->fetch();
			}
		}
	}

	$cacheitem && $u_cache[$uid] = $user;

	return $user;
}