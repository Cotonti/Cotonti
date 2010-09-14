<?php
/**
 * Administration panel - Forums & categories
 *
 * @package forums
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

$t = new XTemplate(sed_skinfile('forums.admin.structure', 'module'));

$adminpath[] = array (sed_url('admin', 'm=forums'), $L['Forums']);
$adminpath[] = array (sed_url('admin', 'm=forums&s=structure'), $L['Structure']);
$adminhelp = $L['adm_help_forum_structure'];

/* === Hook === */
foreach (sed_getextplugins('admin.forums.structure.first') as $pl)
{
	include $pl;
}
/* ===== */

if($n == 'options')
{
	if($a == 'update')
	{
		$rpath = sed_import('rpath', 'P', 'TXT');
		$rtitle = sed_import('rtitle', 'P', 'TXT');
		$rtplmode = sed_import('rtplmode', 'P', 'INT');
		$rdesc = sed_import('rdesc', 'P', 'TXT');
		$ricon = sed_import('ricon', 'P', 'TXT');
		$rdefstate = sed_import('rdefstate', 'P', 'BOL');

		/* === Hook === */
		foreach (sed_getextplugins('admin.forums.structure.options.update') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if($rtplmode == 1)
		{
			$rtpl = '';
		}
		elseif($rtplmode == 3)
		{
			$rtpl = 'same_as_parent';
		}
		/*else
		{
			$rtpl = sed_import('rtplforced','P','ALP');
		}*/

		$sql = sed_sql_query("UPDATE $db_forum_structure SET
			fn_path='".sed_sql_prep($rpath)."',
			fn_tpl='".sed_sql_prep($rtpl)."',
			fn_title='".sed_sql_prep($rtitle)."',
			fn_desc='".sed_sql_prep($rdesc)."',
			fn_icon='".sed_sql_prep($ricon)."',
			fn_defstate='".$rdefstate."'
			WHERE fn_id='".$id."'");

		if ($cot_cache)
		{
			$cot_cache->db->remove('sed_forums_str', 'system');
			if ($cfg['cache_forums'])
			{
				$cot_cache->page->clear('forums');
			}
		}

		sed_message('Updated');

		sed_redirect(sed_url('admin', 'm=forums&s=structure&d='.$d.$additionsforurl, '', true));
	}

	$sql = sed_sql_query("SELECT * FROM $db_forum_structure WHERE fn_id='$id' LIMIT 1");
	sed_die(sed_sql_numrows($sql) == 0);

	$handle = opendir('./themes/'.$cfg['defaultskin'].'/');
	$allskinfiles = array();

	while($f = readdir($handle))
	{
		if(($f != '.') && ($f != '..') && mb_strtolower(mb_substr($f, mb_strrpos($f, '.') + 1, 4)) == 'tpl')
		{
			$allskinfiles[] = $f;
		}
	}
	closedir($handle);

	$allskinfiles = implode(',', $allskinfiles);

	$row = sed_sql_fetcharray($sql);

	$fn_id = $row['fn_id'];
	$fn_code = $row['fn_code'];
	$fn_path = $row['fn_path'];
	$fn_title = $row['fn_title'];
	$fn_desc = $row['fn_desc'];
	$fn_icon = $row['fn_icon'];
	$fn_defstate = $row['fn_defstate'];
	$selected = ($row['fn_defstate']) ? 1 : 0;

	if($row['fn_tpl'] == 'same_as_parent')
	{
		$fn_tpl_sym = '*';
		$check = '3';
	}
	else
	{
		$fn_tpl_sym = "-";
		$check = '1';
	}

	$adminpath[] = array(sed_url('admin', 'm=forums&s=structure&n=options&id='.$id), htmlspecialchars($fn_title));

	$t->assign(array(
		'ADMIN_FORUMS_STRUCTURE_OPTIONS_FORM_URL' => sed_url('admin', 'm=forums&s=structure&n=options&a=update&id='.$fn_id.'&d='.$d),
		'ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_CODE' => $fn_code,
		'ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_PATH' => sed_inputbox('text', 'rpath', $fn_path, 'size="16" maxlength="16"'),
		'ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_TITLE' => sed_inputbox('text', 'rtitle', $fn_title, 'size="64" maxlength="100"'),
		'ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_DESC' => sed_inputbox('text', 'rdesc', $fn_desc, 'size="64"'),
		'ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_ICON' => sed_inputbox('text', 'ricon', $fn_icon, 'size="64" maxlength="128"'),
		'ADMIN_FORUMS_STRUCTURE_OPTIONS_CHECK' => sed_radiobox($check, 'rtplmode', array(1, 3), array($L['adm_tpl_empty'], $L['adm_tpl_parent']), '', '<br />'),
		'ADMIN_FORUMS_STRUCTURE_OPTIONS_SELECT' => sed_selectbox($selected, 'rdefstate', array(0, 1), array($L['adm_defstate_0'], $L['adm_defstate_1']), false)
	));

	/* === Hook === */
	foreach (sed_getextplugins('admin.forums.structure.options') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.OPTIONS');
}
else
{
	if($a == 'update')
	{
		$s = sed_import('s', 'P', 'ARR');

		foreach($s as $i => $k)
		{
			$sql1 = sed_sql_query("UPDATE $db_forum_structure SET
				fn_path='".$s[$i]['rpath']."',
				fn_title='".$s[$i]['rtitle']."',
				fn_defstate='".$s[$i]['rdefstate']."'
				WHERE fn_id='".$i."'");
		}
		if ($cot_cache)
		{
			$cot_cache->db->remove('sed_forums_str', 'system');
			if ($cfg['cache_forums'])
			{
				$cot_cache->page->clear('forums');
			}
		}

		sed_message('Updated');
	}
	elseif($a == 'add')
	{
		$g = array('ncode', 'npath', 'ntitle', 'ndesc', 'nicon', 'ndefstate');
		foreach($g as $k => $x)
		{
			$$x = $_POST[$x];
		}

		if(!empty($ntitle) && !empty($ncode) && !empty($npath) && $ncode != 'all')
		{
			$sql = sed_sql_query("SELECT fn_code FROM $db_forum_structure WHERE fn_code='".sed_sql_prep($ncode)."' LIMIT 1");
			$ncode .= (sed_sql_numrows($sql)>0) ? "_".rand(100,999) : '';

			$sql = sed_sql_query("INSERT INTO $db_forum_structure (fn_code, fn_path, fn_title, fn_desc, fn_icon, fn_defstate) VALUES ('$ncode', '$npath', '$ntitle', '$ndesc', '$nicon', ".(int)$ndefstate.")");
		}

		if ($cot_cache)
		{
			$cot_cache->db->remove('sed_forums_str', 'system');
			if ($cfg['cache_forums'])
			{
				$cot_cache->page->clear('forums');
			}
		}

		sed_message('Added');
	}
	elseif($a == 'delete')
	{
		sed_check_xg();
		$sql = sed_sql_query("DELETE FROM $db_forum_structure WHERE fn_id='$id'");

		if ($cot_cache)
		{
			$cot_cache->db->remove('sed_forums_str', 'system');
			if ($cfg['cache_forums'])
			{
				$cot_cache->page->clear('forums');
			}
		}

		sed_message('Deleted');
	}

	$sql = sed_sql_query("SELECT DISTINCT(fs_category), COUNT(*) FROM $db_forum_sections WHERE 1 GROUP BY fs_category");

	while($row = sed_sql_fetcharray($sql))
	{
		$sectioncount[$row['fs_category']] = $row['COUNT(*)'];
	}

	$totalitems = sed_sql_rowcount($db_forum_structure);

	$pagenav = sed_pagenav('admin', 'm=forums&s=structure', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

	$sql = sed_sql_query("SELECT * FROM $db_forum_structure ORDER by fn_path ASC, fn_code ASC LIMIT $d, ".$cfg['maxrowsperpage']);

	$ii = 0;
	/* === Hook - Part1 : Set === */
	$extp = sed_getextplugins('admin.forums.structure.loop');
	/* ===== */
	while($row = sed_sql_fetcharray($sql))
	{
		$jj++;
		$fn_id = $row['fn_id'];
		$fn_code = $row['fn_code'];
		$fn_path = $row['fn_path'];
		$fn_title = $row['fn_title'];
		$fn_desc = $row['fn_desc'];
		$fn_icon = $row['fn_icon'];

		$pathfieldimg = (mb_strpos($fn_path, '.') == 0) ? false : true;
		$sectioncount[$fn_code] = (!$sectioncount[$fn_code]) ? "0" : $sectioncount[$fn_code];
		$del_url = ($sectioncount[$fn_code] > 0) ? false : true;
		$selected = ($row['fn_defstate']) ? 1 : 0;

		if(empty($row['fn_tpl']))
		{
			$fn_tpl_sym = '-';
		}
		elseif($row['fn_tpl'] == 'same_as_parent')
		{
			$fn_tpl_sym = '*';
		}
		else
		{
			$fn_tpl_sym = '+';
		}

		$t->assign(array(
			'FORUMS_STRUCTURE_ROW_DEL_URL' => sed_url('admin', 'm=forums&s=structure&a=delete&id='.$fn_id.'&c='.$row['fn_code'].'&d='.$d.'&'.sed_xg()),
			'FORUMS_STRUCTURE_ROW_FN_CODE' => $fn_code,
			'FORUMS_STRUCTURE_ROW_FN_PATH' => sed_inputbox('text', 's['.$fn_id.'][rpath]', $fn_path, 'size="'.((mb_strpos($fn_path, '.') == 0) ? 3 : 9).'" maxlength="24"'),
			'FORUMS_STRUCTURE_ROW_SELECT' => sed_selectbox($selected,  's['.$fn_id.'][rdefstate]', array(0, 1), array($L['adm_defstate_0'], $L['adm_defstate_1']), false),
			'FORUMS_STRUCTURE_ROW_FN_TPL_SYM' => $fn_tpl_sym,
			'FORUMS_STRUCTURE_ROW_FN_TITLE' => sed_inputbox('text', 's['.$fn_id.'][rtitle]', $fn_title, 'size="24" maxlength="100"'),
			'FORUMS_STRUCTURE_ROW_SECTIONCOUNT' => $sectioncount[$fn_code],
			'FORUMS_STRUCTURE_ROW_JUMPTO_URL' => sed_url('forums', 'c='.$fn_code),
			'FORUMS_STRUCTURE_ROW_OPTIONS_URL' => sed_url('admin', 'm=forums&s=structure&n=options&id='.$fn_id.'&d='.$d.'&'.sed_xg())
		));
		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */
		$t->parse('MAIN.DEFULT.ROW');

		$ii++;
	}

	$t->assign(array(
		'ADMIN_FORUMS_STRUCTURE_FORM_URL' => sed_url('admin', 'm=forums&s=structure&a=update&d='.$d),
		'ADMIN_FORUMS_STRUCTURE_CODE' => sed_inputbox('text', 'ncode', '', 'size="16" maxlength="16"'),
		'ADMIN_FORUMS_STRUCTURE_PATH' => sed_inputbox('text', 'npath', '', 'size="16" maxlength="16"'),
		'ADMIN_FORUMS_STRUCTURE_TITLE' => sed_inputbox('text', 'ntitle', '', 'size="64" maxlength="100"'),
		'ADMIN_FORUMS_STRUCTURE_DESC' => sed_inputbox('text', 'ndesc', '', 'size="64"'),
		'ADMIN_FORUMS_STRUCTURE_ICON' => sed_inputbox('text', 'nicon', '', 'size="64" maxlength="128"'),
		'ADMIN_FORUMS_STRUCTURE_SELECT' => sed_selectbox('1', 'ndefstate', array(0, 1), array($L['adm_defstate_0'], $L['adm_defstate_1']), false),
		'ADMIN_FORUMS_STRUCTURE_PAGINATION_PREV' => $pagenav['prev'],
		'ADMIN_FORUMS_STRUCTURE_PAGNAV' => $pagenav['main'],
		'ADMIN_FORUMS_STRUCTURE_PAGINATION_NEXT' => $pagenav['next'],
		'ADMIN_FORUMS_STRUCTURE_TOTALITEMS' => $totalitems,
		'ADMIN_FORUMS_STRUCTURE_COUNTER_ROW' => $ii,
		'ADMIN_FORUMS_STRUCTURE_INC_URLFORMADD' => sed_url('admin', 'm=forums&s=structure&a=add')
	));
	$t->parse('MAIN.DEFULT');
}

/* === Hook === */
foreach (sed_getextplugins('admin.forums.structure.tags') as $pl)
{
	include $pl;
}
/* ===== */

?>