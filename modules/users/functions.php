<?php

/**
 * Calculates age out of D.O.B.
 *
 * @param int $birth Date of birth as UNIX timestamp
 * @return int
 */
function sed_build_age($birth)
{
	global $sys;

	if ($birth==1)
	{ return ('?'); }

	$day1 = @date('d', $birth);
	$month1 = @date('m', $birth);
	$year1 = @date('Y', $birth);

	$day2 = @date('d', $sys['now_offset']);
	$month2 = @date('m', $sys['now_offset']);
	$year2 = @date('Y', $sys['now_offset']);

	$age = ($year2-$year1)-1;

	if ($month1<$month2 || ($month1==$month2 && $day1<=$day2))
	{ $age++; }

	if($age < 0)
	{ $age += 136; }

	return ($age);
}

/**
 * Returns group link (button)
 *
 * @param int $grpid Group ID
 * @return string
 */
function sed_build_group($grpid)
{
	if(empty($grpid)) return '';
	global $sed_groups, $L;

	if($sed_groups[$grpid]['hidden'])
	{
		if(sed_auth('users', 'a', 'A'))
		{
			return '<a href="'.sed_url('users', 'gm='.$grpid).'">'.$sed_groups[$grpid]['title'].'</a> ('.$L['Hidden'].')';
		}
		else
		{
			return $L['Hidden'];
		}
	}
	else
	{
		return '<a href="'.sed_url('users', 'gm='.$grpid).'">'.$sed_groups[$grpid]['title'].'</a>';
	}
}

/**
 * Builds "edit group" option group for "user edit" part
 *
 * @param int $userid Edited user ID
 * @param bool $edit Permission
 * @param int $maingrp User main group
 * @return string
 */
function sed_build_groupsms($userid, $edit=FALSE, $maingrp=0)
{
	global $db_groups_users, $sed_groups, $L, $usr;

	$sql = sed_sql_query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid='$userid'");

	while ($row = sed_sql_fetcharray($sql))
	{
		$member[$row['gru_groupid']] = TRUE;
	}

	foreach($sed_groups as $k => $i)
	{
		$checked = ($member[$k]) ? "checked=\"checked\"" : '';
		$checked_maingrp = ($maingrp==$k) ? "checked=\"checked\"" : '';
		$readonly = (!$edit || $usr['level'] < $sed_groups[$k]['level'] || $k==SED_GROUP_GUESTS || $k==SED_GROUP_INACTIVE || $k==SED_GROUP_BANNED || ($k==SED_GROUP_TOPADMINS && $userid==1)) ? "disabled=\"disabled\"" : '';
		$readonly_maingrp = (!$edit || $usr['level'] < $sed_groups[$k]['level'] || $k==SED_GROUP_GUESTS || ($k==SED_GROUP_INACTIVE && $userid==1) || ($k==SED_GROUP_BANNED && $userid==1)) ? "disabled=\"disabled\"" : '';

		if ($member[$k] || $edit)
		{
			if (!($sed_groups[$k]['hidden'] && !sed_auth('users', 'a', 'A')))
			{
				$res .= "<input type=\"radio\" class=\"radio\" name=\"rusermaingrp\" value=\"$k\" ".$checked_maingrp." ".$readonly_maingrp." /> \n";
				$res .= "<input type=\"checkbox\" class=\"checkbox\" name=\"rusergroupsms[$k]\" ".$checked." $readonly />\n";
				$res .= ($k == SED_GROUP_GUESTS) ? $sed_groups[$k]['title'] : "<a href=\"".sed_url('users', 'gm='.$k)."\">".$sed_groups[$k]['title']."</a>";
				$res .= ($sed_groups[$k]['hidden']) ? ' ('.$L['Hidden'].')' : '';
				$res .= "<br />";
			}
		}
	}

	return $res;
}

/**
 * Returns user ICQ pager link
 *
 * @param int $text ICQ number
 * @return string
 */
function sed_build_icq($text)
{
	global $cfg;

	$text = (int) $text;
	if($text > 0)
	{
		return $text.' <a href="http://www.icq.com/'.$text.'#pager"><img src="http://web.icq.com/whitepages/online?icq='.$text.'&amp;img=5" alt="" /></a>';
	}
	return '';
}

/**
 * Returns MSN link as e-mail link
 *
 * @param string $msn MSN address
 * @return string
 */
function sed_build_msn($msn)
{
	return sed_build_email($msn);
}

/**
 * Returns user PM link
 *
 * @param int $user User ID
 * @return string
 */
function sed_build_pm($user)
{
	global $usr, $L, $R;
	return '<a href="'.sed_url('pm', 'm=send&to='.$user).'" title="'.$L['pm_sendnew'].'">'.$R['pm_icon'].'</a>';
}

/**
 * Returns stars image for user level
 *
 * @param int $level User level
 * @return unknown
 */
function sed_build_stars($level)
{
	global $skin, $R;

	if($level>0 and $level<100)
	{
		$stars = floor($level / 10) + 1;
		return sed_rc('icon_stars', array('val' => $stars));
	}
	else
	{
		return '';
	}
}

/**
 * Renders user signature text
 *
 * @param string $text Signature text
 * @return string
 */
function sed_build_usertext($text)
{
	global $cfg;
	if (!$cfg['usertextimg'])
	{
		$bbcodes_img = array(
			'\[img\]([^\[]*)\[/img\]' => '',
			'\[thumb=([^\[]*)\[/thumb\]' => '',
			'\[t=([^\[]*)\[/t\]' => '',
			'\[list\]' => '',
			'\[style=([^\[]*)\]' => '',
			'\[quote' => '',
			'\[code' => ''
		);

		foreach($bbcodes_img as $bbcode => $bbcodehtml)
		{
			$text = preg_replace("#$bbcode#i", $bbcodehtml, $text);
		}
	}
	return sed_parse($text, $cfg['parsebbcodeusertext'], $cfg['parsesmiliesusertext'], 1);
}

/**
 * Renders country dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_countries($check,$name)
{
	global $sed_countries;

	$selected = (empty($check) || $check=='00') ? "selected=\"selected\"" : '';
	$result =  "<select name=\"$name\" size=\"1\">";
	foreach($sed_countries as $i => $x)
	{
		$selected = ($i==$check) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"$i\" $selected>".$x."</option>";
	}
	$result .= "</select>";

	return($result);
}

/**
 * Generates gender dropdown
 *
 * @param string $check Checked gender
 * @param string $name Input name
 * @return string
 */
function sed_selectbox_gender($check,$name)
{
	global $L;

	$genlist = array ('U', 'M', 'F');
	$result =  "<select name=\"$name\" size=\"1\">";
	foreach(array ('U', 'M', 'F') as $i)
	{
		$selected = ($i==$check) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"$i\" $selected>".$L['Gender_'.$i]."</option>";
	}
	$result .= "</select>";
	return($result);
}

/**
 * Returns language selection dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_lang($check, $name)
{
	global $sed_languages, $sed_countries, $cfg;

	$handle = opendir($cfg['system_dir'].'/lang/');
	while ($f = readdir($handle))
	{
		if ($f[0] != '.')
		{ $langlist[] = $f; }
	}
	closedir($handle);
	sort($langlist);

	$result = "<select name=\"$name\" size=\"1\">";
	while(list($i,$x) = each($langlist))
	{
		$selected = ($x==$check) ? "selected=\"selected\"" : '';
		$lng = (empty($sed_languages[$x])) ? $sed_countries[$x] : $sed_languages[$x];
		$result .= "<option value=\"$x\" $selected>".$lng." (".$x.")</option>";
	}
	$result .= "</select>";

	return($result);
}

/**
 * Returns skin selection dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_skin($check, $name)
{
	$handle = opendir('./skins/');
	while ($f = readdir($handle))
	{
		if (mb_strpos($f, '.') === FALSE && is_dir('./skins/' . $f))
		{ $skinlist[] = $f; }
	}
	closedir($handle);
	sort($skinlist);

	$result = '<select name="'.$name.'" size="1">';
	while(list($i,$x) = each($skinlist))
	{
		$selected = ($x==$check) ? 'selected="selected"' : '';
		$skininfo = "./skins/$x/$x.php";
		if (file_exists($skininfo))
		{
			$info = sed_infoget($skininfo);
			$result .= (!empty($info['Error'])) ? '<option value="'.$x.'" '.$selected.'>'.$x.' ('.$info['Error'].')' : '<option value="'.$x.'" '.$selected.'>'.$info['Name'];
		}
		else
		{
			$result .= '<option value="'.$x.'" $selected>'.$x;
		}
		$result .= '</option>';
	}
	$result .= '</select>';

	return $result;
}

/**
 * Returns skin selection dropdown
 *
 * @param string $skinname Skin name
 * @param string $name Dropdown name
 * @param string $theme Selected theme
 * @return string
 */
function sed_selectbox_theme($skinname, $name, $theme)
{
	global $skin_themes;

	if(empty($skin_themes))
	{
		if(file_exists("./skins/$skinname/$skinname.css"))
		{
			$skin_themes = array($skinname => $skinname);
		}
		else
		{
			$skin_themes = array('style' => $skinname);
		}
	}

	$result = '<select name="'.$name.'" size="1">';
	foreach($skin_themes as $x => $tname)
	{
		$selected = ($x==$theme) ? 'selected="selected"' : '';
		$result .= '<option value="'.$x.'" '.$selected.'>'.$tname.'</option>';
	}
	$result .= '</select>';

	return $result;
}

?>
