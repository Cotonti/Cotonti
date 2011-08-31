<?php
/**
 * Administration panel
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('extrafields');
require_once cot_incfile('auth');
require_once cot_incfile('structure');

$id = cot_import('id', 'G', 'INT');
$c = cot_import('c', 'G', 'TXT');
list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['maxrowsperpage']);
$mode = cot_import('mode', 'G', 'ALP');

$t = new XTemplate(cot_tplfile(array('admin', 'structure', $n), 'core'));

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
	if (is_array($structure) && count($structure) > 0)
	{
		foreach ($structure as $code => $mod)
		{
			$icofile = $cfg['modules_dir'] . '/' . $code . '/' . $code . '.png';
			$t->assign(array(
				'ADMIN_STRUCTURE_EXT_URL' => cot_url('admin', 'm=structure&n='.$code),
				'ADMIN_STRUCTURE_EXT_ICO' => (file_exists($icofile)) ? $icofile : '',
				'ADMIN_STRUCTURE_EXT_NAME' => htmlspecialchars($cot_modules[$code]['title'])
			));
			$t->parse('LIST.ADMIN_STRUCTURE_EXT');
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
	// Edit structure for a module
	if (file_exists(cot_incfile($n, 'module')))
	{
		require_once cot_incfile($n, 'module');
	}

	if ($a == 'update')
	{
		$rstructurecode = cot_import('rstructurecode', 'P', 'ARR');
		$rstructurepath = cot_import('rstructurepath', 'P', 'ARR');
		$rstructuretitle = cot_import('rstructuretitle', 'P', 'ARR');
		$rstructuredesc = cot_import('rstructuredesc', 'P', 'ARR');
		$rstructureicon = cot_import('rstructureicon', 'P', 'ARR');
		$rstructurelocked = cot_import('rstructurelocked', 'P', 'ARR');

		foreach ($cot_extrafields[$db_structure] as $row)
		{
			if ($row['field_type'] != 'file' || $row['field_type'] != 'filesize')
			{
				$rstructureextrafieldsarr[$row['field_name']] = cot_import('rstructure'.$row['field_name'], 'P', 'ARR');
			}
			elseif($row['field_type'] == 'file')
			{
				// TODO FIXME!
				//$rstructureextrafieldsarr[$row['field_name']] = cot_import_filesarray('rstructure'.$row['field_name']);
			}
		}

		$rtplmodearr = cot_import('rstructuretplmode', 'P', 'ARR');
		$rtplforcedarr = cot_import('rstructuretplforced', 'P', 'ARR');

		foreach ($rstructurecode as $i => $k)
		{
			$oldrow = $db->query("SELECT * FROM $db_structure WHERE structure_id=".(int)$i)->fetch();
			$rstructure['structure_code'] = cot_import($rstructurecode[$i], 'D', 'TXT');
			$rstructure['structure_path'] = cot_import($rstructurepath[$i], 'D', 'TXT');
			$rstructure['structure_title'] = cot_import($rstructuretitle[$i], 'D', 'TXT');
			$rstructure['structure_desc'] = cot_import($rstructuredesc[$i], 'D', 'TXT');
			$rstructure['structure_icon'] = cot_import($rstructureicon[$i], 'D', 'TXT');
			if (cot_import($rstructurelocked[$i], 'D', 'BOL') != null)
			{
				$rstructure['structure_locked'] = (cot_import($rstructurelocked[$i], 'D', 'BOL')) ? 1 : 0;
			}

			foreach ($cot_extrafields[$db_structure] as $row)
			{
				$rstructure['structure_'.$row['field_name']] = cot_import_extrafields($rstructureextrafieldsarr[$row['field_name']][$i], $row, 'D', $oldrow['structure_'.$row['field_name']]);
			}

			$rtplmode = cot_import($rtplmodearr[$i], 'D', 'INT');

			if ($rtplmode == 3)
			{
				$rstructure['structure_tpl'] = cot_import($rtplforcedarr[$i], 'D', 'ALP');
			}
			elseif ($rtplmode == 2)
			{
				$rstructure['structure_tpl'] = 'same_as_parent';
			}
			else
			{
				$rstructure['structure_tpl'] = '';
			}

			$res = cot_structure_update($n, $i, $oldrow, $rstructure);
			if (is_array($res))
			{
				cot_error($res[0], $res[1]);
			}
		}
		cot_extrafield_movefiles();
		cot_auth_clear('all');
		if ($cache)
		{
			$cache->clear();
		}

		cot_message('Updated');
		cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$durl, '', true));
	}
	elseif ($a == 'add')
	{
		$rstructure['structure_code'] = cot_import('rstructurecode', 'P', 'TXT');
		$rstructure['structure_path'] = cot_import('rstructurepath', 'P', 'TXT');
		$rstructure['structure_title'] = cot_import('rstructuretitle', 'P', 'TXT');
		$rstructure['structure_desc'] = cot_import('rstructuredesc', 'P', 'TXT');
		$rstructure['structure_icon'] = cot_import('rstructureicon', 'P', 'TXT');
		$rstructure['structure_locked'] = (cot_import('rstructurelocked', 'P', 'BOL')) ? 1 : 0;
		$rstructure['structure_area'] = $n;

		foreach ($cot_extrafields[$db_structure] as $row)
		{
			$rstructure['structure_'.$row['field_name']] = cot_import_extrafields('rstructure'.$row['field_name'], $row);
		}

		$rtplmode = cot_import('rtplmode', 'P', 'INT');
		if ($rtplmode == 3)
		{
			$rstructure['structure_tpl'] = cot_import('rtplforced', 'P', 'ALP');
		}
		elseif ($rtplmode == 2)
		{
			$rstructure['structure_tpl'] = 'same_as_parent';
		}
		else
		{
			$rstructure['structure_tpl'] = '';
		}

		$res = cot_structure_add($n, $rstructure);
		if ($res === true)
		{
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

		cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$durl, '', true));
	}
	elseif ($a == 'delete')
	{
		cot_check_xg();

		if (cot_structure_delete($n, $c))
		{
			cot_message('Deleted');
		}

		cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$durl, '', true));
	}
	elseif ($a == 'resyncall')
	{
		cot_check_xg();
		$res = false;
		$area_sync = 'cot_'.$n.'_sync';
		if(function_exists($area_sync))
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
		$res ? cot_message('Resynced') : cot_message("Error: function $area_sync doesn't exist."); // TODO i18n
		($cache && $cfg['cache_'.$n]) && $cache->page->clear($n);
		cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$durl, '', true));
	}

	if($id > 0)
	{
		$sql = $db->query("SELECT * FROM $db_structure WHERE structure_id=$id LIMIT 1");
		cot_die($sql->rowCount() == 0);
	}
	elseif($mode && ($mode=='all' || $structure[$mode]))
	{
		$sqlmask = ($mode == 'all') ? "structure_path NOT LIKE '%.%'" : "structure_path LIKE '".$db->prep($structure[$mode]['rpath']).".%' AND structure_path NOT LIKE '".$db->prep($structure[$mode]['rpath']).".%.%'";
		$sql = $db->query("SELECT * FROM $db_structure WHERE structure_area='".$db->prep($n)."' AND $sqlmask ORDER BY structure_path ASC, structure_code ASC LIMIT $d, ".$cfg['maxrowsperpage']);

		$totalitems = $db->query("SELECT COUNT(*) FROM $db_structure WHERE structure_area='".$db->prep($n)."' AND $sqlmask")->fetchColumn();
		$pagenav = cot_pagenav('admin', 'm=structure&n='.$n.'&mode='.$mode, $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);
	}
	else
	{
		$sql = $db->query("SELECT * FROM $db_structure WHERE structure_area='".$db->prep($n)."' ORDER BY structure_path ASC, structure_code ASC LIMIT $d, ".$cfg['maxrowsperpage']);

		$totalitems = $db->query("SELECT COUNT(*) FROM $db_structure WHERE structure_area='".$db->prep($n)."'")->fetchColumn();
		$pagenav = cot_pagenav('admin', 'm=structure&n='.$n, $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);
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
		($id) && $adminpath[] = array (cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&id='.$id), htmlspecialchars($row['structure_title']));

		$ii++;
		$structure_id = $row['structure_id'];
		$structure_code = $row['structure_code'];
		$pathfielddep = count(explode(".", $row['structure_path']));
		$dozvil = ($row['structure_count'] > 0) ? false : true;

		$pathspaceimg = '';
		for($pathfielddepi = 1; $pathfielddepi < $pathfielddep; $pathfielddepi++)
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
			'ADMIN_STRUCTURE_UPDATE_DEL_URL' => cot_confirm_url(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&a=delete&id='.$structure_id.'&c='.$row['structure_code'].'&d='.$durl.'&'.cot_xg(), 'admin')),
			'ADMIN_STRUCTURE_ID' => $structure_id,
			'ADMIN_STRUCTURE_CODE' => cot_inputbox('text', 'rstructurecode['.$structure_id.']', $structure_code, 'size="10" maxlength="255"'),
			'ADMIN_STRUCTURE_SPACEIMG' => $pathspaceimg,
			'ADMIN_STRUCTURE_PATHFIELDIMG' => (mb_strpos($row['structure_path'], '.') == 0) ? $R['admin_icon_pathfieldnoimg'] : $R['admin_icon_pathfieldimg'],
			'ADMIN_STRUCTURE_PATH' => cot_inputbox('text', 'rstructurepath['.$structure_id.']', $row['structure_path'], 'size="12" maxlength="255"'),
			'ADMIN_STRUCTURE_TPL_SYM' => $structure_tpl_sym,
			'ADMIN_STRUCTURE_TPLMODE' => cot_radiobox($check_tpl, 'rstructuretplmode['.$structure_id.']', array('1', '2', '3'), array($L['adm_tpl_empty'], $L['adm_tpl_parent'], $L['adm_tpl_forced']), '', '<br />'),
			'ADMIN_STRUCTURE_TITLE' => cot_inputbox('text', 'rstructuretitle['.$structure_id.']', $row['structure_title'], 'size="32" maxlength="255"'),
			'ADMIN_STRUCTURE_DESC' => cot_inputbox('text', 'rstructuredesc['.$structure_id.']', $row['structure_desc'], 'size="64" maxlength="255"'),
			'ADMIN_STRUCTURE_ICON' => cot_inputbox('text', 'rstructureicon['.$structure_id.']', $row['structure_icon'], 'size="64" maxlength="128"'),
			'ADMIN_STRUCTURE_LOCKED' => cot_checkbox($row['structure_locked'], 'rstructurelocked['.$structure_id.']'),
			'ADMIN_STRUCTURE_SELECT' => $cat_selectbox,
			'ADMIN_STRUCTURE_COUNT' => $row['structure_count'],
			/*TODO*/		'ADMIN_STRUCTURE_JUMPTO_URL' => cot_url($n, 'c='.$structure_code),
			'ADMIN_STRUCTURE_RIGHTS_URL' => cot_url('admin', 'm=rightsbyitem&ic='.$n.'&io='.$structure_code),
			'ADMIN_STRUCTURE_OPTIONS_URL' => cot_url('admin', 'm=structure&n='.$n.'&id='.$structure_id.'&'.cot_xg()),
			'ADMIN_STRUCTURE_CONFIG_URL' => cot_url('admin', 'm=config&n=edit&o=module&p='.$n.'&sub='.$structure_code),
			'ADMIN_STRUCTURE_ODDEVEN' => cot_build_oddeven($ii)
		));

		foreach($cot_extrafields[$db_structure] as $i => $row2)
		{
			$t->assign('ADMIN_STRUCTURE_'.strtoupper($row2['field_name']).'_TITLE', isset($L['structure_'.$row2['field_name'].'_title']) ?  $L['structure_'.$row2['field_name'].'_title'] : $row2['field_description']);
			$t->assign('ADMIN_STRUCTURE_'.strtoupper($row2['field_name']), cot_build_extrafields('rstructure'.$row2['field_name'].'['.$structure_id.']', $row2, $row['structure_'.$row2['field_name']]));

			// extra fields universal tags
			$t->assign('ADMIN_STRUCTURE_EXTRAFLD', cot_build_extrafields('rstructure'.$row2['field_name'],  $row2, $row['structure_'.$row2['field_name']]));
			$t->assign('ADMIN_STRUCTURE_EXTRAFLD_TITLE', isset($L['structure_'.$row2['field_name'].'_title']) ?  $L['structure_'.$row2['field_name'].'_title'] : $row2['field_description']);
			$t->parse(($id) ? 'MAIN.OPTIONS.EXTRAFLD' : 'MAIN.DEFAULT.ROW.EXTRAFLD');
		}

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse(($id) ? 'MAIN.OPTIONS' : 'MAIN.DEFAULT.ROW');
	}

	if (!$id)
	{
		$t->assign(array(
			'ADMIN_STRUCTURE_PAGINATION_PREV' => $pagenav['prev'],
			'ADMIN_STRUCTURE_PAGNAV' => $pagenav['main'],
			'ADMIN_STRUCTURE_PAGINATION_NEXT' => $pagenav['next'],
			'ADMIN_STRUCTURE_TOTALITEMS' => $totalitems,
			'ADMIN_STRUCTURE_COUNTER_ROW' => $ii,
		));
		$t->parse('MAIN.DEFAULT');

		$t->assign(array(
			'ADMIN_STRUCTURE_URL_FORM_ADD' => cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&a=add&d='.$durl),
			'ADMIN_STRUCTURE_CODE' => cot_inputbox('text', 'rstructurecode', '', 'size="16"'),
			'ADMIN_STRUCTURE_PATH' => cot_inputbox('text', 'rstructurepath', '', 'size="16" maxlength="16"'),
			'ADMIN_STRUCTURE_TITLE' => cot_inputbox('text', 'rstructuretitle', '', 'size="64" maxlength="100"'),
			'ADMIN_STRUCTURE_DESC' => cot_inputbox('text', 'rstructuredesc', '', 'size="64" maxlength="255"'),
			'ADMIN_STRUCTURE_ICON' => cot_inputbox('text', 'rstructureicon', '', 'size="64" maxlength="128"'),
			'ADMIN_STRUCTURE_LOCKED' => cot_checkbox(0, 'rstructurelocked'),
			'ADMIN_STRUCTURE_TPLMODE' => cot_radiobox(1, 'rtplmode', array('1', '2', '3'), array($L['adm_tpl_empty'], $L['adm_tpl_parent'], $L['adm_tpl_forced']), '', '<br />')
		));

		// Extra fields
		foreach($cot_extrafields[$db_structure] as $i => $row2)
		{
			$t->assign('ADMIN_STRUCTURE_'.strtoupper($row2['field_name']), cot_build_extrafields('rstructure'.$row2['field_name'],  $row2, ''));
			$t->assign('ADMIN_STRUCTURE_'.strtoupper($row2['field_name']).'_TITLE', isset($L['structure_'.$row2['field_name'].'_title']) ?  $L['structure_'.$row2['field_name'].'_title'] : $row2['field_description']);

			// extra fields universal tags
			$t->assign('ADMIN_STRUCTURE_EXTRAFLD', cot_build_extrafields('rstructure'.$row2['field_name'],  $row2, ''));
			$t->assign('ADMIN_STRUCTURE_EXTRAFLD_TITLE', isset($L['structure_'.$row2['field_name'].'_title']) ?  $L['structure_'.$row2['field_name'].'_title'] : $row2['field_description']);
			$t->parse('MAIN.NEWCAT.EXTRAFLD');
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
?>