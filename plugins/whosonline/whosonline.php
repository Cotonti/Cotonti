<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Displays users who are currently online
 *
 * @package WhosOnline
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') || defined('COT_PLUG')) or die('Wrong URL.');

$sys['sublocation'] = $L['WhosOnline'];
// to update first
require_once $cfg['plugins_dir'].'/whosonline/whosonline.header.main.php';
require_once cot_incfile('users', 'module');

$pl_cfg = $cfg['plugin']['whosonline'];
$maxuserssperpage = is_numeric($pl_cfg['maxusersperpage']) ? $pl_cfg['maxusersperpage'] : 0;
list($pg, $d, $durl) = cot_import_pagenav('d', $maxuserssperpage);
$maxusers = 0;
if(isset($cfg['plugin']['hits']))
{
	require_once cot_incfile('hits', 'plug');
	$stats = $db->query("SELECT stat_value FROM $db_stats WHERE stat_name='maxusers' LIMIT 1")->fetch();
	$maxusers = $stats[0];
}
$count_users = 0;
$count_guests = 0;

if(cot_plugin_active('hiddengroups'))
{
	require_once cot_incfile('hiddengroups', 'plug');
	$hiddenusers = cot_hiddengroups_get(cot_hiddengroups_mode(), 'users');
}
$ipsearch = cot_plugin_active('ipsearch');

$out['subtitle'] = $L['WhosOnline'];
$out['desc'] = $L['Users'].', '.mb_strtolower($L['Guests'].' '.$L['NowOnline'].' '.$sys['domain'].' - '.$L['Online'].' '. $L['Statistics']);
$out['keywords'] = mb_strtolower($L['WhosOnline'].' '.$L['Guests'].' '.$L['Users'].' '.$sys['domain']);

$join_condition = "LEFT JOIN $db_users AS u ON u.user_id=o.online_userid";
if($pl_cfg['disable_guests'])
{
	$where = "WHERE o.online_userid > 0";
}
$is_user_check = 'IF(o.online_userid > 0,1,0) as is_user';
$limit = $maxuserssperpage ? "LIMIT $d, $maxuserssperpage" : '';
$sql_users = $db->query("
	SELECT DISTINCT u.*, o.*, $is_user_check
	FROM $db_online AS o
	$join_condition $where
	ORDER BY is_user DESC, online_lastseen DESC $limit
");
$sql_users_count = $db->query("SELECT COUNT(*) as cnt, $is_user_check FROM $db_online as o $where GROUP BY is_user");
$who_guests = 0;
$who_users = 0;
foreach ($sql_users_count as $row)
{
	if ($row['is_user'])
	{
		$who_users = (int)$row['cnt'];
	}
	else
	{
		$who_guests = (int)$row['cnt'];
	}
}
$totallines = $who_users + $who_guests;

if ((!$cfg['easypagenav'] && $durl > 0 && $maxuserssperpage > 0 && $durl % $maxuserssperpage > 0)
	|| ($d > 0 && $d >= $totallines))
{
	cot_redirect(cot_url('whosonline'));
}
$pagenav = cot_pagenav('whosonline', array('d' => $durl), $d, $totallines, $maxuserssperpage);

/* === Hooks - Part1 : Set === */
$users_loop_hook = cot_getextplugins('whosonline.users.loop');
$guests_loop_hook = cot_getextplugins('whosonline.guests.loop');
/* ===== */
if ($maxuserssperpage)
{
	$fpu = $who_users/$maxuserssperpage;
	if ($durl > ceil($fpu))
	{
		$guest_start_num = ($maxuserssperpage - ($who_users % $maxuserssperpage)) + ($durl -1 - ceil($fpu)) * $maxuserssperpage;
	}
}
foreach ($sql_users->fetchAll() as $row)
{
	if($hiddenusers && in_array($row['user_id'], $hiddenusers))
	{
		if(cot_auth('plug', 'hiddengroups', '1'))
		{
			$t->assign('USER_HIDDEN', $L['Hidden']);
		}
		else continue;
	}
	if ($row['is_user'])
	{
		$count_users++;
		$url_ipsearch = cot_url('admin',	'm=other&p=ipsearch&a=search&id='.$row['online_ip'].'&'.cot_xg());
		$t->assign(array(
				'USER_LOCATION' => htmlspecialchars($row['online_location']),
				'USER_SUBLOCATION' => htmlspecialchars($row['online_subloc']),
				'USER_IP' => $ipsearch ? cot_rc_link($url_ipsearch, $row['online_ip']) : $row['online_ip'],
				'USER_IP_URL' => $ipsearch ? $url_ipsearch : '',
				'USER_LINK' => cot_build_user($row['online_userid'], htmlspecialchars($row['online_name'])),
				'USER_LASTSEEN' => cot_build_timegap($row['online_lastseen'], $sys['now'])
		));
		$t->assign(cot_generate_usertags($row, 'USER_'));
		/* === Hook - Part2 : Include === */
		foreach ($users_loop_hook as $pl)
		{
			include $pl;
		}
		/* ===== */
		$t->parse('MAIN.USERS');
	}
	else
	{
		$count_guests++;
		$url_ipsearch = cot_url('admin', 'm=other&p=ipsearch&a=search&id='.$row['online_ip'].'&'.cot_xg());
		$t->assign(array(
				'GUEST_LOCATION' => htmlspecialchars($row['online_location']),
				'GUEST_SUBLOCATION' => htmlspecialchars($row['online_subloc']),
				'GUEST_IP' => $ipsearch ? cot_rc_link($url_ipsearch, $row['online_ip']) : $row['online_ip'],
				'GUEST_IP_URL' => $ipsearch ? $url_ipsearch : '',
				'GUEST_NUMBER' => $count_guests + $guest_start_num,
				'GUEST_LASTSEEN' => cot_build_timegap($row['online_lastseen'], $sys['now'])
		));
		/* === Hook - Part2 : Include === */
		foreach ($guests_loop_hook as $pl)
		{
			include $pl;
		}
		/* ===== */
		$t->parse('MAIN.GUESTS');
	}
	//if (($count_users + $count_guests) >= $maxuserssperpage) break;
}
$sql_users->closeCursor();

$t->assign(array(
	'WHO_PAGINATION' => $pagenav['main'],
	'WHO_PAGEPREV' => $pagenav['prev'],
	'WHO_PAGENEXT' => $pagenav['next'],
	'WHO_CURRENTPAGE' => $pagenav['current'],
	'WHO_TOTALLINES' => $totallines,
	'WHO_MAXPERPAGE' => $maxuserssperpage,
	'WHO_TOTALPAGES' => $pagenav['total'],
	'STAT_MAXUSERS' => $maxusers,
	'STAT_COUNT_USERS' => $who_users,
	'STAT_COUNT_GUESTS' => $who_guests,
	'USERS' => cot_declension($who_users, $Ls['Members'], true),
	'GUESTS' => cot_declension($who_guests, $Ls['Guests'], true)
));
