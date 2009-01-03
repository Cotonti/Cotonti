<?PHP
/* ====================
[BEGIN_SED]
File=plugins/whosonline/whosonline.php
Version=0.0.2
Updated=2009-jan-03
Type=Plugin
Author=Neocrome & Cotonti Team
Description=Cotonti - Website engine http://www.cotonti.com
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=whosonline
Part=main
File=whosonline
Hooks=standalone
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE') || !defined('SED_PLUG')) { die('Wrong URL.'); }

$showavatars = $cfg['plugin']['whosonline']['showavatars'];
$miniavatar_x = $cfg['plugin']['whosonline']['miniavatar_x'];
$miniavatar_y = $cfg['plugin']['whosonline']['miniavatar_y'];

$sql1 = sed_sql_query("SELECT u.user_country, u.user_avatar, u.user_maingrp, o.* FROM $db_online AS o LEFT JOIN $db_users AS u ON u.user_id=o.online_userid WHERE online_name!='v' ORDER BY u.user_name ASC");
$sql2 = sed_sql_query("SELECT online_ip, online_lastseen, online_location, online_subloc FROM $db_online WHERE online_name LIKE 'v' ORDER BY online_lastseen DESC");
$sql3 = sed_sql_query("SELECT stat_value FROM $db_stats where stat_name='maxusers' LIMIT 1");
$total1 = sed_sql_numrows($sql1);
$total2 = sed_sql_numrows($sql2);
$row = sed_sql_fetcharray($sql3);
$maxusers = $row[0];
$visitornum = 0;

if($usr['isadmin'])
{
	$t->assign(array(
		'WHOSONlINE_IN'=> $L['plu_in'],
		'WHOSONlINE_IP'=> $L['Ip']
	));
	$t->parse('MAIN.IS_ADMIN');
}

$t->assign(array(
	'WHOSONlINE_TITLE' => $L['plu_title'],
	'WHOSONlINE_MAXUSERS' => $maxusers,
	'WHOSONlINE_VISITORS' => $total2,
	'WHOSONlINE_MEMBERS' => $total1,
	'WHOSONlINE_USER_AVATAR' => ($showavatars) ? $L['plu_user_avatar'] : ''
));

while ($row = sed_sql_fetcharray($sql1))
{
    if($usr['isadmin'])
    {
    	$sublock = (!empty($row['online_subloc'])) ? " ".$cfg['separator']." ".sed_cc($row['online_subloc']) : '';
		$t->assign(array(
			'WHOSONlINE_ROW1_USER_ONLINE_LOCATION'=> $L[$row['online_location']].$sublock,
			'WHOSONlINE_ROW1_USER_ONLINE_IP'=> $row['online_ip']
		));
		$t->parse('MAIN.WHOSONlINE_ROW1.WHOSONlINE_ROW1_IS_ADMIN');
	}

	if ($showavatars)
	{
		$user_avatar = "<a href=\"".sed_url('users', 'm=details&id='.$row['online_userid'])."\">";
		$user_avatar .= (!empty($row['user_avatar'])) ? "<img src=\"".$row['user_avatar']."\" width=\"".$miniavatar_x."\" height=\"".$miniavatar_y."\" alt=\"\" /></a>" : "<img src=\"images/pixel.gif\" width=\"".$miniavatar_x."\" height=\"".$miniavatar_y."\" alt=\"\" /></a>";
	}

	$t->assign(array(
		'WHOSONlINE_ROW1_SHOWAVATARS' => ($showavatars) ? $user_avatar : '',
		'WHOSONlINE_ROW1_USER' => sed_build_user($row['online_userid'], sed_cc($row['online_name'])),
		'WHOSONlINE_ROW1_USER_MAINGRP_URL' => sed_url('users', 'g='.$row['user_maingrp']),
		'WHOSONlINE_ROW1_USER_MAINGRP_TITLE' => $sed_groups[$row['user_maingrp']]['title'],
		'WHOSONlINE_ROW1_USER_COUNTRY'=> sed_build_flag($row['user_country']),
		'WHOSONlINE_ROW1_USER_ONLINE_LASTSEEN'=> sed_build_timegap($row['online_lastseen'],$sys['now']),
	));
	$t->parse('MAIN.WHOSONlINE_ROW1');
}

while ($row = sed_sql_fetcharray($sql2))
{
	$visitornum++;
	$online_location = $L[$row['online_location']];//This line is needed?

    if($usr['isadmin'])
    {
    	$sublock = (!empty($row['online_subloc'])) ? " ".$cfg['separator']." ".sed_cc($row['online_subloc']) : '';
		$t->assign(array(
			'WHOSONlINE_ROW2_USER_ONLINE_LOCATION'=> $L[$row['online_location']].$sublock,
			'WHOSONlINE_ROW2_USER_ONLINE_IP'=> $row['online_ip']
		));
		$t->parse('MAIN.WHOSONlINE_ROW2.WHOSONlINE_ROW2_IS_ADMIN');
	}

	$t->assign(array(
		'WHOSONlINE_ROW2_SHOWAVATARS' => ($showavatars) ? '&nbsp;' : '',
		'WHOSONlINE_ROW2_USER' => $L['plu_visitor']." #".$visitornum,
		'WHOSONlINE_ROW2_USER_ONLINE_LASTSEEN'=> sed_build_timegap($row['online_lastseen'],$sys['now']),
	));
	$t->parse('MAIN.WHOSONlINE_ROW2');
}

?>