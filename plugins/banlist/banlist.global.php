<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Banlist
 *
 * @package Banlist
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

cot::$db->registerTable('banlist');

$userip = explode('.', $usr['ip']);
$ipmasks = "('".$userip[0].'.'.$userip[1].'.'.$userip[2].'.'.$userip[3]."','".$userip[0].'.'.$userip[1].'.'.$userip[2].".*','".$userip[0].'.'.$userip[1].".*.*','".$userip[0].".*.*.*')";
$user_email = $usr['profile']['user_email'];
if ($user_email) {
	$user_email_mask = mb_strstr($user_email, '@');
	$user_email_mask_multi = explode('.', $user_email_mask);
}
else
{
	$user_email = $user_email_mask = $user_email_mask_multi = '-';
}

$sql = $db->query("SELECT banlist_id, banlist_ip, banlist_reason, banlist_expire, banlist_email
	FROM $db_banlist WHERE banlist_ip IN ".$ipmasks.
	" OR banlist_email='".$db->prep($user_email_mask).
	"' OR banlist_email='".$db->prep($user_email_mask_multi[0]).
	"' OR banlist_email='".$db->prep($user_email).
	($usr['name'] ? "' OR banlist_email='".$db->prep($usr['name']) : '').
	"' LIMIT 1");

if ($sql->rowCount() > 0)
{
	$row = $sql->fetch();
	$sql->closeCursor();
	if ($sys['now'] > $row['banlist_expire'] && $row['banlist_expire'] > 0)
	{
		$sql = $db->delete($db_banlist, "banlist_id='".$row['banlist_id']."' LIMIT 1");
	}
	else
	{
		require_once cot_langfile('banlist', 'plug');
		$banlist_email_mask = mb_strpos($row['banlist_email'], '.') ? $row['banlist_email'] : $row['banlist_email'].'.';
		if ($usr['name'] && $row['banlist_email'] == $usr['name'])
		{
			$reason = $L['banlist_blocked_login'];
		}
		elseif ($row['banlist_email'])
		{
			$reason = $L['banlist_blocked_email'];
		}
		else
		{
			$reason = $L['banlist_blocked_ip'];
		}

		$expiretime = ($row['banlist_expire'] > 0) ? cot_date('datetime_medium', $row['banlist_expire']) : $L['banlist_foreverbanned'];
		$disp = cot_rc('banlist_banned', array($reason, $row['banlist_reason'], $expiretime));
		cot_die_message(403, true, '', $disp);
	}
}
