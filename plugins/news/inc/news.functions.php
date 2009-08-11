<?php
/**
 * Pick up pages from a category and display the newest in the home page
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

    /*  === get extra fields === */
$extrafields = array();
$fieldsres = sed_sql_query("SELECT field_name, field_type FROM $db_extra_fields WHERE field_location='pages'");
while ($row = sed_sql_fetchassoc($fieldsres)) $extrafields[] = $row;
    /* ===== */

    /*  === Hook - Part1 : Set === FIRST === */
$news_first_extp = sed_getextplugins('news.first');
    /* ===== */

    /* === Hook - Part1 : Set === LOOP === */
$news_extp = sed_getextplugins('news.loop');
    /* ===== */

    /* === Hook - Part1 : Set === TAGS === */
$news_tags_extp = sed_getextplugins('news.loop');
    /* ===== */

function sed_get_news($cat, $skinfile="news", $deftag="INDEX_NEWS",  $limit=false, $d=0)
{
    global $sed_cat, $db_pages, $db_users, $db_extra_fields, $sys, $cfg, $L, $t, $pag,
    $usr, $sed_dbc, $sed_urltrans, $c, $extrafields, $news_extp, $news_tags_extp, $news_first_extp;
    $jj = 0;
    $mtch = $sed_cat[$cat]['path'].".";
    $mtchlen = mb_strlen($mtch);
    $catsub = array();
    $catsub[] = $cat;
    if(!$limit){$limit=$cfg['maxrowsperpage'];}
    $order=$sed_cat[$cat]['order'];
    $way=$sed_cat[$cat]['way'];
    foreach($sed_cat as $i => $x)
    {
        if(mb_substr($x['path'], 0, $mtchlen) == $mtch && sed_auth('page', $i, 'R')){
            $catsub[] = $i;
        }
    }
    $additionalkey ="page_state=0 AND page_cat <> 'system' AND page_begin<'".$sys['now_offset']."' AND page_expire>'".$sys['now_offset']."'";
    /* === Hook - Part2 : Include === FIRST === */
    if (is_array($news_first_extp))
    {
        foreach ($news_first_extp as $pl)
        {
            include_once("{$cfg['plugins_dir']}/{$pl['pl_code']}/{$pl['pl_file']}.php");
        }
    }
    /* ===== */
    $sql = sed_sql_query("SELECT p.*, u.user_name, user_avatar FROM $db_pages AS p
    LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
    WHERE ".$additionalkey."
    AND page_cat IN ('".implode("','", $catsub)."') ORDER BY page_".$order." ".$way." LIMIT $d, $limit" );

    $sql2 = sed_sql_query("SELECT COUNT(*) FROM $db_pages
    WHERE ".$additionalkey."
    AND page_cat IN ('".implode("','", $catsub)."')");

    $totalnews = sed_sql_result($sql2,0,"COUNT(*)");

    $perpage = $cfg['plugin']['news']['maxpages'];
    $newstag = ($deftag=='INDEX_NEWS') ? true : false;
    $news_link=sed_news_link($cat, $newstag);
    $catd  = (($deftag!="INDEX_NEWS" || $c != $cat) && !$cfg['plugin']['news']['syncpagination']) ? $cat."d" : "d";

    $pagnav = sed_pagination($news_link, $d, $totalnews, $perpage, $catd);
    list($pages_prev, $pages_next) = sed_pagination_pn($news_link, $d, $totalnews, $perpage, TRUE, $catd);

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

        $submitnewpage = (sed_auth('page', $cat, 'W')) ? "<a href=\"page.php?m=add&amp;c=$cat\">".$L['lis_submitnew']."</a>" : '';

        $item_code = 'p'.$pag['page_id'];
        list($pag['page_comments'], $pag['page_comments_display']) = sed_build_comments($item_code, $pag['page_pageurl'], FALSE);

        $news-> assign(array(
            "PAGE_ROW_URL" => $pag['page_pageurl'],
            "PAGE_ROW_ID" => $pag['page_id'],
            "PAGE_ROW_TITLE" => $pag['page_fulltitle'],
            "PAGE_ROW_SHORTTITLE" => htmlspecialchars($pag['page_title']),
            "PAGE_ROW_CAT" => $pag['page_cat'],
            "PAGE_ROW_CATTITLE" => htmlspecialchars($sed_cat[$pag['page_cat']]['title']),
            "PAGE_ROW_CATPATH" => $catpath,
            "PAGE_ROW_CATDESC" => htmlspecialchars($sed_cat[$pag['page_cat']]['desc']),
            "PAGE_ROW_CATICON" => $sed_cat[$pag['page_cat']]['icon'],
            "PAGE_ROW_KEY" => htmlspecialchars($pag['page_key']),
            "PAGE_ROW_DESC" => htmlspecialchars($pag['page_desc']),
            "PAGE_ROW_AUTHOR" => htmlspecialchars($pag['page_author']),
            "PAGE_ROW_OWNER" => sed_build_user($pag['page_ownerid'], htmlspecialchars($pag['user_name'])),
            "PAGE_ROW_AVATAR" => sed_build_userimage($pag['user_avatar'], 'avatar'),
            "PAGE_ROW_DATE" => @date($cfg['formatyearmonthday'], $pag['page_date'] + $usr['timezone'] * 3600),
            "PAGE_ROW_FILEURL" => $pag['page_url'],
            "PAGE_ROW_SIZE" => $pag['page_size'],
            "PAGE_ROW_COUNT" => $pag['page_count'],
            "PAGE_ROW_FILECOUNT" => $pag['page_filecount'],
            "PAGE_ROW_COMMENTS" => $pag['page_comments'],
            "PAGE_ROW_RATINGS" => "<img src=\"skins/".$usr['skin']."/img/system/vote".round($pag['rating_average'],0).".gif\" alt=\"\" />",
            "PAGE_ROW_ODDEVEN" => sed_build_oddeven($jj),
            "PAGE_ROW_NUM" => $jj,
            ));

        switch($pag['page_type'])
        {
            case 1:
                if (!sed_news_cut_more($pag['page_text'], $pag['page_pageurl']))
                {
                    sed_news_cut_more($pag['page_text'], $pag['page_pageurl'], '[more]');
                }
                $news->assign("PAGE_ROW_TEXT", $pag['page_text']);
                break;

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

            default:
                if($cfg['parser_cache'])
                {
                    if(empty($pag['page_html']))
                    {
                        $pag['page_html'] = sed_parse(htmlspecialchars($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
                        sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($pag['page_html'])."' WHERE page_id = " . $pag['page_id']);
                    }
                    sed_news_cut_more($pag['page_html'], $pag['page_pageurl']);
                    sed_news_strip_newpage($pag['page_html']);
                    $cfg['parsebbcodepages'] ? $news->assign('PAGE_ROW_TEXT', sed_post_parse($pag['page_html'], 'pages'))
                    : $news->assign('PAGE_ROW_TEXT', htmlspecialchars($pag['page_text']));
                }
                else
                {
                    $pag['page_text'] = sed_parse(htmlspecialchars($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
                    sed_news_cut_more($pag['page_text'], $pag['page_pageurl']);
                    sed_news_strip_newpage($pag['page_text']);
                    $pag['page_text'] = sed_post_parse($pag['page_text'], 'pages');
                    $news->assign('PAGE_ROW_TEXT', $pag['page_text']);
                }
                break;
        }

        // data from extra fields
        foreach ($extrafields as $row)
        {
            $news->assign('PAGE_ROW_' . mb_strtoupper($row['field_name']),
                sed_build_extrafields_data('page', $row['field_type'], $row['field_name'], $pag["page_{$row['field_name']}"])
            );
        }

        /* === Hook - Part2 : Include === LOOP === */
        if (is_array($news_extp))
        {
            foreach ($news_extp as $pl)
            {
                include_once("{$cfg['plugins_dir']}/{$pl['pl_code']}/{$pl['pl_file']}.php");
            }
        }
        /* ===== */

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
    if (is_array($news_tags_extp))
    {
        foreach ($news_tags_extp as $pl)
        {
            include_once("{$cfg['plugins_dir']}/{$pl['pl_code']}/{$pl['pl_file']}.php");
        }
    }
        /* ===== */

    $news->parse("NEWS");
    $t->assign(strtoupper($deftag), $news->text("NEWS"));
}

/**
 * Cuts the page after 'more' tag
 *
 * @global $L
 * @param string ptr $html Page body
 * @param string $url Page URL
 * @param string $tag 'more' tag
 * @return bool
 */
function sed_news_cut_more(&$html, $url, $tag = '<!--more-->')
{
    global $L;

    $mpos = mb_strpos($html, $tag);

    if ($mpos === false)
    {
        return false;
    }

    $html = mb_substr($html, 0, $mpos) . "<span class=\"readmore\"><a href=\"$url\">{$L['ReadMore']}</a></span>";
    return true;
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

/**
 * Cuts the news page after the first page (if multipage)
 *
 * @param string $html Page body
 */
function sed_news_strip_newpage(&$html)
{
    $newpage = mb_strpos($html, '[newpage]');

    if($newpage !== false)
    {
        $html = mb_substr($html, 0, $newpage);
    }

    $html = preg_replace('#\[title\](.*?)\[/title\][\s\r\n]*(<br />)?#i', '', $html);
}

?>