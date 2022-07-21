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
    $onlineLocation = isset(cot::$sys['online_location']) ? cot::$sys['online_location'] : null;
    $subLocation = isset(cot::$sys['sublocation']) ? cot::$sys['sublocation'] : null;
    $location = isset(cot::$env['location']) ? cot::$env['location'] : null;

	if ($location != $onlineLocation || $subLocation != cot::$sys['online_subloc']) {
		if (cot::$usr['id'] > 0) {
			if (empty($sys['online_location'])) {
                cot::$db->insert($db_online, array(
                    'online_ip' => cot::$usr['ip'],
                    'online_name' => cot::$usr['name'],
                    'online_lastseen' => (int) cot::$sys['now'],
                    'online_location' => $location,
                    'online_subloc' => (string) $subLocation,
                    'online_userid' => (int) cot::$usr['id'],
                    'online_shield' => 0,
                    'online_hammer' => 0
				));

			} else {
			    $onlineHummer = isset(cot::$sys['online_hammer']) ? (int) cot::$sys['online_hammer'] : 0;
                cot::$db->update($db_online, array(
                    'online_lastseen' => cot::$sys['now'],
                    'online_location' => $location,
                    'online_subloc' => (string) $subLocation,
                    'online_hammer' => $onlineHummer
				), "online_userid=".cot::$usr['id']);
			}

		} elseif(!cot::$cfg['plugin']['whosonline']['disable_guests']) {
			if (empty($sys['online_location'])) {
				cot::$db->insert($db_online, array(
                    'online_ip' => cot::$usr['ip'],
                    'online_name' => 'v',
                    'online_lastseen' => (int) cot::$sys['now'],
                    'online_location' => $location,
                    'online_subloc' => isset(cot::$sys['sublocation']) ? (string) cot::$sys['sublocation'] : '',
                    'online_userid' => -1,
                    'online_shield' => 0,
                    'online_hammer' => 0
				));

			} else {
				cot::$db->update($db_online, array(
                    'online_lastseen' => cot::$sys['now'],
                    'online_location' => $location,
                    'online_subloc' => isset(cot::$sys['sublocation']) ? (string) cot::$sys['sublocation'] : '',
                    'online_hammer' => isset(cot::$sys['online_hammer']) ? (int) cot::$sys['online_hammer'] : 0
				), "online_ip='".cot::$usr['ip']."' AND online_userid < 0");
			}
		}
	}

	// Assign online tag
	$t->assign('HEADER_WHOSONLINE', cot::$out['whosonline']);
	define('WHOSONLINE_UPDATED', true);
}
