<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=whosonline
Part=main
File=whosonline
Hooks=standalone
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Displays users who are currently online
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008
 * @license BSD
 */

if (!defined('SED_CODE') || !defined('SED_PLUG')) { die('Wrong URL.'); }

$showavatars = $cfg['plugin']['whosonline']['showavatars'];
$miniavatar_x = $cfg['plugin']['whosonline']['miniavatar_x'];
$miniavatar_y = $cfg['plugin']['whosonline']['miniavatar_y'];

$sql1 = sed_sql_query("SELECT DISTINCT u.*, o.* FROM $db_online AS o LEFT JOIN $db_users AS u ON u.user_id=o.online_userid WHERE online_name!='v' ORDER BY u.user_name ASC");
$sql2 = sed_sql_query("SELECT online_ip, online_lastseen, online_location, online_subloc FROM $db_online WHERE online_name = 'v' ORDER BY online_lastseen DESC");
$sql3 = sed_sql_query("SELECT stat_value FROM $db_stats where stat_name='maxusers' LIMIT 1");
$total1 = sed_sql_numrows($sql1);
$total2 = sed_sql_numrows($sql2);
$row = sed_sql_fetcharray($sql3);
$maxusers = $row[0];
$visitornum = 0;
$visituser = 0;

while ($row = sed_sql_fetcharray($sql1))
{
	$visituser++;
    if($usr['isadmin'])
    {
    	$sublock = (!empty($row['online_subloc'])) ? " ".$cfg['separator']." ".sed_cc($row['online_subloc']) : '';
		$t->assign(array(
			'WHOSONlINE_ROW1_USER_ONLINE_LOCATION'=> $L[$row['online_location']].$sublock,
			'WHOSONlINE_ROW1_USER_ONLINE_IP'=> $row['online_ip']
		));
		$t->parse('MAIN.NOT_EMPTY.WHOSONlINE_ROW1.WHOSONlINE_ROW1_IS_ADMIN');
	}

	if ($showavatars)
	{
		$user_avatar = "<a href=\"".sed_url('users', 'm=details&id='.$row['online_userid'])."\">";
		$user_avatar .= (!empty($row['user_avatar'])) ? "<img src=\"".$row['user_avatar']."\" width=\"".$miniavatar_x."\" height=\"".$miniavatar_y."\" alt=\"\" /></a>" : "<img src=\"images/pixel.gif\" width=\"".$miniavatar_x."\" height=\"".$miniavatar_y."\" alt=\"\" /></a>";
	}

	$row['user_text'] = sed_build_usertext(sed_cc($row['user_text']));

	$t->assign(array(
		'WHOSONlINE_ROW1_SHOWAVATARS' => ($showavatars) ? $user_avatar : '',
		'WHOSONlINE_ROW1_USER_AVATAR' => ($showavatars) ? sed_build_userimage($row['user_avatar'], 'avatar') : '',
		'WHOSONlINE_ROW1_USER_PHOTO' => ($showavatars) ? sed_build_userimage($row['user_photo'], 'photo') : '',
		'WHOSONlINE_ROW1_USER_SIGNATURE' => ($showavatars) ? sed_build_userimage($row['user_signature'], 'sig') : '',
		'WHOSONlINE_ROW1_USER_ID' => $row['online_userid'],
		'WHOSONlINE_ROW1_USER' => sed_build_user($row['online_userid'], sed_cc($row['online_name'])),
		'WHOSONlINE_ROW1_USER_MAINGRP_URL' => sed_url('users', 'g='.$row['user_maingrp']),
		'WHOSONlINE_ROW1_USER_MAINGRP_TITLE' => $sed_groups[$row['user_maingrp']]['title'],
		'WHOSONlINE_ROW1_USER_MAINGRP' => sed_build_group($row['user_maingrp']),
		'WHOSONlINE_ROW1_USER_MAINGRPID' => $row['user_maingrp'],
		'WHOSONlINE_ROW1_USER_MAINGRPSTARS' => sed_build_stars($sed_groups[$row['user_maingrp']]['level']),
		'WHOSONlINE_ROW1_USER_MAINGRPICON' => sed_build_userimage($sed_groups[$row['user_maingrp']]['icon']),
		'WHOSONlINE_ROW1_USER_GROUPS' => sed_build_groupsms($row['user_id'], FALSE, $row['user_maingrp']),
		'WHOSONlINE_ROW1_USER_COUNTRY'=> sed_build_country($row['user_country']),
		'WHOSONlINE_ROW1_USER_COUNTRYFLAG'=> sed_build_flag($row['user_country']),
		'WHOSONlINE_ROW1_USER_ONLINE_LASTSEEN'=> sed_build_timegap($row['online_lastseen'],$sys['now']),
		'WHOSONlINE_ROW1_USER_TEXT' => $cfg['parsebbcodeusertext'] ? sed_bbcode_parse($row['user_text'], true) : $row['user_text'],
		'WHOSONlINE_ROW1_USER_REGDATE' => @date($cfg['dateformat'], $row['user_regdate'] + $row['timezone'] * 3600)." ".$row['timetext'],
		'WHOSONlINE_ROW1_USER_LOCATION' => sed_cc($row['user_location']),
		'WHOSONlINE_ROW1_USER_WEBSITE' => sed_build_url($row['user_website']),
		'WHOSONlINE_ROW1_USER_IRC' => sed_cc($row['user_irc']),
		'WHOSONlINE_ROW1_USER_ICQ' => sed_build_icq($row['user_icq']),
		'WHOSONlINE_ROW1_USER_MSN' => sed_build_msn($row['user_msn']),
		"WHOSONlINE_ROW1_USER_GENDER" => ($row['user_gender']=='' || $row['user_gender']=='U') ?  '' : $L["Gender_".$row['user_gender']],
		"WHOSONlINE_ROW1_USER_AGE" => ($row['user_birthdate']!=0) ? sed_build_age($row['user_birthdate']) : '',
		"WHOSONlINE_ROW1_USER_BIRTHDATE" => ($row['user_birthdate']!=0) ? @date($cfg['formatyearmonthday'], $row['user_birthdate']) : '',
		"WHOSONlINE_ROW1_USER_OCCUPATION" => sed_cc($row['user_occupation'])
	));

	$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='users'");
	while($ros = sed_sql_fetchassoc($fieldsres)) $t->assign('WHOSONlINE_ROW1_USER_'.strtoupper($ros['field_name']), $row['user_'.$ros['field_name']]);

	$t->parse('MAIN.NOT_EMPTY.WHOSONlINE_ROW1');
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
		$t->parse('MAIN.NOT_EMPTY.WHOSONlINE_ROW2.WHOSONlINE_ROW2_IS_ADMIN');
	}

	$t->assign(array(
		'WHOSONlINE_ROW2_SHOWAVATARS' => ($showavatars) ? '&nbsp;' : '',
		'WHOSONlINE_ROW2_USER' => $L['plu_visitor']." #".$visitornum,
		'WHOSONlINE_ROW2_USER_ONLINE_LASTSEEN'=> sed_build_timegap($row['online_lastseen'],$sys['now']),

	));
	$t->parse('MAIN.NOT_EMPTY.WHOSONlINE_ROW2');
}

if($visitornum>0 OR $visituser>0)
{
	if($usr['isadmin'])
	{
		$t->assign(array(
			'WHOSONlINE_IN'=> $L['plu_in'],
			'WHOSONlINE_IP'=> $L['Ip']
		));
		$t->parse('MAIN.NOT_EMPTY.IS_ADMIN');
	}

	$t->assign(array(
		'WHOSONlINE_TITLE' => $L['plu_title'],
		'WHOSONlINE_MAXUSERS' => $maxusers,
		'WHOSONlINE_VISITORS' => $total2,
		'WHOSONlINE_MEMBERS' => $total1,
		'WHOSONlINE_USER_AVATAR' => ($showavatars) ? $L['plu_user_avatar'] : ''
	));
	$t->parse('MAIN.NOT_EMPTY');
}

?>