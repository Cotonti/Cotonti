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

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.cache'));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=cache'), $L['adm_internalcache']);

/* === Hook === */
foreach (sed_getextplugins('admin.cache.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'purge' && $cot_cache)
{
	(sed_check_xg() && $cot_cache->clear()) ? sed_message('adm_purgeall_done') : sed_error('Error');
}
elseif ($a == 'delete')
{
	sed_check_xg();
	$name = sed_sql_prep(sed_import('name', 'G', 'TXT'));

	sed_sql_delete($db_cache, "c_name = '$name'") ? sed_message('adm_delcacheitem') : sed_error('Error');
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
	$t->parse('CACHE.ADMIN_CACHE_MEMORY');
}

$sql = sed_sql_query("SELECT * FROM $db_cache WHERE 1 ORDER by c_name ASC");
$cachesize = 0;
$ii = 0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.cache.loop');
/* ===== */
while ($row = sed_sql_fetcharray($sql))
{
	$row['c_value'] = htmlspecialchars($row['c_value']);
	$row['size'] = mb_strlen($row['c_value']);
	$cachesize += $row['size'];
	$t->assign(array(
		'ADMIN_CACHE_ITEM_DEL_URL' => sed_url('admin', 'm=cache&a=delete&name='.$row['c_name'].'&'.sed_xg()),
		'ADMIN_CACHE_ITEM_NAME' => $row['c_name'],
		'ADMIN_CACHE_EXPIRE' => ($row['c_expire'] - $sys['now']),
		'ADMIN_CACHE_SIZE' => $row['size'],
		'ADMIN_CACHE_VALUE' => ($a == 'showall') ? $row['c_value'] : sed_cutstring($row['c_value'], 80),
		'ADMIN_CACHE_ROW_ODDEVEN' => sed_build_oddeven($ii)
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
	'ADMIN_CACHE_URL_REFRESH' => sed_url('admin', 'm=cache'),
	'ADMIN_CACHE_URL_PURGE' => sed_url('admin', 'm=cache&a=purge&'.sed_xg()),
	'ADMIN_CACHE_URL_SHOWALL' => sed_url('admin', 'm=cache&a=showall'),
	'ADMIN_CACHE_CACHESIZE' => $cachesize
));

if (sed_check_messages())
{
	$t->assign('MESSAGE_TEXT', sed_implode_messages());
	$t->parse('MAIN.MESSAGE');
	sed_clear_messages();
}

/* === Hook  === */
foreach (sed_getextplugins('admin.cache.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (SED_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>