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

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

require_once $cfg['system_dir'] . '/extrafields.php';

$t = new XTemplate(sed_skinfile('admin.extrafields'));

$a = sed_import('a', 'G', 'ALP');
$id = (int) sed_import('id', 'G', 'INT');
$name = sed_import('name', 'G', 'ALP');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
// $n - extrafield value
switch ($n)
{
	case 'pages':
		$adminpath[] = array(sed_url('admin', 'm=page'), $L['Pages']);
		$adminpath[] = array(sed_url('admin', 'm=extrafields&n=pages'), $L['adm_extrafields']);
		$adminhelp = $L['adm_help_pages_extrafield'];
		$extra_path = 'm=extrafields&n=pages';
		break;
	case 'structure':
		$adminpath[] = array(sed_url('admin', 'm=structure'), $L['Categories']);
		$adminpath[] = array(sed_url('admin', 'm=extrafields&n=structure'), $L['adm_extrafields']);
		$adminhelp = $L['adm_help_structure_extrafield'];
		$extra_path = 'm=extrafields&n=structure';
		break;
	default:
		$adminpath[] = array(sed_url('admin', 'm=users'), $L['Users']);
		$adminpath[] = array(sed_url('admin', 'm=extrafields&n=users'), $L['adm_extrafields']);
		$adminhelp = $L['adm_help_users_extrafield'];
		$extra_path = 'm=extrafields';
		$n='users';
		break;
}



/* === Hook === */
$extp = sed_getextplugins('admin.extrafields.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'add')
{
	$field['field_name'] = sed_import('field_name', 'P', 'ALP');
	$field['field_type'] = sed_import('field_type', 'P', 'ALP');
	$field['field_html'] = str_replace("'", "\"", htmlspecialchars_decode(sed_import('field_html', 'P', 'HTM')));
	$field['field_variants'] = sed_import('field_variants', 'P', 'HTM');
	$field['field_description'] = sed_import('field_description', 'P', 'HTM');
	$field['field_noalter'] = sed_import('field_noalter', 'P', 'BOL');
	if ($field['field_html'] == "")
	{
		$field['field_html'] = get_default_html_construction($field['field_type']);
	}
	
	/* === Hook === */
	$extp = sed_getextplugins('admin.extrafields.add');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	
	if (!empty($field['field_name']) && !empty($field['field_type']))
	{
		if (sed_extrafield_add($n, $field['field_name'], $field['field_type'], $field['field_html'], $field['field_variants'], $field['field_description'], $field['field_noalter']))
		{
			$adminwarnings = $L['adm_extrafield_added'];
		}
		else
		{
			$adminwarnings = $L['adm_extrafield_not_added'];
		}
	}
}
elseif ($a == 'upd')
{
	$field_name = sed_import('field_name', 'P', 'ARR');
	$field_type = sed_import('field_type', 'P', 'ARR');
	$field_html = sed_import('field_html', 'P', 'ARR');
	$field_variants = sed_import('field_variants', 'P', 'ARR');
	$field_description = sed_import('field_description', 'P', 'ARR');

	/* === Hook - Part1 : Set === */
	$extp = sed_getextplugins('admin.extrafields.update');
	/* ===== */

	foreach($field_name as $k => $v)
	{
		$field['field_name'] = sed_import($field_name[$k], 'D', 'ALP');
		$field['field_type'] = sed_import($field_type[$k], 'D', 'ALP');
		$field['field_html'] = str_replace("'", "\"", htmlspecialchars_decode(sed_import($field_html[$k], 'D', 'HTM')));
		$field['field_variants'] = sed_import($field_variants[$k], 'D', 'HTM');
		$field['field_description'] = sed_import($field_description[$k], 'D', 'HTM');
		$field['field_location'] = $n;
		if ($field != $sed_extrafields[$n][$field['field_name']]
				&& !empty($field['field_name']) && !empty($field['field_type']))
		{
			if (empty($field['field_html'])
					|| $field['field_type'] != $sed_extrafields[$n][$field['field_name']]['field_type'])
			{
				$field['field_html'] = get_default_html_construction($field['field_type']);
			}			
			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */
			
			if (sed_extrafield_update($n, $k, $field['field_name'], $field['field_type'], $field['field_html'], $field['field_variants'], $field['field_description']))
			{
				$adminwarnings .= sprintf($L['adm_extrafield_updated'], $k).'<br />';
			}
			else
			{
				$adminwarnings .= sprintf($L['adm_extrafield_not_updated'], $k).'<br />';
			}
		}
	}
}
elseif ($a == 'del' && isset($name))
{
	/* === Hook === */
	$extp = sed_getextplugins('admin.extrafields.delete');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	
	if (sed_extrafield_remove($n, $name))
	{
		$adminwarnings = $L['adm_extrafield_removed'];
	}
	else
	{
		$adminwarnings = $L['adm_extrafield_not_removed'];
	}
}
$cot_cache->db->remove('sed_extrafields', 'system');

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_extra_fields WHERE field_location = '$n'"), 0, 0);
$res = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location = '$n' LIMIT $d, ".$cfg['maxrowsperpage']);

$pagenav = sed_pagenav('admin',$extra_path, $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$field_types = array('input', 'textarea', 'select', 'checkbox', 'radio');

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.extrafields.loop');
/* ===== */
while ($row = sed_sql_fetchassoc($res))
{
	foreach ($field_types as $val)
	{
		$t->assign(array(
				"ADMIN_EXTRAFIELDS_ROW_SELECT_SELECTED" => ($val == $row['field_type']) ? ' selected="selected"' : '',
				"ADMIN_EXTRAFIELDS_ROW_SELECT_OPTION" => $val
		));
		$t->parse("EXTRAFIELDS.EXTRAFIELDS_ROW.EXTRAFIELDS_ROW_SELECT");
	}
	
	$t->assign(array(
			"ADMIN_EXTRAFIELDS_ROW_NAME" => $row['field_name'],
			"ADMIN_EXTRAFIELDS_ROW_DESCRIPTION" => $row['field_description'],
			"ADMIN_EXTRAFIELDS_ROW_VARIANTS_STYLE" => ($row['field_type'] == "select" OR $row['field_type'] == "checkbox") ? 'style="display:block;' : 'style="display:none;',
			"ADMIN_EXTRAFIELDS_ROW_VARIANTS" => $row['field_variants'],
			"ADMIN_EXTRAFIELDS_ROW_FIELD_HTML_ENCODED" => htmlspecialchars($row['field_html']),
			"ADMIN_EXTRAFIELDS_ROW_BIGNAME" => strtoupper($row['field_name']),
			"ADMIN_EXTRAFIELDS_ROW_DEL_URL" => sed_url('admin', $extra_path.'&a=del&name='.$row['field_name'])
	));
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse("EXTRAFIELDS.EXTRAFIELDS_ROW");
	$ii++;
}

foreach ($field_types as $val)
{
	$t->assign(array(
			"ADMIN_EXTRAFIELDS_SELECT_FIELD_TYPE_OPTION_SELECTED" => ($val == 'input') ? ' selected="selected"' : '',
			"ADMIN_EXTRAFIELDS_SELECT_FIELD_TYPE_OPTION" => $val
	));
	$t->parse("EXTRAFIELDS.EXTRAFIELDS_FORM_ADD_SELECT_FIELD_TYPE");
}

$t->assign(array(
		"ADMIN_EXTRAFIELDS_URL_FORM_EDIT" => sed_url('admin', $extra_path.'&a=upd&d='.$d),
		"ADMIN_EXTRAFIELDS_URL_FORM_ADD" => sed_url('admin', $extra_path.'&a=add&d='.$d),
		"ADMIN_EXTRAFIELDS_ADMINWARNINGS" => $adminwarnings,
		"ADMIN_EXTRAFIELDS_PAGINATION_PREV" => $pagenav['prev'],
		"ADMIN_EXTRAFIELDS_PAGNAV" => $pagenav['main'],
		"ADMIN_EXTRAFIELDS_PAGINATION_NEXT" => $pagenav['next'],
		"ADMIN_EXTRAFIELDS_TOTALITEMS" => $totalitems,
		"ADMIN_EXTRAFIELDS_COUNTER_ROW" => $ii,
		"ADMIN_EXTRAFIELDS_ODDEVEN" =>sed_build_oddeven($ii)
));

/* === Hook  === */
$extp = sed_getextplugins('admin.extrafields.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('EXTRAFIELDS');
if (SED_AJAX)
{
	$t->out('EXTRAFIELDS');
}
else
{
	$adminmain = $t->text('EXTRAFIELDS');
}

?>