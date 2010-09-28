<?php
/**
 * PM function library.
 *
 * @package pm
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
cot_require_lang('pm', 'module');
cot_require_rc('pm');

// Global variables
$GLOBALS['db_pm'] = (isset($GLOBALS['db_pm'])) ? $GLOBALS['db_pm'] : $GLOBALS['db_x'] . 'pm';

/**
 * Returns usertags
 *
 * @param string $rlang recipient language
 * @param string $remail recipient email
 * @param string $rusername recipient name
 *
 */
function cot_send_translated_mail($rlang, $remail, $rusername)
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
	$rbody = sprintf($L['pm_notify'], $rusername, htmlspecialchars($usr['name']), $cfg['mainurl'] . '/' . cot_url('pm', '', '', true));

	cot_mail($remail, $rsubject, $rbody);
}

/**
 * Delete private messages
 *
 * @param array $message_id messages ids
 * @param string $action delete or archive message
 *
 * @return bool true if  action sucsessfull
 */
function cot_remove_pm($message_id)
{
	global $usr, $db_pm, $cfg;
	if (!is_array($message_id))
	{
		return false;
	}

	foreach($message_id as $k => $v)
	{
		$msg[] = (int)cot_import($k, 'D', 'INT');
	}

	if (count($msg)>0)
	{
		$msg = '('.implode(',', $msg).')';
		$sql = cot_db_query("SELECT * FROM $db_pm WHERE pm_id IN $msg");
		while($row = cot_db_fetcharray($sql))
		{
			$id = $row['pm_id'];
			if (($row['pm_fromuserid'] == $usr['id'] && ($row['pm_tostate'] == 3 || $row['pm_tostate'] == 0)) ||
					($row['pm_touserid'] == $usr['id'] && $row['pm_fromstate'] == 3) ||
					($row['pm_fromuserid'] == $usr['id'] && $row['pm_touserid'] == $usr['id']))
			{
				$sql2 = cot_db_query("DELETE FROM $db_pm WHERE pm_id = '$id'");
			}
			elseif($row['pm_fromuserid'] == $usr['id'] && ($row['pm_tostate'] != 3 || $row['pm_tostate'] != 0))
			{
				$sql2 = cot_db_query("UPDATE $db_pm SET pm_fromstate = 3 WHERE pm_id = '$id'");
			}
			elseif($row['pm_touserid'] == $usr['id'] && $row['pm_fromstate'] != 3)
			{
				$sql2 = cot_db_query("UPDATE $db_pm SET pm_tostate = 3 WHERE pm_id = '$id'");
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
function cot_star_pm($message_id)
{
	
	global $usr, $db_pm, $cfg;
	if (!is_array($message_id))
	{
		return false;
	}

	foreach($message_id as $k => $v)
	{
		$msg[] = (int)cot_import($k, 'D', 'INT');
	}

	if (count($msg)>0)
	{
		$msg = '('.implode(',', $msg).')';
		$sql = cot_db_query("SELECT * FROM $db_pm WHERE pm_id IN $msg");
		while($row = cot_db_fetcharray($sql))
		{
			$id = $row['pm_id'];
			if ($row['pm_fromuserid'] == $usr['id'] && $row['pm_touserid'] == $usr['id'])
			{
				$fromstate = ($row['pm_fromstate'] == 2) ?  1 : 2;
				$sql2 = cot_db_query("UPDATE $db_pm SET pm_tostate = ".(int)$fromstate.", pm_fromstate = ".(int)$fromstate." WHERE pm_id = '$id'");
			}
			elseif ($row['pm_touserid'] == $usr['id'])
			{
				$tostate = ($row['pm_tostate'] == 2) ?  1 : 2;
				$sql2 = cot_db_query("UPDATE $db_pm SET pm_tostate = ".(int)$tostate." WHERE pm_id = '$id'");
			}
			elseif ($row['pm_fromuserid'] == $usr['id'])
			{
				$fromstate = ($row['pm_fromstate'] == 2) ?  1 : 2;
				$sql2 = cot_db_query("UPDATE $db_pm SET pm_fromstate = ".(int)$fromstate." WHERE pm_id = '$id'");
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
function cot_message_count($user_id=0)
{
	global $db_pm;
	$sql = cot_db_query("SELECT COUNT(*) FROM $db_pm WHERE pm_fromuserid = '".$user_id."' AND pm_fromstate <> '3'");
	$totalsentbox = cot_db_result($sql, 0, "COUNT(*)");
	$sql = cot_db_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid = '".$user_id."' AND pm_tostate <> 3");
	$totalinbox = cot_db_result($sql, 0, "COUNT(*)");

	return array($totalsentbox, $totalinbox);
}


?>
