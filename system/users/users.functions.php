<?php

/**
 * User Functions
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

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
	$result =  '<select name="'.$name.'" size="1">';
	foreach(array ('U', 'M', 'F') as $i)
	{
		$selected = ($i==$check) ? 'selected="selected"' : '';
		$result .= '<option value="'.$i.'" $selected>'.$L['Gender_'.$i].'</option>';
	}
	$result .= '</select>';
	return($result);
}


/**
 * Checks whether user is online
 *
 * @param int $id User ID
 * @return bool
 */
function sed_userisonline($id)
{
	global $sed_usersonline;

	$res = FALSE;
	if (is_array($sed_usersonline))
	{
		$res = (in_array($id,$sed_usersonline)) ? TRUE : FALSE;
	}
	return ($res);
}

/**
 * Returns all user tags foк XTemplate
 *
 * @param array $ruser_array User Info Array
 * @param string $tag_prefix Prefix for tags
 * @param string $emptyname Name text if user is not exist
 * @param bool $allgroups Build info about all user groups
 *
 * @return array
 */
function sed_generate_usertags($ruser_array, $tag_prefix = '', $emptyname='', $allgroups = false)
{
	global $sed_extrafields, $cfg, $L, $sed_yesno, $skinlang, $cache;
	if ($ruser_array['user_id'] > 0 && !empty($ruser_array['user_name']))
	{
		if (!is_array($cache['user_'.$ruser_array['user_id']]))
		{
			$ruser_array['user_birthdate'] = sed_date2stamp($ruser_array['user_birthdate']);
			$ruser_array['user_text'] = sed_build_usertext(htmlspecialchars($ruser_array['user_text']));
			$ruser_array['user_age'] = ($ruser_array['user_birthdate']!=0) ? sed_build_age($ruser_array['user_birthdate']) : '';
			$ruser_array['user_birthdate'] = ($ruser_array['user_birthdate']!=0) ? @date($cfg['formatyearmonthday'], $ruser_array['user_birthdate']) : '';
			$ruser_array['user_gender'] = ($ruser_array['user_gender']=='' || $ruser_array['user_gender']=='U') ?  '' : $L['Gender_'.$ruser_array['user_gender']];
			$ruser_array['user_online'] = (sed_userisonline($ruser_array['user_id'])) ? '1' : '0';
			$ruser_array['user_onlinetitle'] = ($ruser_array['user_online']) ? $skinlang['forumspost']['Onlinestatus1'] : $skinlang['forumspost']['Onlinestatus0'];

			$return_array = array(
				$tag_prefix.'ID' => $ruser_array['user_id'],
				$tag_prefix.'PM' => sed_build_pm($ruser_array['user_id']),
				$tag_prefix.'NAME' => sed_build_user($ruser_array['user_id'], htmlspecialchars($ruser_array['user_name'])),
				$tag_prefix.'NICKNAME' => htmlspecialchars($ruser_array['user_name']),
				$tag_prefix.'DETAILSLINK' => sed_url('users', 'm=details&id='.$ruser_array['user_id']),
				$tag_prefix.'MAINGRP' => sed_build_group($ruser_array['user_maingrp']),
				$tag_prefix.'MAINGRPID' => $ruser_array['user_maingrp'],
				$tag_prefix.'MAINGRPSTARS' => sed_build_stars($sed_groups[$ruser_array['user_maingrp']]['level']),
				$tag_prefix.'MAINGRPICON' => sed_build_userimage($sed_groups[$ruser_array['user_maingrp']]['icon']),
				$tag_prefix.'COUNTRY' => sed_build_country($ruser_array['user_country']),
				$tag_prefix.'COUNTRYFLAG' => sed_build_flag($ruser_array['user_country']),
				$tag_prefix.'TEXT' => $cfg['parsebbcodeusertext'] ? sed_bbcode_parse($ruser_array['user_text'], true) : $ruser_array['user_text'],
				$tag_prefix.'AVATAR' => sed_build_userimage($ruser_array['user_avatar'], 'avatar'),
				$tag_prefix.'PHOTO' => sed_build_userimage($ruser_array['user_photo'], 'photo'),
				$tag_prefix.'SIGNATURE' => sed_build_userimage($ruser_array['user_signature'], 'sig'),
				$tag_prefix.'EMAIL' => sed_build_email($ruser_array['user_email'], $ruser_array['user_hideemail']),
				$tag_prefix.'PMNOTIFY' =>  $sed_yesno[$urr['user_pmnotify']],
				$tag_prefix.'SKIN' => $ruser_array['user_skin'],
				$tag_prefix.'WEBSITE' => sed_build_url($ruser_array['user_website']),
				$tag_prefix.'JOURNAL' => $ruser_array['user_journal'],
				$tag_prefix.'ICQ' => sed_build_icq($ruser_array['user_icq']),
				$tag_prefix.'MSN' => sed_build_msn($ruser_array['user_msn']),
				$tag_prefix.'IRC' => htmlspecialchars($ruser_array['user_irc']),
				$tag_prefix.'GENDER' => $ruser_array['user_gender'],
				$tag_prefix.'BIRTHDATE' => $ruser_array['user_birthdate'],
				$tag_prefix.'AGE' => $ruser_array['user_age'],
				$tag_prefix.'TIMEZONE' => sed_build_timezone($ruser_array['user_timezone']),
				$tag_prefix.'LOCATION' => htmlspecialchars($ruser_array['user_location']),
				$tag_prefix.'OCCUPATION' => htmlspecialchars($ruser_array['user_occupation']),
				$tag_prefix.'REGDATE' => @date($cfg['dateformat'], $ruser_array['user_regdate'] + $usr['timezone'] * 3600).' '.$usr['timetext'],
				$tag_prefix.'LASTLOG' => @date($cfg['dateformat'], $ruser_array['user_lastlog'] + $usr['timezone'] * 3600).' '.$usr['timetext'],
				$tag_prefix.'LOGCOUNT' => $ruser_array['user_logcount'],
				$tag_prefix.'POSTCOUNT' => $ruser_array['user_postcount'],
				$tag_prefix.'LASTIP' => $ruser_array['user_lastip'],
				$tag_prefix.'ONLINE' => $ruser_array['user_online'],
				$tag_prefix.'ONLINETITLE' => $ruser_array['user_onlinetitle'],
			);

			if ($allgroups)
			{
				$return_array[$tag_prefix.'GROUPS'] = sed_build_groupsms($ruser_array['user_id'], FALSE, $ruser_array['user_maingrp']);
			}
			if (count($sed_extrafields) > 0)
			{
				// Extra fields
				foreach ($sed_extrafields['users'] as $i => $row)
				{
					$return_array[$tag_prefix.strtoupper($row['field_name'])] = sed_build_extrafields_data('user', $row['field_type'], $row['field_name'], $ruser_array['user_'.$row['field_name']]);
					$return_array[$tag_prefix.strtoupper($row['field_name']).'_TITLE'] = isset($L['user_'.$row['field_name'].'_title']) ? $L['user_'.$row['field_name'].'_title'] : $row['field_description'];
				}
			}

			$cache[$tag_prefix.'_'.$ruser_array['user_id']] = $return_array;
		}
		else
		{
			$return_array = $cache[$tag_prefix.'_'.$ruser_array['user_id']];
		}
	}
	else
	{
		$return_array = array(
			$tag_prefix.'NAME' => (!empty($emptyname)) ? $emptyname : $L['Deleted'],
			$tag_prefix.'NICKNAME' => (!empty($emptyname)) ? $emptyname : $L['Deleted'],
		);
	}
	
	return $return_array;
}

?>
