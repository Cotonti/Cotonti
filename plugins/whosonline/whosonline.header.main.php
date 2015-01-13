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
 * @package WhosOnline
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (!defined('WHOSONLINE_UPDATED'))
{
	// Update online track
	if ($env['location'] != $sys['online_location']
					|| !empty($sys['sublocation']) && $sys['sublocation'] != $sys['online_subloc'])
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
		elseif(!$cfg['plugin']['whosonline']['disable_guests'])
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
				), "online_ip='".$usr['ip']."' AND online_userid < 0");
			}
		}
	}

	// Assign online tag
	$t->assign('HEADER_WHOSONLINE', $out['whosonline']);
	define('WHOSONLINE_UPDATED', true);
}
