<?php
/**
 * Administration panel - Disk cache
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

define('COT_DISKCACHE_ONLYFILES', '*files*');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

$t = new XTemplate(cot_tplfile('admin.cache.disk', 'core'));

$adminpath[] = array(cot_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(cot_url('admin', 'm=cache&s=disk'), $L['adm_diskcache']);

/* === Hook === */
foreach (cot_getextplugins('admin.cache.disk.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'purge')
{
	if (cot_check_xg() && cot_diskcache_clearall())
	{
		cot_message('adm_purgeall_done');
		// Empty resource consolidation cache
		$db->delete($db_cache, "c_name = 'cot_rc_html'");
		cot_rc_consolidate();
	}
	else
	{
		cot_message('Error');
	}
}
elseif ($a == 'delete')
{
	$is_id = mb_strpos($id, '/') === false && mb_strpos($id, '\\') === false && $id != '.' && $id != '..';
	$is_onlyf = $id == COT_DISKCACHE_ONLYFILES;
	if (cot_check_xg() && $is_id && cot_diskcache_clear($cfg['cache_dir'] . ($is_onlyf ? '' : "/$id"), !$is_onlyf))
	{
		cot_message('adm_delcacheitem');
		if ($id == 'static' || $is_onlyf)
		{
			// Empty resource consolidation cache
			$db->delete($db_cache, "c_name = 'cot_rc_html'");
			cot_rc_consolidate();
		}
	}
	else
	{
		cot_message('Error');
	}
}

$row = cot_diskcache_list();
$cachefiles = $cachesize = 0;
$ii = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.cache.disk.loop');
/* ===== */
foreach ($row as $i => $x)
{
	$cachefiles += $x[0];
	$cachesize += $x[1];
	$t->assign(array(
		'ADMIN_DISKCACHE_ITEM_DEL_URL' => cot_url('admin', 'm=cache&s=disk&a=delete&id=' . $i . '&' . cot_xg()),
		'ADMIN_DISKCACHE_ITEM_NAME' => $i,
		'ADMIN_DISKCACHE_FILES' => $x[0],
		'ADMIN_DISKCACHE_SIZE' => $x[1],
		'ADMIN_DISKCACHE_ROW_ODDEVEN' => cot_build_oddeven($ii)
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.ADMIN_DISKCACHE_ROW');
	$ii++;
}

$t->assign(array(
	'ADMIN_DISKCACHE_URL_REFRESH' => cot_url('admin', 'm=cache&s=disk'),
	'ADMIN_DISKCACHE_URL_PURGE' => cot_url('admin', 'm=cache&s=disk&a=purge&' . cot_xg()),
	'ADMIN_DISKCACHE_CACHEFILES' => $cachefiles,
	'ADMIN_DISKCACHE_CACHESIZE' => $cachesize
));

cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('admin.cache.disk.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

/**
 * Calculates directory size
 * It's helper function for cot_diskcache_list()
 *
 * @param string $dir Directory name
 * @param bool $do_subdirs true when enter subdirectories, otherwise false
 * @return array
 */
function cot_diskcache_calc($dir, $do_subdirs = true)
{
	$cnt = $sz = 0;

	$glob = glob("$dir/*");
	if (is_array($glob))
	{
		foreach ($glob as $f)
		{
			if (is_file($f))
			{
				$cnt++;
				$sz += @filesize($f);
			}
			elseif (is_dir($f) && $do_subdirs)
			{
				$a = cot_diskcache_calc($f);
				$cnt += $a[0]/*files*/ + 1/*directory*/;
				$sz += $a[1];
			}
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
function cot_diskcache_list()
{
	global $cfg;

	$dir_a = array();

	$a = cot_diskcache_calc($cfg['cache_dir'], false);
	if ($a[0])
	{
		$dir_a[COT_DISKCACHE_ONLYFILES] = $a;
	}

	$pos = mb_strlen($cfg['cache_dir']) + 1;
	$glob = glob("{$cfg['cache_dir']}/*", GLOB_ONLYDIR);
	if (is_array($glob))
	{
		foreach ($glob as $dir)
		{
			$a = cot_diskcache_calc($dir);
			if ($a[0])
			{
				$dir_a[mb_substr($dir, $pos)] = $a;
			}
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
function cot_diskcache_clear($dir, $do_subdirs = true, $rm_dir = false)
{
	if (!is_dir($dir) || !is_writable($dir))
	{
		return false;
	}

	$glob = glob("$dir/*");
	if (is_array($glob))
	{
		foreach ($glob as $f)
		{
			if (is_file($f))
			{
				@unlink($f);
			}
			elseif (is_dir($f) && $do_subdirs)
			{
				cot_diskcache_clear($f, true, true);
			}
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
function cot_diskcache_clearall()
{
	global $cfg;

	cot_diskcache_clear($cfg['cache_dir'], false);
	$glob = glob("{$cfg['cache_dir']}/*", GLOB_ONLYDIR);
	if (is_array($glob))
	{
		foreach ($glob as $dir)
		{
			cot_diskcache_clear($dir);
		}
	}

	return true;
}

?>