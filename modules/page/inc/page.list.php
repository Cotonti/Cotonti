<?php
/**
 * Page list
 *
 * @package page
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Environment setup
define('COT_LIST', TRUE);
$env['location'] = 'list';

$s = cot_import('s', 'G', 'ALP'); // order field name without 'page_'
$w = cot_import('w', 'G', 'ALP', 4); // order way (asc, desc)
$c = cot_import('c', 'G', 'TXT'); // cat code
$o = cot_import('ord', 'G', 'ALP', 16); // sort field name without 'page_'
$p = cot_import('p', 'G', 'ALP', 16); // sort way (asc, desc)
$maxrowsperpage = ($cfg['page'][$c]['maxrowsperpage']) ? $cfg['page'][$c]['maxrowsperpage'] : $cfg['page']['__default']['maxrowsperpage'];
list($pg, $d) = cot_import_pagenav('d', $maxrowsperpage); //page number for pages list
list($pgc, $dc) = cot_import_pagenav('dc', $maxrowsperpage);// page number for cats list

if ($c == 'all' || $c == 'system')
{
	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
	cot_block($usr['isadmin']);
}
elseif ($c == 'unvalidated')
{
	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', 'any');
	cot_block($usr['auth_write']);
}
elseif (!isset($structure['page'][$c]))
{
	cot_die(true);
}
else
{
	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', $c);
	cot_block($usr['auth_read']);
}

/* === Hook === */
foreach (cot_getextplugins('page.list.first') as $pl)
{
	include $pl;
}
/* ===== */

$cat = &$structure['page'][$c];

if (empty($s))
{
	$s = $cfg['page'][$c]['order'];
	$w = $cfg['page'][$c]['way'];
}
$s = empty($s) ? 'title' : $s;
$w = empty($w) ? 'asc' : $w;
$d = empty($d) ? 0 : (int) $d;
$dc = empty($dc) ? 0 : (int) $dc;

$sys['sublocation'] = $cat['title'];

$cfg['page']['maxrowsperpage'] = ($c == 'all' || $c == 'system') ? $cfg['page']['__default']['maxrowsperpage'] : $cfg['page'][$c]['maxrowsperpage'];

$c = (empty($cat['title'])) ? 'all' : $c;
cot_die((empty($cat['title'])) && !$usr['isadmin']);

$where['state'] = '(page_state=0 OR page_state=2)';
if ($c == 'unvalidated')
{
	$where['state'] = 'page_state = 1';
	$where['ownerid'] = 'page_ownerid = ' . $usr['id'];
	$cat['title'] = $L['page_validation'];
	$cat['desc'] = $L['page_validation_desc'];
}
elseif ($c != 'all')
{
	$where['cat'] = 'page_cat=' . $db->quote($c);
}
if (!empty($o) && !empty($p) && $p != 'password')
{
	$where['filter'] .= "page_$o='$p'";
}
if (!$usr['isadmin'])
{
	$where['date'] = 'page_date <= '.(int)$sys['now_offset'];
}
$list_url_path = array('c' =>$c, 'ord' => $o, 'p' => $p);
if ($s != $cfg['page'][$c]['order'])
{
	$list_url_path['s'] = $s;
}
if ($w != $cfg['page'][$c]['way'])
{
	$list_url_path['w'] = $w;
}
$list_url = cot_url('page', $list_url_path);

/* === Hook === */
foreach (cot_getextplugins('page.list.query') as $pl)
{
	include $pl;
}
/* ===== */

if(empty($sql_page_string))
{
	$where = array_diff($where,array(''));
	$sql_page_count = "SELECT COUNT(*) FROM $db_pages as p $join_condition WHERE ".implode(' AND ', $where);
	$sql_page_string = "SELECT p.*, u.* $join_columns
		FROM $db_pages as p $join_condition
		LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
		WHERE ".implode(' AND ', $where)."
		ORDER BY page_$s $w LIMIT $d, ".$cfg['page']['maxrowsperpage'];
}
$totallines = $db->query($sql_page_count)->fetchColumn();
$sqllist = $db->query($sql_page_string);

/*
$incl = "datas/content/list.$c.txt";
if (@file_exists($incl))
{
	$fd = @fopen ($incl, 'r');
	$extratext = fread ($fd, filesize ($incl));
	fclose ($fd);
}
*/

if ($c == 'all' || $c == 'system' || $c == 'unvalidated')
{
	$catpath = $cat['title'];
}
else
{
	$catpath = cot_structure_buildpath('page', $c, true);
}

$totalpages = ceil($totallines / $cfg['page']['maxrowsperpage']);
$currentpage= ceil($d / $cfg['page']['maxrowsperpage']) + 1;

$pagenav = cot_pagenav('list', $list_url_path + array('dc' => $dc), $d, $totallines, $cfg['page']['maxrowsperpage']);

$title_params = array(
	'TITLE' => $cat['title']
);
$out['desc'] = htmlspecialchars(strip_tags($cat['desc']));
$out['subtitle'] = cot_title('title_list', $title_params);

$_SESSION['cat'] = $c;

/* === Hook === */
foreach (cot_getextplugins('page.list.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('page' ,'list', $cat['tpl']));
$t = new XTemplate($mskin);

$t->assign(array(
	'LIST_PAGETITLE' => $catpath,
	'LIST_CATEGORY' => htmlspecialchars($cat['title']),
	'LIST_CAT' => $c,
	'LIST_CAT_RSS' => cot_url('rss', "c=$c"),
	'LIST_CATTITLE' => $cat['title'],
	'LIST_CATPATH' => $catpath,
	'LIST_CATDESC' => $cat['desc'],
	'LIST_CATICON' => empty($cat['icon']) ? '' : cot_rc('img_structure_cat', array(
			'icon' => $cat['icon'],
			'title' => htmlspecialchars($cat['title']),
			'desc' => htmlspecialchars($cat['desc'])
		)),
	'LIST_EXTRATEXT' => $extratext,
	'LIST_TOP_PAGINATION' => $pagenav['main'],
	'LIST_TOP_PAGEPREV' => $pagenav['prev'],
	'LIST_TOP_PAGENEXT' => $pagenav['next']
));

if ($usr['auth_write'] && $c != 'all' && $c != 'unvalidated')
{
	$t->assign(array(
		'LIST_SUBMITNEWPAGE' => cot_rc('page_submitnewpage', array('sub_url' => cot_url('page', 'm=add&c='.$c))),
		'LIST_SUBMITNEWPAGE_URL' => cot_url('page', 'm=add&c='.$c)
	));
}

// Extra fields for structure
foreach ($cot_extrafields['structure'] as $row_c)
{
	$uname = strtoupper($row_c['field_name']);
	$t->assign(array(
		'LIST_CAT_'.$uname.'_TITLE' => isset($L['structure_'.$row_c['field_name'].'_title']) ?
			$L['structure_'.$row_c['field_name'].'_title'] : $row_c['field_description'],
		'LIST_CAT_'.$uname => cot_build_extrafields_data('structure', $row_c, $cat[$row_c['field_name']])
	));
}

$t->assign(array(
	'LIST_TOP_CURRENTPAGE' => $currentpage,
	'LIST_TOP_TOTALLINES' => $totallines,
	'LIST_TOP_MAXPERPAGE' => $cfg['page']['maxrowsperpage'],
	'LIST_TOP_TOTALPAGES' => $totalpages
));

$arrows = array();
foreach(array('title', 'key', 'date', 'author', 'owner', 'count', 'filecount') as $val)
{
    $arrows[$val]['asc']  = $R['icon_down'];
    $arrows[$val]['desc'] = $R['icon_up'];
	if ($s == $val)
	{
		$arrows[$s][$w] = $R['icon_vert_active'][$w];
	}
	$uname = strtoupper($col);
	$url_asc = cot_url('page', array('s' => $col, 'w' => 'asc') + $list_url_path);
	$url_desc = cot_url('page', array('s' => $col, 'w' => 'desc') + $list_url_path);
	$t->assign(array(
		'LIST_TOP_'.$uname => cot_rc("list_link_$col", array(
			'cot_img_down' => $arrows[$col]['asc'], 'cot_img_up' => $arrows[$col]['desc'],
			'list_link_url_down' => $url_asc, 'list_link_url_up' => $url_desc
		)),
		'LIST_TOP_'.$uname.'_URL_ASC' => $url_asc,
		'LIST_TOP_'.$uname.'_URL_DESC' => $url_desc
	));
}

// Extra fields for pages
foreach ($cot_extrafields['pages'] as $row_p)
{
	$uname = strtoupper($row_p['field_name']);
	$url_asc = cot_url('page',  array('s' => $row_p['field_name'], 'w' => 'asc') + $list_url_path);
	$url_desc = cot_url('page', array('s' => $row_p['field_name'], 'w' => 'desc') + $list_url_path);
	$arrows[$row_p['field_name']]['asc']  = $R['icon_down'];
	$arrows[$row_p['field_name']]['desc'] = $R['icon_up'];
	$arrows[$s][$w]  = $R['icon_vert_active'][$w];
	$extratitle = isset($L['page_'.$row_p['field_name'].'_title']) ?
		$L['page_'.$row_p['field_name'].'_title'] : $row_p['field_description'];
	$t->assign(array(
		'LIST_TOP_'.$uname => cot_rc('list_link_field_name', array(
			'cot_img_down' => $arrows[$row_p['field_name']]['asc'],
			'cot_img_up' => $arrows[$row_p['field_name']]['desc'],
			'list_link_url_down' => $url_asc,
			'list_link_url_up' => $url_desc
		)),
		'LIST_TOP_'.$uname.'_URL_ASC' => $url_asc,
		'LIST_TOP_'.$uname.'_URL_DESC' => $url_desc
	));
}

$kk = 0;
$allsub = cot_structure_children('page', $c, false, false, true, false);
$subcat = array_slice($allsub, $dc, $cfg['page']['maxlistsperpage']);

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('page.list.rowcat.loop');
/* ===== */
foreach ($subcat as $x)
{
	$kk++;
	$sub_count = $db->query("SELECT SUM(structure_count) FROM $db_structure
		WHERE structure_path LIKE '".$db->prep($structure['page'][$x]['rpath']).".%'
		OR structure_path = ".$db->quote($structure['page'][$x]['rpath']))->fetchColumn();

	$t->assign(array(
		'LIST_ROWCAT_URL' => cot_url('page', 'c='.$x),
		'LIST_ROWCAT_TITLE' => $structure['page'][$x]['title'],
		'LIST_ROWCAT_DESC' => $structure['page'][$x]['desc'],
		'LIST_ROWCAT_ICON' => $structure['page'][$x]['icon'],
		'LIST_ROWCAT_COUNT' => $sub_count,
		'LIST_ROWCAT_ODDEVEN' => cot_build_oddeven($kk),
		'LIST_ROWCAT_NUM' => $kk
	));

	// Extra fields for structure
	foreach ($cot_extrafields['structure'] as $row_c)
	{
		$uname = strtoupper($row_c['field_name']);
		$t->assign('LIST_ROWCAT_'.$uname.'_TITLE', isset($L['structure_'.$row_c['field_name'].'_title']) ?  $L['structure_'.$row_c['field_name'].'_title'] : $row_c['field_description']);
		$t->assign('LIST_ROWCAT_'.$uname, cot_build_extrafields_data('structure', $row_c, $structure['page'][$x][$row_c['field_name']]));
	}

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.LIST_ROWCAT');	
}

$pagenav = cot_pagenav('list', $list_url_path + array('d' => $d), $dc, count($allsub), $cfg['page']['maxlistsperpage'], 'dc');

$t->assign(array(
	'LISTCAT_PAGEPREV' => $pagenav['prev'],
	'LISTCAT_PAGENEXT' => $pagenav['next'],
	'LISTCAT_PAGNAV' => $pagenav['main']
));
$jj = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('page.list.loop');
/* ===== */
foreach ($sqllist->fetchAll() as $pag)
{
	$jj++;
	$t->assign(cot_generate_pagetags($pag, 'LIST_ROW_', 0, $usr['isadmin']));
	$t->assign(array(
		'LIST_ROW_OWNER' => cot_build_user($pag['page_ownerid'], htmlspecialchars($pag['user_name'])),
		'LIST_ROW_ODDEVEN' => cot_build_oddeven($jj),
		'LIST_ROW_NUM' => $jj
	));
	$t->assign(cot_generate_usertags($pag, 'LIST_ROW_OWNER_'));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.LIST_ROW');
}

/* === Hook === */
foreach (cot_getextplugins('page.list.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';

if ($cache && $usr['id'] === 0 && $cfg['cache_page'])
{
	$cache->page->write();
}

?>
