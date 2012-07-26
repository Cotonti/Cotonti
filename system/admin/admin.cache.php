<?php
/**
 * Administration panel - Internal cache
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

$t = new XTemplate(cot_tplfile('admin.cache', 'core'));

$adminpath[] = array(cot_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(cot_url('admin', 'm=cache'), $L['adm_internalcache']);

/* === Hook === */
foreach (cot_getextplugins('admin.cache.first') as $pl)
{
	include $pl;
}
/* ===== */

if (!$cache)
{
	// Enforce cache loading
	require_once $cfg['system_dir'].'/cache.php';
	$cache = new Cache();
	$cache->init();
}

if ($a == 'purge' && $cache)
{
	if (cot_check_xg() && $cache->clear())
	{
		$db->update($db_users, array('user_auth' => ''), "user_auth != ''");
		cot_rc_consolidate();
		cot_message('adm_purgeall_done');
	}
	else
	{
		cot_error('Error');
	}
}
elseif ($a == 'delete')
{
	cot_check_xg();
	$name = $db->prep(cot_import('name', 'G', 'TXT'));

	$db->delete($db_cache, "c_name = '$name'") ? cot_message('adm_delcacheitem') : cot_error('Error');
}

if ($cache && $cache->mem)
{
	$info = $cache->get_info();
	if ($info['available'] < 0)
	{
		$info['available'] = '?';
	}
	$t->assign(array(
		'ADMIN_CACHE_MEMORY_DRIVER' => str_replace('_driver', '', $cache->mem_driver),
		'ADMIN_CACHE_MEMORY_PERCENTBAR' => ceil(($info['occupied'] / $info['max']) * 100),
		'ADMIN_CACHE_MEMORY_AVAILABLE' => $info['available'],
		'ADMIN_CACHE_MEMORY_MAX' => $info['max']
	));
	$t->parse('MAIN.ADMIN_CACHE_MEMORY');
}

$sql = $db->query("SELECT * FROM $db_cache WHERE 1 ORDER by c_name ASC");
$cachesize = 0;
$ii = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.cache.loop');
/* ===== */
foreach ($sql->fetchAll() as $row)
{
	$row['c_value'] = htmlspecialchars($row['c_value']);
	$row['size'] = mb_strlen($row['c_value']);
	$cachesize += $row['size'];
	$t->assign(array(
		'ADMIN_CACHE_ITEM_DEL_URL' => cot_url('admin', 'm=cache&a=delete&name='.$row['c_name'].'&'.cot_xg()),
		'ADMIN_CACHE_ITEM_NAME' => $row['c_name'],
		'ADMIN_CACHE_EXPIRE' => $row['c_expire'] > 0 ? cot_date('datetime_short', $row['c_expire']) : '-',
		'ADMIN_CACHE_SIZE' => $row['size'],
		'ADMIN_CACHE_VALUE' => ($a == 'showall') ? $row['c_value'] : cot_cutstring($row['c_value'], 80),
		'ADMIN_CACHE_ROW_ODDEVEN' => cot_build_oddeven($ii)
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.ADMIN_CACHE_ROW');
	$ii++;
}

$t->assign(array(
	'ADMIN_CACHE_URL_REFRESH' => cot_url('admin', 'm=cache'),
	'ADMIN_CACHE_URL_PURGE' => cot_url('admin', 'm=cache&a=purge&'.cot_xg()),
	'ADMIN_CACHE_URL_SHOWALL' => cot_url('admin', 'm=cache&a=showall'),
	'ADMIN_CACHE_CACHESIZE' => $cachesize
));

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.cache.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

?>