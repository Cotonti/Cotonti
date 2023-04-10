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

if (Cot::$cfg['plugin']['cleaner']['userprune'] > 0) {
	$timeago = Cot::$sys['now'] - (Cot::$cfg['plugin']['cleaner']['userprune'] * 86400);
	$sqltmp1 = Cot::$db->query("SELECT user_id FROM $db_users WHERE user_maingrp = '2' AND user_lastlog = '0' AND user_regdate < $timeago");

	while ($row = $sqltmp1->fetch()) {
		Cot::$db->delete($db_users, "user_id='".$row['user_id']."'");
		Cot::$db->delete($db_groups_users, "gru_userid='".$row['user_id']."'");
	}
	$sqltmp1->closeCursor();

	Cot::$db->delete($db_users, "user_maingrp = '2' AND user_lastlog = '0' AND user_regdate < $timeago");
	$deleted = Cot::$db->affectedRows;

	if ($deleted > 0) {
		cot_log("Cleaner plugin deleted ".$deleted." inactivated user account(s)", 'users', 'clear', 'done');
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
