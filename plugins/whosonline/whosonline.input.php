<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=input
Order=10
[END_COT_EXT]
==================== */

/**
 * Who's online (part 1)
 *
 * @package whosonline
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('whosonline', 'plug');

if ($usr['id'] > 0)
{
	$sql = $db->query("SELECT * FROM $db_online WHERE online_userid=".$usr['id']);

	if ($sql->rowCount() == 1)
	{
		$online_row = $sql->fetch();
		$online_count = 1;
		$sys['online_location'] = $online_row['online_location'];
		$sys['online_subloc'] = $online_row['online_subloc'];
	}
	$sql->closeCursor();
}
elseif(!$cfg['plugin']['whosonline']['disable_guests'])
{
	$sql = $db->query("SELECT * FROM $db_online WHERE online_ip='".$usr['ip']."' LIMIT 1");

	if ($sql->rowCount() > 0)
	{
		$online_row = $sql->fetch();
		$sys['online_location'] = $online_row['online_location'];
		$sys['online_subloc'] = $online_row['online_subloc'];
	}
	$sql->closeCursor();
}

if ($cache && $cache->mem && $cache->mem->exists('whosonline', 'system'))
{
	$whosonline_data = $cache->mem->get('whosonline', 'system');
	$sys['whosonline_vis_count'] = $whosonline_data['vis_count'];
	$sys['whosonline_reg_count'] = $whosonline_data['reg_count'];
	$out['whosonline_reg_list'] = $whosonline_data['reg_list'];
	$cot_usersonline = $whosonline_data['user_list'];
	unset($whosonline_data);
}
else
{
	$online_timedout = $sys['now'] - $cfg['timedout'];
	$db->delete($db_online, "online_lastseen < $online_timedout");
	if(!$cfg['plugin']['whosonline']['disable_guests'])
		$sys['whosonline_vis_count'] = $db->query("SELECT COUNT(*) FROM $db_online WHERE online_name='v'")->fetchColumn();
	$sql_o = $db->query("SELECT DISTINCT o.online_name, o.online_userid FROM $db_online o WHERE o.online_name != 'v' ORDER BY online_name ASC");
	$sys['whosonline_reg_count'] = $sql_o->rowCount();
	$ii_o = 0;
	$cot_usersonline = array();
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
			'reg_list' => $out['whosonline_reg_list'],
			'user_list' => $cot_usersonline
		);
		$cache->mem->store('whosonline', $whosonline_data, 'system', 30);
	}
}

?>