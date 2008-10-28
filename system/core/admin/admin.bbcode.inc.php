<?php
/**
 * BBCode editor.
 *
 * @package Seditio-N
 * @version 0.0.1
 * @author Trustmaster
 * @copyright Copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

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
		if(sed_bbcode_add($bbc['name'], $bbc['mode'], $bbc['pattern'], $bbc['replacement'], $bbc['container'], $bbc['priority'], '', $bbc['postrender']))
		{
			$adminmain .= <<<HTM
<div class="error">
{$L['adm_bbcodes_added']}
</div>
HTM;
		}
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
		if(sed_bbcode_update($id, $bbc['enabled'], $bbc['name'], $bbc['mode'], $bbc['pattern'], $bbc['replacement'], $bbc['container'], $bbc['priority'], $bbc['postrender']))
		{
			$adminmain .= <<<HTM
<div class="error">
{$L['adm_bbcodes_updated']}
</div>
HTM;
		}
	}
}
elseif($a == 'del' && $id > 0)
{
	if(sed_bbcode_remove($id))
	{
		$adminmain .= <<<HTM
<div class="error">
{$L['adm_bbcodes_removed']}
</div>
HTM;
	}
}
elseif($a == 'clearcache')
{
	sed_sql_query("UPDATE $db_pages SET page_html = ''");
	sed_sql_query("UPDATE $db_forum_posts SET fp_html = ''");
	sed_sql_query("UPDATE $db_pm SET pm_html = ''");
$adminmain .= <<<HTM
<div class="error">
{$L['adm_bbcodes_clearcache_done']}
</div>
HTM;
}

$totalitems = sed_sql_rowcount($db_bbcode);
$pagnav = sed_pagination(sed_url('admin','m=bbcode'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=bbcode'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$adminmain .= <<<HTM
<div class="pagnav">$pagination_prev $pagnav $pagination_next</div>
<table class="cells">
<tr>
	<td class="coltop">{$L['Name']}<br />{$L['adm_bbcodes_mode']} / {$L['Enabled']}</td>
	<td class="coltop">{$L['adm_bbcodes_pattern']}<br />{$L['adm_bbcodes_priority']} / {$L['adm_bbcodes_container']}</td>
	<td class="coltop">{$L['adm_bbcodes_replacement']}</td>
	<td class="coltop">{$L['Plugin']}<br />{$L['adm_bbcodes_postrender']}</td>
	<td class="coltop">{$L['Update']}<br />{$L['Delete']}</td>
</tr>
HTM;

$bbc_modes = array('str', 'ereg', 'pcre', 'callback');
$res = sed_sql_query("SELECT * FROM $db_bbcode ORDER BY bbc_priority LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;

while($row = sed_sql_fetchassoc($res))
{
	$mode = '';
	foreach($bbc_modes as $val)
	{
		$sel = $val == $row['bbc_mode'] ? ' selected="selected"' : '';
		$mode .= '<option'.$sel.'>'.$val.'</option>';
	}
	$prio = '';
	for($i = 1; $i < 256; $i++)
	{
		$sel = $i == $row['bbc_priority'] ? ' selected="selected"' : '';
		$prio .= '<option'.$sel.'>'.$i.'</option>';
	}
	$enabled = $row['bbc_enabled'] ? ' checked="checked"' : '';
	$container = $row['bbc_container'] ? ' checked="checked"' : '';
	$postrender = $row['bbc_postrender'] ? ' checked="checked"' : '';
	$bbcode_update_url = sed_url('admin', 'm=bbcode&a=upd&id='.$row['bbc_id']);
	$bbcode_delete_url = sed_url('admin', 'm=bbcode&a=upd&id='.$row['bbc_id']);
	$adminmain .= <<<HTM
<form action="{$bbcode_update_url}" method="post">
<tr>
	<td>
		<input type="text" name="bbc_name" value="{$row['bbc_name']}" /><br />
		<select name="bbc_mode">$mode</select> &nbsp; <input type="checkbox" name="bbc_enabled"$enabled />
	</td>
	<td>
		<input type="text" name="bbc_pattern" value="{$row['bbc_pattern']}" /><br />
		<select name="bbc_priority">$prio</select> &nbsp; <input type="checkbox" name="bbc_container"$container />
	</td>
	<td><textarea name="bbc_replacement" rows="2" cols="20">{$row['bbc_replacement']}</textarea></td>
	<td>
		{$row['bbc_plug']}<br />
		<input type="checkbox" name="bbc_postrender"$postrender />
	</td>
	<td>
		<input type="submit" value="{$L['Update']}" /><br />
		<input type="button" value="{$L['Delete']}" onclick="if(confirm('{$L['adm_bbcodes_confirm']}')) location.href='{$bbcode_delete_url}'" />
	</td>
</tr>
</form>
HTM;

$ii++;
}
sed_sql_freeresult($res);
$mode = '';
foreach($bbc_modes as $val)
{
	$sel = $val == 'pcre' ? ' selected="selected"' : '';
	$mode .= '<option'.$sel.'>'.$val.'</option>';
}
$prio = '';
for($i = 1; $i < 256; $i++)
{
	$sel = $i == 128 ? ' selected="selected"' : '';
	$prio .= '<option'.$sel.'>'.$i.'</option>';
}
$form_action = sed_url('admin', 'm=bbcode&a=add');
$form_clear_cache = sed_url('admin', 'm=bbcode&a=clearcache');
$adminmain .= <<<HTM
<tr>
<td colspan="5">{$L['Total']} : $totalitems, {$L['adm_polls_on_page']}: $ii</td>
</tr>
<tr>
<td colspan="5"><br /></td>
</tr>
<tr>
<td colspan="5"><strong>{$L['adm_bbcodes_new']}</strong></td>
</tr>
<form action="{$form_action}" method="post">
<tr>
	<td>
		<input type="text" name="bbc_name" value="" /><br />
		<select name="bbc_mode">$mode</select>
	</td>
	<td>
		<input type="text" name="bbc_pattern" value="" /><br />
		<select name="bbc_priority">$prio</select> &nbsp; <input type="checkbox" name="bbc_container" checked="checked" />
	</td>
	<td><textarea name="bbc_replacement" rows="2" cols="20"></textarea></td>
	<td>
		<input type="checkbox" name="bbc_postrender" />
	</td>
	<td><input type="submit" value="{$L['Add']}" /></td>
</tr>
</form>
</table>
<a href="{$form_clear_cache}" onclick="return confirm('{$L['adm_bbcodes_clearcache_confirm']}')">{$L['adm_bbcodes_clearcache']}</a>
HTM;

?>