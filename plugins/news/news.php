<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/news/news.php
Version=122
Updated=2008-feb-18
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=news
Part=homepage
File=news
Hooks=index.tags
Tags=index.tpl:{INDEX_NEWS}
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]

==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$d = sed_import('d','G','INT');
$c = sed_import('c','G','TXT');

if (empty($d))	{ $d = '0'; }
if (empty($c))
{
	$c = $cfg['plugin']['news']['category'];
}
else
{
	$checkin = strpos($sed_cat[$c]['path'], $sed_cat[$cfg['plugin']['news']['category']]['path']);
	$c = ($checkin === false) ? $cfg['plugin']['news']['category'] :  $c ;
}

if ($cfg['plugin']['news']['maxpages']>0 && !empty($c))
{
	$jj = 0;
	$mtch = $sed_cat[$c]['path'].".";
	$mtchlen = mb_strlen($mtch);
	$catsub = array();
	$catsub[] = $c;

	foreach($sed_cat as $i => $x)
	{
		if (mb_substr($x['path'], 0, $mtchlen)==$mtch && sed_auth('page', $i, 'R'))
		{ $catsub[] = $i; }
	}

	$sql = sed_sql_query("SELECT p.*, u.user_name, user_avatar FROM $db_pages AS p
	LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
	WHERE page_state=0 AND page_cat NOT LIKE 'system'
	AND	page_begin<'".$sys['now_offset']."' AND page_expire>'".$sys['now_offset']."'
	AND page_cat IN ('".implode("','", $catsub)."') ORDER BY page_".$sed_cat[$c]['order']." ".$sed_cat[$c]['way']." LIMIT $d,".$cfg['plugin']['news']['maxpages']);
	
	$sql2 = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=0 
	AND page_cat NOT LIKE 'system' 
	AND	page_begin<'".$sys['now_offset']."' AND page_expire>'".$sys['now_offset']."'
	AND page_cat IN ('".implode("','", $catsub)."')");
	
	$totalnews = sed_sql_result($sql2,0,"COUNT(*)");

	$perpage = $cfg['plugin']['news']['maxpages'];

	$pagnav = sed_pagination(sed_url('index', "c=$c"), $d, $totalnews, $perpage);
	list($pages_prev, $pages_next) = sed_pagination_pn(sed_url('index', "c=$c"), $d, $totalnews, $perpage, TRUE);

	// Extra field - getting
	$extrafields = array(); $number_of_extrafields = 0;
	$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='pages'");
	while($row = sed_sql_fetchassoc($fieldsres)) { $extrafields[] = $row; $number_of_extrafields++; }

	$news = new XTemplate(sed_skinfile('news'));

	while ($pag = sed_sql_fetcharray($sql))
	{
		$jj++;
		$catpath = sed_build_catpath($pag['page_cat'], "<a href=\"%1\$s\">%2\$s</a>");
		$pag['page_pageurl'] = (empty($pag['page_alias'])) ? sed_url('page', 'id='.$pag['page_id']) : sed_url('page', 'al='.$pag['page_alias']);
		$pag['page_fulltitle'] = $catpath." ".$cfg['separator']." <a href=\"".$pag['page_pageurl']."\">".sed_cc($pag['page_title'])."</a>";

		$submitnewpage = (sed_auth('page', $c, 'W')) ? "<a href=\"page.php?m=add&amp;c=$c\">".$L['lis_submitnew']."</a>" : '';

		$item_code = 'p'.$pag['page_id'];
		list($pag['page_comments'], $pag['page_comments_display']) = sed_build_comments($item_code, $pag['page_pageurl'], FALSE);

		$news-> assign(array(
		"PAGE_ROW_URL" => $pag['page_pageurl'],
		"PAGE_ROW_ID" => $pag['page_id'],
		"PAGE_ROW_TITLE" => $pag['page_fulltitle'],
		"PAGE_ROW_SHORTTITLE" => sed_cc($pag['page_title']),
		"PAGE_ROW_CAT" => $pag['page_cat'],
		"PAGE_ROW_CATTITLE" => sed_cc($sed_cat[$pag['page_cat']]['title']),
		"PAGE_ROW_CATPATH" => $catpath,
		"PAGE_ROW_CATDESC" => sed_cc($sed_cat[$pag['page_cat']]['desc']),
		"PAGE_ROW_CATICON" => $sed_cat[$pag['page_cat']]['icon'],
		"PAGE_ROW_KEY" => sed_cc($pag['page_key']),
		"PAGE_ROW_DESC" => sed_cc($pag['page_desc']),
		"PAGE_ROW_AUTHOR" => sed_cc($pag['page_author']),
		"PAGE_ROW_OWNER" => sed_build_user($pag['page_ownerid'], sed_cc($pag['user_name'])),
		"PAGE_ROW_AVATAR" => sed_build_userimage($pag['user_avatar'], 'avatar'),
		"PAGE_ROW_DATE" => @date($cfg['formatyearmonthday'], $pag['page_date'] + $usr['timezone'] * 3600),
		"PAGE_ROW_FILEURL" => $pag['page_url'],
		"PAGE_ROW_SIZE" => $pag['page_size'],
		"PAGE_ROW_COUNT" => $pag['page_count'],
		"PAGE_ROW_FILECOUNT" => $pag['page_filecount'],
		"NEWS_PAGENAV" => $pagnav,
		"NEWS_PAGEPREV" => $pages_prev,
		"NEWS_PAGENEXT" => $pages_next,
		"NEWS_SUBMITNEWPOST" => $submitnewpage,
		"PAGE_ROW_COMMENTS" => $pag['page_comments'],
		"PAGE_ROW_RATINGS" => "<img src=\"skins/".$usr['skin']."/img/system/vote".round($pag['rating_average'],0).".gif\" alt=\"\" />",
		"PAGE_ROW_ODDEVEN" => sed_build_oddeven($jj)
		));

		switch($pag['page_type'])
		{
			case '1':
				$news->assign("PAGE_ROW_TEXT", $pag['page_text']);
				break;

			case '2':

				if ($cfg['allowphp_pages'])
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
						$pag['page_html'] = sed_parse(sed_cc($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
						sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($pag['page_html'])."' WHERE page_id = " . $pag['page_id']);
					}
					$readmore = mb_strpos($pag['page_html'], "<!--more-->");
					if($readmore > 0)
					{
						$pag['page_html'] = mb_substr($pag['page_html'], 0, $readmore);
						$pag['page_html'] .= "<span class=\"more\"><a href=\"".$pag['page_pageurl']."\">".$L['ReadMore']."</a></span>";
					}

					$cfg['parsebbcodepages'] ? $news->assign('PAGE_ROW_TEXT', sed_post_parse($pag['page_html'], 'pages'))
					: $news->assign('PAGE_ROW_TEXT', sed_cc($pag['page_text']));
				}
				else
				{
					$readmore = mb_strpos($pag['page_text'], "[more]");
					$pag['page_text'] = sed_parse(sed_cc($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
					if ($readmore>0)
					{
						$pag['page_text'] = mb_substr($pag['page_text'], 0, $readmore);
						$pag['page_text'] .= "<span class=\"more\"><a href=\"".$pag['page_pageurl']."\">".$L['ReadMore']."</a></span>";
					}
					$pag['page_text'] = sed_post_parse($pag['page_text'], 'pages');
					$news->assign('PAGE_ROW_TEXT', $pag['page_text']);
				}
				break;
		}

		// Extra fields
		if($number_of_extrafields > 0) foreach($extrafields as $row) $news->assign('PAGE_ROW_'.strtoupper($row['field_name']), $pag['page_'.$row['field_name']]);

		$news->parse("NEWS.PAGE_ROW");
	}
	$news->parse("NEWS");
	$t->assign("INDEX_NEWS", $news->text("NEWS"));

}

?>
