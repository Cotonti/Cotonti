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
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') || defined('COT_PLUG')) or die('Wrong URL.');

cot_require('users');
cot_require('hits', true);

if($cfg['plugin']['hiddengroups'])
{
	cot_require('hiddengroups', true);
	$mode = cot_hiddengroups_mode();
}

$sql_users = $db->query("SELECT DISTINCT u.*, o.* FROM $db_online AS o INNER JOIN $db_users AS u ON u.user_id=o.online_userid ORDER BY online_lastseen DESC");
$sql_guests = $db->query("SELECT online_ip, online_lastseen, online_location, online_subloc FROM $db_online WHERE online_userid = -1 ORDER BY online_lastseen DESC");
$sql_stats = $db->query("SELECT stat_value FROM $db_stats where stat_name='maxusers' LIMIT 1");
$total_users = $sql_users->rowCount();
$total_guests = $sql_guests->rowCount();
$stats = $sql_stats->fetch();
$maxusers = $stats[0];
$visitornum = 0;
$visituser = 0;

while ($row = $sql_users->fetch())
{
	$visituser++;
	$t->assign(array(
		'USER_LOCATION' => htmlspecialchars($row['online_location']),
		'USER_SUBLOCATION' => htmlspecialchars($row['online_subloc']),
		'USER_IP' => cot_rc_link(cot_url('admin', 'm=tools&p=ipsearch&a=search&id='.$row['online_ip'].'&'.cot_xg()), $row['online_ip']),
		'USER_LINK' => cot_build_user($row['online_userid'], htmlspecialchars($row['online_name'])),
		'USER_LASTSEEN' => cot_build_timegap($row['online_lastseen'], $sys['now'])
	));
	$t->assign(cot_generate_usertags($row, "USER_"));
	$t->parse('MAIN.USERS');
}

while ($row = $sql_guests->fetch())
{
	$visitornum++;
	$t->assign(array(
		'GUEST_LOCATION' => htmlspecialchars($row['online_location']),
		'GUEST_SUBLOCATION' => htmlspecialchars($row['online_subloc']),
		'GUEST_IP' => cot_rc_link(cot_url('admin', 'm=tools&p=ipsearch&a=search&id='.$row['online_ip'].'&'.cot_xg()), $row['online_ip']),
		'GUEST_NUMBER' => $visitornum,
		'GUEST_LASTSEEN' => cot_build_timegap($row['online_lastseen'], $sys['now'])
	));
	$t->parse('MAIN.GUESTS');
}

$t->assign(array(
	'STAT_MAXUSERS' => $maxusers,
	'STAT_COUNT_GUESTS' => $total_guests,
	'STAT_COUNT_USERS' => $total_users,
	'GUESTS' => cot_declension($total_guests, $Ls['Guests'], true),
	'USERS' => cot_declension($total_users, $Ls['Members'], true)
));

?>