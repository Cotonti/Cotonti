<?php
/**
 * Structure list
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

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
$extp = sed_getextplugins('list.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if (empty($s))
{
	$s = $sed_cat[$c]['order'];
	$w = $sed_cat[$c]['way'];
}
$s = empty($s) ? 'title' : $s;
$w = empty($w) ? 'asc' : $w;
$d = empty($d) ? 0 : (int) $d;
$dc = empty($dc) ? 0 : (int) $dc;

$cfg['maxrowsperpage'] = ($c== 'all' || $c == 'system') ? $cfg['maxrowsperpage'] * 2 : $cfg['maxrowsperpage'];

$item_code = 'list_'.$c;
$join_ratings_columns = ($cfg['disable_ratings']) ? '' : ", r.rating_average";
$join_ratings_condition = ($cfg['disable_ratings']) ? '' : "LEFT JOIN $db_ratings as r ON r.rating_code=CONCAT('p',p.page_id)";

$where = "(page_state=0 OR page_state=2) ";
if ($c == 'unvalidated')
{
	$where = "page_state = 1 AND page_ownerid = " . $usr['id'];
	$sed_cat[$c]['title'] = $L['pag_validation'];
	$sed_cat[$c]['desc'] = $L['pag_validation_desc'];
}
elseif ($c != 'all')
{
	$where .= " AND page_cat='$c'";
}
if (!empty($o) && !empty($p) && $p != 'password')
{
	$where .= " AND page_$o='$p'";
}
$list_url = sed_url('list', "c=$c&s=$s&w=$w&o=$o&p=$p");
/* === Hook === */
$extp = sed_getextplugins('list.query');
foreach ($extp as $pl)
{
	include $pl;
}/*
 *  ===== */
if(empty($sql_string))
{
	$sql_count = "SELECT COUNT(*) FROM $db_pages WHERE $where";
	$sql_string = "SELECT p.*, u.user_name ".$join_ratings_columns."
		FROM $db_pages as p ".$join_ratings_condition."
		LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
		WHERE $where
		ORDER BY page_$s $w LIMIT $d,".$cfg['maxrowsperpage'];
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
	$catpath = $sed_cat[$c]['title'];
}
else
{
	$catpath = sed_build_catpath($c, '<a href="%1$s">%2$s</a>');
}

$totalpages = ceil($totallines / $cfg['maxrowsperpage']);
$currentpage= ceil ($d / $cfg['maxrowsperpage'])+1;
$submitnewpage = ($usr['auth_write'] && $c != 'all' && $c != 'unvalidated') ? "<a href=\"".sed_url('page', 'm=add&c='.$c)."\">".$L['lis_submitnew'].'</a>' : '';

$pagination = sed_pagination($list_url, $d, $totallines, $cfg['maxrowsperpage']);
list($pageprev, $pagenext) = sed_pagination_pn($list_url, $d, $totallines, $cfg['maxrowsperpage'], TRUE);

list($list_comments, $list_comments_display) = sed_build_comments($item_code, sed_url('list', 'c=' . $c), $sed_cat[$c]['com']);
list($list_ratings, $list_ratings_display) = sed_build_ratings($item_code, sed_url('list', 'c=' . $c), $sed_cat[$c]['ratings']);

$sys['sublocation'] = $sed_cat[$c]['title'];
$title_tags[] = array('{TITLE}');
$title_tags[] = array('%1$s');
$title_data = array($sed_cat[$c]['title']);
$out['subtitle'] = sed_title('title_list', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('list.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

// Extra field - getting
$extrafields_p = array();
$extrafields_c = array();
$number_of_extrafields_p = 0;
$number_of_extrafields_c = 0;
$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='pages' OR field_location='structure'");
while ($row = sed_sql_fetchassoc($fieldsres))
{
	if ($row['field_location'] == 'pages')
	{
		$extrafields_p[] = $row;
		$number_of_extrafields_p++;
	}
	elseif ($row['field_location'] == 'structure')
	{
		$extrafields_c[] = $row;
		$number_of_extrafields_c++;
	}
}

if ($sed_cat[$c]['group'])
{
	$mskin = sed_skinfile(array('list', 'group', $sed_cat[$c]['tpl']));
}
else
{
	$mskin = sed_skinfile(array('list', $sed_cat[$c]['tpl']));
}

$t = new XTemplate($mskin);

$t -> assign(array(
	"LIST_PAGETITLE" => $catpath,
	"LIST_CATEGORY" => '<a href="'.sed_url('list', "c=$c").'">'.$sed_cat[$c]['title'].'</a>',
	"LIST_CAT" => $c,
	"LIST_CAT_RSS" => sed_url("rss", "c=$c", "", true),
	"LIST_CATTITLE" => $sed_cat[$c]['title'],
	"LIST_CATPATH" => $catpath,
	"LIST_CATDESC" => $sed_cat[$c]['desc'],
	"LIST_CATICON" => $sed_cat[$c]['icon'],
	"LIST_COMMENTS" => $list_comments,
	"LIST_COMMENTS_DISPLAY" => $list_comments_display,
	"LIST_RATINGS" => $list_ratings,
	"LIST_RATINGS_DISPLAY" => $list_ratings_display,
	"LIST_EXTRATEXT" => $extratext,
	"LIST_SUBMITNEWPAGE" => $submitnewpage,
	"LIST_TOP_PAGINATION" => $pagination,
	"LIST_TOP_PAGEPREV" => $pageprev,
	"LIST_TOP_PAGENEXT" => $pagenext
));

// Extra fields for structure
if ($number_of_extrafields_c > 0)
{
	foreach ($extrafields_c as $row_c)
	{
		$uname = strtoupper($row_c['field_name']);
		isset($L['structure_'.$row_c['field_name'].'_title']) ? $t -> assign('LIST_CAT_'.$uname.'_TITLE', $L['structure_'.$row_c['field_name'].'_title']) : $t -> assign('LIST_CAT_'.$uname.'_TITLE', $row_c['field_description']);
		$t -> assign('LIST_CAT_'.$uname, sed_build_extrafields_data('structure', $row_c['field_type'], $row_c['field_name'], $sed_cat[$c][$row_c['field_name']]));
	}
}

if (!$sed_cat[$c]['group'])
{
	$t -> assign(array(
		"LIST_TOP_CURRENTPAGE" => $currentpage,
		"LIST_TOP_TOTALLINES" => $totallines,
		"LIST_TOP_MAXPERPAGE" => $cfg['maxrowsperpage'],
		"LIST_TOP_TOTALPAGES" => $totalpages,
		"LIST_TOP_TITLE" => "<a href=\"".sed_url('list', "c=$c&s=title&w=asc&o=$o&p=$p")."\">$sed_img_down</a> <a href=\"".sed_url('list', "c=$c&s=title&w=desc&o=$o&p=$p")."\">$sed_img_up</a> ".$L['Title'],
		"LIST_TOP_KEY" => "<a href=\"".sed_url('list', "c=$c&s=key&w=asc&o=$o&p=$p")."\">$sed_img_down</a> <a href=\"".sed_url('list', "c=$c&s=key&w=desc&o=$o&p=$p")."\">$sed_img_up</a> ".$L['Key'],
		"LIST_TOP_DATE" => "<a href=\"".sed_url('list', "c=$c&s=date&w=asc&o=$o&p=$p")."\">$sed_img_down</a> <a href=\"".sed_url('list', "c=$c&s=date&w=desc&o=$o&p=$p")."\">$sed_img_up</a> ".$L['Date'],
		"LIST_TOP_AUTHOR" => "<a href=\"".sed_url('list', "c=$c&s=author&w=asc&o=$o&p=$p")."\">$sed_img_down</a> <a href=\"".sed_url('list', "c=$c&s=author&w=desc&o=$o&p=$p")."\">$sed_img_up</a> ".$L['Author'],
		"LIST_TOP_OWNER" => "<a href=\"".sed_url('list', "c=$c&s=ownerid&w=asc&o=$o&p=$p")."\">$sed_img_down</a> <a href=\"".sed_url('list', "c=$c&s=ownerid&w=desc&o=$o&p=$p")."\">$sed_img_up</a> ".$L['Owner'],
		"LIST_TOP_COUNT" => "<a href=\"".sed_url('list', "c=$c&s=count&w=asc&o=$o&p=$p")."\">$sed_img_down</a> <a href=\"".sed_url('list', "c=$c&s=count&w=desc&o=$o&p=$p")."\">$sed_img_up</a> ".$L['Hits'],
		"LIST_TOP_FILECOUNT" => "<a href=\"".sed_url('list', "c=$c&s=filecount&w=asc&o=$o&p=$p")."\">$sed_img_down</a> <a href=\"".sed_url('list', "c=$c&s=filecount&w=desc&o=$o&p=$p")."\">$sed_img_up</a> ".$L['Hits']
	));
}

// Extra fields for pages
if ($number_of_extrafields_p > 0)
{
	foreach ($extrafields_p as $row_p)
	{
		$uname = strtoupper($row_p['field_name']);
		isset($L['page_'.$row_p['field_name'].'_title']) ? $extratitle = $L['page_'.$row_p['field_name'].'_title'] : $extratitle = $row_p['field_description'];
		$t -> assign('LIST_TOP_'.$uname, "<a href=\"".sed_url('list', "c=$c&s=".$row_p['field_name']."&w=asc&o=$o&p=$p")."\">$sed_img_down</a><a href=\"".sed_url('list', "c=$c&s=".$row_p['field_name']."&w=desc&o=$o&p=$p")."\">$sed_img_up</a> $extratitle");
	}
}

$ii = 0;
$jj = 1;
$mm = 0;
$kk = 0;
$mtch = $sed_cat[$c]['path'].".";
$mtchlen = mb_strlen($mtch);
$mtchlvl = mb_substr_count($mtch,".");

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('list.rowcat.loop');
/* ===== */
while (list($i,$x) = each($sed_cat))
{
	if (mb_substr($x['path'], 0, $mtchlen) == $mtch && mb_substr_count($x['path'], ".")==$mtchlvl && $mm < $dc)
	{
		$mm++;
		$ii++;
	}
	elseif (mb_substr($x['path'], 0, $mtchlen) == $mtch && mb_substr_count($x['path'], ".") == $mtchlvl && $kk < $cfg['maxlistsperpage'])
	{
		$sql4 = sed_sql_query("SELECT SUM(structure_pagecount) FROM $db_structure
		WHERE structure_path LIKE '".$sed_cat[$i]['rpath']."%' ");
		$sub_count = sed_sql_result($sql4, 0, "SUM(structure_pagecount)");

		$t -> assign(array(
			"LIST_ROWCAT_URL" => sed_url('list', 'c='.$i),
			"LIST_ROWCAT_TITLE" => $x['title'],
			"LIST_ROWCAT_DESC" => $x['desc'],
			"LIST_ROWCAT_ICON" => $x['icon'],
			"LIST_ROWCAT_COUNT" => $sub_count,
			"LIST_ROWCAT_ODDEVEN" => sed_build_oddeven($kk),
			"LIST_ROWCAT_NUM" => $kk
		));

		// Extra fields for structure
		if ($number_of_extrafields_c > 0)
		{
			foreach ($extrafields_c as $row_c)
			{
				$uname = strtoupper($row_c['field_name']);
				isset($L['structure_'.$row_c['field_name'].'_title']) ? $t -> assign('LIST_ROWCAT_'.$uname.'_TITLE', $L['structure_'.$row_c['field_name'].'_title']) : $t -> assign('LIST_ROWCAT_'.$uname.'_TITLE', $row_c['field_description']);
				$t -> assign('LIST_ROWCAT_'.$uname, sed_build_extrafields_data('structure', $row_c['field_type'], $row_c['field_name'], $x[$row_c['field_name']]));
			}
		}

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t -> parse("MAIN.LIST_ROWCAT");
		$kk++;
	}
	elseif (mb_substr($x['path'], 0, $mtchlen) == $mtch && mb_substr_count($x['path'], ".") == $mtchlvl)
	{
		$ii++;
	}
}

$totalitems = $ii + $kk;
$pagnav = sed_pagination(sed_url('list', 'c='.$c), $dc, $totalitems, $cfg['maxlistsperpage'], $characters = "dc");
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('list', 'c='.$c), $dc, $totalitems, $cfg['maxlistsperpage'], TRUE, $characters = "dc");

$t -> assign(array(
	"LISTCAT_PAGEPREV" => $pagination_prev,
	"LISTCAT_PAGENEXT" => $pagination_next,
	"LISTCAT_PAGNAV" => $pagnav
));

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('list.loop');
/* ===== */
while ($pag = sed_sql_fetcharray($sql) and ($jj<=$cfg['maxrowsperpage']))
{
	$jj++;
	$pag['page_desc'] = htmlspecialchars($pag['page_desc']);
	$page_urlp = empty($pag['page_alias']) ? 'id='.$pag['page_id'] : 'al='.$pag['page_alias'];
	$pag['page_pageurl'] = sed_url('page', $page_urlp);

	if (!empty($pag['page_url']) && $pag['page_file'])
	{
		$dotpos = mb_strrpos($pag['page_url'],".") + 1;
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

	$item_code = 'p'.$pag['page_id'];
	$pag['page_comcount'] = (!$pag['page_comcount']) ? "0" : $pag['page_comcount'];
	$pag['page_comments'] = sed_rc_link(sed_url('page', $page_urlp, '#comments'), sed_rc('icon_comments_cnt', array('cnt' => $pag['page_comcount'])));
	$pag['admin'] = $usr['isadmin'] ? "<a href=\"".sed_url('admin', "m=page&a=unvalidate&id=".$pag['page_id']."&".sed_xg())."\">".$L['Putinvalidationqueue']."</a> &nbsp;<a href=\"".sed_url('page', "m=edit&id=".$pag['page_id']."&r=list")."\">".$L['Edit']."</a> " : '';
	list($list_ratings, $list_ratings_display) = sed_build_ratings('p'.$pag['page_id'], sed_url('page', 'id='.$pag['page_id']), $ratings);

	$t -> assign(array(
		"LIST_ROW_URL" => $pag['page_pageurl'],
		"LIST_ROW_ID" => $pag['page_id'],
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
		"LIST_ROW_COMMENTS" => $pag['page_comments'],
		"LIST_ROW_RATINGS" => $list_ratings,
		"LIST_ROW_ADMIN" => $pag['admin'],
		"LIST_ROW_ODDEVEN" => sed_build_oddeven($jj),
		"LIST_ROW_NUM" => $jj
	));

	// Adding LIST_ROW_TEXT tag
	switch($pag['page_type'])
	{
		case 1:
			$t -> assign("LIST_ROW_TEXT", $pag['page_text']);
			break;

		case 2:
			if ($cfg['allowphp_pages'] && $cfg['allowphp_override'])
			{
				ob_start();
				eval($pag['page_text']);
				$t -> assign("LIST_ROW_TEXT", ob_get_clean());
			}
			else
			{
				$t -> assign("LIST_ROW_TEXT", "The PHP mode is disabled for pages.<br />Please see the administration panel, then \"Configuration\", then \"Parsers\".");
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
					$pag['page_html'] .= " <span class=\"readmore\"><a href=\"".$pag['page_pageurl']."\">".$L['ReadMore']."</a></span>";
				}
				$html = $cfg['parsebbcodepages'] ? sed_post_parse($pag['page_html']) : htmlspecialchars($pag['page_text']);
				$t -> assign('LIST_ROW_TEXT', $html);
			}
			else
			{
				$readmore = mb_strpos($pag['page_text'], "[more]");
				$text = sed_parse(htmlspecialchars($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
				if ($readmore > 0)
				{
					$pag['page_text'] = mb_substr($pag['page_text'], 0, $readmore);
					$pag['page_text'] .= " <span class=\"readmore\"><a href=\"".$pag['page_pageurl']."\">".$L['ReadMore']."</a></span>";
				}
				$text = sed_post_parse($text, 'pages');
				$t -> assign('LIST_ROW_TEXT', $text);
			}
			break;
	}

	// Extra fields for pages
	if ($number_of_extrafields_p > 0)
	{
		foreach ($extrafields_p as $row_p)
		{
			$uname = strtoupper($row_p['field_name']);
			isset($L['page_'.$row_p['field_name'].'_title']) ? $t -> assign('LIST_ROW_'.$uname.'_TITLE', $L['page_'.$row_p['field_name'].'_title']) : $t -> assign('LIST_ROW_'.$uname.'_TITLE', $row_p['field_description']);
			$t -> assign('LIST_ROW_'.$uname, sed_build_extrafields_data('page', $row_p['field_type'], $row_p['field_name'], $pag['page_'.$row_p['field_name']]));
		}
	}

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t -> parse("MAIN.LIST_ROW");
}

/* === Hook === */
$extp = sed_getextplugins('list.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t -> parse("MAIN");
$t -> out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>