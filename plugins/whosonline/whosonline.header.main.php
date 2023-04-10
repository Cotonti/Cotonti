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

if (!defined('WHOSONLINE_UPDATED')) {
	// Update online track
    $onlineLocation = isset(Cot::$sys['online_location']) ? Cot::$sys['online_location'] : null;
    $subLocation = isset(Cot::$sys['sublocation']) ? Cot::$sys['sublocation'] : null;
    $location = isset(Cot::$env['location']) ? Cot::$env['location'] : null;

	if ($location != $onlineLocation || $subLocation != Cot::$sys['online_subloc']) {
		if (Cot::$usr['id'] > 0) {
			if (empty($sys['online_location'])) {
                Cot::$db->insert($db_online, array(
                    'online_ip' => Cot::$usr['ip'],
                    'online_name' => Cot::$usr['name'],
                    'online_lastseen' => (int) Cot::$sys['now'],
                    'online_location' => $location,
                    'online_subloc' => (string) $subLocation,
                    'online_userid' => (int) Cot::$usr['id'],
                    'online_shield' => 0,
                    'online_hammer' => 0
				));

			} else {
			    $onlineHummer = isset(Cot::$sys['online_hammer']) ? (int) Cot::$sys['online_hammer'] : 0;
                Cot::$db->update($db_online, array(
                    'online_lastseen' => Cot::$sys['now'],
                    'online_location' => $location,
                    'online_subloc' => (string) $subLocation,
                    'online_hammer' => $onlineHummer
				), "online_userid=".Cot::$usr['id']);
			}

		} elseif(!Cot::$cfg['plugin']['whosonline']['disable_guests']) {
			if (empty($sys['online_location'])) {
				Cot::$db->insert($db_online, array(
                    'online_ip' => Cot::$usr['ip'],
                    'online_name' => 'v',
                    'online_lastseen' => (int) Cot::$sys['now'],
                    'online_location' => $location,
                    'online_subloc' => isset(Cot::$sys['sublocation']) ? (string) Cot::$sys['sublocation'] : '',
                    'online_userid' => -1,
                    'online_shield' => 0,
                    'online_hammer' => 0
				));

			} else {
				Cot::$db->update($db_online, array(
                    'online_lastseen' => Cot::$sys['now'],
                    'online_location' => $location,
                    'online_subloc' => isset(Cot::$sys['sublocation']) ? (string) Cot::$sys['sublocation'] : '',
                    'online_hammer' => isset(Cot::$sys['online_hammer']) ? (int) Cot::$sys['online_hammer'] : 0
				), "online_ip='".Cot::$usr['ip']."' AND online_userid < 0");
			}
		}
	}

	// Assign online tag
	$t->assign('HEADER_WHOSONLINE', Cot::$out['whosonline']);
	define('WHOSONLINE_UPDATED', true);
}
