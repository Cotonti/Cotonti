<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home
[END_COT_EXT]
==================== */

/**
 * Will clean various things
 *
 * @package cleaner
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['cleaner']['userprune'] > 0)
{
	$timeago = $sys['now'] - ($cfg['plugin']['cleaner']['userprune'] * 86400);
	$sqltmp1 = $db->query("SELECT user_id FROM $db_users WHERE user_maingrp = '2' AND user_lastlog = '0' AND user_regdate < $timeago");

	while ($row = $sqltmp1->fetch())
	{
		$db->delete($db_users, "user_id='".$row['user_id']."'");
		$db->delete($db_groups_users, "gru_userid='".$row['user_id']."'");
	}
	$sqltmp1->closeCursor();

	$db->delete($db_users, "user_maingrp = '2' AND user_lastlog = '0' AND user_regdate < $timeago");
	$deleted = $db->affectedRows;

	if ($deleted > 0)
	{
		cot_log("Cleaner plugin deleted ".$deleted." inactivated user account(s)", 'adm');
	}
}

if ($cfg['plugin']['cleaner']['logprune'] > 0)
{
	$timeago = $sys['now'] - ($cfg['plugin']['cleaner']['logprune'] * 86400);
	$db->delete($db_logger, "log_date < $timeago");
	if ($db->affectedRows > 0)
	{
		cot_log('Cleaner plugin deleted '.$db->affectedRows.' log entries older than '.$cfg['plugin']['cleaner']['logprune'].' days', 'adm');
	}
}

if ($cfg['plugin']['cleaner']['refprune'] > 0 && $cot_plugins['tools']['referers'])
{
	$timeago = $sys['now'] - ($cfg['plugin']['cleaner']['refprune'] * 86400);
	$db->delete($db_referers, "ref_date < $timeago");
	if ($db->affectedRows > 0)
	{
		cot_log('Cleaner plugin deleted '.$db->affectedRows.' referers entries older than '.$cfg['plugin']['cleaner']['refprune'].' days', 'adm');
	}
}

if (cot_module_active('pm'))
{
	require_once cot_incfile('pm', 'module');
	if ($cfg['plugin']['cleaner']['pmnotread'] > 0)
	{
		$timeago = $sys['now'] - ($cfg['plugin']['cleaner']['pmnotread'] * 86400);
		$sqltmp = $db->delete($db_pm, "pm_date < $timeago AND pm_tostate=0");
		if ($db->affectedRows > 0)
		{
			cot_log("Cleaner plugin deleted ".$db->affectedRows." PM not read since ".$cfg['plugin']['cleaner']['pmnotread']." days", 'adm');
		}
	}

	if ($cfg['plugin']['cleaner']['pmnotarchived'] > 0)
	{
		$timeago = $sys['now'] - ($cfg['plugin']['cleaner']['pmnotarchived'] * 86400);
		$sqltmp = $db->delete($db_pm, "pm_date < $timeago AND pm_tostate=1");
		if ($db->affectedRows > 0)
		{
			cot_log("Cleaner plugin deleted ".$db->affectedRows." PM not archived since ".$cfg['plugin']['cleaner']['pmnotarchived']." days", 'adm');
		}
	}

	if ($cfg['plugin']['cleaner']['pmold'] > 0)
	{
		$timeago = $sys['now'] - ($cfg['plugin']['cleaner']['pmold'] * 86400);
		$sqltmp = $db->delete($db_pm, "pm_date < $timeago");

		$deleted = $db->affectedRows;
		if ($deleted > 0)
		{
			cot_log("Cleaner plugin deleted ".$deleted." PM older than ".$cfg['plugin']['cleaner']['pmold']." days", 'adm');
		}
	}
}

?>