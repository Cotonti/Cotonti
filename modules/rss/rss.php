<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * RSS module main
 *
 * @package rss
 * @version 0.9.1
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// Environment setup
define('COT_RSS', true);
$env['location'] = 'rss';

// Self requirements
require_once cot_langfile('rss', 'module');

// Input import
$c = cot_import('c', 'G', 'ALP');
$id = cot_import('id', 'G', 'ALP');
$c = empty($c) ? "pages" : $c;
$id = empty($id) ? "all" : $id;

ob_clean();
header('Content-type: text/xml; charset=UTF-8');
$sys['now'] = time();

if ($usr['id'] === 0 && $cache)
{
	$rss_cache = $cache->db->get($c . $id, 'rss');
	if ($rss_cache)
	{
		echo $rss_cache;
		exit;
	}
}

$rss_title = $cfg['maintitle'];
$rss_link = $cfg['mainurl'];
$rss_description = $cfg['subtitle'];

$domain = $sys['domain'];
$defult_c = true;

/* === Hook === */
foreach (cot_getextplugins('rss.create') as $pl)
{
	include $pl;
}
/* ===== */

if ($c == "topics")
{
	require_once cot_incfile('forums', 'module');

	$defult_c = false;
	$topic_id = ($id == 'all') ? 0 : (int) $id;

	$sql = "SELECT * FROM $db_forum_topics WHERE ft_id=$topic_id";
	$res = $db->query($sql);
	if ($db->affectedRows > 0)
	{
		$row = $res->fetch();
		if ($row['ft_mode'] == '1')
		{
			die('This topic is private'); // TODO: Need translate
		}

		$rss_title = $domain." : ".$row['ft_title'];
		$rss_description = $L['rss_topic_item_desc'];

		// check forum read permission for guests
		$forum_id = $row['ft_cat'];
		if (!cot_auth('forums', $forum_id, 'R'))
		{
			die('Not readable for guests'); // TODO: Need translate
		}

		// get number of posts in topic
		$sql = "SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid=$topic_id";
		$res = $db->query($sql);
		$totalposts = $res->fetchColumn();

		$sql = "SELECT * FROM $db_forum_posts WHERE fp_topicid=$topic_id ORDER BY fp_creation DESC LIMIT ".$cfg['rss']['rss_maxitems'];
		$res = $db->query($sql);
		$i = 0;
		while ($row = $res->fetch())
		{
			$totalposts--;
			$curpage = $cfg['forums']['maxtopicsperpage'] * floor($totalposts / $cfg['forums']['maxtopicsperpage']);

			$post_id = $row['fp_id'];
			$items[$i]['title'] = $row['fp_postername'];
			$items[$i]['description'] = cot_parse_post_text($post_id, $row['fp_text']);
			$items[$i]['link'] = COT_ABSOLUTE_URL.cot_url('forums', "m=posts&q=$topic_id&d=$curpage", "#post$post_id", true);
			$items[$i]['pubDate'] = cot_date('r', $row['fp_creation']);
			$i++;
		}
		$res->closeCursor();
	}
}
elseif ($c == "section")
{
	require_once cot_incfile('forums', 'module');

	$defult_c = false;
	$forum_id = ($id == 'all') ? 0 : $id;;

	if (isset($structure['forums'][$forum_id]))
	{
		$rss_title = $structure['forums'][$forum_id]['title'];
		$rss_description = $structure['forums'][$forum_id]['desc'];

		$all = cot_structure_children('forums', $forum_id);
		$where = "fp_cat IN ('".implode("', '", $all)."')";

		$sql = "SELECT * FROM $db_forum_posts WHERE $where ORDER BY fp_creation DESC LIMIT ".$cfg['rss']['rss_maxitems'];
		$res = $db->query($sql);
		$i = 0;

		foreach ($res->fetchAll() as $row)
		{
			$post_id = $row['fp_id'];
			$topic_id = $row['fp_topicid'];

			$flag_private = 0;
			$sql = "SELECT * FROM $db_forum_topics WHERE ft_id=$topic_id";
			$res2 = $db->query($sql);
			$row2 = $res2->fetch();
			$topic_title = $row2['ft_title'];
			if ($row2['ft_mode'] == '1')
			{
				$flag_private = 1;
			}

			if (!$flag_private && cot_auth('forums', $forum_id, 'R'))
			{
				//$post_url = ($cfg['plugin']['search']['searchurls'] == 'Single') ? cot_url('forums', 'm=posts&id='.$post_id, "", true) : cot_url('forums', 'm=posts&p='.$post_id, '#'.$post_id, true);
				$post_url = cot_url('forums', 'm=posts&p='.$post_id, '#'.$post_id, true);
				$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
				$items[$i]['description'] = cot_parse_post_text($post_id, $row['fp_text']);
				$items[$i]['link'] = COT_ABSOLUTE_URL.$post_url;
				$items[$i]['pubDate'] = cot_date('r', $row['fp_creation']);
			}

			$i++;
		}
	}
}
elseif ($c == "forums")
{
	require_once cot_incfile('forums', 'module');

	$defult_c = false;
	$rss_title = $domain." : ".$L['rss_allforums_item_title'];
	$rss_description = "";

	$sql = "SELECT * FROM $db_forum_posts ORDER BY fp_creation DESC LIMIT ".$cfg['rss']['rss_maxitems'];
	$res = $db->query($sql);
	$i = 0;
	foreach ($res->fetchAll() as $row)
	{
		$post_id = $row['fp_id'];
		$topic_id = $row['fp_topicid'];
		$forum_id = $row['fp_cat'];

		$flag_private = 0;
		$sql = "SELECT * FROM $db_forum_topics WHERE ft_id=$topic_id";
		$res2 = $db->query($sql);
		$row2 = $res2->fetch();
		$topic_title = $row2['ft_title'];
		if ($row2['ft_mode'] == '1')
		{
			$flag_private = 1;
		}

		if (!$flag_private && cot_auth('forums', $forum_id, 'R'))
		{
			$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
			$items[$i]['description'] = cot_parse_post_text($post_id, $row['fp_text']);
			$items[$i]['link'] = COT_ABSOLUTE_URL.cot_url('forums', "m=posts&p=$post_id", "#$post_id", true);
			$items[$i]['pubDate'] = cot_date('r', $row['fp_creation']);
		}

		$i++;
	}
}
elseif ($defult_c)
{
	require_once cot_incfile('page', 'module');

	if ($id != 'all')
	{
		$mtch = $structure['page'][$id]['path'].".";
		$mtchlen = mb_strlen($mtch);
		$catsub = array();
		$catsub[] = $id;

		foreach ($structure['page'] as $i => $x)
		{
			if (mb_substr($x['path'], 0, $mtchlen) == $mtch)
			{
				$catsub[] = $i;
			}
		}

		$sql = $db->query("SELECT p.*, u.* FROM $db_pages AS p
				LEFT JOIN $db_users AS u ON p.page_ownerid = u.user_id
			WHERE page_state=0 AND page_begin <= {$sys['now']} AND (page_expire = 0 OR page_expire > {$sys['now']}) AND page_cat NOT LIKE 'system' AND page_cat IN ('".implode("','", $catsub)."') 
			ORDER BY page_date DESC LIMIT ".$cfg['rss']['rss_maxitems']);
	}
	else
	{
		$sql = $db->query("SELECT p.*, u.* FROM $db_pages AS p
				LEFT JOIN $db_users AS u ON p.page_ownerid = u.user_id
			WHERE page_state=0 AND page_begin <= {$sys['now']} AND (page_expire = 0 OR page_expire > {$sys['now']}) AND page_cat NOT LIKE 'system'
			ORDER BY page_date DESC LIMIT ".$cfg['rss']['rss_maxitems']);
	}
	$i = 0;
	while ($row = $sql->fetch())
	{
		$row['page_pageurl'] = (empty($row['page_alias'])) ? cot_url('page', 'c='.$row['page_cat'].'&id='.$row['page_id'], '', true) : cot_url('page', 'c='.$row['page_cat'].'&al='.$row['page_alias'], '', true);

		$items[$i]['title'] = $row['page_title'];
		$items[$i]['link'] = COT_ABSOLUTE_URL . $row['page_pageurl'];
		$items[$i]['pubDate'] = cot_date('r', $row['page_date']);
		$items[$i]['description'] = cot_parse_page_text($row['page_id'], $row['page_type'], $row['page_text'], $row['page_pageurl'], $row['page_parser']);
		$items[$i]['fields'] = cot_generate_pagetags($row);

		$i++;
	}
	$sql->closeCursor();
}

$t = new XTemplate(cot_tplfile('rss'));
$t->assign(array(
	'RSS_ENCODING' => $cfg['rss']['rss_charset'],
	'RSS_TITLE' => htmlspecialchars($rss_title),
	'RSS_LINK' => $rss_link,
	'RSS_LANG' => $cfg['defaultlang'],
	'RSS_DESCRIPTION' => htmlspecialchars($rss_description),
	'RSS_DATE' => cot_fix_pubdate(cot_date("r"))
));

if (count($items) > 0)
{
	foreach ($items as $item)
	{
		$t->assign(array(
			'RSS_ROW_TITLE' => htmlspecialchars($item['title']),
			'RSS_ROW_DESCRIPTION' => cot_convert_relative_urls($item['description']),
			'RSS_ROW_DATE' => cot_fix_pubdate($item['pubDate']),
			'RSS_ROW_LINK' => $item['link'],
			'RSS_ROW_FIELDS' => $item['fields']
		));
		$t->parse('MAIN.ITEM_ROW');
	}
}

/* === Hook === */
foreach (cot_getextplugins('rss.output') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$out_rss = $t->text('MAIN');

if ($usr['id'] === 0 && $cache)
{
	$cache->db->store($c . $id, $out_rss, 'rss', $cfg['rss']['rss_timetolive']);
}
echo $out_rss;

function cot_parse_page_text($pag_id, $pag_type, $pag_text, $pag_pageurl, $pag_parser)
{
	global $db, $cfg, $db_pages, $usr;

	$pag_text = cot_parse($pag_text, $pag_parser !== 'none', $pag_parser);
	$readmore = mb_strpos($pag_text, "<!--more-->");
	if ($readmore > 0)
	{
		$pag_text = mb_substr($pag_text, 0, $readmore) . ' ';
		$pag_text .= cot_rc('list_link_more', array('page_url' => $pag_pageurl));
	}

	$newpage = mb_strpos($pag_text, '[newpage]');

	if ($newpage !== false)
	{
		$pag_text = mb_substr($pag_text, 0, $newpage);
	}

	$pag_text = preg_replace('#\[title\](.*?)\[/title\][\s\r\n]*(<br />)?#i', '', $pag_text);
	$text = $pag_text;
	if ((int)$cfg['rss']['rss_pagemaxsymbols'] > 0)
	{
		$text = cot_string_truncate($text, $cfg['rss']['rss_pagemaxsymbols']) . '...';
	}
	return $text;
}

function cot_parse_post_text($post_id, $post_text)
{
	global $db, $cfg, $db_forum_posts, $usr;

	$post_text = cot_parse($post_text, $cfg['forums']['markup']);

	if ((int)$cfg['rss']['rss_postmaxsymbols'] > 0)
	{
		$post_text = cot_string_truncate($post_text, $cfg['rss']['rss_postmaxsymbols']) . '...';
	}
	return $post_text;
}

function cot_relative2absolute($matches)
{
	global $sys;
	$res = $matches[1].$matches[2].'='.$matches[3];
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
			$res .= COT_ABSOLUTE_URL . $matches[4];
		}
	}
	$res .= $matches[5];
	return $res;
}

function cot_convert_relative_urls($text)
{
	$text = preg_replace_callback('#(\s)(href|src)=("|\')?([^"\'\s>]+)(["\'\s>])#', 'cot_relative2absolute', $text);
	return $text;
}

/**
 * Fixes timezone in RSS pubdate
 * @global array $usr
 * @param string $pubdate Pubdate generated with cot_date()
 * @return string Corrected pubdate
 */
function cot_fix_pubdate($pubdate)
{
	global $usr;
	$tz = floatval($usr['timezone']);
	$sign = $tz > 0 ? '+' : '-';
	$base = intval(abs($tz) * 100);
	$tz_str = $sign . str_pad($base, 4, '0', STR_PAD_LEFT);
	return str_replace('+0000', $tz_str, $pubdate);
}

?>
