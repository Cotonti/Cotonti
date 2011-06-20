<?php

/**
 * User Functions
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */
// Requirements
require_once cot_langfile('users', 'core');
require_once cot_incfile('users', 'module', 'resources');

require_once cot_incfile('extrafields');

// Extafield globals
$cot_extrafields[$db_users] = (!empty($cot_extrafields[$db_users])) ? $cot_extrafields[$db_users] : array();

/**
 * Add new user
 *
 * @param string $email Email address
 * @param string $name User name; defaults to $email if omitted
 * @param string $password Password; randomly generated if omitted
 * @param string $country 2-char country code
 * @param float $timezone Time zone
 * @param string $gender Gender: M, F or U (unknown)
 * @param int $birthdate Birth date as timestamp
 * @param array $extrafields Extra fields as columnname => value array
 * @return int New user ID
 */
function add_user($email, $name = null, $password = null, $country = '', $timezone = 0.0, $gender = 'U', $birthdate = null, $extrafields = array())
{
	global $cfg, $cot_extrafields, $db, $db_users, $db_groups_users, $db_x, $L, $R, $sys;
	
	if (!is_string($name)) $name = $email;
	if (!is_string($password)) $password = cot_randomstring();
	
	$ruser = array(
		'user_name' => $name,
		'user_email' => $email,
		'user_password' => $password,
		'user_country' => $country,
		'user_timezone' => $timezone,
		'user_gender' => $gender,
		'user_birthdate' => $birthdate
	);
	$ruser = array_merge($extrafields, $ruser);
	
	$ruser['user_maingrp'] = ($db->countRows($db_users) == 0) ? 5 : ($cfg['regnoactivation']) ? 4 : 2;
	$ruser['user_password'] = md5($password);
	$ruser['user_birthdate'] = ($ruser['user_birthdate'] > $sys['now']) ? ($sys['now'] - 31536000) : $ruser['user_birthdate'];
	$ruser['user_birthdate'] = ($ruser['user_birthdate'] == '0') ? '0000-00-00' : cot_stamp2date($ruser['user_birthdate']);
	$ruser['user_lostpass'] = md5(microtime());
	cot_shield_update(20, "Registration");

	$ruser['user_hideemail'] = 1;
	$ruser['user_theme'] = $cfg['defaulttheme'];
	$ruser['user_scheme'] = $cfg['defaultscheme'];
	$ruser['user_lang'] = $cfg['defaultlang'];
	$ruser['user_regdate'] = (int)$sys['now'];
	$ruser['user_logcount'] = 0;
	$ruser['user_lastip'] = $usr['ip'];

	if (!$db->insert($db_users, $ruser)) return;

	$userid = $db->lastInsertId();

	$db->insert($db_groups_users, array('gru_userid' => (int)$userid, 'gru_groupid' => (int)$ruser['user_maingrp']));
	cot_extrafield_movefiles();
	
	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.register.add.done') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!$cfg['regnoactivation'] && $ruser['user_maingrp'] != 5)
	{
		if ($cfg['regrequireadmin'])
		{
			$subject = $cfg['maintitle']." - ".$L['aut_regrequesttitle'];
			$body = sprintf($L['aut_regrequest'], $ruser['user_name'], $password);
			$body .= "\n\n".$L['aut_contactadmin'];
			cot_mail($ruser['user_email'], $subject, $body);

			$subject = $cfg['maintitle']." - ".$L['aut_regreqnoticetitle'];
			$inactive = $cfg['mainurl'].'/'.cot_url('users', 'gm=2&s=regdate&w=desc', '', true);
			$body = sprintf($L['aut_regreqnotice'], $ruser['user_name'], $inactive);
			cot_mail($cfg['adminemail'], $subject, $body);
		}
		else
		{
			$subject = $cfg['maintitle']." - ".$L['Registration'];
			$activate = $cfg['mainurl'].'/'.cot_url('users', 'm=register&a=validate&v='.$ruser['user_lostpass'].'&y=1', '', true);
			$deactivate = $cfg['mainurl'].'/'.cot_url('users', 'm=register&a=validate&v='.$ruser['user_lostpass'].'&y=0', '', true);
			$body = sprintf($L['aut_emailreg'], $ruser['user_name'], $password, $activate, $deactivate);
			$body .= "\n\n".$L['aut_contactadmin'];
			cot_mail($ruser['user_email'], $subject, $body);
		}
	}
	return $userid;
}

/**
 * Returns group link (button)
 *
 * @param int $grpid Group ID
 * @return string
 */
function cot_build_group($grpid)
{
	if (empty($grpid))
		return '';
	global $cot_groups, $L;

	if ($cot_groups[$grpid]['hidden'])
	{
		if (cot_auth('users', 'a', 'A'))
		{
			return cot_rc_link(cot_url('users', 'gm=' . $grpid), $cot_groups[$grpid]['title'] . ' (' . $L['Hidden'] . ')');
		}
		else
		{
			return $L['Hidden'];
		}
	}
	else
	{
		return cot_rc_link(cot_url('users', 'gm=' . $grpid), $cot_groups[$grpid]['title']);
	}
}

/**
 * Builds list of user's groups, editable or not
 *
 * @param int $userid Edited user ID
 * @param bool $edit Permission
 * @param int $maingrp User main group
 * @return string
 */
function cot_build_groupsms($userid, $edit = FALSE, $maingrp = 0)
{
	global $db, $db_groups_users, $cot_groups, $L, $usr, $R;

	$sql = $db->query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid=$userid");

	while ($row = $sql->fetch())
	{
		$member[$row['gru_groupid']] = TRUE;
	}
	$sql->closeCursor();

	$res = $R['users_code_grplist_begin'];
	foreach ($cot_groups as $k => $i)
	{
		if ($edit)
		{
			$checked = ($member[$k]) ? ' checked="checked"' : '';
			$checked_maingrp = ($maingrp == $k) ? ' checked="checked"' : '';
			$readonly = (!$edit || $usr['level'] < $cot_groups[$k]['level'] || $k == COT_GROUP_GUESTS
					|| $k == COT_GROUP_INACTIVE || $k == COT_GROUP_BANNED || ($k == COT_GROUP_SUPERADMINS && $userid == 1)) ? ' disabled="disabled"' : '';
			$readonly_maingrp = (!$edit || $usr['level'] < $cot_groups[$k]['level'] || $k == COT_GROUP_GUESTS
					|| ($k == COT_GROUP_INACTIVE && $userid == 1) || ($k == COT_GROUP_BANNED && $userid == 1)) ? ' disabled="disabled"' : '';
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
				$item .= ( $k == COT_GROUP_GUESTS) ? $cot_groups[$k]['title'] : cot_rc_link(cot_url('users', 'gm=' . $k), $cot_groups[$k]['title']);
				$item .= ( $cot_groups[$k]['hidden']) ? ' (' . $L['Hidden'] . ')' : '';
				$rc = ($maingrp == $k) ? 'users_code_grplist_item_main' : 'users_code_grplist_item';
				$res .= cot_rc('users_code_grplist_item', array('item' => $item));
			}
		}
	}
	$res .= $R['users_code_grplist_end'];
	return $res;
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
 * Checks whether user is online
 *
 * @param int $id User ID
 * @return bool
 */
function cot_userisonline($id)
{
	global $cot_usersonline;

	$res = FALSE;
	if (is_array($cot_usersonline))
	{
		$res = (in_array($id, $cot_usersonline)) ? TRUE : FALSE;
	}
	return ($res);
}

/**
 * Returns all user tags foк XTemplate
 *
 * @param mixed $user_data User Info Array
 * @param string $tag_prefix Prefix for tags
 * @param string $emptyname Name text if user is not exist
 * @param bool $allgroups Build info about all user groups
 * @param bool $cacheitem Cache tags
 * @return array
 */
function cot_generate_usertags($user_data, $tag_prefix = '', $emptyname='', $allgroups = false, $cacheitem = true)
{
	global $db, $cot_extrafields, $cfg, $L, $cot_yesno, $themelang, $user_cache, $db_users, $usr;

	static $extp_first = null, $extp_main = null;
	
	$return_array = array();

	if (is_null($extp_first))
	{
		$extp_first = cot_getextplugins('usertags.first');
		$extp_main = cot_getextplugins('usertags.main');
	}

	/* === Hook === */
	foreach ($extp_first as $pl)
	{
		include $pl;
	}
	/* ===== */
	
	$user_id = (int) (is_array($user_data) ? $user_data['user_id'] : $user_data);

	if (isset($user_cache[$user_id]))
	{	
		$temp_array = $user_cache[$user_id];
	}
	else
	{
		if (is_int($user_data) && $user_data > 0)
		{
			$sql = $db->query("SELECT * FROM $db_users WHERE user_id = $user_data");
			$user_data = $sql->fetch();
		}

		if (is_array($user_data) && $user_data['user_id'] > 0 && !empty($user_data['user_name']))
		{
			$user_data['user_birthdate'] = cot_date2stamp($user_data['user_birthdate']);
			$user_data['user_text'] = cot_parse($user_data['user_text'], $cfg['usertextimg']);
			
			$temp_array = array(
				'ID' => $user_data['user_id'],
				'NAME' => cot_build_user($user_data['user_id'], htmlspecialchars($user_data['user_name'])),
				'NICKNAME' => htmlspecialchars($user_data['user_name']),
				'DETAILSLINK' => cot_url('users', 'm=details&id=' . $user_data['user_id'].'&u='.htmlspecialchars($user_data['user_name'])),
				'DETAILSLINKSHORT' => cot_url('users', 'm=details&id=' . $user_data['user_id']),
				'MAINGRP' => cot_build_group($user_data['user_maingrp']),
				'MAINGRPID' => $user_data['user_maingrp'],
				'MAINGRPSTARS' => cot_build_stars($cot_groups[$user_data['user_maingrp']]['level']),
				'MAINGRPICON' => cot_build_groupicon($cot_groups[$user_data['user_maingrp']]['icon']),
				'COUNTRY' => cot_build_country($user_data['user_country']),
				'COUNTRYFLAG' => cot_build_flag($user_data['user_country']),
				'TEXT' => $user_data['user_text'],
				'EMAIL' => cot_build_email($user_data['user_email'], $user_data['user_hideemail']),
				'THEME' => $user_data['user_theme'],
				'SCHEME' => $user_data['user_scheme'],
				'GENDER' => ($user_data['user_gender'] == '' || $user_data['user_gender'] == 'U') ? '' : $L['Gender_' . $user_data['user_gender']],
				'BIRTHDATE' => ($user_data['user_birthdate'] != 0) ? cot_date('date_full', $user_data['user_birthdate']) : '',
				'BIRTHDATE_STAMP' => ($user_data['user_birthdate'] != 0) ? $user_data['user_birthdate'] : '',
				'AGE' => ($user_data['user_birthdate'] != 0) ? cot_build_age($user_data['user_birthdate']) : '',
				'TIMEZONE' => cot_build_timezone($user_data['user_timezone']),
				'REGDATE' => cot_date('datetime_medium', $user_data['user_regdate'] + $usr['timezone'] * 3600),
				'REGDATE_STAMP' => $user_data['user_regdate'] + $usr['timezone'] * 3600,
				'LASTLOG' => cot_date('datetime_medium', $user_data['user_lastlog'] + $usr['timezone'] * 3600),
				'LASTLOG_STAMP' => $user_data['user_lastlog'] + $usr['timezone'] * 3600,
				'LOGCOUNT' => $user_data['user_logcount'],
				'POSTCOUNT' => $user_data['user_postcount'],
				'LASTIP' => $user_data['user_lastip'],
				'ONLINE' => (cot_userisonline($user_data['user_id'])) ? '1' : '0',
				'ONLINETITLE' => ($user_data['user_online']) ? $themelang['forumspost']['Onlinestatus1'] : $themelang['forumspost']['Onlinestatus0'],
			);

			if ($allgroups)
			{
				$temp_array['GROUPS'] = cot_build_groupsms($user_data['user_id'], FALSE, $user_data['user_maingrp']);
			}
			// Extra fields
			foreach ($cot_extrafields[$db_users] as $i => $row)
			{
				$temp_array[strtoupper($row['field_name'])] = cot_build_extrafields_data('user', $row, $user_data['user_' . $row['field_name']]);
				$temp_array[strtoupper($row['field_name']) . '_TITLE'] = isset($L['user_' . $row['field_name'] . '_title']) ? $L['user_' . $row['field_name'] . '_title'] : $row['field_description'];
			}
		}
		else
		{
			$temp_array = array(
				'ID' => 0,
				'NAME' => (!empty($emptyname)) ? $emptyname : $L['Deleted'],
				'NICKNAME' => (!empty($emptyname)) ? $emptyname : $L['Deleted'],
				'MAINGRP' => cot_build_group(1),
				'MAINGRPID' => 1,
				'MAINGRPSTARS' => '',
				'MAINGRPICON' => cot_build_groupicon($cot_groups[1]['icon']),
				'COUNTRY' => cot_build_country(''),
				'COUNTRYFLAG' => cot_build_flag(''),
				'TEXT' => '',
				'EMAIL' => '',
				'GENDER' => '',
				'BIRTHDATE' => '',
				'BIRTHDATE_STAMP' => '',
				'AGE' => '',
				'REGDATE' => '',
				'REGDATE_STAMP' => '',
				'POSTCOUNT' => '',
				'LASTIP' => '',
				'ONLINE' => '0',
				'ONLINETITLE' => '',
			);
		}
		
		/* === Hook === */
		foreach ($extp_main as $pl)
		{
			include $pl;
		}
		/* ===== */

		$cacheitem && $user_cache[$user_data['user_id']] = $temp_array;
	}
	foreach ($temp_array as $key => $val)
	{
		$return_array[$tag_prefix . $key] = $val;
	}
	return $return_array;
}

?>
