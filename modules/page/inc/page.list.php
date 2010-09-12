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

defined('SED_CODE') or die('Wrong URL');

sed_dieifdisabled($cfg['disable_page']);

// Environment setup
define('SED_LIST', TRUE);
$location = 'List';

$s = sed_import('s', 'G', 'ALP'); // order field name without "page_"
$w = sed_import('w', 'G', 'ALP', 4); // order way (asc, desc)
$c = sed_import('c', 'G', 'TXT'); // cat code
$o = sed_import('o', 'G', 'ALP', 16); // sort field name without "page_"
$p = sed_import('p', 'G', 'ALP', 16); // sort way (asc, desc)
$d = sed_import('d', 'G', 'INT'); //page number for pages list
$dc = sed_import('dc', 'G', 'INT');// page number for cats list

if ($c == 'all' || $c == 'system')
{
	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
	sed_block($usr['isadmin']);
}
elseif ($c == 'unvalidated')
{
	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
	sed_block($usr['auth_write']);
}
elseif (!isset($sed_cat[$c]))
{
	sed_die(true);
}
else
{
	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', $c);
	sed_block($usr['auth_read']);
}

/* === Hook === */
foreach (sed_getextplugins('page.list.first') as $pl)
{
	include $pl;
}
/* ===== */

$cat = $sed_cat[$c];

if (empty($s))
{
	$s = $cat['order'];
	$w = $cat['way'];
}
$s = empty($s) ? 'title' : $s;
$w = empty($w) ? 'asc' : $w;
$d = empty($d) ? 0 : (int) $d;
$dc = empty($dc) ? 0 : (int) $dc;

$sys['sublocation'] = $cat['title'];
sed_online_update();

$cfg['maxrowsperpage'] = ($c == 'all' || $c == 'system') ? $cfg['maxrowsperpage'] * 2 : $cfg['maxrowsperpage'];

$join_ratings_columns = ($cfg['disable_ratings']) ? '' : ", r.rating_average";
$join_ratings_condition = ($cfg['disable_ratings']) ? '' : "LEFT JOIN $db_ratings as r ON r.rating_code=CONCAT('p',p.page_id)";

$c = (empty($sed_cat[$c]['title'])) ? 'all' : $c;
sed_die((empty($sed_cat[$c]['title'])) && !$usr['isadmin']);

$where = "(page_state=0 OR page_state=2) ";
if ($c == 'unvalidated')
{
	$where = "page_state = 1 AND page_ownerid = " . $usr['id'];
	$cat['title'] = $L['pag_validation'];
	$cat['desc'] = $L['pag_validation_desc'];
}
elseif ($c != 'all')
{
	$catwhere = " AND page_cat='$c'";
}
if (!empty($o) && !empty($p) && $p != 'password')
{
	$where .= " AND page_$o='$p'";
}
$list_url_path = array('c' =>$c, 's' => $s, 'w' => $w, 'o' => $o, 'p' => $p);
$list_url = sed_url('list', $list_url_path);

/* === Hook === */
foreach (sed_getextplugins('page.list.query') as $pl)
{
	include $pl;
}
/* ===== */

if(empty($sql_string))
{
	$sql_count = "SELECT COUNT(*) FROM $db_pages WHERE $where $catwhere";
	$sql_string = "SELECT p.*, u.* ".$join_ratings_columns."
		FROM $db_pages as p ".$join_ratings_condition."
		LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
		WHERE $where $catwhere
		ORDER BY page_$s $w LIMIT $d, ".$cfg['maxrowsperpage'];
}
$sql = sed_sql_query($sql_count);
$totallines = sed_sql_result($sql, 0, 0);
$sql = sed_sql_query($sql_string);

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
	$catpath = sed_build_catpath($c);
}

$totalpages = ceil($totallines / $cfg['maxrowsperpage']);
$currentpage= ceil($d / $cfg['maxrowsperpage']) + 1;

$submitnewpage = ($usr['auth_write'] && $c != 'all' && $c != 'unvalidated') ? sed_rc('page_link_submitnewpage', array('sub_url' => sed_url('page', 'm=add&c='.$c))) : ''; // TODO - to resorses OR move to tpl with logic {if}

$pagenav = sed_pagenav('list', $list_url_path + array('dc' => $dc), $d, $totallines, $cfg['maxrowsperpage']);

list($list_ratings, $list_ratings_display) = sed_build_ratings($item_code, sed_url('list', 'c=' . $c), $cat['ratings']);

$title_params = array(
	'TITLE' => $cat['title']
);
$out['desc'] = htmlspecialchars(strip_tags($cat['desc']));
$out['subtitle'] = sed_title('title_list', $title_params);

$_SESSION['cat'] = $c;

/* === Hook === */
foreach (sed_getextplugins('page.list.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

if ($cat['group'])
{
	$mskin = sed_skinfile(array('page' ,'list', 'group', $cat['tpl']));
}
else
{
	$mskin = sed_skinfile(array('page' ,'list', $cat['tpl']));
}

$t = new XTemplate($mskin);

$t->assign(array(
	"LIST_PAGETITLE" => $catpath,
	"LIST_CATEGORY" => htmlspecialchars($cat['title']),
	"LIST_CAT" => $c,
	"LIST_CAT_RSS" => sed_url('rss', "c=$c"),
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
foreach ($sed_extrafields['structure'] as $row_c)
{
	$uname = strtoupper($row_c['field_name']);
	$t->assign('LIST_CAT_'.$uname.'_TITLE', isset($L['structure_'.$row_c['field_name'].'_title']) ? $L['structure_'.$row_c['field_name'].'_title'] : $row_c['field_description']);
	$t->assign('LIST_CAT_'.$uname, sed_build_extrafields_data('structure', $row_c['field_type'], $row_c['field_name'], $cat[$row_c['field_name']]));
}

$arrows = array();
$params = array('title','key','date','author','owner','count','filecount');
foreach($params as $val)
{
    $arrows[$val]['asc']  = $sed_img_down;
    $arrows[$val]['desc'] = $sed_img_up;
}
$arrows[$s][$w]  = $R['icon_vert_active'][$w];

if (!$cat['group'])
{
	$t->assign(array(
		"LIST_TOP_CURRENTPAGE" => $currentpage,
		"LIST_TOP_TOTALLINES" => $totallines,
		"LIST_TOP_MAXPERPAGE" => $cfg['maxrowsperpage'],
		"LIST_TOP_TOTALPAGES" => $totalpages,
		"LIST_TOP_TITLE" => sed_rc('list_link_title', array('sed_img_down'=>$arrows['title']['asc'],'sed_img_up'=>$arrows['title']['desc'],'list_link_url_down' => sed_url('list', array('s' => 'title', 'w' => 'asc') + $list_url_path), 'list_link_url_up' => sed_url('list', array('s' => 'title', 'w' => 'desc') + $list_url_path))), // TODO - to resorses
		"LIST_TOP_KEY" => sed_rc('list_link_key', array('sed_img_down'=>$arrows['key']['asc'],'sed_img_up'=>$arrows['key']['desc'],'list_link_key_url_down' => sed_url('list', array('s' => 'key', 'w' => 'asc') + $list_url_path), 'list_link_key_url_up' => sed_url('list', array('s' => 'key', 'w' => 'desc') + $list_url_path))), // TODO - to resorses
		"LIST_TOP_DATE" => sed_rc('list_link_date', array('sed_img_down'=>$arrows['date']['asc'],'sed_img_up'=>$arrows['date']['desc'],'list_link_date_url_down' => sed_url('list', array('s' => 'date', 'w' => 'asc') + $list_url_path), 'list_link_date_url_up' => sed_url('list', array('s' => 'date', 'w' => 'desc') + $list_url_path))), // TODO - to resorses
		"LIST_TOP_AUTHOR" => sed_rc('list_link_author', array('sed_img_down'=>$arrows['author']['asc'],'sed_img_up'=>$arrows['author']['desc'],'list_link_author_url_down' => sed_url('list', array('s' => 'author', 'w' => 'asc') + $list_url_path), 'list_link_author_url_up' => sed_url('list', array('s' => 'author', 'w' => 'desc') + $list_url_path))), // TODO - to resorses
		"LIST_TOP_OWNER" => sed_rc('list_link_owner', array('sed_img_down'=>$arrows['owner']['asc'],'sed_img_up'=>$arrows['owner']['desc'],'list_link_owner_url_down' => sed_url('list', array('s' => 'ownerid', 'w' => 'asc') + $list_url_path), 'list_link_owner_url_up' => sed_url('list', array('s' => 'ownerid', 'w' => 'desc') + $list_url_path))), // TODO - to resorses
		"LIST_TOP_COUNT" => sed_rc('list_link_count', array('sed_img_down'=>$arrows['count']['asc'],'sed_img_up'=>$arrows['count']['desc'],'list_link_count_url_down' => sed_url('list', array('s' => 'count', 'w' => 'asc') + $list_url_path), 'list_link_count_url_up' => sed_url('list', array('s' => 'count', 'w' => 'desc') + $list_url_path))), // TODO - to resorses
		"LIST_TOP_FILECOUNT" => sed_rc('list_link_filecount', array('sed_img_down'=>$arrows['filecount']['asc'],'sed_img_up'=>$arrows['filecount']['desc'],'list_link_filecount_url_down' => sed_url('list', array('s' => 'filecount', 'w' => 'acs') + $list_url_path), 'list_link_filecount_url_up' => sed_url('list', array('s' => 'filecount', 'w' => 'desc') + $list_url_path))) // TODO - to resorses
	));


	// Extra fields for pages
	foreach ($sed_extrafields['pages'] as $row_p)
	{
		$uname = strtoupper($row_p['field_name']);
	    $arrows[$row_p['field_name']]['asc']  = $sed_img_down;
		$arrows[$row_p['field_name']]['desc'] = $sed_img_up;
	    $arrows[$s][$w]  = $R['icon_vert_active'][$w];
		isset($L['page_'.$row_p['field_name'].'_title']) ? $extratitle = $L['page_'.$row_p['field_name'].'_title'] : $extratitle = $row_p['field_description'];
		$t->assign('LIST_TOP_'.$uname, sed_rc('list_link_field_name', array('sed_img_down'=>$arrows[$row_p['field_name']]['asc'],'sed_img_up'=>$arrows[$row_p['field_name']]['desc'],'list_link_url_down' => sed_url('list',  array('s' => $row['field_name'], 'w' => 'asc') + $list_url_path), 'list_link_url_up' => sed_url('list', array('s' => $row['field_name'], 'w' => 'desc') + $list_url_path)))); // TODO - to resorses
	}
}
$ii = 0;
$jj = 1;
$mm = 0;
$kk = 0;
$mtch = $cat['path'].".";
$mtchlen = mb_strlen($mtch);
$mtchlvl = mb_substr_count($mtch, ".");

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('page.list.rowcat.loop');
/* ===== */
while (list($i, $x) = each($sed_cat))
{
	if (mb_substr($x['path'], 0, $mtchlen) == $mtch && mb_substr_count($x['path'], ".") == $mtchlvl && $mm < $dc)
	{
		$mm++;
		$ii++;
	}
	elseif (mb_substr($x['path'], 0, $mtchlen) == $mtch && mb_substr_count($x['path'], ".") == $mtchlvl && $kk < $cfg['maxlistsperpage'])
	{
		$sql4 = sed_sql_query("SELECT SUM(structure_pagecount) FROM $db_structure
			WHERE structure_path LIKE '".$sed_cat[$i]['rpath']."%' ");
		$sub_count = sed_sql_result($sql4, 0, "SUM(structure_pagecount)");

		$t->assign(array(
			"LIST_ROWCAT_URL" => sed_url('list', 'c='.$i),
			"LIST_ROWCAT_TITLE" => $x['title'],
			"LIST_ROWCAT_DESC" => $x['desc'],
			"LIST_ROWCAT_ICON" => $x['icon'],
			"LIST_ROWCAT_COUNT" => $sub_count,
			"LIST_ROWCAT_ODDEVEN" => sed_build_oddeven($kk),
			"LIST_ROWCAT_NUM" => $kk
		));

		// Extra fields for structure
		foreach ($sed_extrafields['structure'] as $row_c)
		{
			$uname = strtoupper($row_c['field_name']);
			$t->assign('LIST_ROWCAT_'.$uname.'_TITLE', isset($L['structure_'.$row_c['field_name'].'_title']) ?  $L['structure_'.$row_c['field_name'].'_title'] : $row_c['field_description']);
			$t->assign('LIST_ROWCAT_'.$uname, sed_build_extrafields_data('structure', $row_c['field_type'], $row_c['field_name'], $x[$row_c['field_name']]));
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
	elseif (mb_substr($x['path'], 0, $mtchlen) == $mtch && mb_substr_count($x['path'], ".") == $mtchlvl)
	{
		$ii++;
	}
}

$totalitems = $ii + $kk;
$pagenav = sed_pagenav('list', $list_url_path + array('d' => $d), $dc, $totalitems, $cfg['maxlistsperpage'], 'dc');

$t->assign(array(
	"LISTCAT_PAGEPREV" => $pagenav['prev'],
	"LISTCAT_PAGENEXT" => $pagenav['next'],
	"LISTCAT_PAGNAV" => $pagenav['main']
));

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('page.list.loop');
/* ===== */
while ($pag = sed_sql_fetcharray($sql) and ($jj <= $cfg['maxrowsperpage']))
{
	$jj++;
	$pag['page_desc'] = htmlspecialchars($pag['page_desc']);
	$page_urlp = empty($pag['page_alias']) ? 'id='.$pag['page_id'] : 'al='.$pag['page_alias'];
	$pag['page_pageurl'] = sed_url('page', $page_urlp);

	if (!empty($pag['page_url']) && $pag['page_file'])
	{
		$dotpos = mb_strrpos($pag['page_url'], ".") + 1;
		$type = mb_strtolower(mb_substr($pag['page_url'], $dotpos, 5));
		$pag['page_fileicon'] = sed_rc('page_icon_file_path');
		if (!file_exists($pag['page_fileicon']))
		{
			$pag['page_fileicon'] = sed_rc('page_icon_file_default');
		}
		$pag['page_fileicon'] = sed_rc('page_icon_file', array('icon' => $pag['page_fileicon']));
	}
	else
	{
		$pag['page_fileicon'] = '';
	}

	$pag['admin'] = $usr['isadmin'] ? sed_rc('list_link_row_admin', array('unvalidate_url' => sed_url('admin', "m=page&a=unvalidate&id=".$pag['page_id']."&".sed_xg()),'edit_url' => sed_url('page', "m=edit&id=".$pag['page_id']."&r=list"))) : '';

	list($list_ratings, $list_ratings_display) = sed_build_ratings('p'.$pag['page_id'], sed_url('page', 'id='.$pag['page_id']), $ratings);

	$t->assign(array(
		"LIST_ROW_URL" => $pag['page_pageurl'],
		"LIST_ROW_ID" => $pag['page_id'],
		"LIST_ROW_ALIAS" => $pag['page_alias'],
		"LIST_ROW_CAT" => $pag['page_cat'],
		"LIST_ROW_KEY" => htmlspecialchars($pag['page_key']),
		"LIST_ROW_TITLE" => htmlspecialchars($pag['page_title']),
		"LIST_ROW_DESC" => $pag['page_desc'],
		"LIST_ROW_DESC_OR_TEXT" => sed_cutpost($pag['page_text'], 200, false),
		"LIST_ROW_AUTHOR" => htmlspecialchars($pag['page_author']),
		"LIST_ROW_OWNER" => sed_build_user($pag['page_ownerid'], htmlspecialchars($pag['user_name'])),
		"LIST_ROW_DATE" => @date($cfg['formatyearmonthday'], $pag['page_date'] + $usr['timezone'] * 3600),
		"LIST_ROW_FILEURL" => empty($pag['page_url']) ? '' : sed_url('page', 'id='.$pag['page_id'].'&a=dl'),
		"LIST_ROW_SIZE" => $pag['page_size'],
		"LIST_ROW_COUNT" => $pag['page_count'],
		"LIST_ROW_FILEICON" => $pag['page_fileicon'],
		"LIST_ROW_FILECOUNT" => $pag['page_filecount'],
		"LIST_ROW_JUMP" => sed_url('page', $page_urlp.'&a=dl'),
		"LIST_ROW_RATINGS" => $list_ratings,
		"LIST_ROW_ADMIN" => $pag['admin'],
		"LIST_ROW_ODDEVEN" => sed_build_oddeven($jj),
		"LIST_ROW_NUM" => $jj
	));
	$t->assign(sed_generate_usertags($pag, "LIST_ROW_OWNER_"));

	// Adding LIST_ROW_TEXT tag
	switch ($pag['page_type'])
	{
		case 1:
			$t->assign("LIST_ROW_TEXT", $pag['page_text']);
		break;

		case 2:
			if ($cfg['allowphp_pages'] && $cfg['allowphp_override'])
			{
				ob_start();
				eval($pag['page_text']);
				$t->assign("LIST_ROW_TEXT", ob_get_clean());
			}
			else
			{
				$t->assign("LIST_ROW_TEXT", "The PHP mode is disabled for pages.<br />Please see the administration panel, then \"Configuration\", then \"Parsers\"."); // TODO - i18n
			}
		break;

		default:

			if ($cfg['parser_cache'])
			{
				if (empty($pag['page_html']) && !empty($pag['page_text']))
				{
					$pag['page_html'] = sed_parse(htmlspecialchars($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
					sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($pag['page_html'])."' WHERE page_id = " . $pag['page_id']);
				}
				$readmore = mb_strpos($pag['page_html'], "<!--more-->");
				if ($readmore > 0)
				{
					$pag['page_html'] = mb_substr($pag['page_html'], 0, $readmore);
				    $pag['page_html'] .= sed_rc('list_link_page_html', array('page_url'=> $pag['page_pageurl'])); // TODO - to resorses

				}
				$html = $cfg['parsebbcodepages'] ? sed_post_parse($pag['page_html']) : htmlspecialchars($pag['page_text']);
				$t->assign('LIST_ROW_TEXT', $html);
			}
			else
			{
				$readmore = mb_strpos($pag['page_text'], "[more]");
				$text = sed_parse(htmlspecialchars($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
				if ($readmore > 0)
				{
					$pag['page_text'] = mb_substr($pag['page_text'], 0, $readmore);
					$pag['page_text'] .= sed_rc('list_link_page_text', array('page_url'=> $pag['page_pageurl'])); // TODO - to resorses
				}
				$text = sed_post_parse($text, 'pages');
				$t->assign('LIST_ROW_TEXT', $text);
			}
		break;
	}

	// Extra fields for pages
	foreach ($sed_extrafields['pages'] as $row_p)
	{
		$uname = strtoupper($row_p['field_name']);
		$t->assign('LIST_ROW_'.$uname.'_TITLE', isset($L['page_'.$row_p['field_name'].'_title']) ?  $L['page_'.$row_p['field_name'].'_title'] : $row_p['field_description']);
		$t->assign('LIST_ROW_'.$uname, sed_build_extrafields_data('page', $row_p['field_type'], $row_p['field_name'], $pag['page_'.$row_p['field_name']]));
	}

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse("MAIN.LIST_ROW");
}

/* === Hook === */
foreach (sed_getextplugins('page.list.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

if ($cot_cache && $usr['id'] === 0 && $cfg['cache_page'])
{
	$cot_cache->page->write();
}

?>