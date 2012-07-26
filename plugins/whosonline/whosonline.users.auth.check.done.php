<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.auth.check.done
[END_COT_EXT]
==================== */

/**
 * Removes a guest from online table on login
 *
 * @package whosonline
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$db->delete($db_online, "online_userid < 1 AND online_ip='".$usr['ip']."' LIMIT 1");
$db->insert($db_online, array(
	'online_ip' => $usr['ip'],
	'online_name' => $row['user_name'],
	'online_lastseen' => (int)$sys['now'],
	'online_location' => $env['location'],
	'online_subloc' => (string) $sys['sublocation'],
	'online_userid' => (int)$row['user_id'],
	'online_shield' => 0,
	'online_hammer' => 0
	));
?>