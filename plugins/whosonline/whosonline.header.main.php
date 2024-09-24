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
    $whosOnlineOnlineLocation = isset(Cot::$sys['online_location']) ? Cot::$sys['online_location'] : null;
    $whosOnlineSubLocation = isset(Cot::$sys['sublocation']) ? Cot::$sys['sublocation'] : null;
    $whosOnlineLocation = isset(Cot::$env['location']) ? Cot::$env['location'] : null;

    // $_SERVER['HTTP_HOST'] and $_SERVER['REQUEST_URI'] are shows real url
    $whosOnlineCurrentUrl = Cot::$sys['scheme'] . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($_SERVER['REQUEST_URI'], '/');
    if (mb_strlen($whosOnlineCurrentUrl) > 500) {
        $whosOnlineCurrentUrl = '';
    }

	if (
        $whosOnlineLocation !== $whosOnlineOnlineLocation
        || $whosOnlineSubLocation !== (Cot::$sys['online_subloc'] ?? null)
    ) {
        $whosOnlineLocationToSave = mb_substr((string) $whosOnlineLocation, 0, 128);
        $whosOnlineSubLocationToSave = mb_substr((string) $whosOnlineSubLocation, 0, 255);

		if (Cot::$usr['id'] > 0) {
			if (empty(Cot::$sys['online_location'])) {
                Cot::$db->insert(
                    Cot::$db->online,
                    [
                        'online_ip' => Cot::$usr['ip'],
                        'online_name' => Cot::$usr['name'],
                        'online_lastseen' => (int) Cot::$sys['now'],
                        'online_location' => $whosOnlineLocationToSave,
                        'online_subloc' => $whosOnlineSubLocationToSave,
                        'online_url' => $whosOnlineCurrentUrl,
                        'online_userid' => (int) Cot::$usr['id'],
                        'online_shield' => 0,
                        'online_hammer' => 0,
				    ]
                );
			} else {
			    $onlineHummer = isset(Cot::$sys['online_hammer']) ? (int) Cot::$sys['online_hammer'] : 0;
                Cot::$db->update(
                    Cot::$db->online,
                    [
                        'online_lastseen' => Cot::$sys['now'],
                        'online_location' => $whosOnlineLocationToSave,
                        'online_subloc' => $whosOnlineSubLocationToSave,
                        'online_url' => $whosOnlineCurrentUrl,
                        'online_hammer' => $onlineHummer,
                    ],
                    'online_userid = ' . Cot::$usr['id']
                );
			}

		} elseif (!Cot::$cfg['plugin']['whosonline']['disable_guests']) {
			if (empty($sys['online_location'])) {
				Cot::$db->insert(
                    Cot::$db->online,
                    [
                        'online_ip' => Cot::$usr['ip'],
                        'online_name' => 'v',
                        'online_lastseen' => (int) Cot::$sys['now'],
                        'online_location' => $whosOnlineLocationToSave,
                        'online_subloc' => $whosOnlineSubLocationToSave,
                        'online_url' => $whosOnlineCurrentUrl,
                        'online_userid' => -1,
                        'online_shield' => 0,
                        'online_hammer' => 0,
                    ]
                );
			} else {
				Cot::$db->update(
                    Cot::$db->online,
                    [
                        'online_lastseen' => Cot::$sys['now'],
                        'online_location' => $whosOnlineLocationToSave,
                        'online_subloc' => $whosOnlineSubLocationToSave,
                        'online_url' => $whosOnlineCurrentUrl,
                        'online_hammer' => isset(Cot::$sys['online_hammer']) ? (int) Cot::$sys['online_hammer'] : 0,
                    ],
                    "online_ip = '" . Cot::$usr['ip'] . "' AND online_userid < 0"
                );
			}
		}
	}

	// Assign online tag
	$t->assign('HEADER_WHOSONLINE', Cot::$out['whosonline']);

	define('WHOSONLINE_UPDATED', true);
}
