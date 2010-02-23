<?php
/**
 * Authorization Management API
 * Basic Auth functions are located in functions.php
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

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
	COT_GROUP_GUESTS => 'W12345A',
	COT_GROUP_INACTIVE => 'W12345A',
	COT_GROUP_BANNED => 'RW12345A',
	COT_GROUP_MEMBERS => 'A',
	COT_GROUP_SUPERADMINS => 'RW12345A'
);

/**
 * Registers a user group in auth table
 *
 * @param int $group_id Group ID
 * @param int $base_group_id ID of the group to copy permissions from
 * @return bool Operation status
 */
function sed_auth_add_group($group_id, $base_group_id = COT_GROUP_MEMBERS)
{
	global $db_auth, $usr;
	if ($group_id <= COT_GROUP_SUPERADMINS)
	{
		return false;
	}
	if ($base_group_id <= 0)
	{
		$base_group_id = COT_GROUP_MEMBERS;
	}
	sed_sql_query("INSERT INTO $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid)
		SELECT $group_id, auth_code, auth_option, auth_rights, auth_rights_lock, {$usr['id']}
			FROM $db_auth WHERE auth_groupid = $base_group_id");
	sed_auth_reorder();
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
 * sed_auth_add_item('test', 'item123', $auth_permit, $auth_lock);
 * </code>
 * 
 * @param string $module_name The module object belongs to
 * @param string $item_id Object identifier within the module
 * @param array $auth_permit Allowed permissions map
 * @param array $auth_lock Locked permissions map
 * @return int Number of rows inserted
 */
function sed_auth_add_item($module_name, $item_id, $auth_permit = array(), $auth_lock = array())
{
	global $sed_groups, $db_auth, $usr, $cot_auth_default_permit, $cot_auth_default_lock;
	$auth_permit = $auth_permit + $cot_auth_default_permit;
	$auth_lock = $auth_lock + $cot_auth_default_lock;
	$ins_array = array();
	foreach ($sed_groups as $k => $v)
	{
		$base_grp = $k > COT_GROUP_SUPERADMINS ? COT_GROUP_DEFAULT : $k;
		$ins_array[] = array(
			'groupid' => $k,
			'code' => $module_name,
			'option' => $item_id,
			'rights' => sed_auth_getvalue($auth_permit[$base_grp]),
			'rights_lock' => sed_auth_getvalue($auth_lock[$base_grp]),
			'setbyuserid' => $usr['id']
		);
	}
	$res = sed_sql_insert($db_auth, $ins_array, 'auth_');
	sed_auth_reorder();
	sed_auth_clear('all');
	return $res;
}

/**
 * Clears user permissions cache
 *
 * @param mixed $id User ID or 'all'
 * @return int Number of items affected
 */
function sed_auth_clear($id = 'all')
{
	global $db_users, $cfg, $cot_cache;

	if ($id == 'all')
	{
		sed_sql_query("UPDATE $db_users SET user_auth=''");
		$cot_cache && $cot_cache->db_unset('sed_guest_auth', 'system');
	}
	else
	{
		sed_sql_query("UPDATE $db_users SET user_auth='' WHERE user_id='$id'");
	}
	return sed_sql_affectedrows();
}

/**
 * Returns an access character mask for a given access byte
 *
 * @param int $rn Permission byte
 * @return string
 */
function sed_auth_getmask($rn)
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
function sed_auth_getvalue($mask)
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
 */
function sed_auth_remove_group($group_id)
{
	global $db_auth;

	sed_sql_query("DELETE FROM $db_auth WHERE auth_groupid=$group_id");
	return sed_sql_affectedrows();
}

/**
 * Removes an object from ACL
 *
 * @param string $module_name The module object belongs to
 * @param string $item_id Object identifier within the module. If omitted, all objects will be removed.
 * @return int Number of records removed
 */
function sed_auth_remove_item($module_name, $item_id = null)
{
	global $db_auth;

	$opt = is_null($item_id) ? '' : "AND auth_option='$item_id'";
	sed_sql_query("DELETE FROM $db_auth WHERE auth_code='$module_name' $opt");
	return sed_sql_affectedrows();
}

/**
 * Optimizes auth table by sorting its rows
 */
function sed_auth_reorder()
{
    global $db_auth;

    $sql = sed_sql_query("ALTER TABLE $db_auth ORDER BY auth_code ASC, auth_option ASC, auth_groupid ASC, auth_code ASC");
}
?>
