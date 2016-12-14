<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * RSS module main
 *
 * @package RSS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

// Environment setup
define('COT_RSS', true);
$env['location'] = 'rss';

// Self requirements
require_once cot_langfile('rss', 'module');

// Input import
$m = cot_import('m', 'G', 'ALP');
$c = cot_import('c', 'G', 'TXT');
$m = empty($m) ? "pages" : $m;

ob_clean();
header('Content-type: text/xml; charset=UTF-8');
$sys['now'] = time();

if ($usr['id'] === 0 && $cache)
{
	$rss_cache = $cache->db->get($m . $c, 'rss');
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
$default_mode = true;

/* === Hook === */
foreach (cot_getextplugins('rss.create') as $pl)
{
	include $pl;
}
/* ===== */

if ($m == "topics")
{
	require_once cot_incfile('forums', 'module');

	$default_mode = false;
	$topic_id = empty($c) ? 0 : (int) $c;

	$sql = $db->query("SELECT * FROM $db_forum_topics WHERE ft_id = ?", $topic_id);
	if ($sql->rowCount > 0)
	{
		$row = $sql->fetch();
		if ($row['ft_mode'] == '1')
			die($L['rss_error_private']);

		$rss_title = $domain." : ".$row['ft_title'];
		$rss_description = $L['rss_topic_item_desc'];

		// check forum read permission for guests
		$forum_id = $row['ft_cat'];
		if (!cot_auth('forums', $forum_id, 'R'))
			die($L['rss_error_guests']);

		// get number of posts in topic
		$res = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid = ?", $topic_id);
		$totalposts = $res->fetchColumn();

		$sql = $db->query("SELECT * FROM $db_forum_posts WHERE fp_topicid = ? ORDER BY fp_creation DESC LIMIT ".$cfg['rss']['rss_maxitems'], $topic_id);

		/* === Hook === */
		foreach (cot_getextplugins('rss.topics.main') as $pl)
		{
			include $pl;
		}
		/* ===== */

		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('rss.topics.loop');
		/* ===== */

		$i = 0;
		while ($row = $sql->fetch())
		{
			$totalposts--;
			$curpage = $cfg['forums']['maxtopicsperpage'] * floor($totalposts / $cfg['forums']['maxtopicsperpage']);

			$post_id = $row['fp_id'];
			$items[$i]['title'] = $row['fp_postername'];
			$items[$i]['description'] = cot_parse_post_text($row['fp_text']);
			$url = cot_url('forums', "m=posts&q=$topic_id&d=$curpage", "#post$post_id", true);
			$items[$i]['link'] = (strpos($url, '://') === false) ? COT_ABSOLUTE_URL . $url : $url;
			$items[$i]['pubDate'] = cot_date('r', $row['fp_creation']);

			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			$i++;
		}
		$res->closeCursor();
	}
}
elseif ($m == "section")
{
	require_once cot_incfile('forums', 'module');

	$default_mode = false;
	$forum_cat = empty($c) ? 0 : $c;;

	if (isset($structure['forums'][$forum_cat]))
	{
		$rss_title = $structure['forums'][$forum_cat]['title'];
		$rss_description = $structure['forums'][$forum_cat]['desc'];

		$all = cot_structure_children('forums', $forum_cat);
		$where = "fp_cat IN ('".implode("', '", $all)."')";

		$sql = $db->query("SELECT * FROM $db_forum_posts WHERE $where ORDER BY fp_creation DESC LIMIT ".$cfg['rss']['rss_maxitems']);

		/* === Hook === */
		foreach (cot_getextplugins('rss.section.main') as $pl)
		{
			include $pl;
		}
		/* ===== */

		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('rss.section.loop');
		/* ===== */

		$i = 0;
		foreach ($sql->fetchAll() as $row)
		{
			$post_id = $row['fp_id'];
			$topic_id = $row['fp_topicid'];

			$flag_private = 0;

			$res2 = $db->query("SELECT * FROM $db_forum_topics WHERE ft_id = ?", $topic_id);
			$row2 = $res2->fetch();

			$topic_title = $row2['ft_title'];
			if ($row2['ft_mode'] == '1')
			{
				$flag_private = 1;
			}

			if (!$flag_private && cot_auth('forums', $forum_cat, 'R'))
			{
				//$post_url = ($cfg['plugin']['search']['searchurls'] == 'Single') ? cot_url('forums', 'm=posts&id='.$post_id, "", true) : cot_url('forums', 'm=posts&p='.$post_id, '#'.$post_id, true);
				$post_url = cot_url('forums', 'm=posts&p='.$post_id, '#'.$post_id, true);
				$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
				$items[$i]['description'] = cot_parse_post_text($row['fp_text']);
				$items[$i]['link'] = (strpos($post_url, '://') === false) ? COT_ABSOLUTE_URL . $post_url : $post_url;;
				$items[$i]['pubDate'] = cot_date('r', $row['fp_creation']);
			}

			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			$i++;
		}
	}
}
elseif ($m == "forums")
{
	require_once cot_incfile('forums', 'module');

	$default_mode = false;
	$rss_title = $domain." : ".$L['rss_allforums_item_title'];
	$rss_description = "";

	$sql = $db->query("SELECT * FROM $db_forum_posts ORDER BY fp_creation DESC LIMIT ".$cfg['rss']['rss_maxitems']);

	/* === Hook === */
	foreach (cot_getextplugins('rss.forums.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('rss.forums.loop');
	/* ===== */

	$i = 0;
	foreach ($sql->fetchAll() as $row)
	{
		$post_id = $row['fp_id'];
		$topic_id = $row['fp_topicid'];
		$forum_id = $row['fp_cat'];

		$flag_private = 0;

		$sql2 = $db->query("SELECT * FROM $db_forum_topics WHERE ft_id = ?", $topic_id);
		$row2 = $sql2->fetch();

		$topic_title = $row2['ft_title'];
		if ($row2['ft_mode'] == '1')
		{
			$flag_private = 1;
		}

		if (!$flag_private && cot_auth('forums', $forum_id, 'R'))
		{
			$items[$i]['title'] = $row['fp_postername']." - ".$topic_title;
			$items[$i]['description'] = cot_parse_post_text($row['fp_text']);
			$url = cot_url('forums', "m=posts&p=$post_id", "#$post_id", true);
			$items[$i]['link'] = (strpos($url, '://') === false) ? COT_ABSOLUTE_URL . $url : $url;
			$items[$i]['pubDate'] = cot_date('r', $row['fp_creation']);
		}

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$i++;
	}
}
elseif ($default_mode)
{
	require_once cot_incfile('page', 'module');

	if (!empty($c) && isset($structure['page'][$c]))
	{
		$mtch = $structure['page'][$c]['path'].".";
		$mtchlen = mb_strlen($mtch);
		$catsub = array();
		$catsub[] = $c;

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

	/* === Hook === */
	foreach (cot_getextplugins('rss.pages.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('rss.pages.loop');
	/* ===== */

	$i = 0;
	while ($row = $sql->fetch())
	{
		$url = (empty($row['page_alias'])) ? cot_url('page', 'c='.$row['page_cat'].'&id='.$row['page_id'], '', true) : cot_url('page', 'c='.$row['page_cat'].'&al='.$row['page_alias'], '', true);

		$rssDate = $row['page_date'];
		if(!empty(cot::$usr['timezone'])) $rssDate += cot::$usr['timezone'] * 3600;

		$items[$i]['title'] = $row['page_title'];
		$items[$i]['link'] = (strpos($url, '://') === false) ? COT_ABSOLUTE_URL . $url : $url;
		$items[$i]['pubDate'] = date('r', $rssDate);
		$items[$i]['description'] = cot_parse_page_text($row['page_text'], $url, $row['page_parser']);
		$items[$i]['fields'] = cot_generate_pagetags($row);

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$i++;
	}
	$sql->closeCursor();
}

$rssNow = cot::$sys['now'];
if(!empty(cot::$usr['timezone'])) $rssNow += cot::$usr['timezone'] * 3600;

$t = new XTemplate(cot_tplfile('rss'));
$t->assign(array(
	'RSS_ENCODING' => $cfg['rss']['rss_charset'],
	'RSS_TITLE' => htmlspecialchars($rss_title),
	'RSS_LINK' => $rss_link,
	'RSS_LANG' => $cfg['defaultlang'],
	'RSS_DESCRIPTION' => htmlspecialchars($rss_description),
	'RSS_DATE' => cot_fix_pubdate(date("r", $rssNow))
));

if (count($items) > 0)
{
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('rss.item.loop');
	/* ===== */
	foreach ($items as $item)
	{
		$t->assign(array(
			'RSS_ROW_TITLE' => htmlspecialchars($item['title']),
			'RSS_ROW_DESCRIPTION' => cot_convert_relative_urls($item['description']),
			'RSS_ROW_DATE' => cot_fix_pubdate($item['pubDate']),
			'RSS_ROW_LINK' => $item['link'],
			'RSS_ROW_FIELDS' => $item['fields']
		));

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

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
	$cache->db->store($m . $c, $out_rss, 'rss', $cfg['rss']['rss_timetolive']);
}
echo $out_rss;

function cot_parse_page_text($pag_text, $pag_pageurl, $pag_parser)
{
	global $cfg;

	$pag_text = cot_parse($pag_text, $pag_parser !== 'none', $pag_parser);
	$text_cut = cot_cut_more($pag_text);
	$cutted = (mb_strlen($pag_text) > mb_strlen($text_cut)) ? true : false;

	if($cutted) {
		$text_cut .= cot_rc('list_more', array('page_url' => $pag_pageurl));
	}

	if ((int)$cfg['rss']['rss_pagemaxsymbols'] > 0 ) {
		$text_cut = cot_string_truncate($text_cut, $cfg['rss']['rss_pagemaxsymbols'], true, false, '...');
	}

	return $text_cut;
}

function cot_parse_post_text($post_text)
{
	global $cfg;

	$post_text = cot_parse($post_text, $cfg['forums']['markup']);

	if ((int)$cfg['rss']['rss_postmaxsymbols'] > 0)
	{
		$post_text = cot_string_truncate($post_text, $cfg['rss']['rss_postmaxsymbols'], true, false, '...');
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
