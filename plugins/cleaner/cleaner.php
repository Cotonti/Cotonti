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

if (cot::$cfg['plugin']['cleaner']['userprune'] > 0) {
	$timeago = cot::$sys['now'] - (cot::$cfg['plugin']['cleaner']['userprune'] * 86400);
	$sqltmp1 = cot::$db->query("SELECT user_id FROM $db_users WHERE user_maingrp = '2' AND user_lastlog = '0' AND user_regdate < $timeago");

	while ($row = $sqltmp1->fetch()) {
		cot::$db->delete($db_users, "user_id='".$row['user_id']."'");
		cot::$db->delete($db_groups_users, "gru_userid='".$row['user_id']."'");
	}
	$sqltmp1->closeCursor();

	cot::$db->delete($db_users, "user_maingrp = '2' AND user_lastlog = '0' AND user_regdate < $timeago");
	$deleted = cot::$db->affectedRows;

	if ($deleted > 0) {
		cot_log("Cleaner plugin deleted ".$deleted." inactivated user account(s)", 'adm');
	}
}

if (cot::$cfg['plugin']['cleaner']['logprune'] > 0) {
	$timeago = cot::$sys['now'] - (cot::$cfg['plugin']['cleaner']['logprune'] * 86400);
	cot::$db->delete($db_logger, "log_date < $timeago");
	if (cot::$db->affectedRows > 0) {
		cot_log('Cleaner plugin deleted '.cot::$db->affectedRows.' log entries older than '.cot::$cfg['plugin']['cleaner']['logprune'].' days', 'adm');
	}
}

if (cot::$cfg['plugin']['cleaner']['refprune'] > 0 && cot_plugin_active('referers')) {
	$timeago = cot::$sys['now'] - (cot::$cfg['plugin']['cleaner']['refprune'] * 86400);
	cot::$db->delete($db_referers, "ref_date < $timeago");
	if (cot::$db->affectedRows > 0) {
		cot_log('Cleaner plugin deleted '.cot::$db->affectedRows.' referers entries older than '.cot::$cfg['plugin']['cleaner']['refprune'].' days', 'adm');
	}
}

if (cot_module_active('pm')) {
	require_once cot_incfile('pm', 'module');
	if (cot::$cfg['plugin']['cleaner']['pmnotread'] > 0) {
		$timeago = cot::$sys['now'] - (cot::$cfg['plugin']['cleaner']['pmnotread'] * 86400);
		$sqltmp = cot::$db->delete($db_pm, "pm_date < $timeago AND pm_tostate=0");
		if (cot::$db->affectedRows > 0) {
			cot_log("Cleaner plugin deleted ".cot::$db->affectedRows." PM not read since ".cot::$cfg['plugin']['cleaner']['pmnotread']." days", 'adm');
		}
	}

	if (cot::$cfg['plugin']['cleaner']['pmnotarchived'] > 0) {
		$timeago = cot::$sys['now'] - (cot::$cfg['plugin']['cleaner']['pmnotarchived'] * 86400);
		$sqltmp = cot::$db->delete($db_pm, "pm_date < $timeago AND pm_tostate=1");
		if (cot::$db->affectedRows > 0) {
			cot_log("Cleaner plugin deleted ".cot::$db->affectedRows." PM not archived since ".cot::$cfg['plugin']['cleaner']['pmnotarchived']." days", 'adm');
		}
	}

	if (cot::$cfg['plugin']['cleaner']['pmold'] > 0) {
		$timeago = cot::$sys['now'] - (cot::$cfg['plugin']['cleaner']['pmold'] * 86400);
		$sqltmp = cot::$db->delete($db_pm, "pm_date < $timeago");

		$deleted = cot::$db->affectedRows;
		if ($deleted > 0) {
			cot_log("Cleaner plugin deleted ".$deleted." PM older than ".cot::$cfg['plugin']['cleaner']['pmold']." days", 'adm');
		}
	}
}
