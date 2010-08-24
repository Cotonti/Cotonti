<?php
/**
 * Administration panel - BBCode editor
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.bbcode'));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=bbcode'), $L['adm_bbcodes']);
$adminhelp = $L['adm_help_bbcodes'];

$a = sed_import('a', 'G', 'ALP');
$id = (int) sed_import('id', 'G', 'INT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

/* === Hook === */
foreach (sed_getextplugins('admin.bbcode.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'add')
{
	$bbc['name'] = sed_import('bbc_name', 'P', 'ALP');
	$bbc['mode'] = sed_import('bbc_mode', 'P', 'ALP');
	$bbc['pattern'] = sed_import('bbc_pattern', 'P', 'HTM');
	$bbc['priority'] = sed_import('bbc_priority', 'P', 'INT');
	$bbc['container'] = sed_import('bbc_container', 'P', 'BOL');
	$bbc['replacement'] = sed_import('bbc_replacement', 'P', 'HTM');
	$bbc['postrender'] = sed_import('bbc_postrender', 'P', 'BOL');
	if (!empty($bbc['name']) && !empty($bbc['pattern']) && !empty($bbc['replacement']))
	{
		sed_bbcode_clearcache();
		$adminwarnings = (sed_bbcode_add($bbc['name'], $bbc['mode'], $bbc['pattern'], $bbc['replacement'], $bbc['container'], $bbc['priority'], '', $bbc['postrender'])) ? $L['adm_bbcodes_added'] : $L['Error'];
	}
	else
	{
		$adminwarnings = $L['Error'];
	}
}
elseif ($a == 'upd' && $id > 0)
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
elseif ($a == 'del' && $id > 0)
{
	sed_bbcode_clearcache();
	$adminwarnings = (sed_bbcode_remove($id)) ? $L['adm_bbcodes_removed'] : $L['Error'];
}
elseif ($a == 'clearcache')
{
	sed_bbcode_clearcache();
	$adminwarnings = sed_cache_clearhtml() ? $L['adm_bbcodes_clearcache_done'] : $L['Error'];
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_rowcount($db_bbcode);

// FIXME AJAX-based pagination doesn't work because of some strange PHP bug
// Xtpl_block->text() returns 'str' instead of a long string which it has in $text
//$pagenav = sed_pagenav('admin', 'm=bbcode', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);
$pagenav = sed_pagenav('admin', 'm=bbcode', $d, $totalitems, $cfg['maxrowsperpage'], 'd');

$bbc_modes = array('str', 'pcre', 'callback');
$res = sed_sql_query("SELECT * FROM $db_bbcode ORDER BY bbc_priority LIMIT $d, ".$cfg['maxrowsperpage']);



$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.banlist.loop');
/* ===== */
while ($row = sed_sql_fetchassoc($res))
{

	$t->assign(array(
		'ADMIN_BBCODE_ROW_NAME' => sed_inputbox('text', 'bbc_name', $row['bbc_name']),
		'ADMIN_BBCODE_ROW_ENABLED' => sed_checkbox($row['bbc_enabled'], 'bbc_enabled'),
		'ADMIN_BBCODE_ROW_CONTAINER' => sed_checkbox($row['bbc_container'], 'bbc_container'),
		'ADMIN_BBCODE_ROW_PATTERN' => sed_textarea('bbc_pattern', $row['bbc_pattern'], 2, 20),
		'ADMIN_BBCODE_ROW_REPLACEMENT' => sed_textarea('bbc_replacement', $row['bbc_replacement'], 2, 20),
		'ADMIN_BBCODE_ROW_PLUG' => $row['bbc_plug'],
		'ADMIN_BBCODE_ROW_MODE' => sed_selectbox($row['bbc_mode'], 'bbc_mode', $bbc_modes, $bbc_modes, false),
		'ADMIN_BBCODE_ROW_PRIO' => sed_selectbox($row['bbc_priority'], 'bbc_priority', range(1, 256), range(1, 256), false),
		'ADMIN_BBCODE_ROW_POSTRENDER' => sed_checkbox($row['bbc_postrender'], 'bbc_postrender'),
		'ADMIN_BBCODE_ROW_UPDATE_URL' => sed_url('admin', 'm=bbcode&a=upd&id='.$row['bbc_id'].'&d='.$d),
		'ADMIN_BBCODE_ROW_DELETE_URL' => sed_url('admin', 'm=bbcode&a=del&id='.$row['bbc_id']),
		'ADMIN_BBCODE_ROW_ODDEVEN' => sed_build_oddeven($ii)
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.ADMIN_BBCODE_ROW');
	$ii++;
}
sed_sql_freeresult($res);

$t->assign(array(
	'ADMIN_BBCODE_ADMINWARNINGS' => $adminwarnings,
	'ADMIN_BBCODE_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_BBCODE_PAGNAV' => $pagenav['main'],
	'ADMIN_BBCODE_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_BBCODE_TOTALITEMS' => $totalitems,
	'ADMIN_BBCODE_COUNTER_ROW' => $ii,
	'ADMIN_BBCODE_FORM_ACTION' => sed_url('admin', 'm=bbcode&a=add'),
	'ADMIN_BBCODE_NAME' => sed_inputbox('text', 'bbc_name', ''),
	'ADMIN_BBCODE_ENABLED' => sed_checkbox('', 'bbc_enabled'),
	'ADMIN_BBCODE_CONTAINER' => sed_checkbox(1, 'bbc_container'),
	'ADMIN_BBCODE_PATTERN' => sed_textarea('bbc_pattern', '', 2, 20),
	'ADMIN_BBCODE_REPLACEMENT' => sed_textarea('bbc_replacement', '', 2, 20),
	'ADMIN_BBCODE_MODE' => sed_selectbox('pcre', 'bbc_mode', $bbc_modes, $bbc_modes, false),
	'ADMIN_BBCODE_PRIO' => sed_selectbox('128', 'bbc_priority', range(1, 256), range(1, 256), false),
	'ADMIN_BBCODE_POSTRENDER' => sed_checkbox('0', 'bbc_postrender'),
	'ADMIN_BBCODE_URL_CLEAR_CACHE' => sed_url('admin', 'm=bbcode&a=clearcache&d='.$d)
));

/* === Hook  === */
foreach (sed_getextplugins('admin.bbcode.tags') as $pl)
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