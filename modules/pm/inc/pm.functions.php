<?php
/**
 * PM function library.
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
require_once cot_langfile('pm', 'module');
require_once cot_incfile('pm', 'module', 'resources');

$parser = ! empty(cot::$sys['parser']) ? cot::$sys['parser'] : cot::$cfg['parser'];
$editor = cot::$cfg['plugin'][$parser]['editor'];

cot::$db->registerTable('pm');

cot::$cfg['pm']['turnajax'] = cot::$cfg['pm']['turnajax'] && cot::$cfg['jquery'] && cot::$cfg['turnajax'] && $editor != 'elrte' && $editor != 'epiceditor';

/**
 * Send an email in the recipient's language
 *
 * @param string $rlang Recipient language
 * @param string $remail Recipient email
 * @param string $rusername Recipient name
 */
function cot_send_translated_mail($rlang, $remail, $rusername)
{
	global $cfg, $usr;

	require_once cot_langfile('pm', 'module', $cfg['defaultlang'], $rlang);
	if (!$L || !isset($L['pm_notify']))
	{
		global $L;
	}

	$rsubject = $L['pm_notifytitle'];
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
 * @global CotDB $db
 */
function cot_remove_pm($message_id)
{
	global $db, $usr, $db_pm, $cfg;
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
		$sql = $db->query("SELECT * FROM $db_pm WHERE pm_id IN $msg");
		while($row = $sql->fetch())
		{
			$id = $row['pm_id'];
			if (($row['pm_fromuserid'] == $usr['id'] && ($row['pm_tostate'] == 3 || $row['pm_tostate'] == 0)) ||
					($row['pm_touserid'] == $usr['id'] && $row['pm_fromstate'] == 3) ||
					($row['pm_fromuserid'] == $usr['id'] && $row['pm_touserid'] == $usr['id']))
			{
				$sql2 = $db->delete($db_pm, "pm_id = $id");
			}
			elseif($row['pm_fromuserid'] == $usr['id'] && ($row['pm_tostate'] != 3 || $row['pm_tostate'] != 0))
			{
				$sql2 = $db->update($db_pm, array('pm_fromstate' => '3'), "pm_id = $id");
			}
			elseif($row['pm_touserid'] == $usr['id'] && $row['pm_fromstate'] != 3)
			{
				$sql2 = $db->update($db_pm, array('pm_tostate' => '3'), "pm_id = $id");
			}
		}
		$sql->closeCursor();
	}
	return true;
}

/**
 * Star/Unstar private messages
 *
 * @param array $message_id messages ids
 *
 * @return bool true if  action sucsessfull
 * @global CotDB $db
 */
function cot_star_pm($message_id)
{

	global $db, $usr, $db_pm, $cfg;
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
		$sql = $db->query("SELECT * FROM $db_pm WHERE pm_id IN $msg");
		while($row = $sql->fetch())
		{
			$id = $row['pm_id'];
			if ($row['pm_fromuserid'] == $usr['id'] && $row['pm_touserid'] == $usr['id'])
			{
				$fromstate = ($row['pm_fromstate'] == 2) ?  1 : 2;
				$sql2 = $db->update($db_pm, array('pm_tostate' => (int)$fromstate, 'pm_fromstate' => (int)$fromstate), "pm_id = $id");
			}
			elseif ($row['pm_touserid'] == $usr['id'])
			{
				$tostate = ($row['pm_tostate'] == 2) ?  1 : 2;
				$sql2 = $db->update($db_pm, array('pm_tostate' => (int)$tostate), "pm_id = $id");
			}
			elseif ($row['pm_fromuserid'] == $usr['id'])
			{
				$fromstate = ($row['pm_fromstate'] == 2) ?  1 : 2;
				$sql2 = $db->update($db_pm, array('pm_fromstate' => (int)$fromstate), "pm_id = $id");
			}
		}
		$sql->closeCursor();
	}
	return true;
}

/**
 * User Private Message count
 * @param int $user_id User ID
 * @return array
 * @global CotDB $db
 */
function cot_message_count($user_id=0)
{
	global $db, $db_pm;
	$sql = $db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_fromuserid = $user_id AND pm_fromstate <> 3");
	$totalsentbox = $sql->fetchColumn();
	$sql = $db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid = $user_id AND pm_tostate <> 3");
	$totalinbox = $sql->fetchColumn();

	return array($totalsentbox, $totalinbox);
}

/**
 * Returns user PM link
 *
 * @param int $user User ID
 * @return string
 */
function cot_build_pm($user)
{
	global $L;
	return cot_rc('pm_link', array('url' => cot_url('pm', 'm=send&to='.$user).'" title="'.$L['pm_sendnew']));
}
