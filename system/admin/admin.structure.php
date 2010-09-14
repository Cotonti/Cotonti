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

cot_require_api('extrafields');
cot_require_api('auth');

cot_require('page');

$t = new XTemplate(cot_skinfile('admin.structure'));

$adminpath[] = array (cot_url('admin', 'm=structure'), $L['Categories']);
$adminhelp = $L['adm_help_structure'];

$id = cot_import('id', 'G', 'INT');
$c = cot_import('c', 'G', 'TXT');
$d = cot_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

/* === Hook === */
foreach (cot_getextplugins('admin.structure.first') as $pl)
{
	include $pl;
}
/* ===== */

$options_sort = array(
	'id' => $L['Id'],
	'type' => $L['Type'],
	'key' => $L['Key'],
	'title' => $L['Title'],
	'desc' => $L['Description'],
	'text' => $L['Body'],
	'author' => $L['Author'],
	'ownerid' => $L['Owner'],
	'date' => $L['Date'],
	'begin' => $L['Begin'],
	'expire' => $L['Expire'],
	'rating' => $L['Rating'],
	'count' => $L['Hits'],
	'file' => $L['adm_fileyesno'],
	'url' => $L['adm_fileurl'],
	'size' => $L['adm_filesize'],
	'filecount' => $L['adm_filecount']
);

// Extra fields pages
$extrafields = array();
if (is_array($cot_extrafields['pages']))
{
	foreach($cot_extrafields['pages'] as $i => $row)
	{
		$$extrafields[$row['field_name']] = isset($L['page_'.$row['field_name'].'_title']) ? $L['page_'.$row['field_name'].'_title'] : $row['field_description'];
	}
}
$options_sort = ($options_sort + $extrafields);

$options_way = array(
	'asc' => $L['Ascending'],
	'desc' => $L['Descending']
);

if ($n == 'options')
{
	if ($a == 'update')
	{
		$rcode = cot_import('rcode', 'P', 'TXT');
		$rpath = cot_import('rpath', 'P', 'TXT');
		$rtitle = cot_import('rtitle', 'P', 'TXT');
		$rtplmode = cot_import('rtplmode', 'P', 'INT');
		$rdesc = cot_import('rdesc', 'P', 'TXT');
		$ricon = cot_import('ricon', 'P', 'TXT');
		$rgroup = cot_import('rgroup', 'P', 'BOL');
		$rgroup = ($rgroup) ? 1 : 0;
		$rorder = cot_import('rorder', 'P', 'ALP');
		$rway = cot_import('rway', 'P', 'ALP');
		$rallowratings = cot_import('rallowratings', 'P', 'BOL');

		// Extra fields
		foreach ($cot_extrafields['structure'] as $row)
		{
			$import = cot_import('rstructure'.$row['field_name'], 'P', 'HTM');
			if ($row['field_type'] == 'checkbox')
			{
				$import = $import != '';
			}
			$rstructureextrafields[$row['field_name']] = $import;
		}

		$sqql = cot_db_query("SELECT structure_code FROM $db_structure WHERE structure_id='".$id."' ");
		$roww = cot_db_fetcharray($sqql);

		/* === Hook === */
		foreach (cot_getextplugins('admin.structure.options.update') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($roww['structure_code'] != $rcode)
		{

			$sql = cot_db_query("UPDATE $db_structure SET structure_code='".cot_db_prep($rcode)."' WHERE structure_code='".cot_db_prep($roww['structure_code'])."' ");
			$sql = cot_db_query("DELETE FROM $db_cache WHERE c_name='".cot_db_prep($roww['structure_code'])."' ");
			$sql = cot_db_query("UPDATE $db_auth SET auth_option='".cot_db_prep($rcode)."' WHERE auth_code='page' AND auth_option='".cot_db_prep($roww['structure_code'])."' ");
			$sql = cot_db_query("UPDATE $db_pages SET page_cat='".cot_db_prep($rcode)."' WHERE page_cat='".cot_db_prep($roww['structure_code'])."' ");

			cot_auth_reorder();
			cot_auth_clear('all');
			$cot_cache && $cot_cache->db->remove('cot_cat', 'system');
		}

		if ($rtplmode == 1)
		{
			$rtpl = '';
		}
		elseif ($rtplmode == 3)
		{
			$rtpl = 'same_as_parent';
		}
		else
		{
			$rtpl = cot_import('rtplforced', 'P', 'ALP');
		}

		$sqltxt = "UPDATE $db_structure
			SET structure_path='".cot_db_prep($rpath)."',
				structure_tpl='".cot_db_prep($rtpl)."',
				structure_title='".cot_db_prep($rtitle)."',
				structure_desc='".cot_db_prep($rdesc)."',
				structure_icon='".cot_db_prep($ricon)."',
				structure_group='".$rgroup."',
				structure_order='".cot_db_prep($rorder.".".$rway)."',";

		// Extra fields
		foreach ($cot_extrafields['structure'] as $i => $fildname)
		{
			if (!is_null($rstructureextrafields[$i]))
			{
				$sqltxt .= "structure_".cot_db_prep($fildname['field_name'])."='".cot_db_prep($rstructureextrafields[$i])."',";
			}
		}

		$sqltxt .= "
				structure_ratings='".$rallowratings."'
			WHERE structure_id='".$id."'";
		$sql = cot_db_query($sqltxt);

		if ($cot_cache)
		{
			$cot_cache->db->remove('cot_cat', 'system');
			if ($cfg['cache_page'])
			{
				$cot_cache->page->clear('page');
			}
		}

		cot_message('Updated');

		cot_redirect(cot_url('admin', 'm=structure&d='.$d.$additionsforurl, '', true));
	}
	elseif ($a == 'resync')
	{
		cot_check_xg();

		cot_structure_resync($id) ? cot_message('Resynced') : cot_message('Error');

		if ($cot_cache && $cfg['cache_page'])
		{
			$cot_cache->page->clear('page');
		}
	}

	$sql = cot_db_query("SELECT * FROM $db_structure WHERE structure_id='$id' LIMIT 1");
	cot_die(cot_db_numrows($sql) == 0);

	$handle = opendir('./themes/'.$cfg['defaultskin'].'/');
	$allskinfiles = array();

	while ($f = readdir($handle))
	{
		if (($f != '.') && ($f != '..') && mb_strtolower(mb_substr($f, mb_strrpos($f, '.') + 1, 4)) == 'tpl')
		{
			$allskinfiles[] = $f;
		}
	}
	closedir($handle);

	$allskinfiles = implode(',', $allskinfiles);

	$row = cot_db_fetcharray($sql);

	$structure_id = $row['structure_id'];
	$structure_code = $row['structure_code'];
	$structure_path = $row['structure_path'];
	$structure_title = $row['structure_title'];
	$structure_desc = $row['structure_desc'];
	$structure_icon = $row['structure_icon'];
	$structure_group = $row['structure_group'];
	$structure_ratings = $row['structure_ratings'];
	$raw = explode('.', $row['structure_order']);
	$sort = $raw[0];
	$way = $raw[1];

	reset($options_sort);
	reset($options_way);

	if (empty($row['structure_tpl']))
	{
		$check_tpl = "1";
	}
	elseif ($row['structure_tpl'] == 'same_as_parent')
	{
		$structure_tpl_sym = "*";
		$check_tpl = "2";
	}
	else
	{
		$structure_tpl_sym = "+";
		$check_tpl = "3";
	}

	$adminpath[] = array (cot_url('admin', "m=structure&n=options&id=".$id), htmlspecialchars($structure_title));

	foreach ($cot_cat as $i => $x)
	{
		if ($i != 'all')
		{
			$cat_path[$i] = $x['tpath'];
		}
	}
	$cat_selectbox = cot_selectbox($row['structure_tpl'], 'rtplforced', array_keys($cat_path), array_values($cat_path), false);

	$t->assign(array(
		'ADMIN_STRUCTURE_UPDATE_FORM_URL' => cot_url('admin', 'm=structure&n=options&a=update&id='.$structure_id.'&d='.$d.'&'.cot_xg()),
		'ADMIN_STRUCTURE_CODE' => cot_inputbox('text', 'rcode', $structure_code, 'size="16"'),
		'ADMIN_STRUCTURE_PATH' => cot_inputbox('text', 'rpath', $structure_path, 'size="16" maxlength="16"'),
		'ADMIN_STRUCTURE_TITLE' => cot_inputbox('text', 'rtitle', $structure_title, 'size="64" maxlength="100"'),
		'ADMIN_STRUCTURE_DESC' => cot_inputbox('text', 'rdesc', $structure_desc, 'size="64" maxlength="255"'),
		'ADMIN_STRUCTURE_ICON' => cot_inputbox('text', 'ricon', $structure_icon, 'size="64" maxlength="128"'),
		'ADMIN_STRUCTURE_GROUP' => cot_checkbox(($structure_pages || $structure_group), 'rgroup'),
		'ADMIN_STRUCTURE_SELECT' => $cat_selectbox,
		'ADMIN_STRUCTURE_TPLMODE' => cot_radiobox($check_tpl, 'rtplmode', array('1'. '2', '3'), array($L['adm_tpl_empty'], $L['adm_tpl_forced'].'  '.$cat_selectbox, $L['adm_tpl_parent']), '', '<br />'),
		'ADMIN_STRUCTURE_WAY' => cot_selectbox($way, 'rway', array_keys($options_way), array_values($options_way), false),
		'ADMIN_STRUCTURE_ORDER' => cot_selectbox($sort, 'rorder', array_keys($options_sort), array_values($options_sort), false),
		'ADMIN_STRUCTURE_RATINGS' => cot_radiobox($structure_ratings, 'rallowratings', array(1, 0), array($L['Yes'], $L['No'])),
		'ADMIN_STRUCTURE_RESYNC' => cot_url('admin', 'm=structure&n=options&a=resync&id='.$structure_id.'&'.cot_xg()),
	));

	// Extra fields
	foreach($cot_extrafields['structure'] as $i => $row2)
	{
		$uname = strtoupper($row['field_name']);
		$t->assign('ADMIN_STRUCTURE_'.$uname, cot_build_extrafields('structure',  $row2, $row['structure_'.$row2['field_name']]));
		$t->assign('ADMIN_STRUCTURE_'.$uname.'_TITLE', isset($L['structure_'.$row2['field_name'].'_title']) ?  $L['structure_'.$row2['field_name'].'_title'] : $row2['field_description']);

		// extra fields universal tags
		$t->assign('ADMIN_STRUCTURE_EXTRAFLD', cot_build_extrafields('structure',  $row2, $row['structure_'.$row2['field_name']]));
		$t->assign('ADMIN_STRUCTURE_EXTRAFLD_TITLE', isset($L['structure_'.$row2['field_name'].'_title']) ?  $L['structure_'.$row2['field_name'].'_title'] : $row2['field_description']);
		$t->parse('MAIN.OPTIONS.EXTRAFLD');
	}

	/* === Hook === */
	foreach (cot_getextplugins('admin.structure.options.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.OPTIONS');
}
else
{
	if ($a == 'update')
	{
		$s = cot_import('s', 'P', 'ARR');

		foreach ($s as $i => $k)
		{
			$s[$i]['rgroup'] = (isset($s[$i]['rgroup'])) ? 1 : 0;
			// Extra fields
			foreach ($cot_extrafields['structure'] as $row)
			{
				$import = $s[$i]['rstructure'.$row['field_name']];
				if ($row['field_type'] == 'checkbox')
				{
					$import = $import != '';
				}
				$rstructureextrafields[$row['field_name']] = $import;
			}


			$sqql = cot_db_query("SELECT structure_code FROM $db_structure WHERE structure_id='".$i."' ");
			$roww = cot_db_fetcharray($sqql);

			/* === Hook === */
			foreach (cot_getextplugins('admin.structure.update') as $pl)
			{
				include $pl;
			}
			/* ===== */

			if ($roww['structure_code'] != $s[$i]['rcode'])
			{
				$sql = cot_db_query("UPDATE $db_structure SET structure_code='".cot_db_prep($s[$i]['rcode'])."' WHERE structure_code='".cot_db_prep($roww['structure_code'])."' ");
				$sql = cot_db_query("DELETE FROM $db_cache WHERE c_name='".cot_db_prep($roww['structure_code'])."' ");
				$sql = cot_db_query("UPDATE $db_auth SET auth_option='".cot_db_prep($s[$i]['rcode'])."' WHERE auth_code='page' AND auth_option='".cot_db_prep($roww['structure_code'])."' ");
				$sql = cot_db_query("UPDATE $db_pages SET page_cat='".cot_db_prep($s[$i]['rcode'])."' WHERE page_cat='".cot_db_prep($roww['structure_code'])."' ");

				cot_auth_reorder();
				cot_auth_clear('all');
			}

			$sql1text = "UPDATE $db_structure
				SET ";

			// Extra fields
			foreach ($cot_extrafields['structure'] as $j => $fildname)
			{
				if (!is_null($rstructureextrafields[$j]))
				{
					$sql1text .= "structure_".cot_db_prep($fildname['field_name'])."='".cot_db_prep($rstructureextrafields[$j])."',";
				}
			}

			$sql1text .= "
					structure_path='".cot_db_prep($s[$i]['rpath'])."',
					structure_title='".cot_db_prep($s[$i]['rtitle'])."',
					structure_order='".cot_db_prep($s[$i]['rorder'].".".$s[$i]['rway'])."',
					structure_group='".$s[$i]['rgroup']."'
				WHERE structure_id='".$i."'";
			$sql1 = cot_db_query($sql1text);
		}

		cot_auth_clear('all');
		if ($cot_cache)
		{
			$cot_cache->db->remove('cot_cat', 'system');
			if ($cfg['cache_page'])
			{
				$cot_cache->page->clear('page');
			}
		}

		cot_message('Updated');
	}
	elseif ($a == 'add')
	{
		$g = array ('ncode', 'npath', 'ntitle', 'ndesc', 'nicon', 'ngroup', 'norder', 'nway');
		foreach ($g as $k => $x)
		{
			$$x = $_POST[$x];
		}
		$ngroup = (isset($ngroup)) ? 1 : 0;

		// Extra fields
		foreach ($cot_extrafields['structure'] as $row)
		{
			$import = cot_import('newstructure'.$row['field_name'], 'P', 'HTM');
			if ($row['field_type'] == 'checkbox')
			{
				$import = $import != '';
			}
			$rstructureextrafields[$row['field_name']] = $import;
		}

		/* === Hook === */
		foreach (cot_getextplugins('admin.structure.add') as $pl)
		{
			include $pl;
		}
		/* ===== */

		cot_structure_newcat($ncode, $npath, $ntitle, $ndesc, $nicon, $ngroup, $norder, $nway, $rstructureextrafields)
			? cot_message('Added') : cot_message('Error');

		if ($cot_cache && $cfg['cache_page'])
		{
			$cot_cache->page->clear('page');
		}
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

		cot_structure_delcat($id, $c);

		if ($cot_cache && $cfg['cache_page'])
		{
			$cot_cache->page->clear('page');
		}

		cot_message('Deleted');
	}
	elseif ($a == 'resyncall')
	{
		cot_check_xg();

		cot_structure_resyncall() ? cot_message('Resynced') : cot_message('Error');

		if ($cot_cache && $cfg['cache_page'])
		{
			$cot_cache->page->clear('page');
		}
	}

	$sql = cot_db_query("SELECT DISTINCT(page_cat), COUNT(*) FROM $db_pages WHERE 1 GROUP BY page_cat");

	while ($row = cot_db_fetcharray($sql))
	{
		$pagecount[$row['page_cat']] = $row['COUNT(*)'];
	}

	$totalitems = cot_db_rowcount($db_structure);
	$pagenav = cot_pagenav('admin', 'm=structure', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

	$sql = cot_db_query("SELECT * FROM $db_structure ORDER BY structure_path ASC, structure_code ASC LIMIT $d, ".$cfg['maxrowsperpage']);

	$ii = 0;
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('admin.structure.loop');
	/* ===== */
	while ($row = cot_db_fetcharray($sql))
	{
		$jj++;
		$structure_id = $row['structure_id'];
		$structure_code = $row['structure_code'];
		$structure_path = $row['structure_path'];
		$structure_title = $row['structure_title'];
		$structure_desc = $row['structure_desc'];
		$structure_icon = $row['structure_icon'];
		$structure_group = $row['structure_group'];
		$pathfieldlen = (mb_strpos($structure_path, '.') == 0) ? 3 : 9;
		$pathfieldimg = (mb_strpos($structure_path, '.') == 0) ? '' : '<img src="system/admin/img/join2.png" alt="" /> ';
		$pagecount[$structure_code] = (!$pagecount[$structure_code]) ? '0' : $pagecount[$structure_code];
		$raw = explode('.', $row['structure_order']);
		$sort = $raw[0];
		$way = $raw[1];

		reset($options_sort);
		reset($options_way);

		if (empty($row['structure_tpl']))
		{
			$structure_tpl_sym = '-';
		}
		elseif ($row['structure_tpl'] == 'same_as_parent')
		{
			$structure_tpl_sym = '*';
		}
		else
		{
			$structure_tpl_sym = '+';
		}

		$dozvil = ($pagecount[$structure_code] > 0) ? false : true;

		$t->assign(array(
			'ADMIN_STRUCTURE_UPDATE_DEL_URL' => cot_url('admin', 'm=structure&a=delete&id='.$structure_id.'&c='.$row['structure_code'].'&d='.$d.'&'.cot_xg()),
			'ADMIN_STRUCTURE_ID' => $structure_id,
			'ADMIN_STRUCTURE_CODE' => cot_inputbox('text', 's['.$structure_id.'][rcode]', $structure_code, 'size="8" maxlength="255"'),
			'ADMIN_STRUCTURE_PATHFIELDIMG' => $pathfieldimg,
			'ADMIN_STRUCTURE_PATH' => cot_inputbox('text', 's['.$structure_id.'][rpath]', $structure_path, 'size="'.$pathfieldlen.'" maxlength="24"'),
			'ADMIN_STRUCTURE_TPL_SYM' => $structure_tpl_sym,
			'ADMIN_STRUCTURE_TITLE' => cot_inputbox('text', 's['.$structure_id.'][rtitle]', $structure_title, 'size="24" maxlength="100"'),
			'ADMIN_STRUCTURE_GROUP' => cot_checkbox($structure_group, 's['.$structure_id.'][rgroup]'),
			'ADMIN_STRUCTURE_PAGECOUNT' => $pagecount[$structure_code],
			'ADMIN_STRUCTURE_JUMPTO_URL' => cot_url('list', 'c='.$structure_code),
			'ADMIN_STRUCTURE_RIGHTS_URL' => cot_url('admin', 'm=rightsbyitem&ic=page&io='.$structure_code),
			'ADMIN_STRUCTURE_OPTIONS_URL' => cot_url('admin', 'm=structure&n=options&id='.$structure_id.'&'.cot_xg()),
			'ADMIN_STRUCTURE_WAY' => cot_selectbox($way, 's['.$structure_id.'][rway]', array_keys($options_way), array_values($options_way), false, 'style="width:85px;"'),
			'ADMIN_STRUCTURE_ORDER' => cot_selectbox($sort, 's['.$structure_id.'][rorder]', array_keys($options_sort), array_values($options_sort), false, 'style="width:85px;"'),
			'ADMIN_STRUCTURE_ODDEVEN' => cot_build_oddeven($ii)
		));

		// Extra fields
		/* $extra_array = cot_build_extrafields('structure', 'ADMIN_STRUCTURE', $cot_extrafields['structure'], $row, false);
		$t->assign($extra_array);*/

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse('MAIN.DEFULT.ROW');

		$ii++;
	}

	reset($options_sort);
	reset($options_way);

	$t->assign(array(
		'ADMIN_STRUCTURE_UPDATE_FORM_URL' => cot_url('admin', 'm=structure&a=update&d='.$d),
		'ADMIN_STRUCTURE_PAGINATION_PREV' => $pagenav['prev'],
		'ADMIN_STRUCTURE_PAGNAV' => $pagenav['main'],
		'ADMIN_STRUCTURE_PAGINATION_NEXT' => $pagenav['next'],
		'ADMIN_STRUCTURE_TOTALITEMS' => $totalitems,
		'ADMIN_STRUCTURE_COUNTER_ROW' => $ii,
		'ADMIN_PAGE_STRUCTURE_RESYNCALL' => cot_url('admin', 'm=structure&a=resyncall&'.cot_xg().'&d='.$d),
		'ADMIN_STRUCTURE_URL_FORM_ADD' => cot_url('admin', 'm=structure&a=add'),
		'ADMIN_STRUCTURE_CODE' => cot_inputbox('text', 'ncode', '', 'size="16"'),
		'ADMIN_STRUCTURE_PATH' => cot_inputbox('text', 'npath', '', 'size="16" maxlength="16"'),
		'ADMIN_STRUCTURE_TITLE' => cot_inputbox('text', 'ntitle', '', 'size="64" maxlength="100"'),
		'ADMIN_STRUCTURE_DESC' => cot_inputbox('text', 'ndesc', '', 'size="64" maxlength="255"'),
		'ADMIN_STRUCTURE_ICON' => cot_inputbox('text', 'nicon', '', 'size="64" maxlength="128"'),
		'ADMIN_STRUCTURE_GROUP' => cot_checkbox(0, 'ngroup'),
		'ADMIN_STRUCTURE_WAY' => cot_selectbox('asc', 'nway', array_keys($options_way), array_values($options_way), false),
		'ADMIN_STRUCTURE_ORDER' => cot_selectbox('title', 'norder', array_keys($options_sort), array_values($options_sort), false),
		'ADMIN_STRUCTURE_RATINGS' => cot_radiobox(1, 'nallowratings', array(1, 0), array($L['Yes'], $L['No']))

	));

	// Extra fields
	foreach($cot_extrafields['structure'] as $i => $row2)
	{
		$uname = strtoupper($row['field_name']);
		$t->assign('ADMIN_STRUCTURE_'.$uname, cot_build_extrafields('structure',  $row2, '', true));
		$t->assign('ADMIN_STRUCTURE_'.$uname.'_TITLE', isset($L['structure_'.$row2['field_name'].'_title']) ?  $L['structure_'.$row2['field_name'].'_title'] : $row2['field_description']);

		// extra fields universal tags
		$t->assign('ADMIN_STRUCTURE_EXTRAFLD', cot_build_extrafields('structure',  $row2, '', true));
		$t->assign('ADMIN_STRUCTURE_EXTRAFLD_TITLE', isset($L['structure_'.$row2['field_name'].'_title']) ?  $L['structure_'.$row2['field_name'].'_title'] : $row2['field_description']);
		$t->parse('MAIN.DEFULT.EXTRAFLD');
	}

	$t->parse('MAIN.DEFULT');
}

$t->assign(array(
	'ADMIN_STRUCTURE_URL_CONFIG' => cot_url('admin', 'm=config&n=edit&o=core&p=structure'),
	'ADMIN_STRUCTURE_URL_EXTRAFIELDS' => cot_url('admin', 'm=extrafields&n=structure')
));

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.structure.tags') as $pl)
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