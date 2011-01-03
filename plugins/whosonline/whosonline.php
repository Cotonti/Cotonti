<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Displays users who are currently online
 *
 * @package whosonline
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

(defined('COT_CODE') || defined('COT_PLUG')) or die('Wrong URL.');

require_once cot_incfile('users', 'module');
require_once cot_incfile('hits', 'plug');

$sql_users = $db->query("
	SELECT DISTINCT u.*, o.*
	FROM $db_online AS o
	INNER JOIN $db_users AS u ON u.user_id=o.online_userid
	ORDER BY online_lastseen DESC
");
$sql_guests = $db->query("
	SELECT online_ip, online_lastseen, online_location, online_subloc
	FROM $db_online
	WHERE online_userid = -1
	ORDER BY online_lastseen DESC
");
$sql_stats = $db->query("
	SELECT stat_value
	FROM $db_stats
	WHERE stat_name='maxusers'
	LIMIT 1
");
$stats = $sql_stats->fetch();
$maxusers = $stats[0];
$count_users = 0;
$count_guests = 0;

if(cot_plugin_active('hiddengroups'))
{
	require_once cot_incfile('hiddengroups', 'plug');
	$hiddenusers = cot_hiddengroups_get(cot_hiddengroups_mode(), 'users');
}
$ipsearch = cot_plugin_active('ipsearch');

while ($row = $sql_users->fetch())
{
	if($hiddenusers && in_array($row['user_id'], $hiddenusers))
	{
		if(cot_auth('plug', 'hiddengroups', '1'))
		{
			$t->assign('USER_HIDDEN', $L['Hidden']);
		}
		else continue;
	}
	$count_users++;
	$t->assign(array(
		'USER_LOCATION' => htmlspecialchars($row['online_location']),
		'USER_SUBLOCATION' => htmlspecialchars($row['online_subloc']),
		'USER_IP' => ($ipsearch) ? cot_rc_link(cot_url('admin',
			'm=other&p=ipsearch&a=search&id='.$row['online_ip'].'&'.cot_xg()),
			$row['online_ip']) : $row['online_ip'],
		'USER_LINK' => cot_build_user($row['online_userid'], htmlspecialchars($row['online_name'])),
		'USER_LASTSEEN' => cot_build_timegap($row['online_lastseen'], $sys['now'])
	));
	$t->assign(cot_generate_usertags($row, 'USER_'));
	$t->parse('MAIN.USERS');
}

while ($row = $sql_guests->fetch())
{
	$count_guests++;
	$t->assign(array(
		'GUEST_LOCATION' => htmlspecialchars($row['online_location']),
		'GUEST_SUBLOCATION' => htmlspecialchars($row['online_subloc']),
		'GUEST_IP' => ($ipsearch) ? cot_rc_link(cot_url('admin',
			'm=other&p=ipsearch&a=search&id='.$row['online_ip'].'&'.cot_xg()),
			$row['online_ip']) : $row['online_ip'],
		'GUEST_NUMBER' => $count_guests,
		'GUEST_LASTSEEN' => cot_build_timegap($row['online_lastseen'], $sys['now'])
	));
	$t->parse('MAIN.GUESTS');
}

$t->assign(array(
	'STAT_MAXUSERS' => $maxusers,
	'STAT_COUNT_USERS' => $count_users,
	'STAT_COUNT_GUESTS' => $count_guests,
	'USERS' => cot_declension($count_users, $Ls['Members'], true),
	'GUESTS' => cot_declension($count_guests, $Ls['Guests'], true)
));

?>