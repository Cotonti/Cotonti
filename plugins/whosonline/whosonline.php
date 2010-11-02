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

$sql1 = $db->query("SELECT DISTINCT u.*, o.* FROM $db_online AS o LEFT JOIN $db_users AS u ON u.user_id=o.online_userid WHERE online_name!='v' ORDER BY u.user_name ASC");
$sql2 = $db->query("SELECT online_ip, online_lastseen, online_location, online_subloc FROM $db_online WHERE online_name = 'v' ORDER BY online_lastseen DESC");
$sql3 = $db->query("SELECT stat_value FROM $db_stats where stat_name='maxusers' LIMIT 1");
$total1 = $sql1->rowCount();
$total2 = $sql2->rowCount();
$row = $sql3->fetch();
$maxusers = $row[0];
$visitornum = 0;
$visituser = 0;

while ($row = $sql1->fetch())
{
	$visituser++;
	if ($usr['isadmin'])
	{
		$sublock = (!empty($row['online_subloc'])) ? ' '.$cfg['separator'].' '.htmlspecialchars($row['online_subloc']) : '';
		$t->assign(array(
			'WHOSONLINE_ROW1_USER_ONLINE_LOCATION'=> $L[$row['online_location']].$sublock,
			'WHOSONLINE_ROW1_USER_ONLINE_IP'=> cot_rc_link(cot_url('admin', 'm=tools&p=ipsearch&a=search&id='.$row['online_ip'].'&'.cot_xg()), $row['online_ip'])
		));
		$t->parse('MAIN.NOT_EMPTY.WHOSONLINE_ROW1.WHOSONLINE_ROW1_IS_ADMIN');
	}

	if ($showavatars)
	{
		$user_avatar = '<a href="'.cot_url('users', 'm=details&id='.$row['online_userid'].'&u='.htmlspecialchars($row['online_name'])).'">';
		$user_avatar .= (!empty($row['user_avatar'])) ? '<img src="'.$row['user_avatar'].'" width="'.$miniavatar_x.'" height="'.$miniavatar_y.'" alt="'.$L['Avatar'].'" /></a>' : cot_rc('img_pixel', array('x' => $miniavatar_x, 'y' => $miniavatar_y)) . '</a>';
	}

	$t->assign(array(
		'WHOSONLINE_ROW1_SHOWAVATARS' => ($showavatars) ? $user_avatar : '',
		'WHOSONLINE_ROW1_USER' => cot_build_user($row['online_userid'], htmlspecialchars($row['online_name'])),
	));
	$t->assign(cot_generate_usertags($row, "WHOSONLINE_ROW1_USER_", $L['Guest']));

	$t->parse('MAIN.NOT_EMPTY.WHOSONLINE_ROW1');
}

while ($row = $sql2->fetch())
{
	$visitornum++;
	$online_location = $L[$row['online_location']];//This line is needed?

	if($usr['isadmin'])
	{
		$sublock = (!empty($row['online_subloc'])) ? " ".$cfg['separator'].' '.htmlspecialchars($row['online_subloc']) : '';
		$t->assign(array(
			'WHOSONLINE_ROW2_USER_ONLINE_LOCATION'=> $L[$row['online_location']].$sublock,
			'WHOSONLINE_ROW2_USER_ONLINE_IP'=> cot_rc_link(cot_url('admin', 'm=tools&p=ipsearch&a=search&id='.$row['online_ip'].'&'.cot_xg()), $row['online_ip'])
		));
		$t->parse('MAIN.NOT_EMPTY.WHOSONLINE_ROW2.WHOSONLINE_ROW2_IS_ADMIN');
	}

	$t->assign(array(
		'WHOSONLINE_ROW2_SHOWAVATARS' => ($showavatars) ? '&nbsp;' : '',
		'WHOSONLINE_ROW2_USER' => $L['plu_visitor'].' #'.$visitornum,
		'WHOSONLINE_ROW2_USER_ONLINE_LASTSEEN'=> cot_build_timegap($row['online_lastseen'],$sys['now']),

	));
	$t->parse('MAIN.NOT_EMPTY.WHOSONLINE_ROW2');
}

if ($visitornum > 0 || $visituser > 0)
{
	if ($usr['isadmin'])
	{
		$t->assign(array(
			'WHOSONLINE_IN' => $L['plu_in'],
			'WHOSONLINE_IP' => $L['Ip']
		));
		$t->parse('MAIN.NOT_EMPTY.IS_ADMIN');
	}

	$t->assign(array(
		'WHOSONLINE_TITLE' => $L['plu_title'],
		'WHOSONLINE_MAXUSERS' => $maxusers,
		'WHOSONLINE_VISITORS' => $total2,
		'WHOSONLINE_MEMBERS' => $total1,
		'WHOSONLINE_TEXTVISITORS' => cot_declension($total2, $Ls['Guests'], true),
		'WHOSONLINE_TEXTMEMBERS' => cot_declension($total1, $Ls['Members'], true),
		'WHOSONLINE_USER_AVATAR' => ($showavatars) ? $L['Avatar'] : ''
	));
	$t->parse('MAIN.NOT_EMPTY');
}

?>