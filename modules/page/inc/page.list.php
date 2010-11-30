<?php
/**
 * Page list
 *
 * @package page
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Environment setup
define('COT_LIST', TRUE);
$env['location'] = 'list';

$s = cot_import('s', 'G', 'ALP'); // order field name without "page_"
$w = cot_import('w', 'G', 'ALP', 4); // order way (asc, desc)
$c = cot_import('c', 'G', 'TXT'); // cat code
$o = cot_import('ord', 'G', 'ALP', 16); // sort field name without "page_"
$p = cot_import('p', 'G', 'ALP', 16); // sort way (asc, desc)
$d = cot_import('d', 'G', 'INT'); //page number for pages list
$dc = cot_import('dc', 'G', 'INT');// page number for cats list

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

$cfg['page']['maxrowsperpage'] = ($c == 'all' || $c == 'system') ? $cfg['page']['maxrowsperpage'] * 2 : $cfg['page']['maxrowsperpage'];

$join_columns = ($cfg['disable_ratings']) ? '' : ", r.rating_average";
$join_condition = ($cfg['disable_ratings']) ? '' : "LEFT JOIN $db_ratings as r ON r.rating_code=CONCAT('p',p.page_id)";

$c = (empty($cat['title'])) ? 'all' : $c;
cot_die((empty($cat['title'])) && !$usr['isadmin']);

$where['state'] = "(page_state=0 OR page_state=2)";
if ($c == 'unvalidated')
{
	$where['state'] = "page_state = 1";
	$where['ownerid'] = "page_ownerid = " . $usr['id'];
	$cat['title'] = $L['page_validation'];
	$cat['desc'] = $L['page_validation_desc'];
}
elseif ($c != 'all')
{
	$where['cat'] = "page_cat='$c'";
}
if (!empty($o) && !empty($p) && $p != 'password')
{
	$where['filter'] .= "page_$o='$p'";
}
if (!$usr['isadmin'])
{
	$where['date'] = "page_date <= ".(int)$sys['now_offset'];
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

if(empty($sql_string))
{
	$sql_count = "SELECT COUNT(*) FROM $db_pages as p ".$join_condition." WHERE ".implode(" AND ", $where);
	$sql_string = "SELECT p.*, u.* ".$join_columns."
		FROM $db_pages as p ".$join_condition."
		LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
		WHERE ".implode(" AND ", $where)."
		ORDER BY page_$s $w LIMIT $d, ".$cfg['page']['maxrowsperpage'];
}
$sql = $db->query($sql_count);
$totallines = $sql->fetchColumn();
$sql = $db->query($sql_string);

/*
$incl = "datas/content/list.$c.txt";
if (@file_exists($incl))
{
	$fd = @fopen ($incl, "r");
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
	$catpath = cot_build_catpath('page', $c);
}

$totalpages = ceil($totallines / $cfg['page']['maxrowsperpage']);
$currentpage= ceil($d / $cfg['page']['maxrowsperpage']) + 1;

$submitnewpage = ($usr['auth_write'] && $c != 'all' && $c != 'unvalidated') ? cot_rc('page_submitnewpage', array('sub_url' => cot_url('page', 'm=add&c='.$c))) : ''; // TODO - to resorses OR move to tpl with logic {if}

$pagenav = cot_pagenav('list', $list_url_path + array('dc' => $dc), $d, $totallines, $cfg['page']['maxrowsperpage']);

list($list_ratings, $list_ratings_display) = cot_build_ratings($item_code, cot_url('page', 'c=' . $c), $cat['ratings']);

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
	"LIST_PAGETITLE" => $catpath,
	"LIST_CATEGORY" => htmlspecialchars($cat['title']),
	"LIST_CAT" => $c,
	"LIST_CAT_RSS" => cot_url('rss', "c=$c"),
	"LIST_CATTITLE" => $cat['title'],
	"LIST_CATPATH" => $catpath,
	"LIST_CATDESC" => $cat['desc'],
	"LIST_CATICON" => $cat['icon'],
	"LIST_RATINGS" => $list_ratings,
	"LIST_RATINGS_DISPLAY" => $list_ratings_display,
	"LIST_EXTRATEXT" => $extratext,
	"LIST_SUBMITNEWPAGE" => $submitnewpage,
	"LIST_TOP_PAGINATION" => $pagenav['main'],
	"LIST_TOP_PAGEPREV" => $pagenav['prev'],
	"LIST_TOP_PAGENEXT" => $pagenav['next']
));

// Extra fields for structure
foreach ($cot_extrafields['structure'] as $row_c)
{
	$uname = strtoupper($row_c['field_name']);
	$t->assign('LIST_CAT_'.$uname.'_TITLE', isset($L['structure_'.$row_c['field_name'].'_title']) ? $L['structure_'.$row_c['field_name'].'_title'] : $row_c['field_description']);
	$t->assign('LIST_CAT_'.$uname, cot_build_extrafields_data('structure', $row_c, $cat[$row_c['field_name']]));
}

$arrows = array();
$params = array('title','key','date','author','owner','count','filecount');
foreach($params as $val)
{
    $arrows[$val]['asc']  = $R['icon_down'];
    $arrows[$val]['desc'] = $R['icon_up'];
}
$arrows[$s][$w]  = $R['icon_vert_active'][$w];

$t->assign(array(
	"LIST_TOP_CURRENTPAGE" => $currentpage,
	"LIST_TOP_TOTALLINES" => $totallines,
	"LIST_TOP_MAXPERPAGE" => $cfg['page']['maxrowsperpage'],
	"LIST_TOP_TOTALPAGES" => $totalpages,
	"LIST_TOP_TITLE" => cot_rc('list_link_title', array('cot_img_down'=>$arrows['title']['asc'],'cot_img_up'=>$arrows['title']['desc'],'list_link_url_down' => cot_url('page', array('s' => 'title', 'w' => 'asc') + $list_url_path), 'list_link_url_up' => cot_url('page', array('s' => 'title', 'w' => 'desc') + $list_url_path))),
	"LIST_TOP_KEY" => cot_rc('list_link_key', array('cot_img_down'=>$arrows['key']['asc'],'cot_img_up'=>$arrows['key']['desc'],'list_link_key_url_down' => cot_url('page', array('s' => 'key', 'w' => 'asc') + $list_url_path), 'list_link_key_url_up' => cot_url('page', array('s' => 'key', 'w' => 'desc') + $list_url_path))),
	"LIST_TOP_DATE" => cot_rc('list_link_date', array('cot_img_down'=>$arrows['date']['asc'],'cot_img_up'=>$arrows['date']['desc'],'list_link_date_url_down' => cot_url('page', array('s' => 'date', 'w' => 'asc') + $list_url_path), 'list_link_date_url_up' => cot_url('page', array('s' => 'date', 'w' => 'desc') + $list_url_path))),
	"LIST_TOP_AUTHOR" => cot_rc('list_link_author', array('cot_img_down'=>$arrows['author']['asc'],'cot_img_up'=>$arrows['author']['desc'],'list_link_author_url_down' => cot_url('page', array('s' => 'author', 'w' => 'asc') + $list_url_path), 'list_link_author_url_up' => cot_url('page', array('s' => 'author', 'w' => 'desc') + $list_url_path))),
	"LIST_TOP_OWNER" => cot_rc('list_link_owner', array('cot_img_down'=>$arrows['owner']['asc'],'cot_img_up'=>$arrows['owner']['desc'],'list_link_owner_url_down' => cot_url('page', array('s' => 'ownerid', 'w' => 'asc') + $list_url_path), 'list_link_owner_url_up' => cot_url('page', array('s' => 'ownerid', 'w' => 'desc') + $list_url_path))),
	"LIST_TOP_COUNT" => cot_rc('list_link_count', array('cot_img_down'=>$arrows['count']['asc'],'cot_img_up'=>$arrows['count']['desc'],'list_link_count_url_down' => cot_url('page', array('s' => 'count', 'w' => 'asc') + $list_url_path), 'list_link_count_url_up' => cot_url('page', array('s' => 'count', 'w' => 'desc') + $list_url_path))),
	"LIST_TOP_FILECOUNT" => cot_rc('list_link_filecount', array('cot_img_down'=>$arrows['filecount']['asc'],'cot_img_up'=>$arrows['filecount']['desc'],'list_link_filecount_url_down' => cot_url('page', array('s' => 'filecount', 'w' => 'acs') + $list_url_path), 'list_link_filecount_url_up' => cot_url('page', array('s' => 'filecount', 'w' => 'desc') + $list_url_path)))
));


// Extra fields for pages
foreach ($cot_extrafields['pages'] as $row_p)
{
	$uname = strtoupper($row_p['field_name']);
	$arrows[$row_p['field_name']]['asc']  = $R['icon_down'];
	$arrows[$row_p['field_name']]['desc'] = $R['icon_up'];
	$arrows[$s][$w]  = $R['icon_vert_active'][$w];
	isset($L['page_'.$row_p['field_name'].'_title']) ? $extratitle = $L['page_'.$row_p['field_name'].'_title'] : $extratitle = $row_p['field_description'];
	$t->assign('LIST_TOP_'.$uname, cot_rc('list_link_field_name', array('cot_img_down'=>$arrows[$row_p['field_name']]['asc'],'cot_img_up'=>$arrows[$row_p['field_name']]['desc'],'list_link_url_down' => cot_url('page',  array('s' => $row['field_name'], 'w' => 'asc') + $list_url_path), 'list_link_url_up' => cot_url('page', array('s' => $row['field_name'], 'w' => 'desc') + $list_url_path))));
}

$kk = 0;
$allsub = cot_structure_children('page', $c, false, false, true, false);
$subcat =  array_slice($allsub, $dc, $cfg['page']['maxlistsperpage']);

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('page.list.rowcat.loop');
/* ===== */
foreach ($subcat as $x)
{
	$sub_count = $db->query("SELECT SUM(structure_count) FROM $db_structure WHERE 
		structure_path LIKE '".$structure['page'][$x]['rpath'].".%' OR  structure_path = '".$structure['page'][$x]['rpath']."'")->fetchColumn();

	$t->assign(array(
		"LIST_ROWCAT_URL" => cot_url('page', 'c='.$x),
		"LIST_ROWCAT_TITLE" => $structure['page'][$x]['title'],
		"LIST_ROWCAT_DESC" => $structure['page'][$x]['desc'],
		"LIST_ROWCAT_ICON" => $structure['page'][$x]['icon'],
		"LIST_ROWCAT_COUNT" => $sub_count,
		"LIST_ROWCAT_ODDEVEN" => cot_build_oddeven($kk),
		"LIST_ROWCAT_NUM" => $kk
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

	$t->parse("MAIN.LIST_ROWCAT");
	$kk++;
}

$pagenav = cot_pagenav('list', $list_url_path + array('d' => $d), $dc, count($allsub), $cfg['page']['maxlistsperpage'], 'dc');

$t->assign(array(
	"LISTCAT_PAGEPREV" => $pagenav['prev'],
	"LISTCAT_PAGENEXT" => $pagenav['next'],
	"LISTCAT_PAGNAV" => $pagenav['main']
));
$jj = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('page.list.loop');
/* ===== */
while ($pag = $sql->fetch() and ($jj < $cfg['page']['maxrowsperpage']))
{
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
	$t->parse("MAIN.LIST_ROW");
	$jj++;
}

/* === Hook === */
foreach (cot_getextplugins('page.list.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

if ($cache && $usr['id'] === 0 && $cfg['cache_page'])
{
	$cache->page->write();
}

?>