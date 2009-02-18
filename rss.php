<?php
/**
 * RSS output.
 *
 * @package Cotonti
 * @version 0.0.3
 * @author medar
 * @copyright Copyright (c) 2009 Cotonti Team
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
require_once ($cfg['system_dir'].'/lang/'.$cfg['defaultlang']."/main.lang.php");

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

	$rss_title = "Comments for ".$cfg['maintitle'];

	$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$page_id' LIMIT 1");
	if (sed_sql_affectedrows()>0)
	{
		$row = mysql_fetch_assoc($sql);
		if(sed_auth('page', $row['page_cat'], 'R'))
		{
			$rss_title = $row['page_title'];
			$rss_description = $L['rss_comments_item_desc'];

			$sql = sed_sql_query("SELECT * FROM $db_com WHERE com_code='p$page_id' ORDER BY com_date DESC LIMIT $cfg_maxitems");
			$i = 0;
			while($row = mysql_fetch_assoc($sql))
			{
				$sql2 = sed_sql_query("SELECT * FROM $db_users WHERE user_id='".$row['com_authorid']."' LIMIT 1");
				$row2 = mysql_fetch_assoc($sql2);
				$items[$i]['title'] = $L['rss_comment']." ".$row2['user_name'];
				$text = sed_parse(sed_cc($row['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1);
				$text = sed_post_parse($text, 'pages');
				$items[$i]['description'] = $text;
				$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('page', "id=$page_id", '#c'.$row['com_id'], true);
				$items[$i]['pubDate'] = date('r', $row['com_date']);
				$i++;
			}
			// Attach original page text as last item
			$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$page_id' LIMIT 1");
			$row = mysql_fetch_assoc($sql);
			$items[$i]['title'] = $L['rss_original'];
			//$items[$i]['description'] = sed_parse_page_text($row['page_text']);
			$items[$i]['description'] = $row['page_html']; // TODO page_text parse
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
		$sql = "SELECT auth_rights FROM $db_auth WHERE auth_code='forums' AND auth_groupid='1' AND auth_option='$forum_id'";
		//echo $sql;
		$res = sed_sql_query($sql);
		$row = mysql_fetch_assoc($res);
		//if ($row['auth_rights']=='0') exit(); // forum not readable for guests
		
		if(!sed_auth('forum', $forum_id, 'R' )) exit("not readable for guests");
		
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

			$sql = "SELECT auth_rights FROM $db_auth WHERE auth_code='forums' AND auth_groupid='1' AND auth_option='$forum_id'";
			$res2 = sed_sql_query($sql);
			$row2 = mysql_fetch_assoc($res2);
			if ($row2['auth_rights']=='0')
			$flag_private = 1;

			if (!$flag_private)
			{
				$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
				$items[$i]['description'] = $row['fp_html'];
				$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('forums', "m=posts&id=$post_id", "", true);
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

		$sql = "SELECT auth_rights FROM $db_auth WHERE auth_code='forums' AND auth_groupid='1' AND auth_option='$forum_id'";
		$res2 = sed_sql_query($sql);
		$row2 = mysql_fetch_assoc($res2);
		if ($row2['auth_rights']=='0')
		$flag_private = 1;

		if (!$flag_private)
		{
			$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
			$items[$i]['description'] = $row['fp_html'];
			$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('forums', "m=posts&id=$post_id", "", true);
			//$items[$i]['link'] = $cfg['mainurl']."/forums.php?m=posts&id=$post_id";
			$items[$i]['pubDate'] = date('r', $row['fp_creation']);
		}
		$i++;
	}
}
else
{
	// == Category rss ==
	$res = sed_sql_query("SELECT * FROM $db_structure");
	$flag = 0;
	while($row = mysql_fetch_assoc($res))
	if ($c==$row['structure_code'])
	{
		$flag = 1;
		$category_path = $row['structure_path'];
	}

	if($flag!=0 AND sed_auth('page', $c, 'R'))
	{

		// found subcategories
		$where = "0";
		$sql = "SELECT * FROM $db_structure WHERE structure_path LIKE '%$category_path%'";
		$res = sed_sql_query($sql);
		while($row = mysql_fetch_assoc($res)) $where .= " OR page_cat = '".$row['structure_code']."'";

		$sql = "SELECT * FROM $db_pages WHERE ($where) AND page_state = '0' ORDER BY page_date DESC LIMIT $cfg_maxitems";
		$res = sed_sql_query($sql);
		$i = 0;
		while($pag = mysql_fetch_assoc($res))
		{
			$items[$i]['title'] = $pag['page_title'];
			$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('page', "id=".$pag['page_id'], '', true);
			//$items[$i]['link'] = $cfg['mainurl']."/page.php?id=".$pag['page_id'];
			$items[$i]['pubDate'] = date('r', $pag['page_date']);
			$items[$i]['description'] = sed_parse_page_text($pag);
			$i++;
		}
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
		$out .= "<description><![CDATA[".$item['description']."]]></description>\n";
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


function sed_parse_page_text($pag)
{
	global $cfg, $db_pages;
	switch($pag['page_type'])
	{
		case '1':
			$text = $pag['page_text'];
			break;
		case '2':
			if ($cfg['allowphp_pages']&&$cfg['allowphp_override'])
			{
				ob_start();
				eval($pag['page_text']);
				$text = ob_get_clean();
			}else
			{
				$text = "The PHP mode is disabled for pages.<br />Please see the administration panel, then \"Configuration\", then \"Parsers\".";
			}
			break;
		default:
			if ($cfg['parser_cache'])
			{
				if (empty($pag['page_html'])&&!empty($pag['page_text']))
				{
					$pag['page_html'] = sed_parse(sed_cc($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
					sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($pag['page_html'])."' WHERE page_id = ".$pag['page_id']);
				}
				$html = $cfg['parsebbcodepages'] ? sed_post_parse($pag['page_html']) : sed_cc($pag['page_text']);
				$text = $html;
			}else
			{
				$text = sed_parse(sed_cc($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
				$text = sed_post_parse($text, 'pages');
			}
			break;
	}
	return $text;
}

?>