<?php
/**
 * PM function library.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD License
 */

/**
 * Returns usertags
 *
 * @param string $rlang recipient language
 * @param string $remail recipient email
 * @param string $rusername recipient name
 *
 */
function sed_send_translated_mail($rlang, $remail, $rusername)
{
	global $cfg, $usr, $lang;

	$is_global = true;
	$a = array($rlang, 'en', $cfg['defaultlang'],);
	foreach ($a as $v)
	{
		if ($v == $lang)
		{
			break;
		}
		$r = "{$cfg['system_dir']}/lang/$v/main.lang.php";
		if (file_exists($r))
		{
			require($r);
			$is_global = false;
			break;
		}
	}
	if($is_global)
	{
		global $L;
	}

	$rsubject = "{$cfg['maintitle']} - {$L['pm_notifytitle']}";
	$rbody = sprintf($L['pm_notify'], $rusername, htmlspecialchars($usr['name']), $cfg['mainurl'] . '/' . sed_url('pm', '', '', true));

	sed_mail($remail, $rsubject, $rbody);
}


/**
 * Returns allusertages
 *
 * @param array $ruser_array recipient language
 * @param string $remail recipient email
 * @param string $noidtext text if user_id = 0
 * @param bool $allgroups user all groups list
 *
 * @return array
 */
function sed_generate_usertags($ruser_array, $tag_preffix, $noidtext='', $allgroups = false)
{
	if ($ruser_array['user_id'] > 0)
	{
		global $sed_extrafields, $cfg, $L;
		$return_array = array(
				$tag_preffix."_LINK" => sed_build_user($ruser_array['user_id'], htmlspecialchars($ruser_array['user_name'])),
				$tag_preffix."_ID" => $ruser_array['user_id'],
				$tag_preffix."_PM" => sed_build_pm($ruser_array['user_id']),
				$tag_preffix."_NAME" => htmlspecialchars($ruser_array['user_name']),
				$tag_preffix."_PASSWORD" => $ruser_array['user_password'],
				$tag_preffix."_MAINGRP" => sed_build_group($ruser_array['user_maingrp']),
				$tag_preffix."_MAINGRPID" => $ruser_array['user_maingrp'],
				$tag_preffix."_MAINGRPSTARS" => sed_build_stars($sed_groups[$ruser_array['user_maingrp']]['level']),
				$tag_preffix."_MAINGRPICON" => sed_build_userimage($sed_groups[$ruser_array['user_maingrp']]['icon']),
				$tag_preffix."_COUNTRY" => sed_build_country($ruser_array['user_country']),
				$tag_preffix."_COUNTRYFLAG" => sed_build_flag($ruser_array['user_country']),
				$tag_preffix."_TEXT" => $cfg['parsebbcodeusertext'] ? sed_bbcode_parse($ruser_array['user_text'], true) : $ruser_array['user_text'],
				$tag_preffix."_AVATAR" => sed_build_userimage($ruser_array['user_avatar'], 'avatar'),
				$tag_preffix."_PHOTO" => sed_build_userimage($ruser_array['user_photo'], 'photo'),
				$tag_preffix."_SIGNATURE" => sed_build_userimage($ruser_array['user_signature'], 'sig'),
				$tag_preffix."_EMAIL" => sed_build_email($ruser_array['user_email'], $ruser_array['user_hideemail']),
				$tag_preffix."_SKIN" => $ruser_array['user_skin'],
				$tag_preffix."_WEBSITE" => $ruser_array['user_website'],
				$tag_preffix."_JOURNAL" => $ruser_array['user_journal'],
				$tag_preffix."_ICQ" => sed_build_icq($ruser_array['user_icq']),
				$tag_preffix."_MSN" => sed_build_msn($ruser_array['user_msn']),
				$tag_preffix."_IRC" => htmlspecialchars($ruser_array['user_irc']),
				$tag_preffix."_GENDER" => $ruser_array['user_gender'],
				$tag_preffix."_BIRTHDATE" => $ruser_array['user_birthdate'],
				$tag_preffix."_AGE" => $ruser_array['user_age'],
				$tag_preffix."_TIMEZONE" => sed_build_timezone($ruser_array['user_timezone']),
				$tag_preffix."_LOCATION" => htmlspecialchars($ruser_array['user_location']),
				$tag_preffix."_OCCUPATION" => htmlspecialchars($ruser_array['user_occupation']),
				$tag_preffix."_REGDATE" => @date($cfg['dateformat'], $ruser_array['user_regdate'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
				$tag_preffix."_LASTLOG" => @date($cfg['dateformat'], $ruser_array['user_lastlog'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
				$tag_preffix."_LOGCOUNT" => $ruser_array['user_logcount'],
				$tag_preffix."_POSTCOUNT" => $ruser_array['user_postcount'],
				$tag_preffix."_LASTIP" => $ruser_array['user_lastip']
		);

		if ($allgroups)
		{
			$return_array[$tag_preffix."_GROUPS"] = sed_build_groupsms($ruser_array['user_id'], FALSE, $ruser_array['user_maingrp']);
		}
		// Extra fields
		foreach($sed_extrafields['users'] as $i => $row)
		{
			$return_array[$tag_preffix."_".strtoupper($row['field_name'])] = sed_build_extrafields_data('user', $row['field_type'], $row['field_name'], $ruser_array['user_'.$row['field_name']]);
			$return_array[$tag_preffix."_".strtoupper($row['field_name']).'_TITLE'] = isset($L['user_'.$row['field_name'].'_title']) ? $L['user_'.$row['field_name'].'_title'] : $row['field_description'];
		}
	}
	else
	{
		$return_array[$tag_preffix."_LINK"]=$L['Deleted'];
		$return_array[$tag_preffix."_NAME"]=$L['Deleted'];
	}
	return $return_array;
}
/**
 * Delete private messages
 *
 * @param array $message_id messages ids
 * @param string $action delete or archive message
 *
 * @return bool true if  action sucsessfull
 */
function sed_remove_pm($message_id)
{
	global $usr, $db_pm, $cfg;
	if (!is_array($message_id))
	{
		return false;
	}

	foreach($message_id as $k => $v)
	{
		$msg[] = (int)sed_import($k, 'D', 'INT');
	}

	if (count($msg)>0)
	{
		$msg = '('.implode(',', $msg).')';
		$sql = sed_sql_query("SELECT * FROM $db_pm WHERE pm_id IN $msg");
		while($row = sed_sql_fetcharray($sql))
		{
			$id = $row['pm_id'];
			if (($row['pm_fromuserid'] == $usr['id'] && ($row['pm_tostate'] == 3 || $row['pm_tostate'] == 0)) ||
					($row['pm_touserid'] == $usr['id'] && $row['pm_fromstate'] == 3) ||
					($row['pm_fromuserid'] == $usr['id'] && $row['pm_touserid'] == $usr['id']))
			{
				if ($cfg['trash_pm'])
				{
					sed_trash_put('pm', $L['Private_Messages']." #".$id." ".$row['pm_title']." (".$row['pm_fromuser'].")", $id, $row);
				}
				$sql2 = sed_sql_query("DELETE FROM $db_pm WHERE pm_id = '$id'");
			}
			elseif($row['pm_fromuserid'] == $usr['id'] && ($row['pm_tostate'] != 3 || $row['pm_tostate'] != 0))
			{
				$sql2 = sed_sql_query("UPDATE $db_pm SET pm_fromstate = 3 WHERE pm_id = '$id'");
			}
			elseif($row['pm_touserid'] == $usr['id'] && $row['pm_fromstate'] != 3)
			{
				$sql2 = sed_sql_query("UPDATE $db_pm SET pm_tostate = 3 WHERE pm_id = '$id'");
			}
		}
	}
	return true;
}
/**
 * Star/Unstar private messages
 *
 * @param array $message_id messages ids
 *
 * @return bool true if  action sucsessfull
 */
function sed_star_pm($message_id)
{
	
	global $usr, $db_pm, $cfg;
	if (!is_array($message_id))
	{
		return false;
	}

	foreach($message_id as $k => $v)
	{
		$msg[] = (int)sed_import($k, 'D', 'INT');
	}

	if (count($msg)>0)
	{
		$msg = '('.implode(',', $msg).')';
		$sql = sed_sql_query("SELECT * FROM $db_pm WHERE pm_id IN $msg");
		while($row = sed_sql_fetcharray($sql))
		{
			$id = $row['pm_id'];
			if ($row['pm_fromuserid'] == $usr['id'] && $row['pm_touserid'] == $usr['id'])
			{
				$fromstate = ($row['pm_fromstate'] == 2) ?  1 : 2;
				$sql2 = sed_sql_query("UPDATE $db_pm SET pm_tostate = ".(int)$fromstate.", pm_fromstate = ".(int)$fromstate." WHERE pm_id = '$id'");
			}
			elseif ($row['pm_touserid'] == $usr['id'])
			{
				$tostate = ($row['pm_tostate'] == 2) ?  1 : 2;
				$sql2 = sed_sql_query("UPDATE $db_pm SET pm_tostate = ".(int)$tostate." WHERE pm_id = '$id'");
			}
			elseif ($row['pm_fromuserid'] == $usr['id'])
			{
				$fromstate = ($row['pm_fromstate'] == 2) ?  1 : 2;
				$sql2 = sed_sql_query("UPDATE $db_pm SET pm_fromstate = ".(int)$fromstate." WHERE pm_id = '$id'");
			}
		}
	}
	return true;
}

/**
 * User Private Message count
 * @param int $user_id User ID
 * @return array
 */
function sed_message_count($user_id=0)
{
	global $db_pm;
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_fromuserid = '".$user_id."' AND pm_fromstate <> '3'");
	$totalsentbox = sed_sql_result($sql, 0, "COUNT(*)");
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid = '".$user_id."' AND pm_tostate <> 3");
	$totalinbox = sed_sql_result($sql, 0, "COUNT(*)");

	return array($totalsentbox, $totalinbox);
}


?>
