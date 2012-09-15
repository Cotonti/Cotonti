<?php

/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags
Tags=index.tpl:{INDEX_NEWS}
[END_COT_EXT]
==================== */

/**
 * Pick up pages from a category and display the newest in the home page
 *
 * @package news
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');
require_once cot_langfile('news', 'plug');

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['plugin']['news']['maxpages']);
$c = cot_import('c', 'G', 'TXT');
$c = (!isset($structure['page'][$c])) ? '' : $c;

$categories = explode(',', $cfg['plugin']['news']['category']);
$jj = 0;
$cats = array();
foreach ($categories as $v)
{
	$v = explode('|', trim($v));
	if (isset($structure['page'][$v[0]]))
	{
		$c = (empty($c)) ? $v[0] : $c;
		$indexcat = ($jj == 0) ? $v[0] : $indexcat;

		$v[2] = ((int)$v[2] > 0) ? $v[2] : (int)$cfg['page']['cat_' . $v[0]]['truncatetext'];
		$v[1] = ((int)$v[1] > 0) ? $v[1] : (int)$cfg['plugin']['news']['maxpages'];

		$_GET[$v[0].'d'] = (empty($c) || ($jj == 0) || $cfg['plugin']['news']['syncpagination']) ? $_GET['d'] : $_GET[$v[0].'d'];
		list($v[3]['pg'], $v[3]['d'], $v[3]['durl']) = cot_import_pagenav($v[0] . 'd', $v[1]);

		$cats[$v[0]] = $v;
		$jj++;
	}
}

if (count($cats) > 0)
{
	/* === Hook - Part1 : Set === FIRST === */
	$news_first_extp = cot_getextplugins('news.first');
	/* === Hook - Part1 : Set === LOOP === */
	$news_extp = cot_getextplugins('news.loop');
	/* === Hook - Part1 : Set === TAGS === */
	$news_tags_extp = cot_getextplugins('news.tags');
	/* ===== */
	$catn = 0;
	foreach ($cats as $k => $v)
	{
		$cat = ($catn == 0) ? $c : $v[0];
		$tagname = str_replace(array(' ', ',', '.', '-'), '_', strtoupper($v[0]));

		// Cache for guests
		if ($usr['id'] == 0 && $cache && (int) $cfg['plugin']['news']['cache_ttl'] > 0)
		{
			$news_cache_id = "$theme.$lang.$cat." . $v[3]['d']; // Includes theme, lang, category and current page
			$news_html = $cache->disk->get($news_cache_id, 'news', (int) $cfg['plugin']['news']['cache_ttl']);
			if (!is_null($news_html))
			{
				$t->assign(($catn == 0) ? 'INDEX_NEWS' : 'INDEX_NEWS_' . $tagname, $news_html);
				continue;
			}
		}

		$catsub = cot_structure_children('page', $cat);
		$where = "page_state = 0 AND page_cat <> 'system' AND page_begin <= {$sys['now']} AND (page_expire = 0 OR page_expire > {$sys['now']}) AND page_cat IN ('" . implode("','", $catsub) . "')";

		$news_link_params = ($c != $indexcat) ? "c=" . $c : '';
		$news_join_columns = '';
		$news_join_tables = '';
		/* === Hook - Part2 : Include === FIRST === */
		foreach ($news_first_extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$sql = $db->query("SELECT p.*, u.* $news_join_columns
			FROM $db_pages AS p LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid $news_join_tables
			WHERE $where ORDER BY page_date DESC LIMIT " . $v[3]['d'] . ", " . $v[1]);
		$totalnews = $db->query("SELECT COUNT(*)
			FROM $db_pages AS p $news_join_tables WHERE " . $where)->fetchColumn();

		if (!$cfg['plugin']['news']['syncpagination'])
		{
			$news_link_params .= ($catn != 0 && $d != 0) ? '&d=' . $durl : '';
			$xx = 0;
			foreach ($cats as $key => $var)
			{
				$news_link_params .= (($key != $cat) && $var[3] != 0 && $xx != 0) ? "&" . $key . "d=" . $var[3]['durl'] : '';
				$xx++;
			}
		}

		$news_link = cot_url('index', $news_link_params);
		$catd = ($catn != 0 && !$cfg['plugin']['news']['syncpagination']) ? $cat . "d" : "d";
		$pagenav = cot_pagenav('index', $news_link_params, $v[3]['d'], $totalnews, $v[1], $catd);
		$filename =  str_replace(array(' ', ',', '.', '-'), '_', $v[0]);
		$news = new XTemplate(cot_tplfile(($catn == 0) ? "news" : "news." . $filename, 'plug'));

		$sql_rowset = $sql->fetchAll();
		$jj = 0;
		foreach ($sql_rowset as $pag)
		{
			$jj++;
			$url = cot_url('index', 'c=' . $pag['page_cat']);
			$news->assign(cot_generate_pagetags($pag, 'PAGE_ROW_', $v[2]));
			$news->assign(array(
				'PAGE_ROW_NEWSPATH' => cot_rc_link($url, htmlspecialchars($structure['page'][$row['page_cat']]['title'])),
				'PAGE_ROW_NEWSPATH_URL' => $url,
				'PAGE_ROW_CATDESC' => htmlspecialchars($structure['page'][$pag['page_cat']]['desc']),
				'PAGE_ROW_OWNER' => cot_build_user($pag['page_ownerid'], htmlspecialchars($pag['user_name'])),
				'PAGE_ROW_ODDEVEN' => cot_build_oddeven($jj),
				'PAGE_ROW_NUM' => $jj
			));
			$news->assign(cot_generate_usertags($pag, 'PAGE_ROW_OWNER_'));

			/* === Hook - Part2 : Include === LOOP === */
			foreach ($news_extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			$news->parse('NEWS.PAGE_ROW');
		}

		$url_newpage = cot_url('page', 'm=add&c=' . $cat);
		$news->assign(array(
			'PAGE_PAGENAV' => $pagenav['main'],
			'PAGE_PAGEPREV' => $pagenav['prev'],
			'PAGE_PAGENEXT' => $pagenav['next'],
			'PAGE_PAGELAST' => $pagenav['last'],
			'PAGE_PAGENUM' => $pagenav['current'],
			'PAGE_PAGECOUNT' => $pagenav['total'],
			'PAGE_ENTRIES_ONPAGE' => $pagenav['onpage'],
			'PAGE_ENTRIES_TOTAL' => $pagenav['entries'],
			'PAGE_SUBMITNEWPOST' => (cot_auth('page', $cat, 'W')) ? cot_rc_link($url_newpage, $L['Submitnew']) : '',
			'PAGE_SUBMITNEWPOST_URL' => (cot_auth('page', $cat, 'W')) ? $url_newpage : '',
			'PAGE_CATTITLE' => $structure['page'][$cat]['title'],
			'PAGE_CATPATH' => cot_breadcrumbs(cot_structure_buildpath('page', $cat), false),
			'PAGE_CAT' => $cat
		));

		/* === Hook - Part2 : Include === TAGS === */
		foreach ($news_tags_extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$news->parse('NEWS');
		$news_html = $news->text('NEWS');
		// Cache for guests
		if ($usr['id'] == 0 && $cache && (int) $cfg['plugin']['news']['cache_ttl'] > 0)
		{
			$cache->disk->store($news_cache_id, $news_html, 'news');
		}
		$t->assign(($catn == 0) ? 'INDEX_NEWS' : 'INDEX_NEWS_' . $tagname, $news_html);
		$catn++;
	}
}
?>