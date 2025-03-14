<?php
/**
 * Private Messages function library.
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\modules\pm\services\PrivateMessageService;

defined('COT_CODE') or die('Wrong URL');

/*
 * PM States
 * 0 - new message (Unread)
 * 1 - read message
 * 2 - starred message
 * 3 - deleted message
 *
 * @todo move to PM dictionary
*/
const COT_PM_STATE_UNREAD = 0;
const COT_PM_STATE_READ = 1;
const COT_PM_STATE_STARRED = 2;
const COT_PM_STATE_DELETED = 3;

// Requirements
require_once cot_langfile('pm', 'module');
require_once cot_incfile('pm', 'module', 'resources');

$parser = !empty(Cot::$sys['parser']) ? Cot::$sys['parser'] : Cot::$cfg['parser'];
$editor = isset(Cot::$cfg['plugin'][$parser]) ? Cot::$cfg['plugin'][$parser]['editor'] : null;

Cot::$db->registerTable('pm');

Cot::$cfg['pm']['turnajax'] =
    Cot::$cfg['pm']['turnajax']
    && Cot::$cfg['jquery']
    && Cot::$cfg['turnajax']
    && $editor != 'elrte'
    && $editor != 'epiceditor';

/**
 * Send private message to user
 * @param int $to Recipient user ID
 * @param string $subject
 * @param string $text Message body
 * @param int $from From user ID
 * @param int $fromState
 * @return int|false Message ID or FALSE in fail
 *
 * @deprecated in 0.9.26
 * @see PrivateMessageService::send
 */
function cot_send_pm($to, $subject, $text, $from = null, $fromState = 0)
{
    $from = $from !== null ? $from : null;
    return PrivateMessageService::getInstance()->send((int) $to, $subject, $text, $from, $fromState);
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

/**
 * Generates message list widget
 * @param int $userId
 * @param int $count
 * @param string $template
 * @param string $order
 * @param string $condition
 * @param string $state 'all', or comma separated states. '0' - unread, '0,2' unread or starred messages
 * @param bool $senderUnique one message per sender
 * @param string $pagination Pagination symbol
 * @param int $cache_ttl Cache lifetime in seconds, 0 disables cache
 * @return string
 * @todo
 */
function cot_pm_list(
    $userId,
    $count = 0,
    $template = '',
    $order = '',
    $condition = '',
    $state = COT_PM_STATE_UNREAD,
    $senderUnique = false,
    $pagination = '',
    $cache_ttl = null
) {

}