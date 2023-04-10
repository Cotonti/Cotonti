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

Cot::$db->registerTable('banlist');

$userip = explode('.', Cot::$usr['ip']);
$ipmasks = "('".$userip[0].'.'.$userip[1].'.'.$userip[2].'.'.$userip[3]."','".$userip[0].'.'.$userip[1].'.'.$userip[2].".*','".$userip[0].'.'.$userip[1].".*.*','".$userip[0].".*.*.*')";
$user_email = isset(Cot::$usr['profile'])? Cot::$usr['profile']['user_email'] : null;
if ($user_email) {
	$user_email_mask = mb_strstr($user_email, '@');
	$user_email_mask_multi = explode('.', $user_email_mask);
} else {
	$user_email = $user_email_mask = $user_email_mask_multi = '-';
}

$sql = Cot::$db->query("SELECT banlist_id, banlist_ip, banlist_reason, banlist_expire, banlist_email
	FROM $db_banlist WHERE banlist_ip IN ".$ipmasks.
	" OR banlist_email='".Cot::$db->prep($user_email_mask).
	"' OR banlist_email='".Cot::$db->prep($user_email_mask_multi[0]).
	"' OR banlist_email='".Cot::$db->prep($user_email).
	(Cot::$usr['name'] ? "' OR banlist_email='".Cot::$db->prep(Cot::$usr['name']) : '').
	"' LIMIT 1");

if ($sql->rowCount() > 0) {
	$row = $sql->fetch();
	$sql->closeCursor();
	if (Cot::$sys['now'] > $row['banlist_expire'] && $row['banlist_expire'] > 0) {
		$sql = Cot::$db->delete($db_banlist, "banlist_id='".$row['banlist_id']."' LIMIT 1");

	} else {
		require_once cot_langfile('banlist', 'plug');
		$banlist_email_mask = mb_strpos($row['banlist_email'], '.') ? $row['banlist_email'] : $row['banlist_email'].'.';
		if (Cot::$usr['name'] && $row['banlist_email'] == Cot::$usr['name'])
		{
			$reason = Cot::$L['banlist_blocked_login'];
		}
		elseif ($row['banlist_email'])
		{
			$reason = Cot::$L['banlist_blocked_email'];
		}
		else
		{
			$reason = Cot::$L['banlist_blocked_ip'];
		}

		$expiretime = ($row['banlist_expire'] > 0) ? cot_date('datetime_medium', $row['banlist_expire']) : Cot::$L['banlist_foreverbanned'];
		$disp = cot_rc('banlist_banned', array($reason, $row['banlist_reason'], $expiretime));
		cot_die_message(403, true, '', $disp);
	}
}
