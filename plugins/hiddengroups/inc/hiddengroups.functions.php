<?php

/**
 * Hidden groups
 *
 * @package HiddenGroups
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') || defined('COT_PLUG')) or die('Wrong URL.');

/**
 * Get hiding mode as integer.
 *
 * @return int 0, 1 or 2
 */
function cot_hiddengroups_mode()
{
	global $cfg;
	$mode = $cfg['plugin']['hiddengroups']['mode'];
	if($mode == 'Group + Users (maingroup)') return 1;
	if($mode == 'Group + Users (subgroup)') return 2;
	return 0;
}

/**
 * Get an array of hidden group IDs or hidden user IDs
 *
 * @param int $mode 0, 1 or 2 from cot_hiddengroups_mode()
 * @param string $type 'groups' or 'users'
 * @return array
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_hiddengroups_get($mode, $type='groups')
{
	global $cache, $db, $db_users, $db_groups_users, $cot_groups;
	if($mode !== 1 && $mode !== 2) return array();

	if($type == 'users' && $cache && $cache->db->exists('cot_hiddenusers', 'system'))
	{
		$cachedata = $cache->db->get('cot_hiddenusers', 'system');
		if(is_array($cachedata)) return $cachedata;
	}

	$hiddengroups = array();
	foreach ($cot_groups as $grp)
	{
		if($grp['hidden']) $hiddengroups[] = (int)$grp['id'];
	}
	if($type == 'groups') return $hiddengroups;

	if($type == 'users' && !empty($hiddengroups))
	{
		if($mode == 1)
		{
			$sql = $db->query("SELECT user_id FROM $db_users WHERE user_maingrp IN (".implode(',', $hiddengroups).")");
		}
		if($mode == 2)
		{
			$sql = $db->query("SELECT DISTINCT(gru_userid) AS user_id FROM $db_groups_users WHERE gru_groupid IN (".implode(',', $hiddengroups).")");
		}
		$hiddenusers = array();
		while($row = $sql->fetch())
		{
			$hiddenusers[] = (int)$row['user_id'];
		}
		$sql->closeCursor();
		$cache && $cache->db->store('cot_hiddenusers', $hiddenusers, 'system');
		return $hiddenusers;
	}
	return array();
}
