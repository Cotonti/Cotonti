<?PHP
/**
 * RSS output.
 *
 * @package Cotonti
 * @version 0.6.7
 * @author medar, Cotonti Team
 * @copyright Copyright (c) 2009-2010 Cotonti Team
 * @license BSD License
 */

/*
Example of feeds:
rss.php?c=news (or other category)
rss.php?c=comments&id=XX
rss.php?c=forums
rss.php?c=topics&id=XX
rss.php?c=section&id=XX (this and all subsections)
*/

define('SED_CODE', TRUE);
$location = "RSS";

// TODO move this to config
$cfg_timetolive = 30; // refresh cache every N seconds
$cfg_maxitems = 40; // max items in rss
$cfg_charset = "UTF-8";

require_once ('./datas/config.php');
require_once ($cfg['system_dir'].'/functions.php');
//require_once ($cfg['system_dir'].'/database.'.$cfg['sqldb'].'.php');
require_once ($cfg['system_dir'].'/common.php');
//require_once ($cfg['system_dir'].'/lang/'.$cfg['defaultlang']."/main.lang.php");

$c = sed_import('c', 'G', 'ALP');
$id = sed_import('id', 'G', 'INT');
if ($c=="")	$c = "news";

header('Content-type: text/xml');
$sys['now'] = time();
$cache = sed_cache_get("rss_".$c.$id);
if ($cache)
{
	echo $cache; // output cache if avaiable
	exit();
}

$rss_title = $cfg['maintitle'];
$rss_link = $cfg['mainurl'];
$rss_description = $cfg['subtitle'];

$domain = str_replace("http://","",$cfg['mainurl']);

/* === Hook === */
$extp = sed_getextplugins('rss.create');
if (is_array($extp))
{
	foreach($extp as $k=>$pl)
	{
		include_once ($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

if ($c == "comments")
{
	// == Comments rss ==
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
			$page_args = empty($row['page_alias']) ? "id=$page_id" : 'al=' . $row['page_alias'];

			$sql = sed_sql_query("SELECT * FROM $db_com WHERE com_code='p$page_id' ORDER BY com_date DESC LIMIT ".$cfg_maxitems);
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
				$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('page', $page_args, '#c'.$row['com_id'], true);
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
elseif ($c == "topics")
{
	// == All posts of topic ==
	$topic_id = $id;

	// is topic private ?
	$sql = "SELECT * FROM $db_forum_topics WHERE ft_id='$topic_id'";
	$res = sed_sql_query($sql);
	if (sed_sql_affectedrows()>0)
	{
		$row = mysql_fetch_assoc($res);
		if ($row['ft_mode']=='1')
		exit(); // this topic is private

		$rss_title = $domain." : ".$row['ft_title'];
		$rss_description = $L['rss_topic_item_desc'];

		// check forum read permission for guests
		$forum_id = $row['ft_sectionid'];
		if(!sed_auth('forums', $forum_id, 'R' )) exit("not readable for guests");

		// get number of posts in topic
		$sql = "SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$topic_id'";
		$res = sed_sql_query($sql);
		$totalposts = sed_sql_result($res,0,"COUNT(*)");

		$sql = "SELECT * FROM $db_forum_posts WHERE fp_topicid='$topic_id' ORDER BY fp_creation DESC LIMIT $cfg_maxitems";
		$res = sed_sql_query($sql);
		$i = 0;
		while($row = mysql_fetch_assoc($res))
		{
			$totalposts--;
			$curpage = $cfg['maxtopicsperpage'] * floor($totalposts/$cfg['maxtopicsperpage']);

			$post_id = $row['fp_id'];
			$items[$i]['title'] = $row['fp_postername'];
			$items[$i]['description'] = $row['fp_html'];
			$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('forums', "m=posts&q=$topic_id&d=$curpage", "#post$post_id", true);
			$items[$i]['pubDate'] = date('r', $row['fp_creation']);
			$i++;
		}
	}
}
elseif ($c == "section")
{
	// == All posts of section ==
	$forum_id = $id;

	$sql = "SELECT * FROM $db_forum_sections WHERE fs_id = '$forum_id'";
	$res = sed_sql_query($sql);
	if (sed_sql_affectedrows()>0)
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
		while($row = mysql_fetch_assoc($res))
		{
			$where .= " OR fp_sectionid ='{$row['fs_id']}'";
		}

		$sql = "SELECT * FROM $db_forum_posts WHERE $where ORDER BY fp_creation DESC LIMIT $cfg_maxitems ";
		$res = sed_sql_query($sql);
		$i = 0;

		while($row = mysql_fetch_assoc($res))
		{
			$post_id = $row['fp_id'];
			$topic_id = $row['fp_topicid'];

			$flag_private = 0;
			$sql = "SELECT * FROM $db_forum_topics WHERE ft_id='$topic_id'";
			$res2 = sed_sql_query($sql);
			$row2 = mysql_fetch_assoc($res2);
			$topic_title = $row2['ft_title'];
			if ($row2['ft_mode']=='1')
			$flag_private = 1;

			if (!$flag_private AND sed_auth('forums', $forum_id, 'R'))
			{
				//$post_url = ($cfg['plugin']['search']['searchurls'] == 'Single') ? sed_url('forums', 'm=posts&id='.$post_id, "", true) : sed_url('forums', 'm=posts&p='.$post_id, '#'.$post_id, true);
				$post_url = sed_url('forums', 'm=posts&p='.$post_id, '#'.$post_id, true);
				$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
				$items[$i]['description'] = $row['fp_html'];
				$items[$i]['link'] = SED_ABSOLUTE_URL.$post_url;
				$items[$i]['pubDate'] = date('r', $row['fp_creation']);
			}
			$i++;
		}
	}
}
elseif ($c == "forums")
{
	// == All posts on forums ==
	$rss_title = $domain." : ".$L['rss_allforums_item_title'];
	$rss_description = "";

	$sql = "SELECT * FROM $db_forum_posts ORDER BY fp_creation DESC	LIMIT $cfg_maxitems ";
	$res = sed_sql_query($sql);
	$i = 0;
	while($row = mysql_fetch_assoc($res))
	{
		$post_id = $row['fp_id'];
		$topic_id = $row['fp_topicid'];
		$forum_id = $row['fp_sectionid'];

		$flag_private = 0;
		$sql = "SELECT * FROM $db_forum_topics WHERE ft_id='$topic_id'";
		$res2 = sed_sql_query($sql);
		$row2 = mysql_fetch_assoc($res2);
		$topic_title = $row2['ft_title'];
		if ($row2['ft_mode']=='1')
		$flag_private = 1;

		if (!$flag_private AND sed_auth('forums', $forum_id, 'R'))
		{
			$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
			$items[$i]['description'] = $row['fp_html'];
			$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('forums', "m=posts&p=$post_id", "#$post_id", true);
			//$items[$i]['link'] = $cfg['mainurl']."/forums.php?m=posts&p=$post_id";
			$items[$i]['pubDate'] = date('r', $row['fp_creation']);
		}
		$i++;
	}
}
else
{
	// == Category rss ==
	$mtch = $sed_cat[$c]['path'].".";
	$mtchlen = strlen($mtch);
	$catsub = array();
	$catsub[] = $c;

	foreach($sed_cat as $i => $x)
	{
		if(substr($x['path'], 0, $mtchlen)==$mtch) { $catsub[] = $i; }
	}

	$sql = sed_sql_query("SELECT page_id, page_type, page_title, page_text, page_cat, page_date FROM $db_pages WHERE page_state=0 AND page_cat NOT LIKE 'system' AND page_cat IN ('".implode("','", $catsub)."') ORDER by page_date DESC LIMIT ".$cfg_maxitems);
	$i = 0;
	while ($row = mysql_fetch_assoc($sql))
	{
        $row['page_pageurl'] = (empty($row['page_alias'])) ? sed_url('page', 'id='.$row['page_id']) : sed_url('page', 'al='.$row['page_alias']);

		$items[$i]['title'] = $row['page_title'];
		$items[$i]['link'] = SED_ABSOLUTE_URL . $row['page_pageurl'];
		$items[$i]['pubDate'] = date('r', $row['page_date']);
		$items[$i]['description'] = sed_parse_page_text($row['page_id'], $row['page_type'], $row['page_text'], $row['page_html'], $row['page_pageurl']);
	
		$i++;
	}
}

// RSS output
$out = "<?xml version='1.0' encoding='".$cfg_charset."'?>\n";
$out .= "<rss version='2.0'>\n";
$out .= "<channel>\n";
$out .= "<title>".$rss_title."</title>\n";
$out .= "<link>".$rss_link."</link>\n";
$out .= "<description>".$rss_description."</description>\n";
$out .= "<generator>Cotonti</generator>\n";
$out .= "<pubDate>".date("r", time())."</pubDate>\n";
if (count($items)>0)
{
	foreach($items as $item)
	{
		$out .= "<item>\n";
		$out .= "<title>".htmlspecialchars($item['title'])."</title>\n";
		$out .= "<description><![CDATA[".sed_convert_relative_urls($item['description'])."]]></description>\n";
		$out .= "<pubDate>".$item['pubDate']."</pubDate>\n";
		$out .= "<link><![CDATA[".$item['link']."]]></link>\n";
		$out .= "</item>\n";
	}
}
$out .= "</channel>\n";
$out .= "</rss>";

/* === Hook === */
$extp = sed_getextplugins('rss.output');
if (is_array($extp))
{
	foreach($extp as $k=>$pl)
	{
		include_once ($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

sed_cache_store("rss_".$c.$id, $out, $cfg_timetolive);
echo $out;

// ---------------------------------------------------------------------------------------------


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

function sed_relative2absolute($matches)
{
	global $sys;
	$res = $matches[1] . $matches[2] . '=' . $matches[3];
	if (preg_match('#^(http|https|ftp)://#', $matches[4]))
	{
		$res .= $matches[4];
	}
	else
	{
		if ($matches[4][0] == '/')
		{
			$scheme = $sys['secure'] ? 'https' : 'http';
			$res .= $scheme . '://' . $sys['host'] . $matches[4];
		}
		else
		{
			$res .= SED_ABSOLUTE_URL . $matches[4];
		}
	}
	$res .= $matches[5];
	return $res;
}

function sed_convert_relative_urls($text)
{
	$text = preg_replace_callback('#(\s)(href|src)=("|\')?([^"\'\s>]+)(["\'\s>])#', 'sed_relative2absolute', $text);
	return $text;
}

?>