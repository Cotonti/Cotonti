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
$m = empty($m) ? 'pages' : $m;

ob_clean();
header('Content-type: text/xml; charset=UTF-8');
Cot::$sys['now'] = time();

if (Cot::$usr['id'] === 0 && Cot::$cache) {
	$rssCache = Cot::$cache->db->get($m . $c, 'rss');
	if ($rssCache) {
		echo $rssCache;
		exit;
	}
}

$rss_title = Cot::$cfg['maintitle'];
$rss_link = Cot::$cfg['mainurl'];
$rss_description = Cot::$cfg['subtitle'];

$domain = Cot::$sys['domain'];
$default_mode = true;

/* === Hook === */
foreach (cot_getextplugins('rss.create') as $pl) {
	include $pl;
}
/* ===== */

if ($m === 'topics') {
	require_once cot_incfile('forums', 'module');

	$default_mode = false;
	$topicId = !empty($c) ? (int) $c : 0;

    if ($topicId < 1) {
        cot_die_message(404);
    }

    $postsTable = Cot::$db->forum_posts;
    $topicsTable = Cot::$db->forum_topics;

    $topic = Cot::$db->query("SELECT * FROM $topicsTable WHERE ft_id = ? AND ft_mode = " . COT_FORUMS_TOPIC_MODE_NORMAL, $topicId)->fetch();
    if (!$topic) {
        cot_die_message(404);
    }

    // check forum read permission for guests
    if (!cot_auth('forums', $topic['ft_cat'], 'R')) {
        //die($L['rss_error_guests']);
        cot_die_message(404);
    }

    $rss_title = $domain . " : " . $topic['ft_title'];
    $rss_description = Cot::$L['rss_topic_item_desc'];

    // get number of posts in topic
    $res = Cot::$db->query("SELECT COUNT(*) FROM $postsTable WHERE fp_topicid = ?", $topicId);
    $totalPosts = $res->fetchColumn();

    $sql = Cot::$db->query(
        "SELECT * FROM $postsTable WHERE fp_topicid = ? ORDER BY fp_creation DESC LIMIT " . Cot::$cfg['rss']['rss_maxitems'],
        $topicId
    );

    /* === Hook === */
    foreach (cot_getextplugins('rss.topics.main') as $pl) {
        include $pl;
    }
    /* ===== */

    /* === Hook - Part1 : Set === */
    $extp = cot_getextplugins('rss.topics.loop');
    /* ===== */

    $i = 0;
    while ($row = $sql->fetch()) {
        $totalPosts--;
        $curpage = Cot::$cfg['forums']['maxtopicsperpage'] * floor($totalPosts / Cot::$cfg['forums']['maxtopicsperpage']);

        $post_id = $row['fp_id'];
        $items[$i]['title'] = $row['fp_postername'];
        $items[$i]['description'] = cot_parse_post_text($row['fp_text']);
        $url = cot_url('forums', ['m' => 'posts', 'q' => $topicId, 'd' => $curpage], "#post$post_id", true);
        $items[$i]['link'] = (strpos($url, '://') === false) ? COT_ABSOLUTE_URL . $url : $url;
        $items[$i]['pubDate'] = cot_date('r', $row['fp_creation']);

        /* === Hook - Part2 : Include === */
        foreach ($extp as $pl) {
            include $pl;
        }
        /* ===== */

        $i++;
    }
    $res->closeCursor();
} elseif ($m === 'section') {
	require_once cot_incfile('forums', 'module');

	$default_mode = false;
	$forumCategory = !empty($c)  ? $c : null;

    if ($forumCategory === null || !isset($structure['forums'][$forumCategory]) || !cot_auth('forums', $forumCategory, 'R')) {
        cot_die_message(404);
    }

    $rss_title = Cot::$structure['forums'][$forumCategory]['title'];
    $rss_description = Cot::$structure['forums'][$forumCategory]['desc'];

    $all = cot_structure_children('forums', $forumCategory);
    if (!$all) {
        cot_die_message(404);
    }
    $where['category'] = "fp_cat IN ('" . implode("', '", $all) . "')";

    $postsTable = Cot::$db->forum_posts;
    $topicsTable = Cot::$db->forum_topics;

    $join['topics'] = "LEFT JOIN $topicsTable  ON {$postsTable}.fp_topicid = {$topicsTable}.ft_id";
    $where['notPrivate'] = "({$topicsTable}.ft_mode = " . COT_FORUMS_TOPIC_MODE_NORMAL . ')';

    $sqlJoin = !empty($join) ? "\n" . implode ("\n", $join) : '';

    $sql = "SELECT {$postsTable}.*, {$topicsTable}.ft_title FROM $postsTable $sqlJoin WHERE " . implode(' AND ', $where)
        . ' ORDER BY fp_creation DESC LIMIT ' . Cot::$cfg['rss']['rss_maxitems'];

    $query = Cot::$db->query($sql);

    /* === Hook === */
    foreach (cot_getextplugins('rss.section.main') as $pl) {
        include $pl;
    }
    /* ===== */

    /* === Hook - Part1 : Set === */
    $extp = cot_getextplugins('rss.section.loop');
    /* ===== */

    $i = 0;
    while ($row = $query->fetch()) {
        $post_id = $row['fp_id'];
        $topicId = $row['fp_topicid'];

        $post_url = cot_url('forums', ['m' => 'posts', 'p' => $post_id], '#'.$post_id, true);
        $items[$i]['title'] = $row['fp_postername'] . ' - ' . $row['ft_title'];
        $items[$i]['description'] = cot_parse_post_text($row['fp_text']);
        $items[$i]['link'] = (strpos($post_url, '://') === false) ? COT_ABSOLUTE_URL . $post_url : $post_url;;
        $items[$i]['pubDate'] = cot_date('r', $row['fp_creation']);

        /* === Hook - Part2 : Include === */
        foreach ($extp as $pl) {
            include $pl;
        }
        /* ===== */

        $i++;
    }
    $query->closeCursor();

} elseif ($m === 'forums') {
	require_once cot_incfile('forums', 'module');

    $forumAuthCats = cot_authCategories('forums');

	$default_mode = false;
	$rss_title = $domain . ' : ' . Cot::$L['rss_allforums_item_title'];
	$rss_description = '';

    $postsTable = Cot::$db->forum_posts;
    $topicsTable = Cot::$db->forum_topics;

    $where = [];

    // If user can't read all categories
    if (!$forumAuthCats['readAll']) {
        if (empty($forumAuthCats['read'])) {
            $where['category'] = 'FALSE';
        } else {
            $forumCategories = array_map(function ($value) {return Cot::$db->quote($value);}, $forumAuthCats['read']);
            $where['category'] = 't.ft_cat IN (' . implode(', ', $forumCategories) . ')';
        }
    }

    $join['topics'] = "LEFT JOIN $topicsTable AS t ON p.fp_topicid = t.ft_id";
    $where['notPrivate'] = '(t.ft_mode = ' . COT_FORUMS_TOPIC_MODE_NORMAL . ')';

    /* === Hook === */
    foreach (cot_getextplugins('rss.forums.query') as $pl) {
        include $pl;
    }
    /* ===== */

    $sqlJoin = !empty($join) ? "\n" . implode ("\n", $join) : '';

    $sql = "SELECT p.*, t.ft_title FROM $postsTable AS p $sqlJoin WHERE " . implode(' AND ', $where)
        . ' ORDER BY fp_creation DESC LIMIT ' . Cot::$cfg['rss']['rss_maxitems'];

    $posts = Cot::$db->query($sql)->fetchAll();

	/* === Hook === */
	foreach (cot_getextplugins('rss.forums.main') as $pl) {
		include $pl;
	}
	/* ===== */

	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('rss.forums.loop');
	/* ===== */

	$i = 0;
	foreach ($posts as $row) {
		$post_id = $row['fp_id'];
		$topicId = $row['fp_topicid'];
		$forum_id = $row['fp_cat'];

        $items[$i]['title'] = $row['fp_postername'] . ' - ' . $row['ft_title'];
        $items[$i]['description'] = cot_parse_post_text($row['fp_text']);
        $url = cot_url('forums', ['m' => 'posts', 'p' => $post_id], "#$post_id", true);
        $items[$i]['link'] = (strpos($url, '://') === false) ? COT_ABSOLUTE_URL . $url : $url;
        $items[$i]['pubDate'] = cot_date('r', $row['fp_creation']);

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl) {
			include $pl;
		}
		/* ===== */

		$i++;
	}
} elseif ($default_mode) {
	require_once cot_incfile('page', 'module');

    if (!empty($c) && !isset(Cot::$structure['page'][$c])) {
        cot_die_message(404);
    }

    $authCats = cot_authCategories('page');

    $where = [];

    if (isset(Cot::$structure['page']['system'])) {
        $systemCategories = cot_structure_children('page', 'system', true, true, false, false);
        if (!empty($c) && in_array($c, $systemCategories, true)) {
            cot_die_message(404);
        }
        $systemCategories = array_map(function ($value) {return Cot::$db->quote($value);}, $systemCategories);
        $where['excludeSystemCategories'] = 'p.page_cat NOT IN (' . implode(', ', $systemCategories) . ')';
    }

    $categories = [];
    if (empty($authCats['read'])) {
        $where['categories'] = 'FALSE';
    } elseif (!empty($c)) {
        $categories = cot_structure_children('page', $c, true, true, true, false);
        $categories = array_intersect($categories, $authCats['read']);
        if (empty($categories)) {
            $where['categories'] = 'FALSE';
        }
    } else {
        // If user can't read all categories
        if (!$authCats['readAll']) {
            $categories = $authCats['read'];
        }
    }

    if (!empty($categories)) {
        $categories = array_map(function ($value) {return Cot::$db->quote($value);}, $categories);
        $where['categories'] = 'p.page_cat IN (' . implode(', ', $categories) . ')';
    }

    $where['state'] = 'p.page_state = ' . COT_PAGE_STATE_PUBLISHED;
    $where['date'] = 'p.page_begin <= ' . Cot::$sys['now'] . ' AND (p.page_expire = 0 OR p.page_expire > ' . Cot::$sys['now'] . ')';

    $sqlWhere = implode(" \nAND ", $where);

    /* === Hook === */
    foreach (cot_getextplugins('rss.pages.query') as $pl) {
        include $pl;
    }
    /* ===== */

    $sql = 'SELECT p.*, u.* FROM ' . Cot::$db->pages . ' AS p '
			. 'LEFT JOIN ' . Cot::$db->users . ' AS u ON p.page_ownerid = u.user_id '
			. "WHERE $sqlWhere ORDER BY p.page_date DESC LIMIT " . Cot::$cfg['rss']['rss_maxitems'];


    $query = Cot::$db->query($sql);

	/* === Hook === */
	foreach (cot_getextplugins('rss.pages.main') as $pl) {
		include $pl;
	}
	/* ===== */

	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('rss.pages.loop');
	/* ===== */

	$i = 0;
	while ($row = $query->fetch()) {
        $url = cot_page_url($row, [], '', true);
        $rssDate = $row['page_date'];
		if (!empty(Cot::$usr['timezone'])) {
            $rssDate += Cot::$usr['timezone'] * 3600;
        }
		$items[$i]['title'] = $row['page_title'];
		$items[$i]['link'] = (strpos($url, '://') === false) ? COT_ABSOLUTE_URL . $url : $url;
		$items[$i]['pubDate'] = date('r', $rssDate);
		$items[$i]['description'] = cot_parse_page_text($row['page_text'], $url, $row['page_parser']);
		$items[$i]['fields'] = cot_generate_pagetags($row);

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl) {
			include $pl;
		}
		/* ===== */

		$i++;
	}
    $query->closeCursor();
}

$rssNow = Cot::$sys['now'];
if (!empty(Cot::$usr['timezone'])) {
    $rssNow += Cot::$usr['timezone'] * 3600;
}

$t = new XTemplate(cot_tplfile('rss'));
$t->assign([
	'RSS_ENCODING' => Cot::$cfg['rss']['rss_charset'],
	'RSS_TITLE' => htmlspecialchars($rss_title),
	'RSS_LINK' => $rss_link,
	'RSS_LANG' => Cot::$cfg['defaultlang'],
	'RSS_DESCRIPTION' => htmlspecialchars($rss_description),
	'RSS_DATE' => cot_fix_pubdate(date('r', $rssNow)),
]);

if (!empty($items)) {
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('rss.item.loop');
	/* ===== */

	foreach ($items as $item) {
		$t->assign([
			'RSS_ROW_TITLE' => htmlspecialchars($item['title']),
			'RSS_ROW_DESCRIPTION' => cot_convert_relative_urls($item['description']),
			'RSS_ROW_DATE' => cot_fix_pubdate($item['pubDate']),
			'RSS_ROW_LINK' => $item['link'],
			'RSS_ROW_FIELDS' => isset($item['fields']) ? $item['fields'] : '',
		]);

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl) {
			include $pl;
		}
		/* ===== */

		$t->parse('MAIN.ITEM_ROW');
	}
}

/* === Hook === */
foreach (cot_getextplugins('rss.output') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$out_rss = $t->text('MAIN');

if (Cot::$usr['id'] === 0 && Cot::$cache) {
    Cot::$cache->db->store($m . $c, $out_rss, 'rss', Cot::$cfg['rss']['rss_timetolive']);
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
