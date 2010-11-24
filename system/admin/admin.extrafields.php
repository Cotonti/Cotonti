<?php
/**
 * Administration panel - Extra fields editor for structure part
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('extrafields');

$t = new XTemplate(cot_skinfile(array('admin', 'extrafields', $n)));

$a = cot_import('a', 'G', 'ALP');
$id = (int) cot_import('id', 'G', 'INT');
$name = cot_import('name', 'G', 'ALP');
$d = cot_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

$parse_type = array('HTML', 'Text');

/* === Hook === */
foreach (cot_getextplugins('admin.extrafields.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($n == 'structure')
{
	$adminpath[] = array(cot_url('admin', 'm=structure'), $L['Categories']);
	$adminpath[] = array(cot_url('admin', 'm=extrafields&n=structure'), $L['adm_extrafields']);
	$adminhelp = $L['adm_help_structure_extrafield'];
	$extra_path = 'm=extrafields&n=structure';
	$location = $db_structure;
}

if ($n == 'users')
{
	$adminpath[] = array(cot_url('admin', 'm=users'), $L['Users']);
	$adminpath[] = array(cot_url('admin', 'm=extrafields&n=users'), $L['adm_extrafields']);
	$adminhelp = $L['adm_help_users_extrafield'];
	$extra_path = 'm=extrafields&n=users';
	$location = $db_users;
}

if (empty($extra_path) || empty($n))
{
	cot_redirect(cot_url('message', 'msg=950', '', true));
}

if ($a == 'add')
{
	$field['field_name'] = cot_import('field_name', 'P', 'ALP');
	$field['field_type'] = cot_import('field_type', 'P', 'ALP');
	$field['field_html'] = str_replace("'", "\"", htmlspecialchars_decode(cot_import('field_html', 'P', 'HTM')));
	$field['field_variants'] = cot_import('field_variants', 'P', 'HTM');
	$field['field_description'] = cot_import('field_description', 'P', 'HTM');
	$field['field_default'] = cot_import('field_default', 'P', 'HTM');
	$field['field_required'] = cot_import('field_required', 'P', 'BOL');
	$field['field_parse'] = cot_import('field_parse', 'P', 'ALP');
	$field['field_noalter'] = cot_import('field_noalter', 'P', 'BOL');
	if (empty($field['field_html']))
	{
		$field['field_html'] = cot_default_html_construction($field['field_type']);
	}

	/* === Hook === */
	foreach (cot_getextplugins('admin.extrafields.add') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!empty($field['field_name']) && !empty($field['field_type']))
	{
		if (cot_extrafield_add($location, $field['field_name'], $field['field_type'], $field['field_html'], $field['field_variants'], $field['field_default'], $field['field_required'], $field['field_parse'], $field['field_description'], $field['field_noalter']))
		{
			cot_message('adm_extrafield_added');
		}
		else
		{
			cot_message('adm_extrafield_not_added');
		}
	}
}
elseif ($a == 'upd')
{
	$field_name = cot_import('field_name', 'P', 'ARR');
	$field_type = cot_import('field_type', 'P', 'ARR');
	$field_html = cot_import('field_html', 'P', 'ARR');
	$field_variants = cot_import('field_variants', 'P', 'ARR');
	$field_description = cot_import('field_description', 'P', 'ARR');
	$field_default = cot_import('field_default', 'P', 'ARR');
	$field_required = cot_import('field_required', 'P', 'ARR');
	$field_parse = cot_import('field_parse', 'P', 'ARR');

	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('admin.extrafields.update');
	/* ===== */
	if(is_array($field_name))
	{
		foreach ($field_name as $k => $v)
		{
			$field['field_name'] = cot_import($field_name[$k], 'D', 'ALP');
			$field['field_type'] = cot_import($field_type[$k], 'D', 'ALP');
			$field['field_html'] = str_replace("'", "\"", htmlspecialchars_decode(cot_import($field_html[$k], 'D', 'HTM')));
			$field['field_variants'] = cot_import($field_variants[$k], 'D', 'HTM');
			$field['field_description'] = cot_import($field_description[$k], 'D', 'HTM');
			$field['field_default'] = cot_import($field_default[$k], 'D', 'HTM');
			$field['field_required'] = cot_import($field_required[$k], 'D', 'BOL');
			$field['field_parse'] = cot_import($field_parse[$k], 'D', 'ALP');
			$field['field_location'] = $location;

			if ($field != $cot_extrafields[$location][$field['field_name']] && !empty($field['field_name']) && !empty($field['field_type']))
			{
				if (empty($field['field_html']) || $field['field_type'] != $cot_extrafields[$location][$field['field_name']]['field_type'])
				{
					$field['field_html'] = cot_default_html_construction($field['field_type']);
				}

				/* === Hook - Part2 : Include === */
				foreach ($extp as $pl)
				{
					include $pl;
				}
				/* ===== */

				if (cot_extrafield_update($location, $k, $field['field_name'], $field['field_type'], $field['field_html'], $field['field_variants'], $field['field_default'], $field['field_required'], $field['field_parse'], $field['field_description']))
				{
					cot_message(sprintf($L['adm_extrafield_updated'], $k));
				}
				else
				{
					cot_message(sprintf($L['adm_extrafield_not_updated'], $k));
				}
			}
		}
	}
}
elseif ($a == 'del' && isset($name))
{
	/* === Hook === */
	foreach (cot_getextplugins('admin.extrafields.delete') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (cot_extrafield_remove($location, $name))
	{
		cot_message('adm_extrafield_removed');
	}
	else
	{
		cot_message('adm_extrafield_not_removed');
	}
}

$cfg['cache'] && $cache->db->remove('cot_extrafields', 'system');

$totalitems = $db->query("SELECT COUNT(*) FROM $db_extra_fields WHERE field_location = '$location'")->fetchColumn();
$res = $db->query("SELECT * FROM $db_extra_fields WHERE field_location = '$location' ORDER BY field_name ASC LIMIT $d, ".$cfg['maxrowsperpage']);

$pagenav = cot_pagenav('admin',$extra_path, $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$field_types = array('input', 'inputint', 'currency', 'textarea', 'select', 'checkbox', 'radio', 'datetime', 'file', 'filesize');

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.extrafields.loop');
/* ===== */
while ($row = $res->fetch())
{
	$t->assign(array(
		'ADMIN_EXTRAFIELDS_ROW_NAME' => cot_inputbox('text', 'field_name['.$row['field_name'].']', $row['field_name']),
		'ADMIN_EXTRAFIELDS_ROW_DESCRIPTION' => cot_textarea('field_description['.$row['field_name'].']', $row['field_description'], 1, 30),
		'ADMIN_EXTRAFIELDS_ROW_SELECT' => cot_selectbox($row['field_type'], 'field_type['.$row['field_name'].']', $field_types, $field_types, false),
		'ADMIN_EXTRAFIELDS_ROW_VARIANTS' => cot_textarea('field_variants['.$row['field_name'].']', $row['field_variants'], 1, 60),
		'ADMIN_EXTRAFIELDS_ROW_HTML' => cot_textarea('field_html['.$row['field_name'].']', $row['field_html'], 1, 60),
		'ADMIN_EXTRAFIELDS_ROW_DEFAULT' => cot_textarea('field_default['.$row['field_name'].']', $row['field_default'], 1, 60),
		'ADMIN_EXTRAFIELDS_ROW_REQUIRED' => cot_checkbox($row['field_required'], 'field_required['.$row['field_name'].']'),
		'ADMIN_EXTRAFIELDS_ROW_PARSE' => cot_selectbox($row['field_parse'], 'field_parse['.$row['field_name'].']', $parse_type, $parse_type, false),
		'ADMIN_EXTRAFIELDS_ROW_BIGNAME' => strtoupper($row['field_name']),
		'ADMIN_EXTRAFIELDS_ROW_DEL_URL' => cot_url('admin', $extra_path.'&a=del&name='.$row['field_name'])
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.EXTRAFIELDS_ROW');
	$ii++;
}

$t->assign(array(
	'ADMIN_EXTRAFIELDS_URL_FORM_EDIT' => cot_url('admin', $extra_path.'&a=upd&d='.$d),
	'ADMIN_EXTRAFIELDS_NAME' => cot_inputbox('text', 'field_name', ''),
	'ADMIN_EXTRAFIELDS_DESCRIPTION' => cot_textarea('field_description', '', 1, 30),
	'ADMIN_EXTRAFIELDS_SELECT' => cot_selectbox('input', 'field_type', $field_types, $field_types, false),
	'ADMIN_EXTRAFIELDS_VARIANTS' => cot_textarea('field_variants', '', 1, 60),
	'ADMIN_EXTRAFIELDS_HTML' => cot_textarea('field_html', '', 1, 60),
	'ADMIN_EXTRAFIELDS_DEFAULT' => cot_textarea('field_default', '', 1, 60),
	'ADMIN_EXTRAFIELDS_REQUIRED' => cot_checkbox(0, 'field_required'),
	'ADMIN_EXTRAFIELDS_PARSE' => cot_selectbox('HTML', 'field_parse', $parse_type, $parse_type, false),
	'ADMIN_EXTRAFIELDS_URL_FORM_ADD' => cot_url('admin', $extra_path.'&a=add&d='.$d),
	'ADMIN_EXTRAFIELDS_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_EXTRAFIELDS_PAGNAV' => $pagenav['main'],
	'ADMIN_EXTRAFIELDS_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_EXTRAFIELDS_TOTALITEMS' => $totalitems,
	'ADMIN_EXTRAFIELDS_COUNTER_ROW' => $ii,
	'ADMIN_EXTRAFIELDS_ODDEVEN' => cot_build_oddeven($ii)
));

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.extrafields.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (COT_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>