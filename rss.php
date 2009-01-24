<?php
/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * RSS output.
 *
 * @package Seditio-N
 * @version 0.0.2
 * @author medar
 * @copyright Copyright (c) 2009 Cotonti Team
 * @license BSD License
 */

/*
Example of feeds:
rss.php?c=news - rss of pages in category "news" and subcategories
rss.php?c=comments23 - rss of comments by page with id=23 
rss.php?c=topic23 - rss of last posts of topic with id=23
rss.php?c=allforums - rss of last posts 
*/

define('SED_CODE', TRUE);
$location = "RSS";

// TODO move this to config
$cfg_timetolive = 60; // refresh every N seconds
$cfg_maxitems = 40; // max items in rss
$cfg_charset = "UTF-8";

require_once ('./datas/config.php');
require_once ($cfg['system_dir'].'/functions.php');
require_once ($cfg['system_dir'].'/database.'.$cfg['sqldb'].'.php');
require_once ($cfg['system_dir'].'/lang/'.$cfg['defaultlang']."/main.lang.php");

/* ======== Connect to the SQL DB======== */
$sed_dbc = sed_sql_connect($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword'], $cfg['mysqldb']);
unset($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword']);

/* ======== Configuration settings (from the DB) ======== */
$sql_config = sed_sql_query("SELECT config_owner, config_cat, config_name, config_value FROM $db_config");
while($row = sed_sql_fetcharray($sql_config))
{
	if ($row['config_owner']=='core')
	{
		$cfg[$row['config_name']] = $row['config_value'];
	}else
	{
		$cfg['plugin'][$row['config_cat']][$row['config_name']] = $row['config_value'];
	}
}
sed_bbcode_load();

$c = sed_import('c', 'G', 'ALP');
if ($c=="")	$c = "news";

header('Content-type: text/xml');
$sys['now'] = time();
$cache = sed_cache_get("rss_".$c);
if ($cache)
{
	echo $cache;
	exit();
} // output cache if avaiable 


$rss_title = $cfg['maintitle'];
$rss_link = $cfg['mainurl'];
$rss_description = $cfg['subtitle'];

$parseurl = parse_url($cfg['mainurl']);
$domain = $parseurl['host'];


/* === Hook === */
$extp = sed_getextplugins('rss');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if (strpos($c, "comments")!==false)
{
	
	// == Comments rss ==
	$rss_title = "Comments for ".$cfg['maintitle'];
	$page_id = intval(str_replace("comments", "", $c));
	
	$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$page_id' LIMIT 1");
	$row = mysql_fetch_assoc($sql);
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
		$items[$i]['link'] = $cfg['mainurl'].sed_url('pages', "id=$page_id#c".$row['com_id'], '', true);
		$items[$i]['pubDate'] = date('r', $row['com_date']);
		$i++;
	}
	// Attach original page text as last item
	$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$page_id' LIMIT 1");
	$row = mysql_fetch_assoc($sql);
	$items[$i]['title'] = $L['rss_original'];
	$items[$i]['description'] = sed_parse_page_text($row['page_text']);
	$items[$i]['link'] = $cfg['mainurl'].sed_url('pages', "id=$page_id", '', true);
	$items[$i]['pubDate'] = date('r', $row['page_date']);

}
elseif (strpos($c, "topic")!==false)
{
	
	// == All posts of topic == 
	$topic_id = intval(str_replace("topic", "", $c));
	
	// is topic private ?
	$sql = "SELECT * FROM $db_forum_topics WHERE ft_id='$topic_id'";
	$res = sed_sql_query($sql);
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
	if ($row['auth_rights']=='0')
		exit(); // forum not readable for guests
	

	$sql = "SELECT * FROM $db_forum_posts WHERE fp_topicid='$topic_id' ORDER BY fp_creation DESC LIMIT $cfg_maxitems";
	$res = sed_sql_query($sql);
	$i = 0;
	while($row = mysql_fetch_assoc($res))
	{
		$post_id = $row['fp_id'];
		$items[$i]['title'] = $row['fp_postername'];
		$items[$i]['description'] = $row['fp_html'];
		$items[$i]['link'] = $cfg['mainurl'].sed_url('forums', "m=posts&q=$post_id&n=last#bottom", '', true);
		$items[$i]['pubDate'] = date('r', $row['fp_creation']);
		$i++;
	}

}
elseif (strpos($c, "section")!==false)
{
	// == All posts of section ==
	$forum_id = intval(str_replace("section", "", $c));
	
	$sql = "SELECT * FROM $db_forum_sections WHERE fs_id = '$forum_id'";
	$res = sed_sql_query($sql); $row = mysql_fetch_assoc($res); 
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
		if ($row2['ft_mode']=='1') $flag_private = 1;
		
		$sql = "SELECT auth_rights FROM $db_auth WHERE auth_code='forums' AND auth_groupid='1' AND auth_option='$forum_id'";
		$res2 = sed_sql_query($sql);
		$row2 = mysql_fetch_assoc($res2);
		if ($row2['auth_rights']=='0') $flag_private = 1;
		
		if (!$flag_private)
		{
			$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
			$items[$i]['description'] = $row['fp_html'];
			$items[$i]['link'] = $cfg['mainurl'].sed_url('forums', "m=posts&q=$post_id&n=last#bottom", '', true);
			$items[$i]['pubDate'] = date('r', $row['fp_creation']);
		}
		$i++;
	}
	
}
elseif (strpos($c, "forum")!==false)
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
			$items[$i]['link'] = $cfg['mainurl'].sed_url('forums', "m=posts&q=$post_id&n=last#bottom", '', true);
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
	if ($flag==0)
		exit("requested category not found"); // requested category not found
	

	// found subcategories
	$where = "0";
	$sql = "SELECT * FROM $db_structure WHERE structure_path LIKE '%$category_path%'";
	$res = sed_sql_query($sql);
	while($row = mysql_fetch_assoc($res))
		$where .= " OR page_cat = '".$row['structure_code']."'";
	
	$sql = "SELECT * FROM $db_pages WHERE ($where) AND page_state = '0' ORDER BY page_date DESC LIMIT $cfg_maxitems";
	//echo $sql;
	$res = sed_sql_query($sql);
	$i = 0;
	while($pag = mysql_fetch_assoc($res))
	{
		$items[$i]['title'] = $pag['page_title'];
		$items[$i]['link'] = $cfg['mainurl'].sed_url('pages', "id=".$pag['page_id'], '', true);
		$items[$i]['pubDate'] = date('r', $pag['page_date']);
		$items[$i]['description'] = sed_parse_page_text($pag);
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
$out .= "<generator>Cotonti CMS</generator>\n";
$out .= "<pubDate>".date("r", time())."</pubDate>\n";
foreach($items as $item)
{
	$out .= "<item>\n";
	$out .= "<title>".htmlspecialchars($item['title'])."</title>\n";
	$out .= "<description><![CDATA[".$item['description']."]]></description>\n";
	$out .= "<pubDate>".$item['pubDate']."</pubDate>\n";
	$out .= "<link>".htmlspecialchars($item['link'])."</link>\n";
	$out .= "</item>\n";
}
$out .= "</channel>\n";
$out .= "</rss>";

sed_cache_store("rss_$c", $out, $cfg_timetolive);

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