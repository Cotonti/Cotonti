<?PHP
/* ====================
[BEGIN_SED]
File=admin.users.extrafields.inc.php
Version=0.0.2
Updated=2009-jan-03
Type=Core.admin
Author=medar
Description=Extra fields editor for users part (Cotonti - Website engine http://www.cotonti.com Copyright (c) Cotonti Team 2009 BSD License)
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=users'), $L['core_users']);
$adminpath[] = array (sed_url('admin', 'm=users&s=extrafields'), $L['adm_extrafields']);
$adminhelp = $L['adm_help_users_extrafield'];

$a = sed_import('a', 'G', 'ALP');
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;
$id = (int) sed_import('id', 'G', 'INT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$n = sed_import('name', 'G', 'ALP');

$closediv = '';
if($cfg['jquery'])
{
	$adminmain .= <<<HTM
<script type="text/javascript">
//<![CDATA[
function gopage(d)
	{
		var d = d || 0;
		$.ajax({
		type: 'GET',
		url: 'admin.php?',
		data: '&m=users&s=extrafields&ajax=1&d='+d,

		beforeSend: function(){
			$('#pagtab').addClass('loading');
		},

		success: function(msg){
		$('#pagtab').removeClass('loading');
		$('#pagtab').html(msg).hide().stop().fadeIn('slow');
			},
		error: function(msg){
		$('#pagtab').removeClass('loading');
		alert('Error ajax reload page');
			}

		});

		return false;

	}
//]]>
</script>
<div id="pagtab">
HTM;
	$closediv = '</div>';
}

if($a == 'add')
{
	$field['name'] = sed_import('field_name', 'P', 'ALP');
	$field['type'] = sed_import('field_type', 'P', 'ALP');
	$field['html'] = str_replace("'","\"", htmlspecialchars_decode(sed_import('field_html', 'P', 'HTM')));
	$field['variants'] = sed_import('field_variants', 'P', 'HTM');
	$field['description'] = sed_import('field_description', 'P', 'HTM');
	if($field['html']=="") $field['html'] = get_default_html_construction($field['type']);
	if(!empty($field['name']) && !empty($field['type']))
	{
		//if(sed_sql_insert($db_extra_fields, $field, 'field_'))
		if(sed_extrafield_add("users", $field['name'], $field['type'], $field['html'], $field['variants'], $field['description']))
		{
			$adminmain .= <<<HTM
<div class="error">
{$L['adm_extrafield_added']}
</div>
HTM;
		}
		else {
			$adminmain .= <<<HTM
			<div class="error">
{$L['adm_extrafield_not_added']}
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
	$field['description'] = sed_import('field_description', 'P', 'HTM');
	if ($field['type'] != $oldtype) $field['html'] = "";
	if($field['html']=="") $field['html'] = get_default_html_construction($field['type']);
	if(!empty($field['name']) && !empty($field['type']))
	{
		if(sed_extrafield_update("users", $n, $field['name'], $field['type'], $field['html'], $field['variants'], $field['description']))
		{
			$adminmain .= <<<HTM
<div class="error">
{$L['adm_extrafield_updated']}
</div>
HTM;
		}
		else {

		}
	}
}
elseif($a == 'del' && isset($n))
{
	if(sed_extrafield_remove("users", $n))
	{
		$adminmain .= <<<HTM
<div class="error">
{$L['adm_extrafield_removed']}
</div>
HTM;
	}
	else {
		$adminmain .= <<<HTM
<div class="error">
{$L['adm_extrafield_not_removed']}
</div>
HTM;
	}
}

$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_extra_fields WHERE field_location='users'"), 0, 0);
if($cfg['jquery'])
{
	$pagnav = sed_pagination(sed_url('admin','m=users&s=extrafields&ajax=1'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'gopage');
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=users&s=extrafields&ajax=1'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'gopage');
}
else
{
	$pagnav = sed_pagination(sed_url('admin','m=users&s=extrafields'), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=users&s=extrafields'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
}

$adminmain .= <<<HTM
<div class="pagnav">
$pagination_prev $pagnav $pagination_next
</div>
<table class="cells">
<tr>
	<td class="coltop" style="width:20%;">{$L['extf_Name']}</td>
	<td class="coltop" style="width:25%;">{$L['extf_Type']}</td>
	<td class="coltop" style="width:45%;">{$L['extf_Base HTML']}</td>
	<td class="coltop" style="width:10%;">&nbsp;</td>
</tr>
</table>
HTM;

$field_types = array('input', 'textarea', 'select', 'checkbox');
$res = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='users' LIMIT $d, ".$cfg['maxrowsperpage']);
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
	$extrafield_update_url = sed_url('admin', 'm=users&s=extrafields&a=upd&name='.$row['field_name'].'&oldtype='.$row['field_type']);
	$extrafield_delete_url = sed_url('admin', 'm=users&s=extrafields&a=del&name='.$row['field_name']);

	//$field_html_encoded = htmlspecialchars_encode($row['field_html']);
	$field_html_encoded = htmlspecialchars($row['field_html']);
	$bigname = strtoupper($row['field_name']);
	$adminmain .= <<<HTM
<form action="$extrafield_update_url" method="post">
<table class="cells">
<tr>
	<td style="width:20%;">
		<input type="text" name="field_name" value="{$row['field_name']}" /><br />
		<span style="font-size: 80%;">{$L['extf_Description']}</span><br />
		<textarea name="field_description" rows="1" cols="20">{$row['field_description']}</textarea>
	</td>
	<td style="width:25%;">
		<select name="field_type" >$type</select>
		<!-- <div class="variants_{$row['field_name']}" $variants_style > -->
		<br /><span style="font-size: 80%;">{$L['adm_extrafield_selectable_values']}</span><br />
		<textarea name="field_variants" rows="1" cols="20">{$row['field_variants']}</textarea>

	</td>
	<td style="width:45%;"><textarea name="field_html" rows="1" cols="60" >$field_html_encoded</textarea></td>
	<td style="width:10%;">
		<input type="submit" value="{$L['Update']}" onclick="location.href='{$extrafield_update_url}'"  /><br />
		<input type="button" value="{$L['Delete']}" onclick="if(confirm('{$L['adm_extrafield_confirmdel']}')) location.href='{$extrafield_delete_url}'" />
	</td>
</tr>
<!-- <tr><td colspan="4"><b>{$L['extf_Page tags']}:</b>&nbsp;&nbsp;&nbsp; users.profile.tpl: {USERS_PROFILE_$bigname}&nbsp;&nbsp;&nbsp; users.edit.tpl: {USERS_EDIT_$bigname}&nbsp;&nbsp;&nbsp; users.details.tpl:  {USERS_DETAILS_$bigname}&nbsp;&nbsp;&nbsp; forums.posts.tpl: {FORUMS_POSTS_ROW_USER$bigname}</td></tr> -->
</table>
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
$form_action = sed_url('admin', 'm=users&s=extrafields&a=add');

$adminmain .= <<<HTM
<br />
<form action="{$form_action}" method="post">
<table class="cells">
<tr>
	<td colspan="4" class="coltop"><strong>{$L['adm_extrafield_new']}</strong></td>
</tr>
<tr>
	<td class="coltop" style="width:20%;">{$L['extf_Name']}</td>
	<td class="coltop" style="width:25%;">{$L['extf_Type']}</td>
	<td class="coltop" style="width:45%;">{$L['extf_Base HTML']}</td>
	<td class="coltop" style="width:10%;">&nbsp;</td>
</tr>
<tr>
	<td>
		<input type="text" name="field_name" value="" /><br />
		{$L['extf_Description']}<br />
		<textarea name="field_description" rows="1" cols="20"></textarea>
	</td>
	<td>
		<select name="field_type">$type</select><br />
		<span style="font-size: 80%;">{$L['adm_extrafield_selectable_values']}</span>
		<textarea name="field_variants" rows="1" cols="20"></textarea>
	</td>
	<td><textarea name="field_html" rows="2" cols="40"></textarea></td>
	<td><input type="submit" value="{$L['Add']}" /></td>
</tr>
</table>
</form>
$closediv
HTM;

if($ajax)
{
	sed_sendheaders();

	echo $adminmain;

	exit;
}

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