<?php

/**
 * Administration panel - Extra fields editor for structure part
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('extrafields');

$extra_blacklist = array($db_auth, $db_cache, $db_cache_bindings, $db_core, $db_updates, $db_logger, $db_online, $db_extra_fields, $db_config, $db_plugins);
$extra_whitelist = array(
	$db_structure => array(
		'name' => $db_structure,
		'caption' => $L['Categories'],
		'type' => 'system',
		'code' => 'structure',
		'tags' => array(
			'page.list.tpl' => '{LIST_ROWCAT_XXXXX}, {LIST_CAT_XXXXX}',
			'page.list.group.tpl' => '{LIST_ROWCAT_XXXXX}, {LIST_CAT_XXXXX}',
			'page.tpl' => '{PAGE_CAT_XXXXX}, {PAGE_CAT_XXXXX_TITLE}',
			'admin.structure.inc.tpl' => '{ADMIN_STRUCTURE_XXXXX}, {ADMIN_STRUCTURE_XXXXX_TITLE},{ADMIN_STRUCTURE_FORMADD_XXXXX}, {ADMIN_STRUCTURE_FORMADD_XXXXX_TITLE}'
			)
	)
);
$adminpath[] = array(cot_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(cot_url('admin', 'm=extrafields'), $L['adm_extrafields']);
$adminsubtitle = $L['adm_extrafields'];
$maxperpage = (is_int($cfg['maxrowsperpage']) && $cfg['maxrowsperpage'] > 0 || ctype_digit($cfg['maxrowsperpage'])) ? $cfg['maxrowsperpage'] : 15;

$t = new XTemplate(cot_tplfile(array('admin', 'extrafields', $n), 'core'));

/* === Hook === */
foreach (cot_getextplugins('admin.extrafields.first') as $pl)
{
	include $pl;
}
/* ===== */

if (empty($n) || in_array($n, $extra_blacklist))
{
	// no params
	$sql = $db->query("SHOW TABLES");
	$tablelist = array();
	while ($row = $sql->fetch())
	{
		$table = current($row);
		if (!in_array($table, $extra_blacklist))
		{
			if (cot_import('alltables', 'G', 'BOL'))
			{
				$tablelist[] = $table;
			}
			elseif(isset($extra_whitelist[$table]))
			{
				$tablelist[] = $table;
			}
		}
	}
	cot_import('alltables', 'G', 'BOL') && $adminpath[] = array(cot_url('admin', 'm=extrafields&alltables=1'), $L['adm_extrafields_all']);
	$ii = 0;
	foreach ($tablelist as $table)
	{
		$name = '';
		$ext_info = array();
		if($extra_whitelist[$table]['type'] == 'module' || $extra_whitelist[$table]['type'] == 'plug')
		{
			$ext_info = cot_get_extensionparams($extra_whitelist[$table]['code'], $extra_whitelist[$table]['type'] == 'module');
			$name = $ext_info['name'];
		}

		$name = (empty($name)) ? $extra_whitelist[$table]['caption'] : $name;
		$ii++;
		$t->assign(array(
			'ADMIN_EXTRAFIELDS_ROW_ICO' => $ext_info['icon'],
			'ADMIN_EXTRAFIELDS_ROW_ITEMNAME' => $name,
			'ADMIN_EXTRAFIELDS_ROW_TABLENAME' => $table . ((isset($extra_whitelist[$table])) ? " - " . $extra_whitelist[$table]['caption'] : ''),
			'ADMIN_EXTRAFIELDS_ROW_TABLE' => $table,
			'ADMIN_EXTRAFIELDS_ROW_TYPE' => $extra_whitelist[$table]['type'],
			'ADMIN_EXTRAFIELDS_ROW_TABLEURL' => cot_url('admin', 'm=extrafields&n='.$table),
			'ADMIN_EXTRAFIELDS_COUNTER_ROW' => $ii,
			'ADMIN_EXTRAFIELDS_ODDEVEN' => cot_build_oddeven($ii)
		));
		$t->parse('MAIN.TABLELIST.ROW');
	}
	$t->assign('ADMIN_EXTRAFIELDS_ALLTABLES', cot_url('admin', 'm=extrafields&alltables=1'));
	/* === Hook  === */
	foreach (cot_getextplugins('admin.extrafields.tablelist.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.TABLELIST');
}
else
{
	$a = cot_import('a', 'G', 'ALP');
	$id = (int)cot_import('id', 'G', 'INT');
	$name = cot_import('name', 'G', 'ALP');
	list($pg, $d, $durl) = cot_import_pagenav('d', $maxperpage);
	$parse_type = array('HTML', 'Text');

	$adminpath[] = array(cot_url('admin', 'm=extrafields&n='.$n), $L['adm_extrafields_table'].' '.$n . ((isset($extra_whitelist[$n])) ? ' - ' . $extra_whitelist[$n]['caption'] : ''));

	if ($a == 'add' && !empty($_POST))
	{
		$field['field_name'] = cot_import('field_name', 'P', 'ALP');
		$field['field_type'] = cot_import('field_type', 'P', 'ALP');
		$field['field_html'] = cot_import('field_html', 'P', 'NOC');
		$field['field_variants'] = cot_import('field_variants', 'P', 'HTM');
		$field['field_params'] = cot_import('field_params', 'P', 'HTM');
		$field['field_description'] = cot_import('field_description', 'P', 'NOC');
		$field['field_default'] = cot_import('field_default', 'P', 'HTM');
		$field['field_required'] = cot_import('field_required', 'P', 'BOL');
		$field['field_parse'] = cot_import('field_parse', 'P', 'ALP');
		$field['field_noalter'] = cot_import('field_noalter', 'P', 'BOL');
		$field['field_enabled'] = 1;

		/* === Hook === */
		foreach (cot_getextplugins('admin.extrafields.add') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if (!empty($field['field_name']) && !empty($field['field_type']))
		{
			if (cot_extrafield_add($n, $field['field_name'], $field['field_type'], $field['field_html'], $field['field_variants'], $field['field_default'], $field['field_required'], $field['field_parse'], $field['field_description'], $field['field_params'], $field['field_enabled'], $field['field_noalter']))
			{
				cot_message('adm_extrafield_added');
			}
			else
			{
				cot_error('adm_extrafield_not_added');
			}
		}
		//cot_redirect(cot_url('admin', "m=extrafields&n=$n&d=$durl", '', true));
	}
	elseif ($a == 'upd' && !empty($_POST))
	{
		$field_name = cot_import('field_name', 'P', 'ARR');
		$field_type = cot_import('field_type', 'P', 'ARR');
		$field_html = cot_import('field_html', 'P', 'ARR');
		$field_variants = cot_import('field_variants', 'P', 'ARR');
		$field_params = cot_import('field_params', 'P', 'ARR');
		$field_description = cot_import('field_description', 'P', 'ARR');
		$field_default = cot_import('field_default', 'P', 'ARR');
		$field_required = cot_import('field_required', 'P', 'ARR');
		$field_parse = cot_import('field_parse', 'P', 'ARR');
		$field_enabled = cot_import('field_enabled', 'P', 'ARR');

		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('admin.extrafields.update');
		/* ===== */
		if (is_array($field_name))
		{
			foreach ($field_name as $k => $v)
			{
				$field['field_name'] = cot_import($field_name[$k], 'D', 'ALP');
				$field['field_type'] = cot_import($field_type[$k], 'D', 'ALP');
				$field['field_html'] = cot_import($field_html[$k], 'D', 'NOC');
				$field['field_variants'] = cot_import($field_variants[$k], 'D', 'HTM');
				$field['field_params'] = cot_import($field_params[$k], 'D', 'HTM');
				$field['field_description'] = cot_import($field_description[$k], 'D', 'NOC');
				$field['field_default'] = cot_import($field_default[$k], 'D', 'HTM');
				$field['field_required'] = cot_import($field_required[$k], 'D', 'BOL');
				$field['field_parse'] = cot_import($field_parse[$k], 'D', 'ALP');
				$field['field_enabled'] = cot_import($field_enabled[$k], 'D', 'BOL');
				$field['field_location'] = $n;

				if ($field != $cot_extrafields[$n][$field['field_name']] && !empty($field['field_name']) && !empty($field['field_type']))
				{
					/* === Hook - Part2 : Include === */
					foreach ($extp as $pl)
					{
						include $pl;
					}
					/* ===== */

					$fieldresult = cot_extrafield_update($n, $k, $field['field_name'], $field['field_type'], $field['field_html'], $field['field_variants'], $field['field_default'], $field['field_required'], $field['field_parse'], $field['field_description'], $field['field_params'], $field['field_enabled']);
					if ($fieldresult == 1)
					{
						cot_message(sprintf($L['adm_extrafield_updated'], $k));
					}
					elseif (!$fieldresult)
					{
						cot_error(sprintf($L['adm_extrafield_not_updated'], $k));
					}
				}
			}
		}
		//cot_redirect(cot_url('admin', "m=extrafields&n=$n&d=$durl", '', true));
	}
	elseif ($a == 'del' && isset($name))
	{
		/* === Hook === */
		foreach (cot_getextplugins('admin.extrafields.delete') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if (cot_extrafield_remove($n, $name))
		{
			cot_message('adm_extrafield_removed');
		}
		else
		{
			cot_error('adm_extrafield_not_removed');
		}
		//cot_redirect(cot_url('admin', "m=extrafields&n=$n&d=$durl", '', true));
	}

	$cache && $cache->db->remove('cot_extrafields', 'system');
	cot_load_extrafields(true);

	$totalitems = $db->query("SELECT COUNT(*) FROM $db_extra_fields WHERE field_location = '$n'")->fetchColumn();
	$res = $db->query("SELECT * FROM $db_extra_fields WHERE field_location = '$n' ORDER BY field_name ASC LIMIT $d, ".$maxperpage);

	$pagenav = cot_pagenav('admin', 'm=extrafields&n='.$n, $d, $totalitems, $maxperpage, 'd', '', $cfg['jquery'] && $cfg['turnajax']);

	$field_types = array('input', 'inputint', 'currency', 'double', 'textarea', 'select', 'checkbox', 'radio', 'datetime', 'country', 'range', 'checklistbox', 'file'/* , 'filesize' */);

	$ii = 0;
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('admin.extrafields.loop');
	/* ===== */
	foreach ($res->fetchAll() as $row)
	{
		$ii++;
		$t->assign(array(
			'ADMIN_EXTRAFIELDS_ROW_NAME' => cot_inputbox('text', 'field_name['.$row['field_name'].']', $row['field_name'], 'class="exfldname"'),
			'ADMIN_EXTRAFIELDS_ROW_FIELDNAME' => htmlspecialchars($row['field_name']),
			'ADMIN_EXTRAFIELDS_ROW_DESCRIPTION' => cot_textarea('field_description['.$row['field_name'].']', $row['field_description'], 1, 30, 'class="exflddesc"'),
			'ADMIN_EXTRAFIELDS_ROW_SELECT' => cot_selectbox($row['field_type'], 'field_type['.$row['field_name'].']', $field_types, $field_types, false, 'class="exfldtype"'),
			'ADMIN_EXTRAFIELDS_ROW_VARIANTS' => cot_textarea('field_variants['.$row['field_name'].']', $row['field_variants'], 1, 60, 'class="exfldvariants"'),
			'ADMIN_EXTRAFIELDS_ROW_PARAMS' => cot_textarea('field_params['.$row['field_name'].']', $row['field_params'], 1, 60, 'class="exfldparams"'),
			'ADMIN_EXTRAFIELDS_ROW_HTML' => cot_textarea('field_html['.$row['field_name'].']', $row['field_html'], 1, 60, 'class="exfldhtml"'),
			'ADMIN_EXTRAFIELDS_ROW_DEFAULT' => cot_textarea('field_default['.$row['field_name'].']', $row['field_default'], 1, 60, 'class="exflddefault"'),
			'ADMIN_EXTRAFIELDS_ROW_REQUIRED' => cot_checkbox($row['field_required'], 'field_required['.$row['field_name'].']', '', 'class="exfldrequired"'),
			'ADMIN_EXTRAFIELDS_ROW_ENABLED' => cot_checkbox($row['field_enabled'], 'field_enabled['.$row['field_name'].']', '', 'title="'.$L['adm_extrafield_enable'].'" class="exfldenabled" '),
			'ADMIN_EXTRAFIELDS_ROW_PARSE' => cot_selectbox($row['field_parse'], 'field_parse['.$row['field_name'].']', $parse_type, array($L['Default'], $L['No']), false, 'class="exfldparse"'),
			'ADMIN_EXTRAFIELDS_ROW_BIGNAME' => strtoupper($row['field_name']),
			'ADMIN_EXTRAFIELDS_ROW_ID' => $row['field_name'],
			'ADMIN_EXTRAFIELDS_ROW_DEL_URL' => cot_url('admin', 'm=extrafields&n='.$n.'&a=del&name='.$row['field_name']),
			'ADMIN_EXTRAFIELDS_ROW_COUNTER_ROW' => $ii,
			'ADMIN_EXTRAFIELDS_ROW_ODDEVEN' => cot_build_oddeven($ii)
		));

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse('MAIN.TABLE.EXTRAFIELDS_ROW');
	}

	$tags_list = '';
	$tags_list_li = '';
	if(is_array($extra_whitelist[$n]['tags']))
	{
		foreach($extra_whitelist[$n]['tags'] as $ktags => $vtags)
		{
			$tags_list .= cot_rc('admin_exflds_array', array('tplfile' => $ktags, 'tags' => $vtags));
			$tags_list_li .= '<li>'.cot_rc('admin_exflds_array', array('tplfile' => $ktags, 'tags' => $vtags)).'</li>';
		}
	}

	$t->assign(array(
		'ADMIN_EXTRAFIELDS_URL_FORM_EDIT' => cot_url('admin', 'm=extrafields&n='.$n.'&a=upd&d='.$durl),
		'ADMIN_EXTRAFIELDS_NAME' => cot_inputbox('text', 'field_name', '', 'class="exfldname"'),
		'ADMIN_EXTRAFIELDS_DESCRIPTION' => cot_textarea('field_description', '', 1, 30, 'class="exflddesc"'),
		'ADMIN_EXTRAFIELDS_SELECT' => cot_selectbox('input', 'field_type', $field_types, $field_types, false, 'class="exfldtype"'),
		'ADMIN_EXTRAFIELDS_VARIANTS' => cot_textarea('field_variants', '', 1, 60, 'class="exfldvariants"'),
		'ADMIN_EXTRAFIELDS_PARAMS' => cot_textarea('field_params', '', 1, 60, 'class="exfldparams"'),
		'ADMIN_EXTRAFIELDS_HTML' => cot_textarea('field_html', '', 1, 60, 'class="exfldhtml"'),
		'ADMIN_EXTRAFIELDS_DEFAULT' => cot_textarea('field_default', '', 1, 60, 'class="exflddefault"'),
		'ADMIN_EXTRAFIELDS_REQUIRED' => cot_checkbox(0, 'field_required', '', 'class="exfldrequired"'),
		'ADMIN_EXTRAFIELDS_PARSE' => cot_selectbox('HTML', 'field_parse', $parse_type, array($L['Default'], $L['No']), false, 'class="exfldparse"'),
		'ADMIN_EXTRAFIELDS_URL_FORM_ADD' => cot_url('admin', 'm=extrafields&n='.$n.'&a=add&d='.$durl),
		'ADMIN_EXTRAFIELDS_PAGINATION_PREV' => $pagenav['prev'],
		'ADMIN_EXTRAFIELDS_PAGNAV' => $pagenav['main'],
		'ADMIN_EXTRAFIELDS_PAGINATION_NEXT' => $pagenav['next'],
		'ADMIN_EXTRAFIELDS_TOTALITEMS' => $totalitems,
		'ADMIN_EXTRAFIELDS_TAGS' => $tags_list
	));

	cot_display_messages($t);

	if (isset($extra_whitelist[$n]['help']))
	{
		$adminhelp = $extra_whitelist[$n]['help'];
	}
	else
	{
		$adminhelp = $L['adm_help_info'];
		if(!empty($tags_list))
		{
			$adminhelp .= $L['adm_help_newtags'].'<ul class="follow">'.$tags_list_li.'</ul>';
		}
	}
	/* === Hook  === */
	foreach (cot_getextplugins('admin.extrafields.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.TABLE');
}
$t->parse('MAIN');
$adminmain = $t->text('MAIN');
