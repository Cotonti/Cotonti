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

// Extafield globals
$cot_extrafields['users'] = (!empty($cot_extrafields[$db_users])) ? $cot_extrafields[$db_users] : array();

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

	if (is_array($user_data) && is_array($user_cache[$user_data['user_id']]))
	{	
		$temp_array = $user_cache[$user_data['user_id']];
	}
	elseif (is_int($user_data) && is_array($user_cache[$user_data]))
	{
		$temp_array = $user_cache[$user_data];
	}
	else
	{
		if (!is_array($user_data))
		{
			$sql = $db->query("SELECT * FROM $db_users WHERE user_id = '" . (int) $user_data . "' LIMIT 1");
			$user_data = $sql->fetch();
		}

		if ($user_data['user_id'] > 0 && !empty($user_data['user_name']))
		{

			$user_data['user_birthdate'] = cot_date2stamp($user_data['user_birthdate']);
			$user_data['user_text'] = cot_parse($user_data['user_text'], $cfg['usertextimg']);
			
			$temp_array = array(
				'ID' => $user_data['user_id'],
				'NAME' => cot_build_user($user_data['user_id'], htmlspecialchars($user_data['user_name'])),
				'NICKNAME' => htmlspecialchars($user_data['user_name']),
				'DETAILSLINK' => cot_url('users', 'm=details&id=' . $user_data['user_id']),
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
				'BIRTHDATE' => ($user_data['user_birthdate'] != 0) ? @date($cfg['formatyearmonthday'], $user_data['user_birthdate']) : '',
				'AGE' => ($user_data['user_birthdate'] != 0) ? cot_build_age($user_data['user_birthdate']) : '',
				'TIMEZONE' => cot_build_timezone($user_data['user_timezone']),
				'REGDATE' => @date($cfg['dateformat'], $user_data['user_regdate'] + $usr['timezone'] * 3600) . ' ' . $usr['timetext'],
				'LASTLOG' => @date($cfg['dateformat'], $user_data['user_lastlog'] + $usr['timezone'] * 3600) . ' ' . $usr['timetext'],
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
			foreach ($cot_extrafields['users'] as $i => $row)
			{
				$temp_array[strtoupper($row['field_name'])] = cot_build_extrafields_data('user', $row, $user_data['user_' . $row['field_name']]);
				$temp_array[strtoupper($row['field_name']) . '_TITLE'] = isset($L['user_' . $row['field_name'] . '_title']) ? $L['user_' . $row['field_name'] . '_title'] : $row['field_description'];
			}

			/* === Hook === */
			foreach ($extp_main as $pl)
			{
				include $pl;
			}
			/* ===== */

			$cacheitem && $user_cache[$user_data['user_id']] = $temp_array;
		}
		else
		{
			$temp_array = array(
				'NAME' => (!empty($emptyname)) ? $emptyname : $L['Deleted'],
				'NICKNAME' => (!empty($emptyname)) ? $emptyname : $L['Deleted'],
			);
		}
	}
	foreach ($temp_array as $key => $val)
	{
		$return_array[$tag_prefix . $key] = $val;
	}
	return $return_array;
}

?>
