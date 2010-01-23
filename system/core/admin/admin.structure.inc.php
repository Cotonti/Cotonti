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

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.structure.inc', false, true));

$adminpath[] = array (sed_url('admin', 'm=structure'), $L['Categories']);
$adminhelp = $L['adm_help_structure'];

$id = sed_import('id', 'G', 'INT');
$c = sed_import('c', 'G', 'TXT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

/* === Hook === */
$extp = sed_getextplugins('admin.structure.first');
foreach ($extp as $pl)
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
	'comcount' => $L['Comments'],
	'file' => $L['adm_fileyesno'],
	'url' => $L['adm_fileurl'],
	'size' => $L['adm_filesize'],
	'filecount' => $L['adm_filecount']
);

// Extra fields pages
$extrafields = array();
$number_of_extrafields = 0;
$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='pages'");
while ($row = sed_sql_fetchassoc($fieldsres))
{
	$extrafields[$row['field_name']] = isset($L['page_'.$row['field_name'].'_title']) ? $L['page_'.$row['field_name'].'_title'] : $row['field_description'];
	$number_of_extrafields++;
}
$options_sort = ($options_sort + $extrafields);

$options_way = array(
	'asc' => $L['Ascending'],
	'desc' => $L['Descending']
);

// Extra fields structure
$extrafields = array();
$number_of_extrafields = 0;
$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='structure'");
while ($row = sed_sql_fetchassoc($fieldsres))
{
	$extrafields[] = $row;
	$number_of_extrafields++;
}

if ($n == 'options')
{
	if ($a == 'update')
	{
		$rcode = sed_import('rcode', 'P', 'TXT');
		$rpath = sed_import('rpath', 'P', 'TXT');
		$rtitle = sed_import('rtitle', 'P', 'TXT');
		$rtplmode = sed_import('rtplmode', 'P', 'INT');
		$rdesc = sed_import('rdesc', 'P', 'TXT');
		$ricon = sed_import('ricon', 'P', 'TXT');
		$rgroup = sed_import('rgroup', 'P', 'BOL');
		$rgroup = ($rgroup) ? 1 : 0;
		$rorder = sed_import('rorder', 'P', 'ALP');
		$rway = sed_import('rway', 'P', 'ALP');
		$rallowcomments = sed_import('rallowcomments', 'P', 'BOL');
		$rallowratings = sed_import('rallowratings', 'P', 'BOL');

		// Extra fields
		if ($number_of_extrafields > 0)
		{
			foreach ($extrafields as $row)
			{
				$import = sed_import('rstructure'.$row['field_name'], 'P', 'HTM');
				if ($row['field_type'] == "checkbox")
				{
					if ($import == "0" OR $import == "on")
					{
						$import = 1;
					}
					else
					{
						$import = 0;
					}
				}
				$rstructureextrafields[] = $import;
			}
		}

		$sqql = sed_sql_query("SELECT structure_code FROM $db_structure WHERE structure_id='".$id."' ");
		$roww = sed_sql_fetcharray($sqql);

		/* === Hook === */
		$extp = sed_getextplugins('admin.structure.options.update');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($roww['structure_code'] != $rcode)
		{

			$sql = sed_sql_query("UPDATE $db_structure SET structure_code='".sed_sql_prep($rcode)."' WHERE structure_code='".sed_sql_prep($roww['structure_code'])."' ");
			$sql = sed_sql_query("DELETE FROM $db_cache WHERE c_name='".sed_sql_prep($roww['structure_code'])."' ");
			$sql = sed_sql_query("UPDATE $db_auth SET auth_option='".sed_sql_prep($rcode)."' WHERE auth_code='page' AND auth_option='".sed_sql_prep($roww['structure_code'])."' ");
			$sql = sed_sql_query("UPDATE $db_pages SET page_cat='".sed_sql_prep($rcode)."' WHERE page_cat='".sed_sql_prep($roww['structure_code'])."' ");

			sed_auth_reorder();
			sed_auth_clear('all');
			$cot_cache->db_unset('sed_cat', 'system');
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
			$rtpl = sed_import('rtplforced', 'P', 'ALP');
		}

		$sqltxt = "UPDATE $db_structure
			SET structure_path='".sed_sql_prep($rpath)."',
				structure_tpl='".sed_sql_prep($rtpl)."',
				structure_title='".sed_sql_prep($rtitle)."',
				structure_desc='".sed_sql_prep($rdesc)."',
				structure_icon='".sed_sql_prep($ricon)."',
				structure_group='".$rgroup."',
				structure_order='".sed_sql_prep($rorder.".".$rway)."',";

		// Extra fields
		if ($number_of_extrafields > 0)
		{
			foreach ($extrafields as $i => $fildname)
			{
				if (!is_null($rstructureextrafields[$i]))
				{
					$sqltxt .= "structure_".sed_sql_prep($fildname['field_name'])."='".sed_sql_prep($rstructureextrafields[$i])."',";
				}
			}
		}

		$sqltxt .= "
				structure_comments='".$rallowcomments."',
				structure_ratings='".$rallowratings."'
			WHERE structure_id='".$id."'";
		$sql = sed_sql_query($sqltxt);

		$cot_cache->db_unset('sed_cat', 'system');

		sed_redirect(sed_url('admin', 'm=structure&d='.$d.$additionsforurl, '', true));
	}
	elseif ($a == 'resync')
	{
		sed_check_xg();

		$adminwarnings = sed_structure_resync($id) ? $L['Resynced'] : $L['Error'];
	}

	$sql = sed_sql_query("SELECT * FROM $db_structure WHERE structure_id='$id' LIMIT 1");
	sed_die(sed_sql_numrows($sql) == 0);

	$handle = opendir('./skins/'.$cfg['defaultskin'].'/');
	$allskinfiles = array();

	while ($f = readdir($handle))
	{
		if (($f != ".") && ($f != "..") && mb_strtolower(mb_substr($f, mb_strrpos($f, '.') + 1, 4)) == 'tpl')
		{
			$allskinfiles[] = $f;
		}
	}
	closedir($handle);

	$allskinfiles = implode(',', $allskinfiles);

	$row = sed_sql_fetcharray($sql);

	$structure_id = $row['structure_id'];
	$structure_code = $row['structure_code'];
	$structure_path = $row['structure_path'];
	$structure_title = $row['structure_title'];
	$structure_desc = $row['structure_desc'];
	$structure_icon = $row['structure_icon'];
	$structure_group = $row['structure_group'];
	$structure_comments = $row['structure_comments'];
	$structure_ratings = $row['structure_ratings'];
	$raw = explode('.', $row['structure_order']);
	$sort = $raw[0];
	$way = $raw[1];

	reset($options_sort);
	reset($options_way);

	while (list($i, $x) = each($options_sort))
	{
		$t->assign(array(
			"ADMIN_STRUCTURE_CATORDER_SELECT_SORT_SELECTED" => ($i == $sort) ? ' selected="selected"' : '',
			"ADMIN_STRUCTURE_CATORDER_SELECT_SORT_NAME" => $x,
			"ADMIN_STRUCTURE_CATORDER_SELECT_SORT_VALUE" => $i
		));
		$t->parse("STRUCTURE.OPTIONS.STRUCTURE_CATORDER_SELECT_SORT");
	}
	while (list($i, $x) = each($options_way))
	{
		$t->assign(array(
			"ADMIN_STRUCTURE_CATORDER_SELECT_WAY_SELECTED" => ($i == $way) ? ' selected="selected"' : '',
			"ADMIN_STRUCTURE_CATORDER_SELECT_WAY_NAME" => $x,
			"ADMIN_STRUCTURE_CATORDER_SELECT_WAY_VALUE" => $i
		));
		$t->parse("STRUCTURE.OPTIONS.STRUCTURE_CATORDER_SELECT_WAY");
	}

	if (empty($row['structure_tpl']))
	{

		$check1 = " checked=\"checked\"";
	}
	elseif ($row['structure_tpl'] == 'same_as_parent')
	{
		$structure_tpl_sym = "*";
		$check3 = " checked=\"checked\"";
	}
	else
	{
		$structure_tpl_sym = "+";
		$check2 = " checked=\"checked\"";
	}

	$adminpath[] = array (sed_url('admin', "m=structure&n=options&id=".$id), htmlspecialchars($structure_title));

	foreach ($sed_cat as $i => $x)
	{
		if ($i != 'all')
		{
			$t->assign(array(
				"ADMIN_STRUCTURE_OPTION_SELECTED" => ($i == $row['structure_tpl']) ? " selected=\"selected\"" : '',
				"ADMIN_STRUCTURE_OPTION_I" => $i,
				"ADMIN_STRUCTURE_OPTION_TPATH" => $x['tpath']
			));
			$t->parse("STRUCTURE.OPTIONS.SELECT");
		}
	}

	$t->assign(array(
		"ADMIN_STRUCTURE_UPDATE_FORM_URL" => sed_url('admin', "m=structure&n=options&a=update&id=".$structure_id."&d=".$d."&".sed_xg()),
		"ADMIN_STRUCTURE_CODE" => $structure_code,
		"ADMIN_STRUCTURE_PATH" => $structure_path,
		"ADMIN_STRUCTURE_TITLE" => $structure_title,
		"ADMIN_STRUCTURE_DESC" => $structure_desc,
		"ADMIN_STRUCTURE_ICON" => $structure_icon,
		"ADMIN_STRUCTURE_CHECK" => ($structure_pages || $structure_group) ? " checked=\"checked\"" : '',
		"ADMIN_STRUCTURE_CHECK1" => $check1,
		"ADMIN_STRUCTURE_CHECK2" => $check2,
		"ADMIN_STRUCTURE_CHECK3" => $check3,
		"ADMIN_STRUCTURE_RESYNC" => sed_url('admin', "m=structure&n=options&a=resync&id=".$structure_id."&".sed_xg()),
	));

	// Extra fields
	if ($number_of_extrafields > 0)
	{
		$extra_array = sed_build_extrafields('structure', 'ADMIN_STRUCTURE', $extrafields, $row);
	}
	$t->assign($extra_array);

	/* === Hook === */
	$extp = sed_getextplugins('admin.structure.options.tags');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse("STRUCTURE.OPTIONS");
}
else
{
	if ($a == 'update')
	{
		$s = sed_import('s', 'P', 'ARR');

		foreach ($s as $i => $k)
		{
			$s[$i]['rgroup'] = (isset($s[$i]['rgroup'])) ? 1 : 0;
			// Extra fields
			if ($number_of_extrafields > 0)
			{
				foreach ($extrafields as $row)
				{
					$import = $s[$i]['rstructure'.$row['field_name']];
					if ($row['field_type'] == "checkbox")
					{
						if ($import == "0" OR $import == "on")
						{
							$import = 1;
						}
						else
						{
							$import = 0;
						}
					}
					$rstructureextrafields[] = $import;
				}
			}

			$sqql = sed_sql_query("SELECT structure_code FROM $db_structure WHERE structure_id='".$i."' ");
			$roww = sed_sql_fetcharray($sqql);

			/* === Hook === */
			$extp = sed_getextplugins('admin.structure.update');
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			if ($roww['structure_code'] != $s[$i]['rcode'])
			{
				$sql = sed_sql_query("UPDATE $db_structure SET structure_code='".sed_sql_prep($s[$i]['rcode'])."' WHERE structure_code='".sed_sql_prep($roww['structure_code'])."' ");
				$sql = sed_sql_query("DELETE FROM $db_cache WHERE c_name='".sed_sql_prep($roww['structure_code'])."' ");
				$sql = sed_sql_query("UPDATE $db_auth SET auth_option='".sed_sql_prep($s[$i]['rcode'])."' WHERE auth_code='page' AND auth_option='".sed_sql_prep($roww['structure_code'])."' ");
				$sql = sed_sql_query("UPDATE $db_pages SET page_cat='".sed_sql_prep($s[$i]['rcode'])."' WHERE page_cat='".sed_sql_prep($roww['structure_code'])."' ");

				sed_auth_reorder();
				sed_auth_clear('all');
				$cot_cache->db_unset('sed_cat', 'system');
			}

			$sql1text = "UPDATE $db_structure
				SET ";

			// Extra fields
			if ($number_of_extrafields > 0)
			{
				foreach ($extrafields as $j => $fildname)
				{
					if (!is_null($rstructureextrafields[$j]))
					{
						$sql1text .= "structure_".sed_sql_prep($fildname['field_name'])."='".sed_sql_prep($rstructureextrafields[$j])."',";
					}
				}
			}

			$sql1text .= "
					structure_path='".sed_sql_prep($s[$i]['rpath'])."',
					structure_title='".sed_sql_prep($s[$i]['rtitle'])."',
					structure_order='".sed_sql_prep($s[$i]['rorder'].".".$s[$i]['rway'])."',
					structure_group='".$s[$i]['rgroup']."'
				WHERE structure_id='".$i."'";
			$sql1 = sed_sql_query($sql1text);
		}

		sed_auth_clear('all');
		$cot_cache->db_unset('sed_cat', 'system');

		$adminwarnings = $L['Updated'];
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
		if ($number_of_extrafields > 0)
		{
			foreach ($extrafields as $row)
			{
				$import = sed_import('newstructure'.$row['field_name'], 'P', 'HTM');
				if ($row['field_type'] == "checkbox")
				{
					if ($import == "0" OR $import == "on")
					{
						$import = 1;
					}
					else
					{
						$import = 0;
					}
				}
				$rstructureextrafields[$row['field_name']] = $import;
			}
		}

		/* === Hook === */
		$extp = sed_getextplugins('admin.structure.add');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$adminwarnings = (sed_structure_newcat($ncode, $npath, $ntitle, $ndesc, $nicon, $ngroup, $norder, $nway, $rstructureextrafields)) ? $L['Added'] : $L['Error'];
	}
	elseif ($a == 'delete')
	{
		sed_check_xg();

		/* === Hook === */
		$extp = sed_getextplugins('admin.structure.delete');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		sed_structure_delcat($id, $c);

		$adminwarnings = $L['Deleted'];
	}
	elseif ($a == 'resyncall')
	{
		sed_check_xg();

		$adminwarnings = sed_structure_resyncall() ? $L['Resynced'] : $L['Error'];
	}

	$sql = sed_sql_query("SELECT DISTINCT(page_cat), COUNT(*) FROM $db_pages WHERE 1 GROUP BY page_cat");

	while ($row = sed_sql_fetcharray($sql))
	{
		$pagecount[$row['page_cat']] = $row['COUNT(*)'];
	}

	$totalitems = sed_sql_rowcount($db_structure);
	$pagenav = sed_pagenav('admin', 'm=structure', $d, $totalitems, $cfg['maxrowsperpage'], 'd', $cfg['jquery'] && $cfg['turnajax']);

	$sql = sed_sql_query("SELECT * FROM $db_structure ORDER BY structure_path ASC, structure_code ASC LIMIT $d, ".$cfg['maxrowsperpage']);

	$ii = 0;
	/* === Hook - Part1 : Set === */
	$extp = sed_getextplugins('admin.structure.loop');
	/* ===== */
	while ($row = sed_sql_fetcharray($sql))
	{
		$jj++;
		$structure_id = $row['structure_id'];
		$structure_code = $row['structure_code'];
		$structure_path = $row['structure_path'];
		$structure_title = $row['structure_title'];
		$structure_desc = $row['structure_desc'];
		$structure_icon = $row['structure_icon'];
		$structure_group = $row['structure_group'];
		$pathfieldlen = (mb_strpos($structure_path, ".") == 0) ? 3 : 9;
		$pathfieldimg = (mb_strpos($structure_path, ".") == 0) ? '' : "<img src=\"images/admin/join2.gif\" alt=\"\" /> ";
		$pagecount[$structure_code] = (!$pagecount[$structure_code]) ? "0" : $pagecount[$structure_code];
		$raw = explode('.', $row['structure_order']);
		$sort = $raw[0];
		$way = $raw[1];

		reset($options_sort);
		reset($options_way);

		while (list($i, $x) = each($options_sort))
		{
			$t->assign(array(
				"ADMIN_STRUCTURE_CATORDER_SELECT_SORT_SELECTED" => ($i == $sort) ? ' selected="selected"' : '',
				"ADMIN_STRUCTURE_CATORDER_SELECT_SORT_NAME" => $x,
				"ADMIN_STRUCTURE_CATORDER_SELECT_SORT_VALUE" => $i
			));
			$t->parse("STRUCTURE.DEFULT.ROW.STRUCTURE_CATORDER_SELECT_SORT");
		}
		while (list($i, $x) = each($options_way))
		{
			$t->assign(array(
				"ADMIN_STRUCTURE_CATORDER_SELECT_WAY_SELECTED" => ($i == $way) ? ' selected="selected"' : '',
				"ADMIN_STRUCTURE_CATORDER_SELECT_WAY_NAME" => $x,
				"ADMIN_STRUCTURE_CATORDER_SELECT_WAY_VALUE" => $i
			));
			$t->parse("STRUCTURE.DEFULT.ROW.STRUCTURE_CATORDER_SELECT_WAY");
		}

		if (empty($row['structure_tpl']))
		{
			$structure_tpl_sym = "-";
		}
		elseif ($row['structure_tpl'] == 'same_as_parent')
		{
			$structure_tpl_sym = "*";
		}
		else
		{
			$structure_tpl_sym = "+";
		}

		$dozvil = ($pagecount[$structure_code] > 0) ? false : true;

		$t->assign(array(
			"ADMIN_STRUCTURE_UPDATE_DEL_URL" => sed_url('admin', "m=structure&a=delete&id=".$structure_id."&c=".$row['structure_code']."&d=".$d."&".sed_xg()),
			"ADMIN_STRUCTURE_ID" => $structure_id,
			"ADMIN_STRUCTURE_CODE" => $structure_code,
			"ADMIN_STRUCTURE_PATHFIELDIMG" => $pathfieldimg,
			"ADMIN_STRUCTURE_PATH" => $structure_path,
			"ADMIN_STRUCTURE_PATHFIELDLEN" => $pathfieldlen,
			"ADMIN_STRUCTURE_TPL_SYM" => $structure_tpl_sym,
			"ADMIN_STRUCTURE_TITLE" => $structure_title,
			"ADMIN_STRUCTURE_CHECKED" => ($structure_group) ? " checked=\"checked\"" : '',
			"ADMIN_STRUCTURE_PAGECOUNT" => $pagecount[$structure_code],
			"ADMIN_STRUCTURE_JUMPTO_URL" => sed_url('list', "c=".$structure_code),
			"ADMIN_STRUCTURE_RIGHTS_URL" => sed_url('admin', "m=rightsbyitem&ic=page&io=".$structure_code),
			"ADMIN_STRUCTURE_OPTIONS_URL" => sed_url('admin', "m=structure&n=options&id=".$structure_id."&".sed_xg()),
			"ADMIN_STRUCTURE_ODDEVEN" => sed_build_oddeven($ii)
		));

		// Extra fields
		/*if ($number_of_extrafields > 0)
		{
			$extra_array = sed_build_extrafields('structure', 'ADMIN_STRUCTURE', $extrafields, $row, false);
		}
		$t->assign($extra_array);*/

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse("STRUCTURE.DEFULT.ROW");

		$ii++;
	}

	reset($options_sort);
	reset($options_way);

	while (list($i, $x) = each($options_sort))
	{
		$t->assign(array(
			"ADMIN_STRUCTURE_CATORDER_SORT_SELECTED" => ($i == 'title') ? ' selected="selected"' : '',
			"ADMIN_STRUCTURE_CATORDER_SORT_NAME" => $x,
			"ADMIN_STRUCTURE_CATORDER_SORT_VALUE" => $i
		));
		$t->parse("STRUCTURE.DEFULT.STRUCTURE_CATORDER_SORT");
	}
	while (list($i, $x) = each($options_way))
	{
		$t->assign(array(
			"ADMIN_STRUCTURE_CATORDER_WAY_SELECTED" => ($i == 'asc') ? ' selected="selected"' : '',
			"ADMIN_STRUCTURE_CATORDER_WAY_NAME" => $x,
			"ADMIN_STRUCTURE_CATORDER_WAY_VALUE" => $i
		));
		$t->parse("STRUCTURE.DEFULT.STRUCTURE_CATORDER_WAY");
	}

	$t->assign(array(
		"ADMIN_STRUCTURE_UPDATE_FORM_URL" => sed_url('admin', "m=structure&a=update&d=".$d),
		"ADMIN_STRUCTURE_PAGINATION_PREV" => $pagenav['prev'],
		"ADMIN_STRUCTURE_PAGNAV" => $pagenav['main'],
		"ADMIN_STRUCTURE_PAGINATION_NEXT" => $pagenav['next'],
		"ADMIN_STRUCTURE_TOTALITEMS" => $totalitems,
		"ADMIN_STRUCTURE_COUNTER_ROW" => $ii,
		"ADMIN_STRUCTURE_URL_FORM_ADD" => sed_url('admin', "m=structure&a=add"),
		"ADMIN_PAGE_STRUCTURE_RESYNCALL" => sed_url('admin', "m=structure&a=resyncall&".sed_xg()."&d=".$d)
	));

	// Extra fields
	if ($number_of_extrafields > 0)
	{
		$extra_array = sed_build_extrafields('structure', 'ADMIN_STRUCTURE_FORMADD', $extrafields, '', true);
	}
	$t->assign($extra_array);

	$t->parse("STRUCTURE.DEFULT");
}

$lincif_conf = sed_auth('admin', 'a', 'A');
$is_adminwarnings = isset($adminwarnings);

$t->assign(array(
	"ADMIN_STRUCTURE_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_STRUCTURE_URL_CONFIG" => sed_url('admin', "m=config&n=edit&o=core&p=structure"),
	"ADMIN_STRUCTURE_URL_EXTRAFIELDS" => sed_url('admin', 'm=structure&s=extrafields')
));

/* === Hook  === */
$extp = sed_getextplugins('admin.structure.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('STRUCTURE');
if (SED_AJAX)
{
	$t->out('STRUCTURE');
}
else
{
	$adminmain = $t->text('STRUCTURE');
}

?>