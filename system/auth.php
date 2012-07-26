<?php
/**
 * Authorization Management API
 * Basic Auth functions are located in functions.php
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Default allowed permissions map. If some value is missing in user-defined
 * permission map, it will be taken from this one.
 */
$cot_auth_default_permit = array(
	COT_GROUP_DEFAULT => 'RW',
	COT_GROUP_GUESTS => 'R',
	COT_GROUP_INACTIVE => 'R',
	COT_GROUP_BANNED => '0',
	COT_GROUP_MEMBERS => 'RW',
	COT_GROUP_SUPERADMINS => 'RW12345A'
);

/**
 * Default disabled (locked) permissions map. If some value is missing
 * in user-defined permission lock map, it will be taken from this one.
 */
$cot_auth_default_lock = array(
	COT_GROUP_DEFAULT => '0',
	COT_GROUP_GUESTS => 'A',
	COT_GROUP_INACTIVE => 'W12345A',
	COT_GROUP_BANNED => 'RW12345A',
	COT_GROUP_MEMBERS => '0',
	COT_GROUP_SUPERADMINS => 'RW12345A'
);

/**
 * Registers a user group in auth table
 *
 * @param int $group_id Group ID
 * @param int $base_group_id ID of the group to copy permissions from
 * @return bool Operation status
 * @global CotDB $db
 */
function cot_auth_add_group($group_id, $base_group_id = COT_GROUP_MEMBERS)
{
	global $db, $db_auth, $usr;
	if ($group_id <= COT_GROUP_SUPERADMINS)
	{
		return false;
	}
	if ($base_group_id <= 0)
	{
		$base_group_id = COT_GROUP_MEMBERS;
	}
	$db->query("INSERT INTO $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid)
		SELECT $group_id, auth_code, auth_option, auth_rights, auth_rights_lock, {$usr['id']}
			FROM $db_auth WHERE auth_groupid = $base_group_id");
	cot_auth_reorder();
	return true;
}

/**
 * Adds a new object into access control lists
 *
 * Usage example:
 * <code>
 * $auth_permit = array(
 *     COT_GROUP_DEFAULT => 'R',
 *     COT_GROUP_GUESTS => '0',,
 *     COT_GROUP_MEMBERS => 'R',
 *     12 => 'RW', // allows Read & Write for group with ID = 12
 * );
 *
 * $auth_lock = array(
 *     COT_GROUP_DEFAULT => 'A',
 *     COT_GROUP_GUESTS => 'W12345A',
 *     COT_GROUP_MEMBERS => 'A',
 *     12 => 'R', // cannot change Read for group with ID = 12
 * );
 *
 * cot_auth_add_item('test', 'item123', $auth_permit, $auth_lock);
 * </code>
 *
 * @param string $module_name The module object belongs to
 * @param string $item_id Object identifier within the module
 * @param array $auth_permit Allowed permissions map
 * @param array $auth_lock Locked permissions map
 * @return int Number of rows inserted
 * @global CotDB $db
 */
function cot_auth_add_item($module_name, $item_id, $auth_permit = array(), $auth_lock = array())
{
	global $db, $cot_groups, $db_auth, $usr, $cot_auth_default_permit, $cot_auth_default_lock;
	$auth_permit = $auth_permit + $cot_auth_default_permit;
	$auth_lock = $auth_lock + $cot_auth_default_lock;
	$ins_array = array();
	foreach ($cot_groups as $k => $v)
	{
		if (!$v['skiprights'])
		{
			$base_grp = $k > COT_GROUP_SUPERADMINS ? COT_GROUP_DEFAULT : $k;
			$ins_array[] = array(
				'auth_groupid' => $k,
				'auth_code' => $module_name,
				'auth_option' => $item_id,
				'auth_rights' => cot_auth_getvalue($auth_permit[$base_grp]),
				'auth_rights_lock' => cot_auth_getvalue($auth_lock[$base_grp]),
				'auth_setbyuserid' => $usr['id']
			);
		}
	}
	$res = $db->insert($db_auth, $ins_array);
	cot_auth_reorder();
	cot_auth_clear('all');
	return $res;
}

/**
 * Clears user permissions cache
 *
 * @param mixed $id User ID (int) or 'all'
 * @return int Number of items affected
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_auth_clear($id = 'all')
{
	global $db, $db_users, $cache;

	if (is_numeric($id))
	{
		$db->update($db_users, array('user_auth' => ''), "user_id=$id");
	}
	else
	{
		$db->update($db_users, array('user_auth' => ''), "user_auth != ''");
		$cache && $cache->db->remove('cot_guest_auth', 'system');
	}
	return $db->affectedRows;
}

/**
 * Returns highest level of all groups a user belongs to.
 *
 * @param int $userid User ID
 * @param bool $maingroup Return level of maingroup
 * @return int
 */
function cot_auth_getlevel($userid, $maingroup = false)
{
	global $db, $db_groups, $db_groups_users, $db_users;
	if ($maingroup)
	{
		return (int)$db->query("
			SELECT grp_level FROM $db_groups
			INNER JOIN $db_users
			ON user_maingrp = grp_id
			WHERE user_id = ?
		", array($userid))->fetchColumn();
	}
	else
	{
		return (int)$db->query("
			SELECT MAX(grp_level) FROM $db_groups
			WHERE grp_id IN (
				SELECT gru_groupid
				FROM $db_groups_users
				WHERE gru_userid = ?
			)
		", array($userid))->fetchColumn();
	}
}

/**
 * Returns an access character mask for a given access byte
 *
 * @param int $rn Permission byte
 * @return string
 */
function cot_auth_getmask($rn)
{
    $res = ($rn & 1) ? 'R' : '';
    $res .= (($rn & 2) == 2) ? 'W' : '';
    $res .= (($rn & 4) == 4) ? '1' : '';
    $res .= (($rn & 8) == 8) ? '2' : '';
    $res .= (($rn & 16) == 16) ? '3' : '';
    $res .= (($rn & 32) == 32) ? '4' : '';
    $res .= (($rn & 64) == 64) ? '5' : '';
    $res .= (($rn & 128) == 128) ? 'A' : '';
    return $res;
}

/**
 * Converts an access character mask into a permission byte
 *
 * @param string $mask Access character mask, e.g. 'RW1A'
 * @return int
 */
function cot_auth_getvalue($mask)
{
    $mn['0'] = 0;
    $mn['R'] = 1;
    $mn['W'] = 2;
    $mn['1'] = 4;
    $mn['2'] = 8;
    $mn['3'] = 16;
    $mn['4'] = 32;
    $mn['5'] = 64;
    $mn['A'] = 128;

	$res = 0;
    $masks = str_split($mask);

    foreach ($masks as $k)
    {
        $res += $mn[$k];
    }
    return $res;
}

/**
 * Removes a user group from auth table
 *
 * @param int $group_id Group ID
 * @return int Number of records removed
 * @global CotDB $db
 */
function cot_auth_remove_group($group_id)
{
	global $db, $db_auth;
	$db->delete($db_auth, 'auth_groupid=' . (int)$group_id);
	return $db->affectedRows;
}

/**
 * Removes an object from ACL
 *
 * @param string $module_name The module object belongs to
 * @param string $item_id Object identifier within the module. If omitted, all objects will be removed.
 * @return int Number of records removed
 * @global CotDB $db
 */
function cot_auth_remove_item($module_name, $item_id = null)
{
	global $db, $db_auth;

	$opt = is_null($item_id) ? '' : 'AND auth_option=' . $db->quote($item_id);
	$db->delete($db_auth, "auth_code='$module_name' $opt");
	return $db->affectedRows;
}

/**
 * Optimizes auth table by sorting its rows
 * @global CotDB $db
 */
function cot_auth_reorder()
{
    global $db, $db_auth;

    $sql = $db->query("ALTER TABLE $db_auth ORDER BY auth_code ASC, auth_option ASC, auth_groupid ASC, auth_code ASC");
}
?>
