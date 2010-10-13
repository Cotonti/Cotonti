<?php
/**
 * Administration panel - Internal cache
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

$t = new XTemplate(cot_skinfile('admin.cache'));

$adminpath[] = array(cot_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(cot_url('admin', 'm=cache'), $L['adm_internalcache']);

/* === Hook === */
foreach (cot_getextplugins('admin.cache.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'purge' && $cot_cache)
{
	(cot_check_xg() && $cot_cache->clear()) ? cot_message('adm_purgeall_done') : cot_error('Error');
}
elseif ($a == 'delete')
{
	cot_check_xg();
	$name = $cot_db->prep(cot_import('name', 'G', 'TXT'));

	$cot_db->delete($db_cache, "c_name = '$name'") ? cot_message('adm_delcacheitem') : cot_error('Error');
}

if ($cot_cache && $cot_cache->mem)
{
	$info = $cot_cache->get_info();
	if ($info['available'] < 0)
	{
		$info['available'] = '?';
	}
	$t->assign(array(
		'ADMIN_CACHE_MEMORY_DRIVER' => str_replace('_driver', '', $cot_cache->mem_driver),
		'ADMIN_CACHE_MEMORY_PERCENTBAR' => ceil(($info['occupied'] / $info['max']) * 100),
		'ADMIN_CACHE_MEMORY_AVAILABLE' => $info['available'],
		'ADMIN_CACHE_MEMORY_MAX' => $info['max']
	));
	$t->parse('MAIN.ADMIN_CACHE_MEMORY');
}

$sql = $cot_db->query("SELECT * FROM $db_cache WHERE 1 ORDER by c_name ASC");
$cachesize = 0;
$ii = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.cache.loop');
/* ===== */
while ($row = $sql->fetch())
{
	$row['c_value'] = htmlspecialchars($row['c_value']);
	$row['size'] = mb_strlen($row['c_value']);
	$cachesize += $row['size'];
	$t->assign(array(
		'ADMIN_CACHE_ITEM_DEL_URL' => cot_url('admin', 'm=cache&a=delete&name='.$row['c_name'].'&'.cot_xg()),
		'ADMIN_CACHE_ITEM_NAME' => $row['c_name'],
		'ADMIN_CACHE_EXPIRE' => ($row['c_expire'] - $sys['now']),
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
if (COT_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>