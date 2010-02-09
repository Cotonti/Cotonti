<?php
/**
 * Administration panel - Disk cache
 *
 * @package Cotonti
 * @version 0.6.1
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

define('SED_DISKCACHE_ONLYFILES', '*files*');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.cache.disk.inc'));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=cache&s=disk'), $L['adm_diskcache']);

/* === Hook === */
$extp = sed_getextplugins('admin.cache.disk.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'purge')
{
	$adminwarnings = (sed_check_xg() && sed_diskcache_clearall()) ? $L['adm_purgeall_done'] : $L['Error'];
}
elseif ($a == 'delete')
{
	$is_id = mb_strpos($id, '/') === false && mb_strpos($id, '\\') === false && $id != '.' && $id != '..';
	$is_onlyf = $id == SED_DISKCACHE_ONLYFILES;
	$adminwarnings = (sed_check_xg() && $is_id && sed_diskcache_clear($cfg['cache_dir'] . ($is_onlyf ? '' : "/$id"), !$is_onlyf)) ? $L['adm_delcacheitem'] : $L['Error'];
}

$is_adminwarnings = isset($adminwarnings);

$row = sed_diskcache_list();
$cachefiles = $cachesize = 0;
$ii = 0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.cache.disk.loop');
/* ===== */
foreach ($row as $i => $x)
{
	$cachefiles += $x[0];
	$cachesize += $x[1];
	$t->assign(array(
		'ADMIN_DISKCACHE_ITEM_DEL_URL' => sed_url('admin', 'm=cache&s=disk&a=delete&id=' . $i . '&' . sed_xg()),
		'ADMIN_DISKCACHE_ITEM_NAME' => $i,
		'ADMIN_DISKCACHE_FILES' => $x[0],
		'ADMIN_DISKCACHE_SIZE' => $x[1],
		'ADMIN_DISKCACHE_ROW_ODDEVEN' => sed_build_oddeven($ii)
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('DISKCACHE.ADMIN_DISKCACHE_ROW');
	$ii++;
}

$t->assign(array(
	'ADMIN_DISKCACHE_URL_REFRESH' => sed_url('admin', 'm=cache&s=disk'),
	'ADMIN_DISKCACHE_URL_PURGE' => sed_url('admin', 'm=cache&s=disk&a=purge&' . sed_xg()),
	'ADMIN_DISKCACHE_ADMINWARNINGS' => $adminwarnings,
	'ADMIN_DISKCACHE_CACHEFILES' => $cachefiles,
	'ADMIN_DISKCACHE_CACHESIZE' => $cachesize
));

/* === Hook === */
$extp = sed_getextplugins('admin.cache.disk.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('DISKCACHE');
if (SED_AJAX)
{
	$t->out('DISKCACHE');
}
else
{
	$adminmain = $t->text('DISKCACHE');
}

/**
 * Calculates directory size
 * It's helper function for sed_diskcache_list()
 *
 * @param string $dir Directory name
 * @param bool $do_subdirs true when enter subdirectories, otherwise false
 * @return array
 */
function sed_diskcache_calc($dir, $do_subdirs = true)
{
	$cnt = $sz = 0;

	foreach (glob("$dir/*") as $f)
	{
		if (is_file($f))
		{
			$cnt++;
			$sz += @filesize($f);
		}
		elseif (is_dir($f) && $do_subdirs)
		{
			$a = sed_diskcache_calc($f);
			$cnt += $a[0]/*files*/ + 1/*directory*/;
			$sz += $a[1];
		}
	}

	return array($cnt, $sz);
}

/**
 * Returns list of non-empty subdirectories in disk cache directory
 *
 * @global $cfg
 * @return array
 */
function sed_diskcache_list()
{
	global $cfg;

	$dir_a = array();

	$a = sed_diskcache_calc($cfg['cache_dir'], false);
	if ($a[0])
	{
		$dir_a[SED_DISKCACHE_ONLYFILES] = $a;
	}

	$pos = mb_strlen($cfg['cache_dir']) + 1;
	foreach (glob("{$cfg['cache_dir']}/*", GLOB_ONLYDIR) as $dir)
	{
		$a = sed_diskcache_calc($dir);
		if ($a[0])
		{
			$dir_a[mb_substr($dir, $pos)] = $a;
		}
	}

	return $dir_a;
}

/**
 * Clears disk cache directory
 *
 * @param string $dir Directory name
 * @param bool $do_subdirs true when enter subdirectories, otherwise false
 * @param bool $rm_dir true when remove directory, otherwise false
 * @return bool
 */
function sed_diskcache_clear($dir, $do_subdirs = true, $rm_dir = false)
{
	if (!is_dir($dir) || !is_writable($dir))
	{
		return false;
	}

	foreach (glob("$dir/*") as $f)
	{
		if (is_file($f))
		{
			@unlink($f);
		}
		elseif (is_dir($f) && $do_subdirs)
		{
			sed_diskcache_clear($f, true, true);
		}
	}

	if ($rm_dir)
	{
		@rmdir($dir);
	}

	return true;
}

/**
 * Clears disk cache completely
 *
 * @global $cfg
 * @return bool
 */
function sed_diskcache_clearall()
{
	global $cfg;

	sed_diskcache_clear($cfg['cache_dir'], false);
	foreach (glob("{$cfg['cache_dir']}/*", GLOB_ONLYDIR) as $dir)
	{
		sed_diskcache_clear($dir);
	}

	return true;
}
?>