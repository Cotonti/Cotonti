<?php
/**
 * RSS
 *
 * @package Cotonti
 * @version 0.7.0
 * @author medar, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

/*
Example of feeds:

rss.php?c=comments&id=XX		=== Show comments from page "XX" ===						=== Where XX - is code or alias of page ===

rss.php?c=comments				=== Show comments from all page ===

rss.php?c=topics&id=XX			=== Show posts from topic "XX" ===							=== Where XX - is code of topic ===

rss.php?c=section&id=XX 		=== Show posts from all topics of section "XX" ===			=== Where XX - is code of section (this and all subsections) forum ===

rss.php?c=forums				=== Show posts from all topics of all sections forum ===

rss.php?c=pages&id=XX			=== Show pages from category "XX" ===						=== Where XX - is code of category pages ===

rss.php
	OR rss.php?c=pages			=== Show pages from category "news" ===
*/

$c = sed_import('c', 'G', 'ALP');
$id = sed_import('id', 'G', 'ALP');
$c = empty($c) ? "pages" : $c;
$id = empty($id) ? "all" : $id;

header('Content-type: text/xml');
$sys['now'] = time();
$cache = sed_cache_get("sed_rss_".$c.$id);
if ($cache)
{
	echo $cache;
	exit();
}

$rss_title = $cfg['maintitle'];
$rss_link = $cfg['mainurl'];
$rss_description = $cfg['subtitle'];

$domain = str_replace("http://", "", $cfg['mainurl']);//type of domains may by "https" or not? and may be other format?

/* === Hook === */
$extp = sed_getextplugins('rss.create');
if (is_array($extp))
{
	foreach ($extp as $k=>$pl)
	{
		include_once ($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

if ($c == "comments")
{
	if ($id == 'all')
	{
		$rss_title = $L['rss_comments']." ".$cfg['maintitle'];
		$rss_description = $L['rss_comments_item_desc'];

		$sql = sed_sql_query("SELECT * FROM $db_com WHERE com_code LIKE 'p%' ORDER BY com_date DESC LIMIT ".$cfg['rss_maxitems']);
		$i = 0;
		while ($row = mysql_fetch_assoc($sql))
		{
			$sql2 = sed_sql_query("SELECT * FROM $db_users WHERE user_id='".$row['com_authorid']."' LIMIT 1");
			$row2 = mysql_fetch_assoc($sql2);
			$items[$i]['title'] = $L['rss_comment_of_user']." ".$row2['user_name'];
			if ($cfg['parser_cache'])
			{
				if (empty($row['com_html']) && !empty($row['com_text']))
				{
					$row['com_html'] = sed_parse(htmlspecialchars($row['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true);
					sed_sql_query("UPDATE $db_com SET com_html = '".sed_sql_prep($row['com_html'])."' WHERE com_id = ".$row['com_id']);
				}
				$text = $cfg['parsebbcodepages'] ? sed_post_parse($row['com_html']) : htmlspecialchars($row['com_text']);
			}
			else
			{
				$text = sed_parse(htmlspecialchars($row['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true);
				$text = sed_post_parse($com_text, 'pages');
			}
			$items[$i]['description'] = $text;
			$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('page', "id=".strtr($row['com_code'], 'p', ''), '#c'.$row['com_id'], true);
			$items[$i]['pubDate'] = date('r', $row['com_date']);
			$i++;
		}
	}
	else
	{
		$page_id = $id;

		$rss_title = $L['rss_comments']." ".$cfg['maintitle'];

		$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$page_id' LIMIT 1");
		if (sed_sql_affectedrows() > 0)
		{
			$row = mysql_fetch_assoc($sql);
			if (sed_auth('page', $row['page_cat'], 'R'))
			{
				$rss_title = $row['page_title'];
				$rss_description = $L['rss_comments_item_desc'];

				$sql = sed_sql_query("SELECT * FROM $db_com WHERE com_code='p$page_id' ORDER BY com_date DESC LIMIT ".$cfg['rss_maxitems']);
				$i = 0;
				while ($row1 = mysql_fetch_assoc($sql))
				{
					$sql2 = sed_sql_query("SELECT * FROM $db_users WHERE user_id='".$row1['com_authorid']."' LIMIT 1");
					$row2 = mysql_fetch_assoc($sql2);
					$items[$i]['title'] = $L['rss_comment_of_user']." ".$row2['user_name'];
					if ($cfg['parser_cache'])
					{
						if (empty($row1['com_html']) && !empty($row1['com_text']))
						{
							$row1['com_html'] = sed_parse(htmlspecialchars($row1['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true);
							sed_sql_query("UPDATE $db_com SET com_html = '".sed_sql_prep($row1['com_html'])."' WHERE com_id = ".$row1['com_id']);
						}
						$text = $cfg['parsebbcodepages'] ? sed_post_parse($row1['com_html']) : htmlspecialchars($row1['com_text']);
					}
					else
					{
						$text = sed_parse(htmlspecialchars($row1['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true);
						$text = sed_post_parse($com_text, 'pages');
					}
					$items[$i]['description'] = $text;
					$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('page', "id=$page_id", '#c'.$row['com_id'], true);
					$items[$i]['pubDate'] = date('r', $row['com_date']);
					$i++;
				}
				// Attach original page text as last item
		        $row['page_pageurl'] = (empty($row['page_alias'])) ? sed_url('page', 'id='.$row['page_id']) : sed_url('page', 'al='.$row['page_alias']);
				$items[$i]['title'] = $L['rss_original'];
				$items[$i]['description'] = sed_parse_page_text($row['page_id'], $row['page_type'], $row['page_text'], $row['page_html'], $row['page_pageurl']);
				$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('page', "id=$page_id", '', true);
				$items[$i]['pubDate'] = date('r', $row['page_date']);
			}
		}
	}
}
elseif ($c == "topics")
{
	$topic_id = ($id == 'all') ? 0 : $id;

	// is topic private ?
	$sql = "SELECT * FROM $db_forum_topics WHERE ft_id='$topic_id'";
	$res = sed_sql_query($sql);
	if (sed_sql_affectedrows() > 0)
	{
		$row = mysql_fetch_assoc($res);
		if ($row['ft_mode'] == '1')
		{
			exit("This topic is private");
		}

		$rss_title = $domain." : ".$row['ft_title'];
		$rss_description = $L['rss_topic_item_desc'];

		// check forum read permission for guests
		$forum_id = $row['ft_sectionid'];
		if (!sed_auth('forums', $forum_id, 'R'))
		{
			exit("Not readable for guests");
		}

		// get number of posts in topic
		$sql = "SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$topic_id'";
		$res = sed_sql_query($sql);
		$totalposts = sed_sql_result($res, 0, "COUNT(*)");

		$sql = "SELECT * FROM $db_forum_posts WHERE fp_topicid='$topic_id' ORDER BY fp_creation DESC LIMIT ".$cfg['rss_maxitems'];
		$res = sed_sql_query($sql);
		$i = 0;
		while ($row = mysql_fetch_assoc($res))
		{
			$totalposts--;
			$curpage = $cfg['maxtopicsperpage'] * floor($totalposts / $cfg['maxtopicsperpage']);

			$post_id = $row['fp_id'];
			$items[$i]['title'] = $row['fp_postername'];
			$items[$i]['description'] = sed_parse_post_text($post_id, $row['fp_text'], $row['fp_html']);
			$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('forums', "m=posts&q=$topic_id&d=$curpage", "#post$post_id", true);
			$items[$i]['pubDate'] = date('r', $row['fp_creation']);
			$i++;
		}
	}
}
elseif ($c == "section")
{
	$forum_id = ($id == 'all') ? 0 : $id;;

	$sql = "SELECT * FROM $db_forum_sections WHERE fs_id = '$forum_id'";
	$res = sed_sql_query($sql);
	if (sed_sql_affectedrows() > 0)
	{
		$row = mysql_fetch_assoc($res);
		$section_title = $row['fs_title'];
		$section_desc = $row['fs_desc'];
		$rss_title = $section_title;
		$rss_description = $section_desc;

		$where = "fp_sectionid = '$forum_id'";
		// get subsections
		unset($subsections);
		$sql = "SELECT fs_id FROM $db_forum_sections WHERE fs_mastername = '$section_title'";
		$res = sed_sql_query($sql);
		while ($row = mysql_fetch_assoc($res))
		{
			$where .= " OR fp_sectionid ='{$row['fs_id']}'";
		}

		$sql = "SELECT * FROM $db_forum_posts WHERE $where ORDER BY fp_creation DESC LIMIT ".$cfg['rss_maxitems'];
		$res = sed_sql_query($sql);
		$i = 0;

		while ($row = mysql_fetch_assoc($res))
		{
			$post_id = $row['fp_id'];
			$topic_id = $row['fp_topicid'];

			$flag_private = 0;
			$sql = "SELECT * FROM $db_forum_topics WHERE ft_id='$topic_id'";
			$res2 = sed_sql_query($sql);
			$row2 = mysql_fetch_assoc($res2);
			$topic_title = $row2['ft_title'];
			if ($row2['ft_mode'] == '1')
			{
				$flag_private = 1;
			}

			if (!$flag_private AND sed_auth('forums', $forum_id, 'R'))
			{
				//$post_url = ($cfg['plugin']['search']['searchurls'] == 'Single') ? sed_url('forums', 'm=posts&id='.$post_id, "", true) : sed_url('forums', 'm=posts&p='.$post_id, '#'.$post_id, true);
				$post_url = sed_url('forums', 'm=posts&p='.$post_id, '#'.$post_id, true);
				$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
				$items[$i]['description'] = sed_parse_post_text($post_id, $row['fp_text'], $row['fp_html']);
				$items[$i]['link'] = SED_ABSOLUTE_URL.$post_url;
				$items[$i]['pubDate'] = date('r', $row['fp_creation']);
			}

			$i++;
		}
	}
}
elseif ($c == "forums")
{
	$rss_title = $domain." : ".$L['rss_allforums_item_title'];
	$rss_description = "";

	$sql = "SELECT * FROM $db_forum_posts ORDER BY fp_creation DESC	LIMIT ".$cfg['rss_maxitems'];
	$res = sed_sql_query($sql);
	$i = 0;
	while ($row = mysql_fetch_assoc($res))
	{
		$post_id = $row['fp_id'];
		$topic_id = $row['fp_topicid'];
		$forum_id = $row['fp_sectionid'];

		$flag_private = 0;
		$sql = "SELECT * FROM $db_forum_topics WHERE ft_id='$topic_id'";
		$res2 = sed_sql_query($sql);
		$row2 = mysql_fetch_assoc($res2);
		$topic_title = $row2['ft_title'];
		if ($row2['ft_mode'] == '1')
		{
			$flag_private = 1;
		}

		if (!$flag_private AND sed_auth('forums', $forum_id, 'R'))
		{
			$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
			$items[$i]['description'] = sed_parse_post_text($post_id, $row['fp_text'], $row['fp_html']);
			$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('forums', "m=posts&p=$post_id", "#$post_id", true);
			$items[$i]['pubDate'] = date('r', $row['fp_creation']);
		}

		$i++;
	}
}
else
{
	if ($id != 'all')
	{
		$mtch = $sed_cat[$id]['path'].".";
		$mtchlen = strlen($mtch);
		$catsub = array();
		$catsub[] = $id;

		foreach ($sed_cat as $i => $x)
		{
			if (substr($x['path'], 0, $mtchlen) == $mtch)
			{
				$catsub[] = $i;
			}
		}

		$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_state=0 AND page_cat NOT LIKE 'system' AND page_cat IN ('".implode("','", $catsub)."') ORDER BY page_date DESC LIMIT ".$cfg['rss_maxitems']);
	}
	else
	{
		$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_state=0 AND page_cat NOT LIKE 'system' ORDER BY page_date DESC LIMIT ".$cfg['rss_maxitems']);
	}
	$i = 0;
	while ($row = mysql_fetch_assoc($sql))
	{
        $row['page_pageurl'] = (empty($row['page_alias'])) ? sed_url('page', 'id='.$row['page_id']) : sed_url('page', 'al='.$row['page_alias']);

		$items[$i]['title'] = $row['page_title'];
		$items[$i]['link'] = $row['page_pageurl'];
		$items[$i]['pubDate'] = date('r', $row['page_date']);
		$items[$i]['description'] = sed_parse_page_text($row['page_id'], $row['page_type'], $row['page_text'], $row['page_html'], $row['page_pageurl']);

		$i++;
	}
}

$t = new XTemplate(sed_skinfile('rss'));
$t -> assign(array(
	"RSS_ENCODING" => $cfg['rss_charset'],
	"RSS_TITLE" => $rss_title,
	"RSS_LINK" => $rss_link,
	"RSS_LANG" => $cfg['defaultlang'],
	"RSS_DESCRIPTION" => $rss_description,
	"RSS_DATE" => date("r", time())
));

if (count($items) > 0)
{
	foreach ($items as $item)
	{
		$t -> assign(array(
			"RSS_ROW_TITLE" => htmlspecialchars($item['title']),
			"RSS_ROW_DESCRIPTION" => $item['description'],
			"RSS_ROW_DATE" => $item['pubDate'],
			"RSS_ROW_LINK" => $item['link']
		));
		$t -> parse("MAIN.ITEM_ROW");
	}
}

/* === Hook === */
$extp = sed_getextplugins('rss.output');
if (is_array($extp))
{
	foreach ($extp as $k=>$pl)
	{
		include_once ($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

$t -> parse("MAIN");
$out_rss = $t -> out("MAIN");

sed_cache_store("sed_rss_".$c.$id, $out_rss, $cfg['rss_timetolive']);
echo $out_rss;

function sed_parse_page_text($pag_id, $pag_type, $pag_text, $pag_html, $pag_pageurl)
{
	global $cfg, $db_pages, $usr;
	switch($pag_type)
	{
		case '1':
			$text = $pag_text;
		break;

		case '2':
			if ($cfg['allowphp_pages'] && $cfg['allowphp_override'])
			{
				ob_start();
				eval($pag_text);
				$text = ob_get_clean();
			}
			else
			{
				$text = "The PHP mode is disabled for pages.<br />Please see the administration panel, then \"Configuration\", then \"Parsers\".";
			}
		break;

		default:
			if ($cfg['parser_cache'])
			{
				if (empty($pag_html))
				{
					$pag_html = sed_parse(htmlspecialchars($pag_text), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
					sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($pag_html)."' WHERE page_id = ".$pag_id);
				}
				$readmore = mb_strpos($pag_html, "<!--more-->");
				if ($readmore > 0)
				{
					$pag_html = mb_substr($pag_html, 0, $readmore);
					$pag_html .= " <span class=\"readmore\"><a href=\"".$pag_pageurl."\">".$L['ReadMore']."</a></span>";
				}

			    $newpage = mb_strpos($pag_html, '[newpage]');

			    if ($newpage !== false)
			    {
			        $pag_html = mb_substr($pag_html, 0, $newpage);
			    }

			    $pag_html = preg_replace('#\[title\](.*?)\[/title\][\s\r\n]*(<br />)?#i', '', $pag_html);

				$cfg['parsebbcodepages'] ? $text = sed_post_parse($pag_html, 'pages') : $text = htmlspecialchars($pag_text);
			}
			else
			{
				$pag_text = sed_parse(htmlspecialchars($pag_text), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
				$readmore = mb_strpos($pag_text, "<!--more-->");
				if ($readmore>0)
				{
					$pag_text = mb_substr($pag_text, 0, $readmore);
					$pag_text .= " <span class=\"readmore\"><a href=\"".$pag_pageurl."\">".$L['ReadMore']."</a></span>";
				}

			    $newpage = mb_strpos($pag_html, '[newpage]');

			    if ($newpage !== false)
			    {
			        $pag_html = mb_substr($pag_html, 0, $newpage);
			    }

			    $pag_html = preg_replace('#\[title\](.*?)\[/title\][\s\r\n]*(<br />)?#i', '', $pag_html);

				$pag_text = sed_post_parse($pag_text, 'pages');
				$text = $pag_text;
			}
		break;
	}
	return $text;
}

function sed_parse_post_text($post_id, $post_text, $post_html)
{
	global $cfg, $db_forum_posts, $usr, $fs_allowbbcodes, $fs_allowsmilies;
	if ($cfg['parser_cache'])
	{
		if (empty($post_html) && !empty($post_text))
		{
			$post_html = sed_parse($post_text, $cfg['parsebbcodeforums']  && $fs_allowbbcodes, $cfg['parsesmiliesforums']  && $fs_allowsmilies, 1);
			sed_sql_query("UPDATE $db_forum_posts SET fp_html = '".sed_sql_prep($post_html)."' WHERE fp_id = ".$post_id);
		}
		$post_text = sed_post_parse($post_html, 'forums');
	}
	else
	{
		$post_text = sed_parse($post_text, ($cfg['parsebbcodeforums'] && $fs_allowbbcodes), ($cfg['parsesmiliesforums'] && $fs_allowsmilies), 1);
		$post_text = sed_post_parse($post_text, 'forums');
	}
	return $post_text;
}

?>