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

if ($cfg['plugin']['news']['maxpages']>0 && !empty($cfg['plugin']['news']['category']) && !empty($sed_cat[$cfg['plugin']['news']['category']]['order']))
{
	$jj = 0;
	$mtch = $sed_cat[$cfg['plugin']['news']['category']]['path'].".";
	$mtchlen = strlen($mtch);
	$catsub = array();
	$catsub[] = $cfg['plugin']['news']['category'];

	foreach($sed_cat as $i => $x)
	{
		if (substr($x['path'], 0, $mtchlen)==$mtch && sed_auth('page', $i, 'R'))
		{ $catsub[] = $i; }
	}

	$sql = sed_sql_query("SELECT p.*, u.user_name, user_avatar FROM $db_pages AS p
	LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
	WHERE page_state=0 AND page_cat NOT LIKE 'system'
	AND	page_begin<'".$sys['now_offset']."' AND page_expire>'".$sys['now_offset']."'
	AND page_cat IN ('".implode("','", $catsub)."') ORDER BY page_".$sed_cat[$cfg['plugin']['news']['category']]['order']." ".$sed_cat[$cfg['plugin']['news']['category']]['way']." LIMIT ".$cfg['plugin']['news']['maxpages']);

	$news = new XTemplate(sed_skinfile('news'));

	while ($pag = sed_sql_fetcharray($sql))
	{
		$jj++;
		$catpath = sed_build_catpath($pag['page_cat'], "<a href=\"list.php?c=%1\$s\">%2\$s</a>");
		$pag['page_pageurl'] = (empty($pag['page_alias'])) ? "page.php?id=".$pag['page_id'] : "page.php?al=".$pag['page_alias'];
		$pag['page_fulltitle'] = $catpath." ".$cfg['separator']." <a href=\"".$pag['page_pageurl']."\">".$pag['page_title']."</a>";

		$item_code = 'p'.$pag['page_id'];
		list($pag['page_comments'], $pag['page_comments_display']) = sed_build_comments($item_code, $pag['page_pageurl'], FALSE);

		$news-> assign(array(
			"PAGE_ROW_URL" => $pag['page_pageurl'],
			"PAGE_ROW_ID" => $pag['page_id'],
			"PAGE_ROW_TITLE" => $pag['page_fulltitle'],
			"PAGE_ROW_SHORTTITLE" => $pag['page_title'],
			"PAGE_ROW_CAT" => $pag['page_cat'],
			"PAGE_ROW_CATTITLE" => $sed_cat[$pag['page_cat']]['title'],
			"PAGE_ROW_CATPATH" => $catpath,
			"PAGE_ROW_CATDESC" => $sed_cat[$pag['page_cat']]['desc'],
			"PAGE_ROW_CATICON" => $sed_cat[$pag['page_cat']]['icon'],
			"PAGE_ROW_KEY" => sed_cc($pag['page_key']),
			"PAGE_ROW_EXTRA1" => sed_cc($pag['page_extra1']),
			"PAGE_ROW_EXTRA2" => sed_cc($pag['page_extra2']),
			"PAGE_ROW_EXTRA3" => sed_cc($pag['page_extra3']),
			"PAGE_ROW_EXTRA4" => sed_cc($pag['page_extra4']),
			"PAGE_ROW_EXTRA5" => sed_cc($pag['page_extra5']),
			"PAGE_ROW_DESC" => sed_cc($pag['page_desc']),
			"PAGE_ROW_AUTHOR" => sed_cc($pag['page_author']),
			"PAGE_ROW_OWNER" => sed_build_user($pag['page_ownerid'], sed_cc($pag['user_name'])),
			"PAGE_ROW_AVATAR" => sed_build_userimage($pag['user_avatar']),
			"PAGE_ROW_DATE" => @date($cfg['formatyearmonthday'], $pag['page_date'] + $usr['timezone'] * 3600),
			"PAGE_ROW_FILEURL" => $pag['page_url'],
			"PAGE_ROW_SIZE" => $pag['page_size'],
			"PAGE_ROW_COUNT" => $pag['page_count'],
			"PAGE_ROW_FILECOUNT" => $pag['page_filecount'],
			"PAGE_ROW_COMMENTS" => $pag['page_comments'],
			"PAGE_ROW_RATINGS" => "<a href=\"".$pag['page_pageurl']."&amp;ratings=1\"><img src=\"skins/".$usr['skin']."/img/system/vote".round($pag['rating_average'],0).".gif\" alt=\"\" /></a>",
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
					$readmore = strpos($pag['page_html'], "[more]");
					if($readmore > 0)
					{
						$pag['page_html'] = substr($pag['page_html'], 0, $readmore)."<br />";
						$pag['page_html'] .= "<a href=\"".$pag['page_pageurl']."\">".$L['ReadMore']."</a>";
					}

					$cfg['parsebbcodepages'] ? $news->assign('PAGE_ROW_TEXT', sed_bbcode_parse($pag['page_html'], true))
					: $news->assign('PAGE_ROW_TEXT', $pag['page_html']);
				}
				else
				{
					$readmore = strpos($pag['page_text'], "[more]");
					if ($readmore>0)
					{
						$pag['page_text'] = substr($pag['page_text'], 0, $readmore)."<br />";
						$pag['page_text'] .= "<a href=\"".$pag['page_pageurl']."\">".$L['ReadMore']."</a>";
					}
					$news->assign("PAGE_ROW_TEXT",sed_parse(sed_cc($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1));
				}
				break;
		}
		$news->parse("NEWS.PAGE_ROW");
	}
	$news->parse("NEWS");
	$t->assign("INDEX_NEWS", $news->text("NEWS"));

}

?>
