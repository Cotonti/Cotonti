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
$o = cot_import('ord', 'G', 'ARR'); // filter field names without 'page_'
$p = cot_import('p', 'G', 'ARR'); // filter values
$maxrowsperpage = ($cfg['page'][$c]['maxrowsperpage']) ? $cfg['page'][$c]['maxrowsperpage'] : $cfg['page']['__default']['maxrowsperpage'];
list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for pages list
list($pgc, $dc, $dcurl) = cot_import_pagenav('dc', $maxrowsperpage);// page number for cats list

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
	cot_die_message(404, TRUE);
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
elseif (!$db->fieldExists($db_pages, "page_$s"))
{
	$s = 'title';
}
$s = empty($s) ? $cfg['page']['__default']['order'] : $s;
$w = empty($w) ? $cfg['page']['__default']['way'] : $w;


$sys['sublocation'] = $cat['title'];

$cfg['page']['maxrowsperpage'] = ($c == 'all' || $c == 'system' || $c == 'unvalidated') ? 
	$cfg['page']['__default']['maxrowsperpage'] : 
	$cfg['page'][$c]['maxrowsperpage'];
$cfg['page']['truncatetext'] = ($c == 'all' || $c == 'system' || $c == 'unvalidated') ? 
	$cfg['page']['__default']['truncatetext'] : 
	$cfg['page'][$c]['truncatetext'];

$where = array();
$params = array();

$where['state'] = '(page_state=0 OR page_state=2)';
if ($c == 'unvalidated')
{
	$cat['tpl'] = 'unvalidated';
	$where['state'] = 'page_state != 0';
	$where['ownerid'] = 'page_ownerid = ' . $usr['id'];
	$cat['title'] = $L['page_validation'];
	$cat['desc'] = $L['page_validation_desc'];
	$s = 'state';
	$w = 'desc';
}
elseif ($c != 'all')
{
	$where['cat'] = 'page_cat=' . $db->quote($c);
}

$c = (empty($cat['title'])) ? 'all' : $c;
cot_die((empty($cat['title'])) && !$usr['isadmin']);

if ($o && $p)
{
	if (!is_array($o)) $o = array($o);
	if (!is_array($p)) $p = array($p);
	$filters = array_combine($o, $p);
	foreach ($filters as $key => $val)
	{
		$key = cot_import($key, 'D', 'ALP', 16);
		$val = cot_import($val, 'D', 'TXT', 16);
		if ($key && $val && $db->fieldExists($db_pages, "page_$key"))
		{
			$params[$key] = $val;
			$where['filter'][] = "page_$key = :$key";
		}
	}
	empty($where['filter']) || $where['filter'] = implode(' AND ', $where['filter']);
}
if (!$usr['isadmin'] && $c != 'unvalidated')
{
	$where['date'] = "page_begin <= {$sys['now']} AND (page_expire = 0 OR page_expire > {$sys['now']})";
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

$catpath = ($c == 'all' || $c == 'system' || $c == 'unvalidated') ? $cat['title'] : cot_breadcrumbs(cot_structure_buildpath('page', $c), $cfg['homebreadcrumb'], true);

/* === Hook === */
foreach (cot_getextplugins('page.list.query') as $pl)
{
	include $pl;
}
/* ===== */

if(empty($sql_page_string))
{
	$where = array_filter($where);
	$where = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';
	$sql_page_count = "SELECT COUNT(*) FROM $db_pages as p $join_condition $where";
	$sql_page_string = "SELECT p.*, u.* $join_columns
		FROM $db_pages as p $join_condition
		LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
		$where
		ORDER BY page_$s $w LIMIT $d, ".$cfg['page']['maxrowsperpage'];
}
$totallines = $db->query($sql_page_count, $params)->fetchColumn();
$sqllist = $db->query($sql_page_string, $params);

$pagenav = cot_pagenav('page', $list_url_path + array('dc' => $dcurl), $d, $totallines, $cfg['page']['maxrowsperpage']);

$out['desc'] = htmlspecialchars(strip_tags($cat['desc']));
$out['subtitle'] = $cat['title'];

// Building the canonical URL
$pageurl_params = array('c' => $c, 'ord' => $o, 'p' => $p);
if ($durl > 1)
{
	$pageurl_params['d'] = $durl;
}
if ($dcurl > 1)
{
	$pageurl_params['dc'] = $dcurl;
}
$out['canonical_uri'] = cot_url('page', $pageurl_params);

$_SESSION['cat'] = $c;

/* === Hook === */
foreach (cot_getextplugins('page.list.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('page', 'list', $cat['tpl']));
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
	'LIST_TOP_PAGENEXT' => $pagenav['next'],
	'LIST_TOP_CURRENTPAGE' => $pagenav['current'],
	'LIST_TOP_TOTALLINES' => $totallines,
	'LIST_TOP_MAXPERPAGE' => $cfg['page']['maxrowsperpage'],
	'LIST_TOP_TOTALPAGES' => $pagenav['total']	
));

if ($usr['auth_write'] && $c != 'all' && $c != 'unvalidated')
{
	$t->assign(array(
		'LIST_SUBMITNEWPAGE' => cot_rc('page_submitnewpage', array('sub_url' => cot_url('page', 'm=add&c='.$c))),
		'LIST_SUBMITNEWPAGE_URL' => cot_url('page', 'm=add&c='.$c)
	));
}

// Extra fields for structure
foreach ($cot_extrafields[$db_structure] as $row_c)
{
	$uname = strtoupper($row_c['field_name']);
	$t->assign(array(
		'LIST_CAT_'.$uname.'_TITLE' => isset($L['structure_'.$row_c['field_name'].'_title']) ?
			$L['structure_'.$row_c['field_name'].'_title'] : $row_c['field_description'],
		'LIST_CAT_'.$uname => cot_build_extrafields_data('structure', $row_c, $cat[$row_c['field_name']])
	));
}

$arrows = array();
foreach ($cot_extrafields[$db_pages] + array('title' => 'title', 'key' => 'key', 'date' => 'date', 'author' => 'author', 'owner' => 'owner', 'count' => 'count', 'filecount' => 'filecount') as $row_k => $row_p)
{
	$uname = strtoupper($row_k);
	$url_asc = cot_url('page',  array('s' => $row_k, 'w' => 'asc') + $list_url_path);
	$url_desc = cot_url('page', array('s' => $row_k, 'w' => 'desc') + $list_url_path);
	$arrows[$row_k]['asc']  = $R['icon_down'];
	$arrows[$row_k]['desc'] = $R['icon_up'];
	if ($s == $val)
	{
		$arrows[$s][$w] = $R['icon_vert_active'][$w];
	}
	if(in_array($row_k, array('title', 'key', 'date', 'author', 'owner', 'count', 'filecount')))
	{
		$t->assign(array(
		'LIST_TOP_'.$uname => cot_rc("list_link_$col", array(
			'cot_img_down' => $arrows[$col]['asc'], 'cot_img_up' => $arrows[$col]['desc'],
			'list_link_url_down' => $url_asc, 'list_link_url_up' => $url_desc
		))));
	}
	else
	{
		$extratitle = isset($L['page_'.$row_k.'_title']) ?	$L['page_'.$row_k.'_title'] : $row_p['field_description'];
		$t->assign(array(
			'LIST_TOP_'.$uname => cot_rc('list_link_field_name', array(
				'cot_img_down' => $arrows[$row_k]['asc'],
				'cot_img_up' => $arrows[$row_k]['desc'],
				'list_link_url_down' => $url_asc,
				'list_link_url_up' => $url_desc
		))));
	}	
	$t->assign(array(	
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
		'LIST_ROWCAT_URL' => cot_url('page', array('c' => $x, 'ord' => $o, 'p' => $p)),
		'LIST_ROWCAT_TITLE' => $structure['page'][$x]['title'],
		'LIST_ROWCAT_DESC' => $structure['page'][$x]['desc'],
		'LIST_ROWCAT_ICON' => $structure['page'][$x]['icon'],
		'LIST_ROWCAT_COUNT' => $sub_count,
		'LIST_ROWCAT_ODDEVEN' => cot_build_oddeven($kk),
		'LIST_ROWCAT_NUM' => $kk
	));

	// Extra fields for structure
	foreach ($cot_extrafields[$db_structure] as $row_c)
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

$pagenav_cat = cot_pagenav('page', $list_url_path + array('d' => $durl), $dc, count($allsub), $cfg['page']['maxlistsperpage'], 'dc');

$t->assign(array(
	'LISTCAT_PAGEPREV' => $pagenav_cat['prev'],
	'LISTCAT_PAGENEXT' => $pagenav_cat['next'],
	'LISTCAT_PAGNAV' => $pagenav_cat['main']
));
$jj = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('page.list.loop');
/* ===== */
foreach ($sqllist->fetchAll() as $pag)
{
	$jj++;
	$t->assign(cot_generate_pagetags($pag, 'LIST_ROW_', $cfg['page']['truncatetext'], $usr['isadmin']));
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
