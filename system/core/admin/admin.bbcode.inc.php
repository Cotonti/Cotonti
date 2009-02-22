<?PHP
/**
 * Administration panel - BBCode editor
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Trustmaster, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if(!defined('SED_CODE') || !defined('SED_ADMIN')){die('Wrong URL.');}

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
		$adminbbcode = (sed_bbcode_add($bbc['name'], $bbc['mode'], $bbc['pattern'], $bbc['replacement'], $bbc['container'], $bbc['priority'], '', $bbc['postrender'])) ? $L['adm_bbcodes_added'] : $L['Error'];
	}
	else
	{
		$adminbbcode = $L['Error'];
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
		$adminbbcode = (sed_bbcode_update($id, $bbc['enabled'], $bbc['name'], $bbc['mode'], $bbc['pattern'], $bbc['replacement'], $bbc['container'], $bbc['priority'], $bbc['postrender'])) ? $L['adm_bbcodes_updated'] : $L['Error'];
	}
	else
	{
		$adminbbcode = $L['Error'];
	}
}
elseif($a == 'del' && $id > 0)
{
	$adminbbcode = (sed_bbcode_remove($id)) ? $L['adm_bbcodes_removed'] : $L['Error'];
}
elseif($a == 'clearcache')
{
	$sqlpag = sed_sql_query("UPDATE $db_pages SET page_html = ''");
	$sqlfrm = sed_sql_query("UPDATE $db_forum_posts SET fp_html = ''");
	$sqlpmg = sed_sql_query("UPDATE $db_pm SET pm_html = ''");
	$adminbbcode = ($sqlpag && $sqlfrm && $sqlpmg) ? $L['adm_bbcodes_clearcache_done'] : $L['Error'];
}

$totalitems = sed_sql_rowcount($db_bbcode);
$pagnav = sed_pagination(sed_url('admin','m=bbcode'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=bbcode'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$bbc_modes = array('str', 'ereg', 'pcre', 'callback');
$res = sed_sql_query("SELECT * FROM $db_bbcode ORDER BY bbc_priority LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;

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
		"ADMIN_BBCODE_ROW_UPDATE_URL" => sed_url('admin', 'm=bbcode&a=upd&id='.$row['bbc_id']),
		"ADMIN_BBCODE_ROW_DELETE_URL" => sed_url('admin', 'm=bbcode&a=del&id='.$row['bbc_id'])
	));
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
$form_clear_cache = sed_url('admin', 'm=bbcode&a=clearcache');

if(!empty($adminbbcode))
{
	$t -> assign(array("ADMIN_BBCODE_MESAGE" => $adminbbcode));
	$t -> parse("BBCODE.MESAGE");
}

$t -> assign(array(
	"ADMIN_BBCODE_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_BBCODE_PAGNAV" => $pagnav,
	"ADMIN_BBCODE_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_BBCODE_TOTALITEMS" => $totalitems,
	"ADMIN_BBCODE_COUNTER_ROW" => $ii,
	"ADMIN_BBCODE_FORM_ACTION" => $form_action,
	"ADMIN_BBCODE_URL_CLEAR_CACHE" => $form_clear_cache,
));

$t -> parse("BBCODE");
$adminmain = $t -> text("BBCODE");

?>