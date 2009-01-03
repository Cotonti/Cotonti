<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=system/core/list/list.inc.php
Version=125
Updated=2008-may-26
Type=Core
Author=Neocrome
Description=Pages
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$id = sed_import('id','G','INT');
$s = sed_import('s','G','ALP');
$d = sed_import('d','G','INT');
$c = sed_import('c','G','TXT');
$w = sed_import('w','G','ALP',4);
$o = sed_import('o','G','ALP',16);
$p = sed_import('p','G','ALP',16);

$dc = sed_import('dc','G','INT');

if ($c=='all' || $c=='system')
{
	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
	sed_block($usr['isadmin']);
}
else
{
	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', $c);
	sed_block($usr['auth_read']);
}

/* === Hook === */
$extp = sed_getextplugins('list.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if (empty($s))
{
	$s = $sed_cat[$c]['order'];
	$w = $sed_cat[$c]['way'];
}

if (empty($s)) { $s = 'title'; }
if (empty($w)) { $w = 'asc'; }
if (empty($d)) { $d = '0'; }
if (empty($dc)) { (int)$dc = 0; }
$cfg['maxrowsperpage'] = ($c=='all' || $c=='system') ? $cfg['maxrowsperpage']*2 : $cfg['maxrowsperpage'];


$item_code = 'list_'.$c;
$join_ratings_columns = ($cfg['disable_ratings']) ? '' : ", r.rating_average";
$join_ratings_condition = ($cfg['disable_ratings']) ? '' : "LEFT JOIN $db_ratings as r ON r.rating_code=CONCAT('p',p.page_id)";

if ($c=='all')
{
	$sql = sed_sql_query("SELECT SUM(structure_pagecount) FROM $db_structure ");
	$totallines = sed_sql_result($sql, 0, "SUM(structure_pagecount)");
	$sql = sed_sql_query("SELECT p.*, u.user_name ".$join_ratings_columns."
	FROM $db_pages as p ".$join_ratings_condition."
	LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
	WHERE page_state='0'
	ORDER BY page_$s $w LIMIT $d,".$cfg['maxrowsperpage']);
}
elseif (!empty($o) && !empty($p) && $p!='password')
{
	$sql = sed_sql_query("SELECT SUM(structure_pagecount) FROM $db_structure WHERE structure_code='$c' ");
	$totallines = sed_sql_result($sql, 0, "SUM(structure_pagecount)");
	$sql = sed_sql_query("SELECT p.*, u.user_name ".$join_ratings_columns."
	FROM $db_pages as p ".$join_ratings_condition."
	LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
	WHERE page_cat='$c' AND (page_state='0' OR page_state='2') AND page_$o='$p'
	ORDER BY page_$s $w LIMIT $d,".$cfg['maxrowsperpage']);
}
else
{
	sed_die(empty($sed_cat[$c]['title']) && !$usr['isadmin']);
	$sql = sed_sql_query("SELECT SUM(structure_pagecount) FROM $db_structure WHERE structure_code='$c' ");
	$totallines = sed_sql_result($sql, 0, "SUM(structure_pagecount)");
	$sql = sed_sql_query("SELECT p.*, u.user_name ".$join_ratings_columns."
	FROM $db_pages as p ".$join_ratings_condition."
	LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
	WHERE page_cat='$c' AND (page_state='0' OR page_state='2')
	ORDER BY page_$s $w LIMIT $d,".$cfg['maxrowsperpage']);
}

$incl="datas/content/list.$c.txt";

if (@file_exists($incl))
{
	$fd = @fopen ($incl, "r");
	$extratext = fread ($fd, filesize ($incl));
	fclose ($fd);
}

if ($c=='all' || $c=='system')
{ $catpath = $sed_cat[$c]['title']; }
else
{ $catpath = sed_build_catpath($c, '<a href="'.sed_url('list', 'c=%1$s').'">%2$s</a>'); }

$totalpages = ceil($totallines / $cfg['maxrowsperpage']);
$currentpage= ceil ($d / $cfg['maxrowsperpage'])+1;
$submitnewpage = ($usr['auth_write'] && $c!='all') ? "<a href=\"".sed_url('page', "m=add&c=".$c)."\">".$L['lis_submitnew']."</a>" : '';

$pagination = sed_pagination(sed_url('list', "c=$c&amp;s=$s&amp;w=$w&amp;o=$o&amp;p=$p"), $d, $totallines, $cfg['maxrowsperpage']);
list($pageprev, $pagenext) = sed_pagination_pn(sed_url('list', "c=$c&amp;s=$s&amp;w=$w&amp;o=$o&amp;p=$p"), $d, $totallines, $cfg['maxrowsperpage'], TRUE);

list($list_comments, $list_comments_display) = sed_build_comments($item_code, sed_url('list', 'c=$c'), $comments);
list($list_ratings, $list_ratings_display) = sed_build_ratings($item_code, sed_url('list', 'c=$c'), $ratings);

$sys['sublocation'] = $sed_cat[$c]['title'];
$title_tags[] = array('{TITLE}');
$title_tags[] = array('%1$s');
$title_data = array($sed_cat[$c]['title']);
$out['subtitle'] = sed_title('title_list', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('list.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

if ($sed_cat[$c]['group'])
{ $mskin = sed_skinfile(array('list', 'group', $sed_cat[$c]['tpl'])); }
else
{ $mskin = sed_skinfile(array('list', $sed_cat[$c]['tpl'])); }

$t = new XTemplate($mskin);

$t->assign(array(
	"LIST_PAGETITLE" => $catpath,
	"LIST_CATEGORY" => '<a href="'.sed_url('list', "c=$c").'">'.$sed_cat[$c]['title'].'</a>',
	"LIST_CAT" => $c,
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

if (!$sed_cat[$c]['group'])
{
	$t->assign(array(
	"LIST_TOP_CURRENTPAGE" => $currentpage,
	"LIST_TOP_TOTALLINES" => $totallines,
	"LIST_TOP_MAXPERPAGE" => $cfg['maxrowsperpage'],
	"LIST_TOP_TOTALPAGES" => $totalpages,
	"LIST_TOP_TITLE" => "<a href=\"".sed_url('list', "c=$c&amp;s=title&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=title&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a> ".$L['Title'],
	"LIST_TOP_KEY" => "<a href=\"".sed_url('list', "c=$c&amp;s=key&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=key&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a> ".$L['Key'],
	"LIST_TOP_EXTRA1" => "<a href=\"".sed_url('list', "c=$c&amp;s=extra1&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=extra1&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a>",
	"LIST_TOP_EXTRA2" => "<a href=\"".sed_url('list', "c=$c&amp;s=extra2&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=extra2&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a>",
	"LIST_TOP_EXTRA3" => "<a href=\"".sed_url('list', "c=$c&amp;s=extra3&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=extra3&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a>",
	"LIST_TOP_EXTRA4" => "<a href=\"".sed_url('list', "c=$c&amp;s=extra4&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=extra4&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a>",
	"LIST_TOP_EXTRA5" => "<a href=\"".sed_url('list', "c=$c&amp;s=extra5&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=extra5&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a>",
	"LIST_TOP_DATE" => "<a href=\"".sed_url('list', "c=$c&amp;s=date&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=date&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a> ".$L['Date'],
	"LIST_TOP_AUTHOR" => "<a href=\"".sed_url('list', "c=$c&amp;s=author&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=author&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a> ".$L['Author'],
	"LIST_TOP_OWNER" => "<a href=\"".sed_url('list', "c=$c&amp;s=ownerid&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=ownerid&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a> ".$L['Owner'],
	"LIST_TOP_COUNT" => "<a href=\"".sed_url('list', "c=$c&amp;s=count&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=count&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a> ".$L['Hits'],
	"LIST_TOP_FILECOUNT" => "<a href=\"".sed_url('list', "c=$c&amp;s=filecount&amp;w=asc&amp;o=$o&amp;p=$p")."\">$sed_img_down</a>
	<a href=\"".sed_url('list', "c=$c&amp;s=filecount&amp;w=desc&amp;o=$o&amp;p=$p")."\">$sed_img_up</a> ".$L['Hits']
	));
}

$ii=0;
$jj=1;
$mm=0;
$kk=0;
$mtch = $sed_cat[$c]['path'].".";
$mtchlen = mb_strlen($mtch);
$mtchlvl = mb_substr_count($mtch,".");

while (list($i,$x) = each($sed_cat))
{
	if(mb_substr($x['path'],0,$mtchlen)==$mtch && mb_substr_count($x['path'],".")==$mtchlvl && $mm<$dc)
	{
		$mm++;
		$ii++;
	}
	elseif (mb_substr($x['path'],0,$mtchlen)==$mtch && mb_substr_count($x['path'],".")==$mtchlvl && $kk<$cfg['maxrowsperpage'])
	{
		$sql4 = sed_sql_query("SELECT SUM(structure_pagecount) FROM $db_structure
		WHERE structure_path LIKE '".$sed_cat[$i]['rpath']."%' ");
		$sub_count = sed_sql_result($sql4,0,"SUM(structure_pagecount)");

		$t-> assign(array(
					"LIST_ROWCAT_URL" => sed_url('list', 'c='.$i),
					"LIST_ROWCAT_TITLE" => $x['title'],
					"LIST_ROWCAT_DESC" => $x['desc'],
					"LIST_ROWCAT_ICON" => $x['icon'],
					"LIST_ROWCAT_COUNT" => $sub_count,
					"LIST_ROWCAT_ODDEVEN" => sed_build_oddeven($kk)
		));
		$t->parse("MAIN.LIST_ROWCAT");
		$kk++;
	}
	elseif (mb_substr($x['path'],0,$mtchlen)==$mtch && mb_substr_count($x['path'],".")==$mtchlvl)
	{
		$ii++;
	}
}

$totalitems = $ii + $kk;
$pagnav = sed_pagination(sed_url('list','c='.$c), $dc, $totalitems, $cfg['maxrowsperpage'], $characters="dc");
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('list', 'c='.$c), $dc, $totalitems, $cfg['maxrowsperpage'], TRUE, $characters="dc");

$t->assign(array(
		"LISTCAT_PAGEPREV" => $pagination_prev,
		"LISTCAT_PAGENEXT" => $pagination_next,
		"LISTCAT_PAGNAV" => $pagnav)
);

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('list.loop');
/* ===== */

while ($pag = sed_sql_fetcharray($sql) and ($jj<=$cfg['maxrowsperpage']))
{
	$jj++;
	$pag['page_desc'] = sed_cc($pag['page_desc']);
	$page_urlp = empty($pag['page_alias']) ? 'id='.$pag['page_id'] : 'al='.$pag['page_alias'];
	$pag['page_pageurl'] = sed_url('page', $page_urlp);

	if (!empty($pag['page_url']) && $pag['page_file'])
	{
		$dotpos = mb_strrpos($pag['page_url'],".")+1;
		$pag['page_fileicon'] = (mb_strlen($pag['page_url'])-$dotpos>4) ? "images/admin/page.gif" : "images/pfs/".mb_strtolower(mb_substr($pag['page_url'], $dotpos, 5)).".gif";
		$pag['page_fileicon'] = "<img src=\"".$pag['page_fileicon']."\" alt=\"\" />";
	}
	else
	{ $pag['page_fileicon'] = ''; }

	$item_code = 'p'.$pag['page_id'];
	$pag['page_comcount'] = (!$pag['page_comcount']) ? "0" : $pag['page_comcount'];
	$pag['page_comments'] = "<a href=\"".sed_url('page', $page_urlp.'&comments=1')."\"><img src=\"skins/".$usr['skin']."/img/system/icon-comment.gif\" alt=\"\" /> (".$pag['page_comcount'].")</a>";
	$pag['admin'] = $usr['isadmin'] ? "<a href=\"".sed_url('admin', "m=page&s=queue&a=unvalidate&id=".$pag['page_id']."&".sed_xg())."\">".$L['Putinvalidationqueue']."</a> &nbsp;<a href=\"".sed_url('page', "m=edit&id=".$pag['page_id']."&r=list")."\">".$L['Edit']."</a> " : '';

	if(empty($pag['page_desc'])) $pag['page_desc'] = sed_cutpost($pag['page_text'], 200, false);

	$t-> assign(array(
		"LIST_ROW_URL" => $pag['page_pageurl'],
		"LIST_ROW_ID" => $pag['page_id'],
		"LIST_ROW_CAT" => $pag['page_cat'],
		"LIST_ROW_KEY" => sed_cc($pag['page_key']),
		"LIST_ROW_EXTRA1" => sed_cc($pag['page_extra1']),
		"LIST_ROW_EXTRA2" => sed_cc($pag['page_extra2']),
		"LIST_ROW_EXTRA3" => sed_cc($pag['page_extra3']),
		"LIST_ROW_EXTRA4" => sed_cc($pag['page_extra4']),
		"LIST_ROW_EXTRA5" => sed_cc($pag['page_extra5']),
		"LIST_ROW_TITLE" => sed_cc($pag['page_title']),
		"LIST_ROW_DESC" => $pag['page_desc'],
		"LIST_ROW_AUTHOR" => sed_cc($pag['page_author']),
		"LIST_ROW_OWNER" => sed_build_user($pag['page_ownerid'], sed_cc($pag['user_name'])),
		"LIST_ROW_DATE" => @date($cfg['formatyearmonthday'], $pag['page_date'] + $usr['timezone'] * 3600),
		"LIST_ROW_FILEURL" => $pag['page_url'],
		"LIST_ROW_SIZE" => $pag['page_size'],
		"LIST_ROW_COUNT" => $pag['page_count'],
		"LIST_ROW_FILEICON" => $pag['page_fileicon'],
		"LIST_ROW_FILECOUNT" => $pag['page_filecount'],
		"LIST_ROW_JUMP" => sed_url('page', $page_urlp.'&a=dl'),
		"LIST_ROW_COMMENTS" => $pag['page_comments'],
		"LIST_ROW_RATINGS" => "<a href=\"".sed_url('page', $page_urlp.'&ratings=1')."\"><img src=\"skins/".$usr['skin']."/img/system/vote".round($pag['rating_average'],0).".gif\" alt=\"\" /></a>",
		"LIST_ROW_ADMIN" => $pag['admin'],
		"LIST_ROW_ODDEVEN" => sed_build_oddeven($jj)
	));

	// Adding LIST_ROW_TEXT tag
	switch($pag['page_type'])
	{
		case '1':
			$t->assign("LIST_ROW_TEXT", $pag['page_text']);
			break;

		case '2':

			if ($cfg['allowphp_pages'] && $cfg['allowphp_override'])
			{
				ob_start();
				eval($pag['page_text']);
				$t->assign("LIST_ROW_TEXT", ob_get_clean());
			}
			else
			{
				$t->assign("LIST_ROW_TEXT", "The PHP mode is disabled for pages.<br />Please see the administration panel, then \"Configuration\", then \"Parsers\".");
			}
			break;

		default:
			if($cfg['parser_cache'])
			{
				if(empty($pag['page_html']) && !empty($pag['page_text']))
				{
					$pag['page_html'] = sed_parse(sed_cc($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
					sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($pag['page_html'])."' WHERE page_id = " . $pag['page_id']);
				}
				$html = $cfg['parsebbcodepages'] ? sed_post_parse($pag['page_html']) : sed_cc($pag['page_text']);
				$t->assign('LIST_ROW_TEXT', $html);
			}
			else
			{
				$text = sed_parse(sed_cc($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
				$text = sed_post_parse($text, 'pages');
				$t->assign('LIST_ROW_TEXT', $text);
			}
			break;
	}
	
		// Extra fields - adding LIST_ROW_X tag
	$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='pages'");
	while($row = sed_sql_fetchassoc($fieldsres)) $t->assign('LIST_ROW_'.strtoupper($row['field_name']), $pag['page_'.$row['field_name']]);

	/* === Hook - Part2 : Include === */
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t->parse("MAIN.LIST_ROW");
}


/* === Hook === */
$extp = sed_getextplugins('list.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>