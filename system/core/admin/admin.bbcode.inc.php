<?php
/**
 * Administration panel - BBCode editor
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Trustmaster, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.bbcode.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=bbcode'), $L['adm_bbcodes']);
$adminhelp = $L['adm_help_bbcodes'];

$a = sed_import('a', 'G', 'ALP');
$id = (int) sed_import('id', 'G', 'INT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

/* === Hook === */
$extp = sed_getextplugins('admin.bbcode.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if($a == 'add')
{
	$bbc['name'] = sed_import('bbc_name', 'P', 'ALP');
	$bbc['mode'] = sed_import('bbc_mode', 'P', 'ALP');
	$bbc['pattern'] = sed_import('bbc_pattern', 'P', 'HTM');
	$bbc['priority'] = sed_import('bbc_priority', 'P', 'INT');
	$bbc['container'] = sed_import('bbc_container', 'P', 'BOL');
	$bbc['replacement'] = sed_import('bbc_replacement', 'P', 'HTM');
	$bbc['postrender'] = sed_import('bbc_postrender', 'P', 'BOL');
	if(!empty($bbc['name']) && !empty($bbc['pattern']) && !empty($bbc['replacement']))
	{
		sed_bbcode_clearcache();
		$adminwarnings = (sed_bbcode_add($bbc['name'], $bbc['mode'], $bbc['pattern'], $bbc['replacement'], $bbc['container'], $bbc['priority'], '', $bbc['postrender'])) ? $L['adm_bbcodes_added'] : $L['Error'];
	}
	else
	{
		$adminwarnings = $L['Error'];
	}
}
elseif($a == 'upd' && $id > 0)
{
	$bbc['name'] = sed_import('bbc_name', 'P', 'ALP');
	$bbc['mode'] = sed_import('bbc_mode', 'P', 'ALP');
	$bbc['pattern'] = sed_import('bbc_pattern', 'P', 'HTM');
	$bbc['priority'] = sed_import('bbc_priority', 'P', 'INT');
	$bbc['container'] = sed_import('bbc_container', 'P', 'BOL');
	$bbc['replacement'] = sed_import('bbc_replacement', 'P', 'HTM');
	$bbc['postrender'] = sed_import('bbc_postrender', 'P', 'BOL');
	$bbc['enabled'] = sed_import('bbc_enabled', 'P', 'BOL');
	if(!empty($bbc['name']) && !empty($bbc['pattern']) && !empty($bbc['replacement']))
	{
		sed_bbcode_clearcache();
		$adminwarnings = (sed_bbcode_update($id, $bbc['enabled'], $bbc['name'], $bbc['mode'], $bbc['pattern'], $bbc['replacement'], $bbc['container'], $bbc['priority'], $bbc['postrender'])) ? $L['adm_bbcodes_updated'] : $L['Error'];
	}
	else
	{
		$adminwarnings = $L['Error'];
	}
}
elseif($a == 'del' && $id > 0)
{
	sed_bbcode_clearcache();
	$adminwarnings = (sed_bbcode_remove($id)) ? $L['adm_bbcodes_removed'] : $L['Error'];
}
elseif($a == 'clearcache')
{
	sed_bbcode_clearcache();
	$adminwarnings = sed_cache_clearhtml() ? $L['adm_bbcodes_clearcache_done'] : $L['Error'];
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_rowcount($db_bbcode);
if($cfg['jquery'])
{
	$pagnav = sed_pagination(sed_url('admin','m=bbcode'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'ajaxSend', "url: '".sed_url('admin','m=bbcode&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=bbcode'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'ajaxSend', "url: '".sed_url('admin','m=bbcode&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
}
else
{
	$pagnav = sed_pagination(sed_url('admin','m=bbcode'), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=bbcode'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
}
$bbc_modes = array('str', 'pcre', 'callback');
$res = sed_sql_query("SELECT * FROM $db_bbcode ORDER BY bbc_priority LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.banlist.loop');
/* ===== */
while($row = sed_sql_fetchassoc($res))
{
	foreach($bbc_modes as $val)
	{
		$t -> assign(array(
			"ADMIN_BBCODE_ROW_MODE_ITEM_SELECTED" => ($val == $row['bbc_mode']) ? ' selected="selected"' : '',
			"ADMIN_BBCODE_ROW_MODE_ITEM" => $val
		));
		$t -> parse("BBCODE.ADMIN_BBCODE_ROW.ADMIN_BBCODE_MODE_ROW");
	}
	for($i = 1; $i < 256; $i++)
	{
		$t -> assign(array(
			"ADMIN_BBCODE_ROW_PRIO_ITEM_SELECTED" => ($i == $row['bbc_priority']) ? ' selected="selected"' : '',
			"ADMIN_BBCODE_ROW_PRIO_ITEM" => $i
		));
		$t -> parse("BBCODE.ADMIN_BBCODE_ROW.ADMIN_BBCODE_PRIO_ROW");
	}
	$t -> assign(array(
		"ADMIN_BBCODE_ROW_BBC_NAME" => $row['bbc_name'],
		"ADMIN_BBCODE_ROW_ENABLED" => $row['bbc_enabled'] ? ' checked="checked"' : '',
		"ADMIN_BBCODE_ROW_CONTAINER" => $row['bbc_container'] ? ' checked="checked"' : '',
		"ADMIN_BBCODE_ROW_PATTERN" => $row['bbc_pattern'],
		"ADMIN_BBCODE_ROW_REPLACEMENT" => $row['bbc_replacement'],
		"ADMIN_BBCODE_ROW_PLUG" => $row['bbc_plug'],
		"ADMIN_BBCODE_ROW_POSTRENDER" => $row['bbc_postrender'] ? ' checked="checked"' : '',
		"ADMIN_BBCODE_ROW_UPDATE_URL" => sed_url('admin', 'm=bbcode&a=upd&id='.$row['bbc_id'].'&d='.$d),
		"ADMIN_BBCODE_ROW_DELETE_URL" => sed_url('admin', 'm=bbcode&a=del&id='.$row['bbc_id']),
        "ADMIN_BBCODE_ROW_ODDEVEN" => sed_build_oddeven($ii),
	));

	/* === Hook - Part2 : Include === */
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */
	$t -> parse("BBCODE.ADMIN_BBCODE_ROW");
	$ii++;
}
sed_sql_freeresult($res);

foreach($bbc_modes as $val)
{
	$t -> assign(array(
		"ADMIN_BBCODE_MODE_ITEM_SELECTED" => ($val == 'pcre') ? ' selected="selected"' : '',
		"ADMIN_BBCODE_MODE_ITEM" => $val
	));
	$t -> parse("BBCODE.ADMIN_BBCODE_MODE");
}
for($i = 1; $i < 256; $i++)
{
	$t -> assign(array(
		"ADMIN_BBCODE_PRIO_ITEM_SELECTED" => ($i == 128) ? ' selected="selected"' : '',
		"ADMIN_BBCODE_PRIO_ITEM" => $i
	));
	$t -> parse("BBCODE.ADMIN_BBCODE_PRIO");
}
$form_action = sed_url('admin', 'm=bbcode&a=add');
$form_clear_cache = sed_url('admin', 'm=bbcode&a=clearcache&d='.$d);

$t -> assign(array(
	"ADMIN_BBCODE_AJAX_OPENDIVID" => 'pagtab',
	"ADMIN_BBCODE_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_BBCODE_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_BBCODE_PAGNAV" => $pagnav,
	"ADMIN_BBCODE_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_BBCODE_TOTALITEMS" => $totalitems,
	"ADMIN_BBCODE_COUNTER_ROW" => $ii,
	"ADMIN_BBCODE_FORM_ACTION" => $form_action,
	"ADMIN_BBCODE_URL_CLEAR_CACHE" => $form_clear_cache,
));

/* === Hook  === */
$extp = sed_getextplugins('admin.bbcode.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t -> parse("BBCODE");
$adminmain = $t -> text("BBCODE");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>