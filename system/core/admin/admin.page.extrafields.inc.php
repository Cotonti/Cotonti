<?php
/**
 * Extra fields editor.
 *
 * @package Seditio-N
 * @version 0.0.2
 * @author medar
 * @copyright Copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=page'), $L['Pages']);
$adminpath[] = array (sed_url('admin', 'm=page&s=extrafields'), $L['adm_extrafields']);
$adminhelp = $L['adm_help_extrafield'];

$a = sed_import('a', 'G', 'ALP');
$id = (int) sed_import('id', 'G', 'INT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$n = sed_import('name', 'G', 'ALP');

if($a == 'add')
{
	$field['name'] = sed_import('field_name', 'P', 'ALP');
	$field['type'] = sed_import('field_type', 'P', 'ALP');
	$field['html'] = str_replace("'","\"", htmlspecialchars_decode(sed_import('field_html', 'P', 'HTM')));
	$field['variants'] = sed_import('field_variants', 'P', 'HTM');
	if($field['html']=="") $field['html'] = get_default_html_construction($field['type']);
	if(!empty($field['name']) && !empty($field['type']))
	{
		//if(sed_sql_insert($db_pages_extra_fields, $field, 'field_'))	
		if(sed_extrafield_add($field['name'], $field['type'], $field['html'], $field['variants']))
		{
			$adminmain .= <<<HTM
<div class="error">
{$L['adm_extrafield_added']}
</div>
HTM;
		}
	}
}
elseif($a == 'upd' && isset($n))
{
	$oldtype = sed_import('oldtype', 'G', 'ALP'); 
	$field['name'] = sed_import('field_name', 'P', 'ALP');
	$field['type'] = sed_import('field_type', 'P', 'ALP');
	$field['html'] = str_replace("'","\"", htmlspecialchars_decode(sed_import('field_html', 'P', 'HTM')));
	$field['variants'] = sed_import('field_variants', 'P', 'HTM');
	if ($field['type'] != $oldtype) $field['html'] = "";
	if($field['html']=="") $field['html'] = get_default_html_construction($field['type']);
	if(!empty($field['name']) && !empty($field['type']))
	{
		if(sed_extrafield_update($n, $field['name'], $field['type'], $field['html'], $field['variants']))
		{
			$adminmain .= <<<HTM
<div class="error">
{$L['adm_extrafield_updated']}
</div>
HTM;
		}
	}
}
elseif($a == 'del' && isset($n))
{
	if(sed_extrafield_remove($n))
	{
		$adminmain .= <<<HTM
<div class="error">
{$L['adm_extrafield_removed']}
</div>
HTM;
	}
}

$totalitems = sed_sql_rowcount($db_pages_extra_fields);
$pagnav = sed_pagination('admin.php?s=extrafields', $d, $totalitems, $cfg['maxrowsperpage']); //this will be changed

$adminmain .= <<<HTM
<div class="pagnav">
$pagnav
</div>
<table class="cells">
<tr>
	<td class="coltop">{$L['extf_Name']}</td>
	<td class="coltop">{$L['extf_Type']}</td>
	<td class="coltop">{$L['extf_Base HTML']}</td>
	<td class="coltop"></td>
</tr>
HTM;

$field_types = array('input', 'textarea', 'select', 'checkbox');
$res = sed_sql_query("SELECT * FROM $db_pages_extra_fields LIMIT $d, ".$cfg['maxrowsperpage']);
while($row = sed_sql_fetchassoc($res))
{
	$type = '';
	foreach($field_types as $val)
	{
		$sel = $val == $row['field_type'] ? ' selected="selected"' : '';
		$type .= '<option'.$sel.'>'.$val.'</option>';
	}
	if ($row['field_type']=="select" OR $row['field_type']=="checkbox" )	{
		$variants_style = 'style="display:block;';
	}
	else{
		$variants_style = 'style="display:none;';
	}
	$extrafield_update_url = sed_url('admin', 'm=page&s=extrafields&a=upd&name='.$row['field_name'].'&oldtype='.$row['field_type']);
	$extrafield_delete_url = sed_url('admin', 'm=page&s=extrafields&a=del&name='.$row['field_name']);
	
	//$field_html_encoded = htmlspecialchars_encode($row['field_html']);
	$field_html_encoded = htmlspecialchars($row['field_html']);
	$bigname = strtoupper($row['field_name']);
	$adminmain .= <<<HTM
<form action="$extrafield_update_url" method="post">
<tr>
	<td>
		<input type="text" name="field_name" value="{$row['field_name']}" />
	</td>
	<td>
		<select name="field_type" >$type</select>
		<!-- <div class="variants_{$row['field_name']}" $variants_style > -->
		<br><span style="font-size: 80%;">{$L['adm_extrafield_selectable_values']}</span>
		<textarea name="field_variants" rows="1" cols="20">{$row['field_variants']}</textarea>
		
	</td>
	<td><textarea name="field_html" rows="1" cols="60" >$field_html_encoded</textarea></td>
	<td>
		<input type="submit" value="{$L['Update']}" onclick="if(confirm('{$L['adm_extrafield_confirmupd']}')) location.href='{$extrafield_update_url}'"/><br />
		<input type="button" value="{$L['Delete']}" onclick="if(confirm('{$L['adm_extrafield_confirmdel']}')) location.href='{$extrafield_delete_url}'" />
	</td>
</tr>
<tr><td colspan="4"><b>{$L['extf_Page tags']}:</b>&nbsp;&nbsp;&nbsp; page.tpl: {PAGE_MY_$bigname}&nbsp;&nbsp;&nbsp; page.add.tpl: {PAGEADD_FORM_MY_$bigname}&nbsp;&nbsp;&nbsp; page.edit.tpl: {PAGEEDIT_FORM_MY_$bigname} &nbsp;&nbsp;&nbsp; list.tpl: {LIST_ROW_MY_$bigname}</td></tr>
<tr><td colspan="4"></td></tr>
</form>
HTM;
}

sed_sql_freeresult($res);
$type = '';
foreach($field_types as $val)
{
	$sel = $val == 'input' ? ' selected="selected"' : '';
	$type .= '<option'.$sel.'>'.$val.'</option>';
}
$form_action = sed_url('admin', 'm=page&s=extrafields&a=add');

$adminmain .= <<<HTM
<tr>
<td colspan="4">
<strong>{$L['adm_extrafield_new']}</strong>
</td>
</tr>
<form action="{$form_action}" method="post">
<tr>
	<td>
		<input type="text" name="field_name" value="" />
	</td>
	<td>
		<select name="field_type">$type</select>
		<div class="variantsnew" style="display:none; >
		<textarea name="field_variants" rows="2" cols="40"></textarea>
		</div>
	</td>
	<td><textarea name="field_html" rows="2" cols="40"></textarea></td>
	<td><input type="submit" value="{$L['Add']}" /></td>
	
</tr>
</form>
</table>
HTM;

/**
 * Extra fields - Return default base html-construction for various types of fields (without value= and name=)
 * 
 * @access private
 * @param string $type Type of field (input, textarea etc)
 * @return string
 * 
 */ 
function get_default_html_construction($type)
{
	$html = "";
	switch($type){
	case "input":
		$html = '<input class="text" type="text" maxlength="255" size="56" />'; break;
	case "textarea":
		$html = '<textarea cols="80" rows="6" ></textarea>'; break;
	case "select":
		$html = '<select></select>'; break;
	case "checkbox":
		$html = '<input type=checkbox >'; break;
	}
	return $html;
}

?>