<?php
/**
 * Pick up pages from a category and display the newest in the home page
 *
 * @package Cotonti
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

require_once $cfg['system_dir'] . '/tags.php';

    /* === Hook - Part1 : Set === FIRST === */
$news_first_extp = sed_getextplugins('news.first');
    /* === Hook - Part1 : Set === LOOP === */
$news_extp = sed_getextplugins('news.loop');
    /* === Hook - Part1 : Set === TAGS === */
$news_tags_extp = sed_getextplugins('news.tags');
    /* ===== */

function sed_get_news($cat, $skinfile="news", $limit=false, $d=0, $textlength=0, $deftag=false)
{
	global $sed_cat, $db_pages, $db_users, $sys, $cfg, $L, $pag, $sed_extrafields,
	$usr, $sed_dbc, $sed_urltrans, $c, $news_extp, $news_tags_extp, $news_first_extp;
	$jj = 0;
	$mtch = $sed_cat[$cat]['path'].".";
	$mtchlen = mb_strlen($mtch);
	$catsub = array();
	$catsub[] = $cat;
	foreach($sed_cat as $i => $x)
	{
		if(mb_substr($x['path'], 0, $mtchlen) == $mtch && sed_auth('page', $i, 'R'))
		{
			$catsub[] = $i;
		}
	}

	if(!$limit)
	{
		$limit = $cfg['plugin']['news']['maxpages'];
	}
	$order=$sed_cat[$cat]['order'];
	$way=$sed_cat[$cat]['way'];

	$where ="page_state=0 AND page_cat <> 'system' AND page_begin<'".$sys['now_offset']."'
            AND page_expire>'".$sys['now_offset']."' AND page_cat IN ('".implode("','", $catsub)."')";
    /* === Hook - Part2 : Include === FIRST === */
	foreach ($news_first_extp as $pl)
	{
		include $pl;
	}
    /* ===== */
	$sql = sed_sql_query("SELECT p.*, u.user_name, user_avatar FROM $db_pages AS p
    LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
    WHERE ".$where."
     ORDER BY page_".$order." ".$way." LIMIT $d, $limit" );

	$sql2 = sed_sql_query("SELECT COUNT(*) FROM $db_pages
    WHERE ".$where);

	$totalnews = sed_sql_result($sql2,0,"COUNT(*)");
	$news_link=sed_news_link($cat, $deftag);
	$catd  = ((!$deftag || $c != $cat) && !$cfg['plugin']['news']['syncpagination']) ? $cat."d" : "d";
	// TODO use sed_pagenav()
	$pagnav = sed_pagination($news_link, $d, $totalnews, $limit, $catd);
	list($pages_prev, $pages_next) = sed_pagination_pn($news_link, $d, $totalnews, $limit, TRUE, $catd);

	if(file_exists(sed_skinfile($skinfile, true)))
	{
		$news = new XTemplate(sed_skinfile($skinfile, true));
	}
	else
	{
		$news = new XTemplate(sed_skinfile('news', true));
	}

	while($pag = sed_sql_fetcharray($sql))
	{
		$jj++;
		$catpath = sed_build_catpath($pag['page_cat'], "<a href=\"%1\$s\">%2\$s</a>");
		$pag['page_pageurl'] = (empty($pag['page_alias'])) ? sed_url('page', 'id='.$pag['page_id']) : sed_url('page', 'al='.$pag['page_alias']);
		$pag['page_fulltitle'] = $catpath." ".$cfg['separator']." <a href=\"".$pag['page_pageurl']."\">".htmlspecialchars($pag['page_title'])."</a>";

		$submitnewpage = (sed_auth('page', $cat, 'W')) ? "<a href=\"page.php?m=add&amp;c=$cat\">".$L['Submitnew']."</a>" : '';

		$item_code = 'p'.$pag['page_id'];
		list($pag['page_comments'], $pag['page_comments_display']) = sed_build_comments($item_code, $pag['page_pageurl'], FALSE);
		list($pag['page_ratings'], $pag['page_ratings_display']) = sed_build_ratings($item_code, $pag['page_pageurl'], $ratings);

		switch($pag['page_type'])
		{
			case 2:
				if ($cfg['allowphp_pages'] && $cfg['allowphp_override'])
				{
					ob_start();
					eval($pag['page_text']);
					$news->assign("PAGE_ROW_TEXT", ob_get_clean());
				}
				else
				{
					$news->assign("PAGE_ROW_TEXT", "The PHP mode is disabled for pages.<br />Please see the administration panel, then \"Configuration\", then \"Parsers\".");
				}
				break;

			case 1:
				$pag_more = ((int)$textlength>0) ? sed_string_truncate($pag['page_text'], $textlength) : sed_cut_more($pag['page_text']);
				$news->assign("PAGE_ROW_TEXT", $pag['page_text']);
				break;

			default:
				if($cfg['parser_cache'])
				{
					if(empty($pag['page_html']))
					{
						$pag['page_html'] = sed_parse(htmlspecialchars($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
						sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($pag['page_html'])."' WHERE page_id = " . $pag['page_id']);
					}
					$pag['page_html'] = ($cfg['parsebbcodepages']) ?  $pag['page_html'] : htmlspecialchars($pag['page_text']);
					$pag_more = ((int)$textlength>0) ? sed_string_truncate($pag['page_html'], $textlength) : sed_cut_more($pag['page_html']);
					$pag['page_html'] = sed_post_parse($pag['page_html'], 'pages');
					$news->assign('PAGE_ROW_TEXT', $pag['page_html']);
				}
				else
				{
					$pag['page_html'] = sed_parse(htmlspecialchars($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
					$pag_more = ((int)$textlength>0) ? sed_string_truncate($pag['page_html'], $textlength) : sed_cut_more($pag['page_html']);
					$pag['page_html'] = sed_post_parse($pag['page_html'], 'pages');
					$news->assign('PAGE_ROW_TEXT', $pag['page_html']);
				}
				break;
		}

		$news-> assign(array(
			"PAGE_ROW_URL" => $pag['page_pageurl'],
			"PAGE_ROW_ID" => $pag['page_id'],
			"PAGE_ROW_TITLE" => $pag['page_fulltitle'],
			"PAGE_ROW_SHORTTITLE" => htmlspecialchars($pag['page_title']),
			"PAGE_ROW_CAT" => $pag['page_cat'],
			"PAGE_ROW_CATTITLE" => htmlspecialchars($sed_cat[$pag['page_cat']]['title']),
			"PAGE_ROW_CATPATH" => $catpath,
			"PAGE_ROW_CATPATH_SHORT" => "<a href=\"".sed_url('list', 'c='.$pag['page_cat'])."\">".htmlspecialchars($sed_cat[$pag['page_cat']]['title'])."</a>",
			"PAGE_ROW_NEWSPATH" => "<a href=\"".sed_url('index', 'c='.$pag['page_cat'])."\">".htmlspecialchars($sed_cat[$row['page_cat']]['title'])."</a>",
			"PAGE_ROW_CATDESC" => htmlspecialchars($sed_cat[$pag['page_cat']]['desc']),
			"PAGE_ROW_CATICON" => $sed_cat[$pag['page_cat']]['icon'],
			"PAGE_ROW_KEY" => htmlspecialchars($pag['page_key']),
			"PAGE_ROW_DESC" => htmlspecialchars($pag['page_desc']),
			"PAGE_ROW_MORE" => ($pag_more) ? "<span class='readmore'><a href='".$pag['page_pageurl']."'>{$L['ReadMore']}</a></span>" : "",
			"PAGE_ROW_AUTHOR" => htmlspecialchars($pag['page_author']),
			"PAGE_ROW_OWNER" => sed_build_user($pag['page_ownerid'], htmlspecialchars($pag['user_name'])),
			"PAGE_ROW_AVATAR" => sed_build_userimage($pag['user_avatar'], 'avatar'),
			"PAGE_ROW_DATE" => @date($cfg['formatyearmonthday'], $pag['page_date'] + $usr['timezone'] * 3600),
			"PAGE_ROW_BEGIN" => @date($cfg['formatyearmonthday'], $pag['page_begin'] + $usr['timezone'] * 3600),
			"PAGE_ROW_EXPIRE" => @date($cfg['formatyearmonthday'], $pag['page_expire'] + $usr['timezone'] * 3600),
			"PAGE_ROW_FILEURL" => $pag['page_url'],
			"PAGE_ROW_SIZE" => $pag['page_size'],
			"PAGE_ROW_COUNT" => $pag['page_count'],
			"PAGE_ROW_FILECOUNT" => $pag['page_filecount'],
			"PAGE_ROW_COMMENTS" => $pag['page_comments'],
			"PAGE_ROW_RATINGS" => $pag['page_ratings'],
			"PAGE_ROW_ODDEVEN" => sed_build_oddeven($jj),
			"PAGE_ROW_NUM" => $jj,
		));

		// Extrafields
		foreach ($sed_extrafields['pages'] as $row)
		{
			$news -> assign('PAGE_ROW_'.strtoupper($row_p['field_name']).'_TITLE', isset($L['page_'.$row['field_name'].'_title']) ?  $L['page_'.$row['field_name'].'_title'] : $row['field_description']);
			$news->assign('PAGE_ROW_' . mb_strtoupper($row['field_name']),
				sed_build_extrafields_data('page', $row['field_type'], $row['field_name'], $pag["page_{$row['field_name']}"]));
		}
	

        /* === Hook - Part2 : Include === LOOP === */
		foreach ($news_extp as $pl)
		{
			include $pl;
		}
        /* ===== */
		if($cfg['plugin']['tags']['pages'])
		{
			require_once(sed_langfile('tags'));
			$item_id = $pag['page_id'];
			$tags = sed_tag_list($item_id);
			if(count($tags) > 0)
			{
				$tag_ii = 0;
				foreach($tags as $tag)
				{
					$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
					$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
					$news->assign(array(
						'PAGE_TAGS_ROW_TAG' => $cfg['plugin']['tags']['title'] ? htmlspecialchars(sed_tag_title($tag)) : htmlspecialchars($tag),
						'PAGE_TAGS_ROW_TAG_COUNT' => $tag_ii,
						'PAGE_TAGS_ROW_URL' => sed_url('plug', 'e=tags&a=pages&t='.$tag_u.$tl)
					));
					$news->parse('NEWS.PAGE_ROW.PAGE_TAGS.PAGE_TAGS_ROW');
					$tag_ii++;
				}
				$news->parse('NEWS.PAGE_ROW.PAGE_TAGS');
			}
			else
			{
				$news->assign(array(
					'PAGE_NO_TAGS' => $L['tags_Tag_cloud_none'],
				));
				$news->parse('NEWS.PAGE_ROW.PAGE_NO_TAGS');
			}
		}

		$news->parse("NEWS.PAGE_ROW");
	}

	$catpath = sed_build_catpath($cat, "<a href=\"%1\$s\">%2\$s</a>");
	$news-> assign(array(
		"PAGE_PAGENAV" => $pagnav,
		"PAGE_PAGEPREV" => $pages_prev,
		"PAGE_PAGENEXT" => $pages_next,
		"PAGE_SUBMITNEWPOST" => $submitnewpage,
		"PAGE_CATTITLE" =>$sed_cat[$cat]['title'],
		"PAGE_CATPATH" =>$catpath,
		"PAGE_CAT" => $cat,
	));

        /* === Hook - Part2 : Include === TAGS === */
		foreach ($news_tags_extp as $pl)
		{
			include $pl;
		}
        /* ===== */

	$news->parse("NEWS");
	return ($news->text("NEWS"));
}

function sed_news_link($maincat, $tag)
{
	global $c, $cats, $indexcat, $d, $cfg;
	if ($c != $indexcat)
	{
		$valtext = "c=".$c;
	}
	if (!$cfg['plugin']['news']['syncpagination'] && !empty($cats))
	{

		if (($c != $maincat || !$tag) && $d != 0)
		{
			$valtext .= "&d=".$d;
		}
		foreach($cats as $k => $v)
		{
			if (($k != $maincat || $tag) && $v[2]!=0)
			{
				$valtext .= "&".$k."d=".$v[2];
			}
		}
	}
	return sed_url('index', $valtext);
}

?>