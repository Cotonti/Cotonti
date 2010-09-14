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

$showavatars = $cfg['plugin']['whosonline']['showavatars'];
$miniavatar_x = $cfg['plugin']['whosonline']['miniavatar_x'];
$miniavatar_y = $cfg['plugin']['whosonline']['miniavatar_y'];

$sql1 = cot_db_query("SELECT DISTINCT u.*, o.* FROM $db_online AS o LEFT JOIN $db_users AS u ON u.user_id=o.online_userid WHERE online_name!='v' ORDER BY u.user_name ASC");
$sql2 = cot_db_query("SELECT online_ip, online_lastseen, online_location, online_subloc FROM $db_online WHERE online_name = 'v' ORDER BY online_lastseen DESC");
$sql3 = cot_db_query("SELECT stat_value FROM $db_stats where stat_name='maxusers' LIMIT 1");
$total1 = cot_db_numrows($sql1);
$total2 = cot_db_numrows($sql2);
$row = cot_db_fetcharray($sql3);
$maxusers = $row[0];
$visitornum = 0;
$visituser = 0;

while ($row = cot_db_fetcharray($sql1))
{
	$visituser++;
	if ($usr['isadmin'])
	{
		$sublock = (!empty($row['online_subloc'])) ? ' '.$cfg['separator'].' '.htmlspecialchars($row['online_subloc']) : '';
		$t->assign(array(
			'WHOSONlINE_ROW1_USER_ONLINE_LOCATION'=> $L[$row['online_location']].$sublock,
			'WHOSONlINE_ROW1_USER_ONLINE_IP'=> cot_rc_link(cot_url('admin', 'm=tools&p=ipsearch&a=search&id='.$row['online_ip'].'&'.cot_xg()), $row['online_ip'])
		));
		$t->parse('MAIN.NOT_EMPTY.WHOSONlINE_ROW1.WHOSONlINE_ROW1_IS_ADMIN');
	}

	if ($showavatars)
	{
		$user_avatar = '<a href="'.cot_url('users', 'm=details&id='.$row['online_userid'].'&u='.htmlspecialchars($row['online_name'])).'">';
		$user_avatar .= (!empty($row['user_avatar'])) ? '<img src="'.$row['user_avatar'].'" width="'.$miniavatar_x.'" height="'.$miniavatar_y.'" alt="" /></a>' : cot_rc('img_pixel', array('x' => $miniavatar_x, 'y' => $miniavatar_y)) . '</a>';
	}

	$t->assign(array(
		'WHOSONlINE_ROW1_SHOWAVATARS' => ($showavatars) ? $user_avatar : '',
		'WHOSONlINE_ROW1_USER' => cot_build_user($row['online_userid'], htmlspecialchars($row['online_name'])),
	));
	$t->assign(cot_generate_usertags($row, "WHOSONlINE_ROW1_USER_", $L['Guest']));

	$t->parse('MAIN.NOT_EMPTY.WHOSONlINE_ROW1');
}

while ($row = cot_db_fetcharray($sql2))
{
	$visitornum++;
	$online_location = $L[$row['online_location']];//This line is needed?

	if($usr['isadmin'])
	{
		$sublock = (!empty($row['online_subloc'])) ? " ".$cfg['separator'].' '.htmlspecialchars($row['online_subloc']) : '';
		$t->assign(array(
			'WHOSONlINE_ROW2_USER_ONLINE_LOCATION'=> $L[$row['online_location']].$sublock,
			'WHOSONlINE_ROW2_USER_ONLINE_IP'=> cot_rc_link(cot_url('admin', 'm=tools&p=ipsearch&a=search&id='.$row['online_ip'].'&'.cot_xg()), $row['online_ip'])
		));
		$t->parse('MAIN.NOT_EMPTY.WHOSONlINE_ROW2.WHOSONlINE_ROW2_IS_ADMIN');
	}

	$t->assign(array(
		'WHOSONlINE_ROW2_SHOWAVATARS' => ($showavatars) ? '&nbsp;' : '',
		'WHOSONlINE_ROW2_USER' => $L['plu_visitor'].' #'.$visitornum,
		'WHOSONlINE_ROW2_USER_ONLINE_LASTSEEN'=> cot_build_timegap($row['online_lastseen'],$sys['now']),

	));
	$t->parse('MAIN.NOT_EMPTY.WHOSONlINE_ROW2');
}

if ($visitornum > 0 || $visituser > 0)
{
	if ($usr['isadmin'])
	{
		$t->assign(array(
			'WHOSONlINE_IN' => $L['plu_in'],
			'WHOSONlINE_IP' => $L['Ip']
		));
		$t->parse('MAIN.NOT_EMPTY.IS_ADMIN');
	}

	$t->assign(array(
		'WHOSONlINE_TITLE' => $L['plu_title'],
		'WHOSONlINE_MAXUSERS' => $maxusers,
		'WHOSONlINE_VISITORS' => $total2,
		'WHOSONlINE_MEMBERS' => $total1,
		'WHOSONlINE_TEXTVISITORS' => cot_declension($total2, $Ls['Guests'], true),
		'WHOSONlINE_TEXTMEMBERS' => cot_declension($total1, $Ls['Members'], true),
		'WHOSONlINE_USER_AVATAR' => ($showavatars) ? $L['Avatar'] : ''
	));
	$t->parse('MAIN.NOT_EMPTY');
}

?>