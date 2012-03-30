<?php
/**
 * Shield API
 *
 * @package shield
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Clears current user action in Who's online.
 *
 * @global CotDB $db
 */
function cot_shield_clearaction()
{
	global $db, $db_online, $usr;
	$db->update($db_online, array('online_action' => ''), 'online_ip="'.$usr['ip'].'"');
}

/**
 * Anti-hammer protection
 *
 * @param int $hammer Hammer rate
 * @param string $action Action type
 * @param int $lastseen User last seen timestamp
 * @return int
 */
function cot_shield_hammer($hammer,$action, $lastseen)
{
	global $cfg, $sys, $usr;

	if ($action=='Hammering')
	{
		cot_shield_protect();
		cot_shield_clearaction();
		cot_stat_inc('totalantihammer');
	}

	if (($sys['now']-$lastseen)<4)
	{
		$hammer++;
		if ($hammer > $cfg['plugin']['shield']['shieldzhammer'])
		{
			cot_shield_update(180, 'Hammering');
			cot_log('IP banned 3 mins, was hammering', 'sec');
			$hammer = 0;
		}
	}
	else
	{
		if ($hammer>0)
		{
			$hammer--;
		}
	}
	return($hammer);
}

/**
 * Warn user of shield protection
 *
 */
function cot_shield_protect()
{
	global $cfg, $sys, $online_count, $shield_limit, $shield_action;

	if ($online_count > 0 && $shield_limit > $sys['now'])
	{
		cot_diefatal(cot_rc('shield_protect', array(
			'sec' => $shield_limit-$sys['now'],
			'action' => $shield_action
		)));
	}
}

/**
 * Updates shield state
 *
 * @param int $shield_add Hammer
 * @param string $shield_newaction New action type
 * @global CotDB $db
 */
function cot_shield_update($shield_add, $shield_newaction)
{
	global $db, $cfg, $usr, $sys, $db_online;

	$shield_newlimit = $sys['now'] + floor($shield_add * $cfg['plugin']['shield']['shieldtadjust'] /100);
	$db->update($db_online, array('online_shield' => $shield_newlimit, 'online_action' => $shield_newaction), 'online_ip="'.$usr['ip'].'"');
}

?>
