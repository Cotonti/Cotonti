<?php
/**
 * Administration panel
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('extrafields');
require_once cot_incfile('structure');

$id = cot_import('id', 'G', 'INT');
$al = cot_import('al', 'G', 'ALP');
$c = cot_import('c', 'G', 'TXT');
$v = cot_import('v', 'G', 'TXT');

$maxrowsperpage = (is_int($cfg['maxrowsperpage']) && $cfg['maxrowsperpage'] > 0 || ctype_digit($cfg['maxrowsperpage'])) ? $cfg['maxrowsperpage'] : 15;
list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage);
$mode = cot_import('mode', 'G', 'ALP');

$t = new XTemplate(cot_tplfile(array('admin', 'structure', $n), 'core'));

$adminsubtitle = $L['Structure'];

$modules_structure = &$extension_structure; // for compatibility

/* === Hook === */
foreach (cot_getextplugins('admin.structure.first') as $pl)
{
	include $pl;
}
/* ===== */

if (empty($n))
{
	$adminpath[] = array(cot_url('admin', 'm=structure'), $L['Structure']);
	// Show available module list
	if (is_array($extension_structure) && count($extension_structure) == 1 && ((cot_plugin_active($extension_structure[0]) || cot_module_active($extension_structure[0]))))
	{
		cot_redirect(cot_url('admin', 'm=structure&n='.$extension_structure[0], '', true));
	}
	if (is_array($extension_structure) && count($extension_structure) > 0)
	{
		foreach ($extension_structure as $code)
		{
			$parse = false;
			if (cot_plugin_active($code))
			{
				$is_module = false;
				$parse = true;
			}
			if (cot_module_active($code))
			{
				$is_module = true;
				$parse = true;
			}
			if ($parse)
			{
				$ext_info = cot_get_extensionparams($code, $is_module);
				$t->assign(array(
					'ADMIN_STRUCTURE_EXT_URL' => cot_url('admin', 'm=structure&n='.$code),
					'ADMIN_STRUCTURE_EXT_ICO' => $ext_info['icon'],
					'ADMIN_STRUCTURE_EXT_NAME' => $ext_info['name']
				));
				$t->parse('LIST.ADMIN_STRUCTURE_EXT');
			}
		}
	}
	else
	{
		$t->parse('LIST.ADMIN_STRUCTURE_EMPTY');
	}

	$t->assign(array(
		'ADMIN_STRUCTURE_EXFLDS_URL' => cot_url('admin', 'm=extrafields')
	));
	$t->parse('LIST');
	$adminmain = $t->text('LIST');
}
else
{
	$parse = false;
	if (cot_plugin_active($n))
	{
		$is_module = false;
		$parse = true;
	}
	if (cot_module_active($n))
	{
		$is_module = true;
		$parse = true;
	}
	if (!$parse)
	{
		cot_redirect(cot_url('admin', 'm=structure', '', true));
	}
	// Edit structure for a module
	if (file_exists(cot_incfile($n, $is_module ? 'module' : 'plug')))
	{
		require_once cot_incfile($n, $is_module ? 'module' : 'plug');
	}
	if (empty($adminhelp))
	{
		$adminhelp = $L['adm_help_structure'];
	}

	if ($a == 'reset' && !empty($al))
	{
		cot_config_reset($n, $v, $is_module, $al);
	}
	if ($a == 'update' && !empty($_POST))
	{
		$editconfig = cot_import('editconfig', 'P', 'TXT');
		if (!empty($editconfig))
		{
            $owner = $is_module ? 'module' : 'plug';
			$optionslist = cot_config_list($owner, $n, $editconfig);
			foreach ($optionslist as $key => $val)
			{
				$data = cot_import($key, 'P', sizeof($cot_import_filters[$key]) ? $key : 'NOC');
				if ($optionslist[$key]['config_value'] != $data)
				{
					if (is_null($optionslist[$key]['config_subdefault']))
					{
						$optionslist[$key]['config_value'] = $data;
						$optionslist[$key]['config_subcat'] = $editconfig;
						$db->insert($db_config, $optionslist[$key]);
					}
					else
					{
						$db->update($db_config, array('config_value' => $data),
                            "config_name = ? AND config_owner = ? AND config_cat = ?  AND config_subcat = ?",
                            array($key, $owner, $n, $editconfig));
					}
				}

			}

            $dir = $owner == 'module' ? $cfg['modules_dir'] : $cfg['plugins_dir'];
            // Run configure extension part if present
            if (file_exists($dir."/".$n."/setup/".$n.".configure.php"))
            {
                include $dir."/".$n."/setup/".$n.".configure.php";
            }
		}

		$rstructurecode = cot_import('rstructurecode', 'P', 'ARR');
		$rstructurepath = cot_import('rstructurepath', 'P', 'ARR');
		$rstructuretitle = cot_import('rstructuretitle', 'P', 'ARR');
		$rstructuredesc = cot_import('rstructuredesc', 'P', 'ARR');
		$rstructureicon = cot_import('rstructureicon', 'P', 'ARR');
		$rstructurelocked = cot_import('rstructurelocked', 'P', 'ARR');

		$rtplmodearr = cot_import('rstructuretplmode', 'P', 'ARR');
		$rtplforcedarr = cot_import('rstructuretplforced', 'P', 'ARR');
		$rtplquickarr = cot_import('rstructuretplquick', 'P', 'ARR');

		/* === Hook === */
		foreach (cot_getextplugins('admin.structure.update.first') as $pl)
		{
			include $pl;
		}
		/* ===== */

		foreach ($rstructurecode as $i => $k)
		{
			$oldrow = cot::$db->query("SELECT * FROM ".cot::$db->structure." WHERE structure_id=".(int)$i)->fetch();
			$rstructure['structure_code'] = preg_replace('#[^\w\p{L}\-]#u', '', cot_import($rstructurecode[$i], 'D', 'TXT'));
			$rstructure['structure_path'] = cot_import($rstructurepath[$i], 'D', 'TXT');
			$rstructure['structure_title'] = cot_import($rstructuretitle[$i], 'D', 'TXT');
			$rstructure['structure_desc'] = cot_import($rstructuredesc[$i], 'D', 'TXT');
			$rstructure['structure_icon'] = cot_import($rstructureicon[$i], 'D', 'TXT');
			if (cot_import($rstructurelocked[$i], 'D', 'BOL') !== null)
			{
				$rstructure['structure_locked'] = (cot_import($rstructurelocked[$i], 'D', 'BOL')) ? 1 : 0;
			}

            if(!empty(cot::$extrafields[cot::$db->structure])) {
                foreach (cot::$extrafields[cot::$db->structure] as $exfld) {
                    $rstructure['structure_' . $exfld['field_name']] = cot_import_extrafields('rstructure' . $exfld['field_name'] . '_' . $i,
                        $exfld, 'P', $oldrow['structure_' . $exfld['field_name']], 'structure_');
                }
            }
            
			($rstructure['structure_code'] != 'all') || cot_error('adm_structure_code_reserved', 'rstructurecode');
			$rstructure['structure_code'] || cot_error('adm_structure_code_required', 'rstructurecode');
			$rstructure['structure_path'] || cot_error('adm_structure_path_required', 'rstructurepath');
			$rstructure['structure_title'] || cot_error('adm_structure_title_required', 'rstructuretitle');

			$rtplmode = cot_import($rtplmodearr[$i], 'D', 'INT');
			$rtplquick = cot_import($rtplquickarr[$i], 'D', 'TXT');

			$rstructure['structure_tpl'] = null;
			if (!empty($rtplquick) || empty($rtplmode))
			{
				$rstructure['structure_tpl'] = $rtplquick;
			}
			elseif ($rtplmode == 3)
			{
				$rstructure['structure_tpl'] = cot_import($rtplforcedarr[$i], 'D', 'TXT');
			}
			elseif ($rtplmode == 2)
			{
				$rstructure['structure_tpl'] = 'same_as_parent';
			}
			elseif ($rtplmode == 1)
			{
				$rstructure['structure_tpl'] = '';
			}
			if (!cot_error_found())
			{
				$res = cot_structure_update($n, $i, $oldrow, $rstructure, $is_module);
				if (is_array($res))
				{
					cot_error($res[0], $res[1]);
				}
			}
		}
		cot_extrafield_movefiles();
		cot_auth_clear('all');
		if ($cache)
		{
			$cache->clear();
		}

		/* === Hook === */
		foreach (cot_getextplugins('admin.structure.update.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if (!cot_error_found())
		{
			cot_message('Updated');
		}
		else
		{
			cot_error('adm_structure_somenotupdated');
		}
		cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$durl, '', true));
	}
	elseif ($a == 'add' && !empty($_POST))
	{
		$rstructure['structure_code'] = preg_replace('#[^\w\p{L}\-]#u', '', cot_import('rstructurecode', 'P', 'TXT'));
		$rstructure['structure_path'] = cot_import('rstructurepath', 'P', 'TXT');
		$rstructure['structure_title'] = cot_import('rstructuretitle', 'P', 'TXT');
		$rstructure['structure_desc'] = cot_import('rstructuredesc', 'P', 'TXT');
		$rstructure['structure_icon'] = cot_import('rstructureicon', 'P', 'TXT');
		$rstructure['structure_locked'] = (cot_import('rstructurelocked', 'P', 'BOL')) ? 1 : 0;
		$rstructure['structure_area'] = $n;
		$rtplmode = cot_import('rtplmode', 'P', 'INT');
		$rtplquick = cot_import('rtplquick', 'P', 'TXT');

        if(!empty(cot::$extrafields[cot::$db->structure])) {
            foreach (cot::$extrafields[cot::$db->structure] as $exfld) {
                $rstructure['structure_' . $exfld['field_name']] = cot_import_extrafields('rstructure' . $exfld['field_name'],
                    $exfld, 'P', '', 'structure_');
            }
        }

		($rstructure['structure_code'] != 'all') || cot_error('adm_structure_code_reserved', 'rstructurecode');
		$rstructure['structure_code'] || cot_error('adm_structure_code_required', 'rstructurecode');
		$rstructure['structure_path'] || cot_error('adm_structure_path_required', 'rstructurepath');
		$rstructure['structure_title'] || cot_error('adm_structure_title_required', 'rstructuretitle');

		if (!empty($rtplquick))
		{
			$rstructure['structure_tpl'] = $rtplquick;
		}
		elseif ($rtplmode == 3)
		{
			$rstructure['structure_tpl'] = cot_import('rtplforced', 'P', 'TXT');
		}
		elseif ($rtplmode == 2)
		{
			$rstructure['structure_tpl'] = 'same_as_parent';
		}
		else
		{
			$rstructure['structure_tpl'] = '';
		}

		/* === Hook === */
		foreach (cot_getextplugins('admin.structure.add.first') as $pl)
		{
			include $pl;
		}
		/* ===== */
		if (!cot_error_found())
		{
			$res = cot_structure_add($n, $rstructure, $is_module);
			if ($res === true)
			{
				cot_extrafield_movefiles();
				/* === Hook === */
				foreach (cot_getextplugins('admin.structure.add.done') as $pl)
				{
					include $pl;
				}
				/* ===== */
				cot_message('Added');
			}
			elseif (is_array($res))
			{
				cot_error($res[0], $res[1]);
			}
			else
			{
				cot_error('Error');
			}
		}
		cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$durl, '', true));
	}
	elseif ($a == 'delete')
	{
		cot_check_xg();

		if (cot_structure_delete($n, $c, $is_module))
		{
			/* === Hook === */
			foreach (cot_getextplugins('admin.structure.delete.done') as $pl)
			{
				include $pl;
			}
			/* ===== */
			cot_message('Deleted');
		}

		cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$durl, '', true));
	}
	elseif ($a == 'resyncall')
	{
		cot_check_xg();
		$res = false;
		$area_sync = 'cot_'.$n.'_sync';
		if (function_exists($area_sync))
		{
			$res = true;
			$sql = $db->query("SELECT structure_code FROM $db_structure WHERE structure_area='".$db->prep($n)."'");
			foreach ($sql->fetchAll() as $row)
			{
				$cat = $row['structure_code'];
				$items = $area_sync($cat);
				$db->update($db_structure, array("structure_count" => (int)$items), "structure_code='".$db->prep($cat)."' AND structure_area='".$db->prep($n)."'");
			}
			$sql->closeCursor();
		}

		/* === Hook === */
		foreach (cot_getextplugins('admin.structure.resync.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$res ? cot_message('Resynced') : cot_message("Error: function $area_sync doesn't exist."); // TODO i18n
		($cache && $cfg['cache_'.$n]) && $cache->page->clear($n);
		cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$durl, '', true));
	}
	$ext_info = cot_get_extensionparams($n, true);
	$adminpath[] = array(cot_url('admin', 'm=extensions'), $L['Extensions']);
    $urlParams = array('m' => 'extensions', 'a' => 'details');
    if($is_module) {
        $urlParams['mod'] = $n;

    } else {
        $urlParams['pl'] = $n;
    }
	$adminpath[] = array(cot_url('admin', $urlParams), $ext_info['name']);
	$adminpath[] = array(cot_url('admin', 'm=structure&n='.$n), $L['Structure']);

	if ($id > 0 || !empty($al))
	{
		$where = $id > 0 ? 'structure_id='.(int)$id : "structure_code='".$db->prep($al)."'";
		$sql = $db->query("SELECT * FROM $db_structure WHERE $where LIMIT 1");
		cot_die($sql->rowCount() == 0);
	}
	elseif ($mode && ($mode == 'all' || $structure[$n][$mode]))
	{
		$sqlmask = ($mode == 'all') ? "structure_path NOT LIKE '%.%'" : "structure_path LIKE '".$db->prep($structure[$n][$mode]['rpath']).".%' AND structure_path NOT LIKE '".$db->prep($structure[$n][$mode]['rpath']).".%.%'";
		$sql = $db->query("SELECT * FROM $db_structure WHERE structure_area='".$db->prep($n)."' AND $sqlmask ORDER BY structure_path ASC, structure_code ASC LIMIT $d, ".$maxrowsperpage);

		$totalitems = $db->query("SELECT COUNT(*) FROM $db_structure WHERE structure_area='".$db->prep($n)."' AND $sqlmask")->fetchColumn();
		$pagenav = cot_pagenav('admin', 'm=structure&n='.$n.'&mode='.$mode, $d, $totalitems, $maxrowsperpage, 'd', '', $cfg['jquery'] && $cfg['turnajax']);
	}
	else
	{
		$sql = $db->query("SELECT * FROM $db_structure WHERE structure_area='".$db->prep($n)."' ORDER BY structure_path ASC, structure_code ASC LIMIT $d, ".$maxrowsperpage);

		$totalitems = $db->query("SELECT COUNT(*) FROM $db_structure WHERE structure_area='".$db->prep($n)."'")->fetchColumn();
		$pagenav = cot_pagenav('admin', 'm=structure&n='.$n, $d, $totalitems, $maxrowsperpage, 'd', '', $cfg['jquery'] && $cfg['turnajax']);
	}

	$t->assign(array(
		'ADMIN_STRUCTURE_UPDATE_FORM_URL' => cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&a=update&d='.$durl),
		'ADMIN_PAGE_STRUCTURE_RESYNCALL' => cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&a=resyncall&'.cot_xg().'&d='.$durl),
		'ADMIN_STRUCTURE_URL_EXTRAFIELDS' => cot_url('admin', 'm=extrafields&n='.$db_structure)
	));

	$ii = 0;
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('admin.structure.loop');
	/* ===== */
	foreach ($sql->fetchAll() as $row)
	{
		($id) && $adminpath[] = array(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&id='.$id), htmlspecialchars($row['structure_title']));
		($al) && $adminpath[] = array(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&al='.$al), htmlspecialchars($row['structure_title']));

		$ii++;
		$structure_id = $row['structure_id'];
		$structure_code = $row['structure_code'];
		$pathfielddep = count(explode(".", $row['structure_path']));
		$dozvil = ($row['structure_count'] > 0) ? false : true;

		$pathspaceimg = '';
		for ($pathfielddepi = 1; $pathfielddepi < $pathfielddep; $pathfielddepi++)
		{
			$pathspaceimg .= '.'.$R['admin_icon_blank'];
		}

		if (empty($row['structure_tpl']))
		{
			$structure_tpl_sym = '-';
			$check_tpl = "1";
		}
		elseif ($row['structure_tpl'] == 'same_as_parent')
		{
			$structure_tpl_sym = '*';
			$check_tpl = "2";
		}
		else
		{
			$structure_tpl_sym = '+';
			$check_tpl = "3";
		}

		foreach ($structure[$n] as $i => $x)
		{
			if ($i != 'all')
			{
				$cat_path[$i] = $x['tpath'];
			}
		}
		$cat_selectbox = cot_selectbox($row['structure_tpl'], 'rstructuretplforced['.$structure_id.']', array_keys($cat_path), array_values($cat_path), false);

		$t->assign(array(
			'ADMIN_STRUCTURE_UPDATE_DEL_URL' => cot_confirm_url(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&a=delete&id='.$structure_id.'&c='.$row['structure_code'].'&d='.$durl.'&'.cot_xg()), 'admin'),
			'ADMIN_STRUCTURE_ID' => $structure_id,
			'ADMIN_STRUCTURE_CODE' => cot_inputbox('text', 'rstructurecode['.$structure_id.']', $structure_code, 'size="10" maxlength="255"'),
			'ADMIN_STRUCTURE_SPACEIMG' => $pathspaceimg,
			'ADMIN_STRUCTURE_LEVEL' => ($pathfielddep > 0) ? $pathfielddep - 1 : 0,
			'ADMIN_STRUCTURE_PATHFIELDIMG' => (mb_strpos($row['structure_path'], '.') == 0) ? $R['admin_icon_join1'] : $R['admin_icon_join2'],
			'ADMIN_STRUCTURE_PATH' => cot_inputbox('text', 'rstructurepath['.$structure_id.']', $row['structure_path'], 'size="12" maxlength="255"'),
			'ADMIN_STRUCTURE_TPL_SYM' => $structure_tpl_sym,
			'ADMIN_STRUCTURE_TPLMODE' => cot_radiobox($check_tpl, 'rstructuretplmode['.$structure_id.']', array('1', '2', '3'), array($L['adm_tpl_empty'], $L['adm_tpl_parent'], $L['adm_tpl_forced']), '', '<br />'),
			'ADMIN_STRUCTURE_TPLQUICK' => cot_inputbox('text', 'rstructuretplquick['.$structure_id.']', $id > 0 && array_key_exists($row['structure_tpl'], $cat_path) ? '' : $row['structure_tpl'], 'size="10" maxlength="255"'),
			'ADMIN_STRUCTURE_TITLE' => cot_inputbox('text', 'rstructuretitle['.$structure_id.']', $row['structure_title'], 'size="32" maxlength="255"'),
			'ADMIN_STRUCTURE_DESC' => cot_inputbox('text', 'rstructuredesc['.$structure_id.']', $row['structure_desc'], 'size="64" maxlength="255"'),
			'ADMIN_STRUCTURE_ICON' => cot_inputbox('text', 'rstructureicon['.$structure_id.']', $row['structure_icon'], 'size="64" maxlength="128"'),
			'ADMIN_STRUCTURE_LOCKED' => cot_checkbox($row['structure_locked'], 'rstructurelocked['.$structure_id.']'),
			'ADMIN_STRUCTURE_SELECT' => $cat_selectbox,
			'ADMIN_STRUCTURE_COUNT' => $row['structure_count'],
			/* TODO */ 'ADMIN_STRUCTURE_JUMPTO_URL' => cot_url($n, 'c='.$structure_code),
			'ADMIN_STRUCTURE_RIGHTS_URL' => $is_module ? cot_url('admin', 'm=rightsbyitem&ic='.$n.'&io='.$structure_code) : '',
			'ADMIN_STRUCTURE_OPTIONS_URL' => cot_url('admin', 'm=structure&n='.$n.'&d='.$durl.'&id='.$structure_id.'&'.cot_xg()),
			'ADMIN_STRUCTURE_ODDEVEN' => cot_build_oddeven($ii)
		));

        if(!empty(cot::$extrafields[cot::$db->structure])) {
            foreach (cot::$extrafields[cot::$db->structure] as $exfld) {
                $exfld_val = cot_build_extrafields('rstructure' . $exfld['field_name'] . '_' . $structure_id, $exfld,
                    $row['structure_' . $exfld['field_name']]);
                $exfld_title = cot_extrafield_title($exfld, 'structure_');

                $t->assign(array(
                    'ADMIN_STRUCTURE_' . strtoupper($exfld['field_name']) => $exfld_val,
                    'ADMIN_STRUCTURE_' . strtoupper($exfld['field_name']) . '_TITLE' => $exfld_title,
                    'ADMIN_STRUCTURE_EXTRAFLD' => $exfld_val,
                    'ADMIN_STRUCTURE_EXTRAFLD_TITLE' => $exfld_title
                ));
                $t->parse(($id || !empty($al)) ? 'MAIN.OPTIONS.EXTRAFLD' : 'MAIN.DEFAULT.ROW.EXTRAFLD');
            }
        }

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($id || !empty($al))
		{
			require_once cot_incfile('configuration');
			$optionslist = cot_config_list($is_module ? 'module' : 'plug', $n, $structure_code);

			/* === Hook - Part1 : Set === */
			$extp = cot_getextplugins('admin.config.edit.loop');
			/* ===== */
			foreach ($optionslist as $row_c)
			{
				list($title, $hint) = cot_config_titles($row_c['config_name'], $row_c['config_text']);

				if ($row_c['config_type'] == COT_CONFIG_TYPE_SEPARATOR)
				{
					$t->assign('ADMIN_CONFIG_FIELDSET_TITLE', $title);
					$t->parse('MAIN.OPTIONS.CONFIG.ADMIN_CONFIG_ROW.ADMIN_CONFIG_FIELDSET_BEGIN');
				}
				else
				{
					$t->assign(array(
						'ADMIN_CONFIG_ROW_CONFIG' => cot_config_input($row_c),
						'ADMIN_CONFIG_ROW_CONFIG_TITLE' => $title,
						'ADMIN_CONFIG_ROW_CONFIG_MORE_URL' => cot_url('admin', 'm=structure&n='.$n.'&d='.$durl.'&id='.$structure_id.'&al='.$structure_code.'&a=reset&v='.$row_c['config_name'].'&'.cot_xg()),
						'ADMIN_CONFIG_ROW_CONFIG_MORE' => $hint
					));
					/* === Hook - Part2 : Include === */
					foreach ($extp as $pl)
					{
						include $pl;
					}
					/* ===== */
					$t->parse('MAIN.OPTIONS.CONFIG.ADMIN_CONFIG_ROW.ADMIN_CONFIG_ROW_OPTION');
				}
				$t->parse('MAIN.OPTIONS.CONFIG.ADMIN_CONFIG_ROW');
			}
			/* === Hook  === */
			foreach (cot_getextplugins('admin.config.edit.tags') as $pl)
			{
				include $pl;
			}
			/* ===== */
			$t->assign('CONFIG_HIDDEN', cot_inputbox('hidden', 'editconfig', $structure_code));
			$t->parse('MAIN.OPTIONS.CONFIG');
		}
		$t->parse(($id || !empty($al)) ? 'MAIN.OPTIONS' : 'MAIN.DEFAULT.ROW');
	}

	if (!$id && empty($al))
	{
		$t->assign(array(
			'ADMIN_STRUCTURE_PAGINATION_PREV' => $pagenav['prev'],
			'ADMIN_STRUCTURE_PAGNAV' => $pagenav['main'],
			'ADMIN_STRUCTURE_PAGINATION_NEXT' => $pagenav['next'],
			'ADMIN_STRUCTURE_TOTALITEMS' => $totalitems,
			'ADMIN_STRUCTURE_COUNTER_ROW' => $ii,
		));
		$t->parse('MAIN.DEFAULT');

		// flush post buffer if it contains Update Table data
		$uri = str_replace('&_ajax=1', '', $_SERVER['REQUEST_URI']);
		$hash = md5($uri);
		if (is_array($_SESSION['cot_buffer'][$hash]['rstructurecode']))
			unset($_SESSION['cot_buffer'][$hash]);

		$t->assign(array(
			'ADMIN_STRUCTURE_URL_FORM_ADD' => cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&a=add&d='.$durl),
			'ADMIN_STRUCTURE_CODE' => cot_inputbox('text', 'rstructurecode', null, 'size="16"'),
			'ADMIN_STRUCTURE_PATH' => cot_inputbox('text', 'rstructurepath', null, 'size="16" maxlength="16"'),
			'ADMIN_STRUCTURE_TITLE' => cot_inputbox('text', 'rstructuretitle', null, 'size="64" maxlength="100"'),
			'ADMIN_STRUCTURE_DESC' => cot_inputbox('text', 'rstructuredesc', null, 'size="64" maxlength="255"'),
			'ADMIN_STRUCTURE_ICON' => cot_inputbox('text', 'rstructureicon', null, 'size="64" maxlength="128"'),
			'ADMIN_STRUCTURE_LOCKED' => cot_checkbox(null, 'rstructurelocked'),
			'ADMIN_STRUCTURE_TPLMODE' => cot_radiobox(null, 'rtplmode', array('1', '2', '3'), array($L['adm_tpl_empty'], $L['adm_tpl_parent'], $L['adm_tpl_forced']), '', '<br />')
		));

		// Extra fields
        if(!empty(cot::$extrafields[cot::$db->structure])) {
            foreach (cot::$extrafields[cot::$db->structure] as $exfld) {
                $exfld_val = cot_build_extrafields('rstructure' . $exfld['field_name'], $exfld, null);
                $exfld_title = cot_extrafield_title($exfld, 'structure_');
                
                $t->assign(array(
                    'ADMIN_STRUCTURE_' . strtoupper($exfld['field_name']) => $exfld_val,
                    'ADMIN_STRUCTURE_' . strtoupper($exfld['field_name']) . '_TITLE' => $exfld_title,
                    'ADMIN_STRUCTURE_EXTRAFLD' => $exfld_val,
                    'ADMIN_STRUCTURE_EXTRAFLD_TITLE' => $exfld_title
                ));
                $t->parse('MAIN.NEWCAT.EXTRAFLD');
            }
        }
		$t->parse('MAIN.NEWCAT');
	}

	cot_display_messages($t);

	/* === Hook  === */
	foreach (cot_getextplugins('admin.structure.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN');
	$adminmain = $t->text('MAIN');
}