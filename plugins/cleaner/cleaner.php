<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home
[END_COT_EXT]
==================== */

/**
 * Will clean various things
 *
 * @package Cleaner
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

global $cot_plugins;

$cleanerCache = Cot::$cache->mem ?: Cot::$cache->disk;
$cleanerCacheKey = 'cleaner-last-executed';
if ($cleanerCache) {
    $cleanerLastExecuted = $cleanerCache->get($cleanerCacheKey);
    if ($cleanerLastExecuted && $cleanerLastExecuted > Cot::$sys['now'] - 86400) {
        return;
    }
}

if (Cot::$cfg['plugin']['cleaner']['userprune'] > 0) {
	$timeAgo = Cot::$sys['now'] - (Cot::$cfg['plugin']['cleaner']['userprune'] * 86400);

    $sql = 'SELECT user_id FROM ' . Cot::$db->users
        . ' WHERE (user_maingrp = ' . COT_GROUP_INACTIVE
        . ' OR user_id IN (SELECT gru_userid FROM ' . Cot::$db->groups_users . ' WHERE gru_groupid = ' . COT_GROUP_INACTIVE . ')) '
        . " AND user_lastlog = 0 AND user_regdate < $timeAgo";

	$usersIds = Cot::$db->query($sql)->fetchAll(PDO::FETCH_COLUMN);

    $deleted = 0;
    if (!empty($usersIds)) {
        Cot::$db->getConnection()->beginTransaction();
        try {
            Cot::$db->delete(Cot::$db->groups_users, 'gru_userid IN (' . implode(',', $usersIds) . ')');
            Cot::$db->delete(Cot::$db->users, 'user_id IN (' . implode(',', $usersIds) . ')');
            $deleted = count($usersIds);
            Cot::$db->getConnection()->commit();
        } catch (PDOException $err) {
            Cot::$db->getConnection()->rollBack();
        }
    }

	if ($deleted > 0) {
		cot_log("Cleaner plugin deleted $deleted inactivated user account(s)", 'users', 'clear', 'done');
	}
}

if (Cot::$cfg['plugin']['cleaner']['logprune'] > 0) {
	$timeago = Cot::$sys['now'] - (Cot::$cfg['plugin']['cleaner']['logprune'] * 86400);
	Cot::$db->delete($db_logger, "log_date < $timeago");
	if (Cot::$db->affectedRows > 0) {
		cot_log('Cleaner plugin deleted '.Cot::$db->affectedRows.' log entries older than '.Cot::$cfg['plugin']['cleaner']['logprune'].' days', 'adm', 'log', 'delete');
	}
}

if (Cot::$cfg['plugin']['cleaner']['refprune'] > 0 && cot_plugin_active('referers')) {
	$timeago = Cot::$sys['now'] - (Cot::$cfg['plugin']['cleaner']['refprune'] * 86400);
	Cot::$db->delete($db_referers, "ref_date < $timeago");
	if (Cot::$db->affectedRows > 0) {
		cot_log('Cleaner plugin deleted '.Cot::$db->affectedRows.' referers entries older than '.Cot::$cfg['plugin']['cleaner']['refprune'].' days', 'referers', 'delete', 'done');
	}
}

if (cot_module_active('pm')) {
	require_once cot_incfile('pm', 'module');
	if (Cot::$cfg['plugin']['cleaner']['pmnotread'] > 0) {
		$timeago = Cot::$sys['now'] - (Cot::$cfg['plugin']['cleaner']['pmnotread'] * 86400);
		$sqltmp = Cot::$db->delete($db_pm, "pm_date < $timeago AND pm_tostate=0");
		if (Cot::$db->affectedRows > 0) {
			cot_log("Cleaner plugin deleted ".Cot::$db->affectedRows." PM not read since ".Cot::$cfg['plugin']['cleaner']['pmnotread']." days", 'pm', 'delete', 'done');
		}
	}

	if (Cot::$cfg['plugin']['cleaner']['pmnotarchived'] > 0) {
		$timeago = Cot::$sys['now'] - (Cot::$cfg['plugin']['cleaner']['pmnotarchived'] * 86400);
		$sqltmp = Cot::$db->delete($db_pm, "pm_date < $timeago AND pm_tostate=1");
		if (Cot::$db->affectedRows > 0) {
			cot_log("Cleaner plugin deleted ".Cot::$db->affectedRows." PM not archived since ".Cot::$cfg['plugin']['cleaner']['pmnotarchived']." days", 'pm', 'delete', 'done');
		}
	}

	if (Cot::$cfg['plugin']['cleaner']['pmold'] > 0) {
		$timeago = Cot::$sys['now'] - (Cot::$cfg['plugin']['cleaner']['pmold'] * 86400);
		$sqltmp = Cot::$db->delete($db_pm, "pm_date < $timeago");

		$deleted = Cot::$db->affectedRows;
		if ($deleted > 0) {
			cot_log("Cleaner plugin deleted ".$deleted." PM older than ".Cot::$cfg['plugin']['cleaner']['pmold']." days", 'pm', 'delete', 'done');
		}
	}
}

if ($cleanerCache) {
    $cleanerCache->store($cleanerCacheKey, Cot::$sys['now']);
}
