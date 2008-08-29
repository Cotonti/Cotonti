<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=plugins/whosonline/whosonline.php
Version=120
Updated=2007-mar-03
Type=Plugin
Author=Neocrome
Description=
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

$cfg_showavatars = 1;
$cfg_miniavatar_x = 16;
$cfg_miniavatar_y = 16;

$plugin_title = $L['plu_title'];

$sql1 = sed_sql_query("SELECT u.user_country, u.user_avatar, u.user_maingrp, o.* FROM $db_online AS o LEFT JOIN $db_users AS u ON u.user_id=o.online_userid WHERE online_name!='v' ORDER BY u.user_name ASC");
$sql2 = sed_sql_query("SELECT online_ip, online_lastseen, online_location, online_subloc FROM $db_online WHERE online_name LIKE 'v' ORDER BY online_lastseen DESC");
$sql3 = sed_sql_query("SELECT stat_value FROM $db_stats where stat_name='maxusers' LIMIT 1");
$total1 = sed_sql_numrows($sql1);
$total2 = sed_sql_numrows($sql2);
$row = sed_sql_fetcharray($sql3);
$maxusers = $row[0];
$visitornum = 0;

$plugin_body .= $L['plu_mostonline'].$maxusers.".<br />";
$plugin_body .= $L['plu_therescurrently'].$total2.$L['plu_visitors'].$total1.$L['plu_members']."<br />&nbsp;<br /><table class=\"cells\">";

	$plugin_body .= "<tr>";
	$plugin_body .= ($cfg_showavatars) ? "<td class=\"coltop\">".$user_avatar."</td>" : '';
	$plugin_body .= "<td class=\"coltop\">".$L['User']."</td>";
	$plugin_body .= "<td class=\"coltop\">".$L['Group']."</td>";
	$plugin_body .= "<td class=\"coltop\">".$L['Country']."</td>";
	$plugin_body .= "<td class=\"coltop\">".$L['plu_lastseen1']."</td>";
	$plugin_body .= ($usr['isadmin']) ? "<td class=\"coltop\">".$L['plu_in']."</td>" : '';
	$plugin_body .= ($usr['isadmin']) ? "<td class=\"coltop\">".$L['Ip']."</td>" : '';
	$plugin_body .= "</tr>";

while ($row = sed_sql_fetcharray($sql1))
	{
	if ($cfg_showavatars)
		{
		$user_avatar = "<a href=\"users.php?m=details&amp;id=".$row['online_userid']."\">";
		$user_avatar .= (!empty($row['user_avatar'])) ? "<img src=\"".$row['user_avatar']."\" width=\"".$cfg_miniavatar_x."\" height=\"".$cfg_miniavatar_y."\" alt=\"\" /></a>" : "<img src=\"{$cfg['plugins_dir']}/whosonline/img/blank.gif\" width=\"".$cfg_miniavatar_x."\" height=\"".$cfg_miniavatar_y."\" alt=\"\" /></a>";
		}

	$plugin_body .= "<tr>";
	$plugin_body .= ($cfg_showavatars) ? "<td>".$user_avatar."</td>" : '';
	$plugin_body .= "<td>".sed_build_user($row['online_userid'], sed_cc($row['online_name']))."</td>";
	$plugin_body .= "<td><a href=\"users.php?g=".$row['user_maingrp']."\">".$sed_groups[$row['user_maingrp']]['title']."</a></td>";
	$plugin_body .= "<td style=\"text-align:center;\">".sed_build_flag($row['user_country'])."</td>";
	$plugin_body .= "<td>".sed_build_timegap($row['online_lastseen'],$sys['now'])."</td>";
	$plugin_body .= ($usr['isadmin']) ? "<td>".$L[$row['online_location']] : '';
	$plugin_body .= ($usr['isadmin'] && !empty($row['online_subloc'])) ? " ".$cfg['separator']." ".sed_cc($row['online_subloc']) : '';
	$plugin_body .= ($usr['isadmin']) ? "</td>" : '';
	$plugin_body .= ($usr['isadmin']) ? "<td style=\"text-align:center;\">".$row['online_ip']."</td>" : '';
	$plugin_body .= "</tr>";
	}

while ($row = sed_sql_fetcharray($sql2))
	{
	$visitornum++;
	$online_location = $L[$row['online_location']];
	$plugin_body .= "<tr>";
	$plugin_body .= ($cfg_showavatars) ? "<td>&nbsp;</td>" : '';
	$plugin_body .= "<td colspan=\"3\">".$L['plu_visitor']." #".$visitornum."</td>";
	$plugin_body .= "<td>".sed_build_timegap($row['online_lastseen'],$sys['now'])."</td>";
	$plugin_body .= ($usr['isadmin']) ? "<td>".$L[$row['online_location']] : '';
	$plugin_body .= ($usr['isadmin'] && !empty($row['online_subloc'])) ? " ".$cfg['separator']." ".sed_cc($row['online_subloc']) : '';
	$plugin_body .= ($usr['isadmin']) ? "</td>" : '';
	$plugin_body .= ($usr['isadmin']) ? "<td style=\"text-align:center;\">".$row['online_ip']."</td>" : '';
	$plugin_body .= "</tr>";
	}

$plugin_body .= "</table>";

?>