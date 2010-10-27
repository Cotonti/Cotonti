<?php
/**
 * Administration panel - Internal cache
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.cache.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=cache'), $L['adm_internalcache']);

$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

/* === Hook === */
$extp = sed_getextplugins('admin.cache.first');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

if($a == 'purge')
{
	$adminwarnings = (sed_check_xg() && sed_cache_clearall()) ? $L['adm_purgeall_done'] : $L['Error'];
}
elseif($a == 'delete')
{
	$name = sed_sql_prep(sed_import('name', 'G', 'TXT'));
	sed_check_xg();
	$sql = sed_sql_query("DELETE FROM $db_cache WHERE c_name='$name'");

	$adminwarnings = ($sql) ? $L['adm_delcacheitem'] : $L['Error'];
}
elseif($a == 'clearhtml')
{
	$adminwarnings = sed_cache_clearhtml() ? $L['adm_bbcodes_clearcache_done'] : $L['Error'];
}

$is_adminwarnings = isset($adminwarnings);

$sql = sed_sql_query("SELECT * FROM $db_cache WHERE 1 ORDER by c_name ASC");
$cachesize = 0;
$ii = 0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.cache.loop');
/* ===== */
while($row = sed_sql_fetcharray($sql))
{
	$row['c_value'] = htmlspecialchars($row['c_value']);
	$row['size'] = mb_strlen($row['c_value']);
	$cachesize += $row['size'];
	$t -> assign(array(
		"ADMIN_CACHE_ITEM_DEL_URL" => sed_url('admin', 'm=cache&a=delete&name='.$row['c_name'].'&'.sed_xg()),
		"ADMIN_CACHE_ITEM_DEL_URL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=cache&a=delete&ajax=1&name='.$row['c_name'].'&'.sed_xg())."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
		"ADMIN_CACHE_ITEM_NAME" => $row['c_name'],
		"ADMIN_CACHE_EXPIRE" => ($row['c_expire'] - $sys['now']),
		"ADMIN_CACHE_SIZE" => $row['size'],
		"ADMIN_CACHE_VALUE" => ($a == 'showall') ? $row['c_value'] : sed_cutstring($row['c_value'], 80),
        "ADMIN_CACHE_ROW_ODDEVEN" => sed_build_oddeven($ii)
	));

	/* === Hook - Part2 : Include === */
	if(is_array($extp))
	{
		foreach($extp as $k => $pl)
		{
			include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
		}
	}
	/* ===== */

	$t -> parse("CACHE.ADMIN_CACHE_ROW");
    $ii++;
}

$t -> assign(array(
	"ADMIN_CACHE_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_CACHE_URL_REFRESH" => sed_url('admin', 'm=cache'),
	"ADMIN_CACHE_URL_REFRESH_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=cache&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
	"ADMIN_CACHE_URL_PURGE" => sed_url('admin', 'm=cache&a=purge&'.sed_xg()),
	"ADMIN_CACHE_URL_PURGE_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=cache&a=purge&ajax=1&id='.$row['c_name'].'&'.sed_xg())."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
	"ADMIN_CACHE_URL_SHOWALL" => sed_url('admin', 'm=cache&a=showall'),
	"ADMIN_CACHE_URL_SHOWALL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=cache&a=showall&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
	"ADMIN_CACHE_URL_CLEAR_HTML" => sed_url('admin', 'm=cache&a=clearhtml'),
	"ADMIN_CACHE_CACHESIZE" => $cachesize
));

/* === Hook  === */
$extp = sed_getextplugins('admin.cache.tags');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

$t -> parse("CACHE");
$adminmain = $t -> text("CACHE");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>