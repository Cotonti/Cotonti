<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
Tags=header.tpl:{HEADER_WHOSONLINE}
[END_COT_EXT]
==================== */

/**
 * Header part
 *
 * @package whosonline
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Update online track
if ($env['location'] != $sys['online_location']
	|| !empty($sys['sublocaction']) && $sys['sublocaction'] != $sys['online_subloc'])
{
	if ($usr['id'] > 0)
	{
		if (empty($sys['online_location']))
		{
			$db->insert($db_online, array(
				'online_ip' => $usr['ip'],
				'online_name' => $usr['name'],
				'online_lastseen' => (int)$sys['now'],
				'online_location' => $env['location'],
				'online_subloc' => (string) $sys['sublocation'],
				'online_userid' => (int)$usr['id'],
				'online_shield' => 0,
				'online_hammer' => 0
			));
		}
		else
		{
			$db->update($db_online, array(
				'online_lastseen' => $sys['now'],
				'online_location' => $env['location'],
				'online_subloc' => (string) $sys['sublocation'],
				'online_hammer' => (int)$sys['online_hammer']
				), "online_userid=".$usr['id']);
		}
	}
	else
	{
		if (empty($sys['online_location']))
		{
			$db->insert($db_online, array(
				'online_ip' => $usr['ip'],
				'online_name' => 'v',
				'online_lastseen' => (int)$sys['now'],
				'online_location' => $env['location'],
				'online_subloc' => (string) $sys['sublocation'],
				'online_userid' => -1,
				'online_shield' => 0,
				'online_hammer' => 0
			));
		}
		else
		{
			$db->update($db_online, array(
				'online_lastseen' => $sys['now'],
				'online_location' => $env['location'],
				'online_subloc' => (string)$sys['sublocation'],
				'online_hammer' => (int)$sys['online_hammer']
				), "online_ip='".$usr['ip']."'");
		}
	}
}
if ($cache && $cache->mem && $cache->mem->exists('whosonline', 'system'))
{
	$whosonline_data = $cache->mem->get('whosonline', 'system');
	$sys['whosonline_vis_count'] = $whosonline_data['vis_count'];
	$sys['whosonline_reg_count'] = $whosonline_data['reg_count'];
	$out['whosonline_reg_list'] = $whosonline_data['reg_list'];
	unset($whosonline_data);
}
else
{
	$online_timedout = $sys['now'] - $cfg['timedout'];
	$db->delete($db_online, "online_lastseen < $online_timedout");
	$sys['whosonline_vis_count'] = $db->query("SELECT COUNT(*) FROM $db_online WHERE online_name='v'")->fetchColumn();
	$sql_o = $db->query("SELECT DISTINCT o.online_name, o.online_userid FROM $db_online o WHERE o.online_name != 'v' ORDER BY online_name ASC");
	$sys['whosonline_reg_count'] = $sql_o->rowCount();
	$ii_o = 0;
	while ($row_o = $sql_o->fetch())
	{
		$out['whosonline_reg_list'] .= ($ii_o > 0) ? ', ' : '';
		$out['whosonline_reg_list'] .= cot_build_user($row_o['online_userid'], htmlspecialchars($row_o['online_name']));
		$cot_usersonline[] = $row_o['online_userid'];
		$ii_o++;
	}
	$sql_o->closeCursor();
	unset($ii_o, $sql_o, $row_o);
	if ($cache && $cache->mem)
	{
		$whosonline_data = array(
			'vis_count' => $sys['whosonline_vis_count'],
			'reg_count' => $sys['whosonline_reg_count'],
			'reg_list' => $out['whosonline_reg_list']
		);
		$cache->mem->store('whosonline', $whosonline_data, 'system', 30);
	}
}
$sys['whosonline_all_count'] = $sys['whosonline_reg_count'] + $sys['whosonline_vis_count'];
$out['whosonline'] = ($cfg['disablewhosonline']) ? '' : cot_declension($sys['whosonline_reg_count'], $Ls['Members']).', '.cot_declension($sys['whosonline_vis_count'], $Ls['Guests']);

// Assign online tag
$t->assign('HEADER_WHOSONLINE', $out['whosonline']);

?>
