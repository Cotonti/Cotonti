<?php
/**
 * Administration panel - Extra fields editor for pages part
 *
 * @package Cotonti
 * @version 0.7.0
 * @author medar, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.page.extrafields.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=page'), $L['Pages']);
$adminpath[] = array(sed_url('admin', 'm=page&s=extrafields'), $L['adm_extrafields']);
$adminhelp = $L['adm_help_pages_extrafield'];

$a = sed_import('a', 'G', 'ALP');
$id = (int) sed_import('id', 'G', 'INT');
$n = sed_import('name', 'G', 'ALP');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

/* === Hook === */
$extp = sed_getextplugins('admin.page.extrafields.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'add')
{
	$field['name'] = sed_import('field_name', 'P', 'ALP');
	$field['type'] = sed_import('field_type', 'P', 'ALP');
	$field['html'] = str_replace("'", "\"", htmlspecialchars_decode(sed_import('field_html', 'P', 'HTM')));
	$field['variants'] = sed_import('field_variants', 'P', 'HTM');
	$field['description'] = sed_import('field_description', 'P', 'HTM');
	$field['noalter'] = sed_import('field_noalter', 'P', 'BOL');
	if ($field['html'] == "")
	{
		$field['html'] = get_default_html_construction($field['type']);
	}

	/* === Hook === */
	$extp = sed_getextplugins('admin.page.extrafields.add');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!empty($field['name']) && !empty($field['type']))
	{
		if (sed_extrafield_add('pages', $field['name'], $field['type'], $field['html'], $field['variants'], $field['description'], $field['noalter']))
		{
			$adminwarnings = $L['adm_extrafield_added'];
		}
		else
		{
			$adminwarnings = $L['adm_extrafield_not_added'];
		}
	}
}
elseif ($a == 'upd' && isset($n))
{
	$oldtype = sed_import('oldtype', 'G', 'ALP');
	$field['name'] = sed_import('field_name', 'P', 'ALP');
	$field['type'] = sed_import('field_type', 'P', 'ALP');
	$field['html'] = str_replace("'", "\"", htmlspecialchars_decode(sed_import('field_html', 'P', 'HTM')));
	$field['variants'] = sed_import('field_variants', 'P', 'HTM');
	$field['description'] = sed_import('field_description', 'P', 'HTM');
	if ($field['type'] != $oldtype)
	{
		$field['html'] = "";
	}
	if ($field['html'] == "")
	{
		$field['html'] = get_default_html_construction($field['type']);
	}

	/* === Hook === */
	$extp = sed_getextplugins('admin.page.extrafields.update');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!empty($field['name']) && !empty($field['type']))
	{
		if (sed_extrafield_update('pages', $n, $field['name'], $field['type'], $field['html'], $field['variants'], $field['description']))
		{
			$adminwarnings = $L['adm_extrafield_updated'];
		}
		else
		{
			$adminwarnings = $L['adm_extrafield_not_updated'];
		}
	}
}
elseif ($a == 'del' && isset($n))
{
	/* === Hook === */
	$extp = sed_getextplugins('admin.page.extrafields.delete');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (sed_extrafield_remove('pages', $n))
	{
		$adminwarnings = $L['adm_extrafield_removed'];
	}
	else
	{
		$adminwarnings = $L['adm_extrafield_not_removed'];
	}
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_extra_fields WHERE field_location='pages'"), 0, 0);
$pagenav = sed_pagenav('admin', 'm=page&s=extrafields', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$field_types = array('input', 'textarea', 'select', 'checkbox', 'radio');
$res = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='pages' LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.page.extrafields.loop');
/* ===== */
while ($row = sed_sql_fetchassoc($res))
{
	foreach ($field_types as $val)
	{
		$t->assign(array(
			"ADMIN_PAG_EXTRAFIELDS_ROW_SELECT_SELECTED" => ($val == $row['field_type']) ? ' selected="selected"' : '',
			"ADMIN_PAG_EXTRAFIELDS_ROW_SELECT_OPTION" => $val
		));
		$t->parse("PAG_EXTRAFIELDS.PAG_EXTRAFIELDS_ROW.PAG_EXTRAFIELDS_ROW_SELECT");
	}

	$t->assign(array(
		"ADMIN_PAG_EXTRAFIELDS_ROW_FORM_URL" => sed_url('admin', 'm=page&s=extrafields&a=upd&name='.$row['field_name'].'&oldtype='.$row['field_type'].'&d='.$d),
		"ADMIN_PAG_EXTRAFIELDS_ROW_NAME" => $row['field_name'],
		"ADMIN_PAG_EXTRAFIELDS_ROW_DESCRIPTION" => $row['field_description'],
		"ADMIN_PAG_EXTRAFIELDS_ROW_VARIANTS_STYLE" => ($row['field_type'] == "select" OR $row['field_type'] == "checkbox") ? 'style="display:block;' : 'style="display:none;',
		"ADMIN_PAG_EXTRAFIELDS_ROW_VARIANTS" => $row['field_variants'],
		"ADMIN_PAG_EXTRAFIELDS_ROW_FIELD_HTML_ENCODED" => htmlspecialchars($row['field_html']),
		"ADMIN_PAG_EXTRAFIELDS_ROW_BIGNAME" => strtoupper($row['field_name']),
		"ADMIN_PAG_EXTRAFIELDS_ROW_DEL_URL" => sed_url('admin', 'm=page&s=extrafields&a=del&name='.$row['field_name']),
		"ADMIN_PAG_EXTRAFIELDS_ROW_ODDEVEN" => sed_build_oddeven($ii)
	));
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse("PAG_EXTRAFIELDS.PAG_EXTRAFIELDS_ROW");
	$ii++;
}

foreach ($field_types as $val)
{
	$t->assign(array(
		"ADMIN_PAG_EXTRAFIELDS_SELECT_FIELD_TYPE_OPTION_SELECTED" => ($val == 'input') ? ' selected="selected"' : '',
		"ADMIN_PAG_EXTRAFIELDS_SELECT_FIELD_TYPE_OPTION" => $val
	));
	$t->parse("PAG_EXTRAFIELDS.PAG_EXTRAFIELDS_FORM_ADD_SELECT_FIELD_TYPE");
}

$t->assign(array(
	"ADMIN_PAG_EXTRAFIELDS_URL_FORM_ADD" => sed_url('admin', 'm=page&s=extrafields&a=add&d='.$d),
	"ADMIN_PAG_EXTRAFIELDS_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_PAG_EXTRAFIELDS_PAGINATION_PREV" => $pagenav['prev'],
	"ADMIN_PAG_EXTRAFIELDS_PAGNAV" => $pagenav['main'],
	"ADMIN_PAG_EXTRAFIELDS_PAGINATION_NEXT" => $pagenav['next'],
	"ADMIN_PAG_EXTRAFIELDS_TOTALITEMS" => $totalitems,
	"ADMIN_PAG_EXTRAFIELDS_COUNTER_ROW" => $ii
));

/* === Hook  === */
$extp = sed_getextplugins('admin.page.extrafields.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('PAG_EXTRAFIELDS');
if (SED_AJAX)
{
	$t->out('PAG_EXTRAFIELDS');
}
else
{
	$adminmain = $t->text('PAG_EXTRAFIELDS');
}

?>