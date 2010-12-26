<?php
/**
 * Administration panel
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('extrafields');
require_once cot_incfile('auth');

$id = cot_import('id', 'G', 'INT');
$c = cot_import('c', 'G', 'TXT');
list($pg, $d) = cot_import_pagenav('d', $cfg['maxrowsperpage']);
$mode = cot_import('mode', 'G', 'ALP');

$t = new XTemplate(cot_tplfile(array('admin', 'structure', $n), 'core'));

/* === Hook === */
foreach (cot_getextplugins('admin.structure.first') as $pl)
{
	include $pl;
}
/* ===== */

(empty($n)) && cot_redirect(cot_url('message', 'msg=950', '', true));

if ($a == 'update')
{
	$rstructurecode = cot_import('rstructurecode', 'P', 'ARR');
	$rstructurepath = cot_import('rstructurepath', 'P', 'ARR');
	$rstructuretitle = cot_import('rstructuretitle', 'P', 'ARR');
	$rstructuredesc = cot_import('rstructuredesc', 'P', 'ARR');
	$rstructureicon = cot_import('rstructureicon', 'P', 'ARR');
	$rstructurelocked = cot_import('rstructurelocked', 'P', 'ARR');

	foreach ($cot_extrafields['structure'] as $row)
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
	$rtplforcedarr = cot_import('rtplforced', 'P', 'ARR');
	
	foreach ($rstructurecode as $i => $k)
	{
		$oldrow = $db->query("SELECT structure_code FROM $db_structure WHERE structure_id='".$i."' ")->fetch();
		$rstructure['structure_code'] = cot_import($rstructurecode[$i], 'D', 'TXT');
		$rstructure['structure_path'] = cot_import($rstructurepath[$i], 'D', 'TXT');
		$rstructure['structure_title'] = cot_import($rstructuretitle[$i], 'D', 'TXT');
		$rstructure['structure_desc'] = cot_import($rstructuredesc[$i], 'D', 'TXT');
		$rstructure['structure_icon'] = cot_import($rstructureicon[$i], 'D', 'TXT');
		$rstructure['structure_locked'] = (cot_import($rstructurelocked[$i], 'D', 'BOL')) ? 1 : 0;

		foreach ($cot_extrafields['structure'] as $row)
		{
			$rstructure['structure_'.$row['field_name']] = cot_import_extrafields($rstructureextrafieldsarr[$row['field_name']][$i], $row, 'D', $oldrow['structure_'.$row['field_name']]);
		}

		$rtplmode = cot_import($rtplmodearr[$i], 'D', 'INT');
		($rtplmode > 0) && $rstructure['structure_tpl'] = ($rtplmode == 1) ? '' : (($rtplmode == 3) ? 'same_as_parent' : cot_import($rtplforcedarr[$i], 'D', 'ALP'));

		/* === Hook === */
		foreach (cot_getextplugins('admin.structure.update') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($oldrow['structure_code'] != $rstructure['structure_code'])
		{
			$db->update($db_auth, array("auth_option" => $rstructure['structure_code']), "auth_code='".$db->prep($n)."' AND auth_option='".$db->prep($roww['structure_code'])."'");
			$db->update($db_config, array("config_subcat" => $rstructure['structure_code']), "config_cat='".$db->prep($n)."' AND config_subcat='".$db->prep($roww['structure_code'])."' AND config_owner='module'");
			$area_updatecat = 'cot_'.$n.'_updatecat';
			(function_exists($area_updatecat)) ? $area_updatecat($roww['structure_code'], $rstructure['structure_code']) : FALSE;
			cot_auth_reorder();
		}

		$area_sync = 'cot_'.$n.'_sync';
		$rstructure['structure_count'] = (function_exists($area_sync)) ? $area_sync($rstructure['structure_code']) : 0;

		$sql1 = $db->update($db_structure, $rstructure, "structure_id='".$i."'");
	}
	cot_extrafield_movefiles();
	cot_auth_clear('all');
	if ($cache)
	{
		$cache->db->remove('structure', 'system');
		$cfg['cache_'.$n] &&  $cache->page->clear($n);
	}

	cot_message('Updated');
	cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$d, '', true));
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

	foreach ($cot_extrafields['structure'] as $row)
	{
		$rstructure['structure_'.$row['field_name']] = cot_import_extrafields('rstructure'.$row['field_name'], $row);
	}

	$rtplmode = cot_import('rtplmode', 'P', 'INT');
	$rstructure['structure_tpl'] = ($rtplmode == 1) ? '' : (($rtplmode == 3) ? 'same_as_parent' : cot_import('rtplforced', 'P', 'ALP'));

	/* === Hook === */
	foreach (cot_getextplugins('admin.structure.add') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!empty($rstructure['structure_title']) && !empty($rstructure['structure_code']) && !empty($rstructure['structure_path']) && $rstructure['structure_code'] != 'all')
	{
		$sql = $db->query("SELECT structure_code FROM $db_structure WHERE structure_code='".$db->prep($rstructure['structure_code'])."' LIMIT 1");
		if ($sql->rowCount() == 0 || $rstructure['structure_code'] != 'all')
		{
			$sql = $db->insert($db_structure, $rstructure);
			$auth_permit = array(COT_GROUP_DEFAULT => 7, COT_GROUP_GUESTS => 5, COT_GROUP_MEMBERS => 7);
			$auth_lock = array(COT_GROUP_DEFAULT => 0, COT_GROUP_GUESTS => 250, COT_GROUP_MEMBERS => 128);
			cot_auth_add_item($n, $rstructure['structure_code'], $auth_permit, $auth_lock);
			$area_addcat = 'cot_'.$n.'_addcat';
			(function_exists($area_addcat)) ? $area_addcat($rstructure['structure_code']) : FALSE;
			$cache && $cache->db->remove('structure', 'system');
			cot_message('Added');
		}
		else
		{
			cot_message('Error');
		}
	}
	else
	{
		cot_message('Error');
	}
	($cache && $cfg['cache_'.$n]) && $cache->page->clear($n);
	cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$d, '', true));
}
elseif ($a == 'delete')
{
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('admin.structure.delete') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$db->delete($db_structure, "structure_code='".$db->prep($c)."' AND structure_area='".$db->prep($n)."'");
	$db->delete($db_config, "config_cat='".$db->prep($n)."' AND config_subcat='".$db->prep($c)."' AND config_owner='module'");
	cot_auth_remove_item($n, $c);
	$area_deletecat = 'cot_'.$n.'_deletecat';
	(function_exists($area_deletecat)) ? $area_deletecat($c) : FALSE;
	if ($cache)
	{
		$cache->db->remove('structure', 'system');
		$cfg['cache_'.$n] && $cache->page->clear($n);
	}
	cot_message('Deleted');
	cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$d, '', true));
}
elseif ($a == 'resyncall')
{
	cot_check_xg();
	$res = TRUE;
	$area_sync = 'cot_'.$n.'_sync';
	$sql = $db->query("SELECT structure_code FROM $db_structure WHERE structure_area='".$db->prep($n)."'");
	while ($row = $sql->fetch())
	{
		if(function_exists($area_sync))
		{
			$items = (function_exists($area_sync)) ? $area_sync($cat) : 0;		
			$db->update($db_structure, array("structure_count" => (int)$items), "structure_code='".$db->prep($cat)."' AND structure_area='".$db->prep($n)."'");
		}
	}
	$sql->closeCursor();
	$res ? cot_message('Resynced') : cot_message('Error');
	($cache && $cfg['cache_'.$n]) && $cache->page->clear($n);
	cot_redirect(cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&d='.$d, '', true));
}

if($id > 0)
{
	$sql = $db->query("SELECT * FROM $db_structure WHERE structure_id='$id' LIMIT 1");
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

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.structure.loop');
/* ===== */
while ($row = $sql->fetch())
{
	($id) && $adminpath[] = array (cot_url('admin', "m=structure&n='.$n.'&mode='.$mode.'&id=".$id), htmlspecialchars($row['structure_title']));

	$ii++;
	$structure_id = $row['structure_id'];
	$structure_code = $row['structure_code'];
	$pathfielddep = count(explode(".", $row['structure_path']));
	$dozvil = ($row['structure_count'] > 0) ? false : true;
	
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
		'ADMIN_STRUCTURE_UPDATE_DEL_URL' => cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&a=delete&id='.$structure_id.'&c='.$row['structure_code'].'&d='.$d.'&'.cot_xg()),
		'ADMIN_STRUCTURE_ID' => $structure_id,
		'ADMIN_STRUCTURE_CODE' => cot_inputbox('text', 'rstructurecode['.$structure_id.']', $structure_code, 'size="8" maxlength="255"'),
		'ADMIN_STRUCTURE_PATHFIELDIMG' => (mb_strpos($row['structure_path'], '.') == 0) ? '' : $R['admin_icon_pathfieldimg'],
		'ADMIN_STRUCTURE_PATH' => cot_inputbox('text', 'rstructurepath['.$structure_id.']', $row['structure_path'], 'size="3" maxlength="255"'),
		'ADMIN_STRUCTURE_TPL_SYM' => $structure_tpl_sym,
		'ADMIN_STRUCTURE_TPLMODE' => cot_radiobox($check_tpl, 'rstructuretplmode['.$structure_id.']', array('1'. '2', '3'), array($L['adm_tpl_empty'], $L['adm_tpl_forced'].'  '.$cat_selectbox, $L['adm_tpl_parent']), '', '<br />'),
		'ADMIN_STRUCTURE_TITLE' => cot_inputbox('text', 'rstructuretitle['.$structure_id.']', $row['structure_title'], 'size="18" maxlength="255"'),
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

	foreach($cot_extrafields['structure'] as $i => $row2)
	{
		$t->assign('ADMIN_STRUCTURE_'.strtoupper($row2['field_name']).'_TITLE', isset($L['structure_'.$row2['field_name'].'_title']) ?  $L['structure_'.$row2['field_name'].'_title'] : $row2['field_description']);
		$t->assign('ADMIN_STRUCTURE_'.strtoupper($row2['field_name']), cot_build_extrafields('rstructure'.$row2['field_name'].'['.$structure_id.']', $row2, $row['structure_'.$row2['field_name']]));

		// extra fields universal tags
		$t->assign('ADMIN_STRUCTURE_EXTRAFLD', cot_build_extrafields('rstructure'.$row2['field_name'],  $row2, $row['structure_'.$row2['field_name']]));
		$t->assign('ADMIN_STRUCTURE_EXTRAFLD_TITLE', isset($L['structure_'.$row2['field_name'].'_title']) ?  $L['structure_'.$row2['field_name'].'_title'] : $row2['field_description']);
		$t->parse(($id) ? 'MAIN.OPTIONS.EXTRAFLD' : 'MAIN.DEFULT.ROW.EXTRAFLD');
	}

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse(($id) ? 'MAIN.OPTIONS' : 'MAIN.DEFULT.ROW');
}
if(!$id)
{
	$t->assign(array(
		'ADMIN_STRUCTURE_PAGINATION_PREV' => $pagenav['prev'],
		'ADMIN_STRUCTURE_PAGNAV' => $pagenav['main'],
		'ADMIN_STRUCTURE_PAGINATION_NEXT' => $pagenav['next'],
		'ADMIN_STRUCTURE_TOTALITEMS' => $totalitems,
		'ADMIN_STRUCTURE_COUNTER_ROW' => $ii,
	));
	$t->parse('MAIN.DEFULT');

	$t->assign(array(
		'ADMIN_STRUCTURE_URL_FORM_ADD' => cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&a=add'),
		'ADMIN_STRUCTURE_CODE' => cot_inputbox('text', 'rstructurecode', '', 'size="16"'),
		'ADMIN_STRUCTURE_PATH' => cot_inputbox('text', 'rstructurepath', '', 'size="16" maxlength="16"'),
		'ADMIN_STRUCTURE_TITLE' => cot_inputbox('text', 'rstructuretitle', '', 'size="64" maxlength="100"'),
		'ADMIN_STRUCTURE_DESC' => cot_inputbox('text', 'rstructuredesc', '', 'size="64" maxlength="255"'),
		'ADMIN_STRUCTURE_ICON' => cot_inputbox('text', 'rstructureicon', '', 'size="64" maxlength="128"'),
		'ADMIN_STRUCTURE_LOCKED' => cot_checkbox(0, 'rstructurelocked')
	));

	// Extra fields
	foreach($cot_extrafields['structure'] as $i => $row2)
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

$t->assign(array(
	'ADMIN_STRUCTURE_UPDATE_FORM_URL' => cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&a=update&d='.$d),
	'ADMIN_PAGE_STRUCTURE_RESYNCALL' => cot_url('admin', 'm=structure&n='.$n.'&mode='.$mode.'&a=resyncall&'.cot_xg().'&d='.$d),
	'ADMIN_STRUCTURE_URL_EXTRAFIELDS' => cot_url('admin', 'm=extrafields&n=structure')
));

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

?>