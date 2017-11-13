<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Tag search
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

$a = cot_import('a', 'G', 'ALP');
$a = empty($a) ? 'all' : $a;
$qs = cot_import('t', 'G', 'TXT');
if(empty($qs)) $qs = cot_import('t', 'P', 'TXT');
$qs = str_replace('-', ' ', $qs);

$tl = cot_import('tl', 'G', 'BOL');
if ($tl && file_exists(cot_langfile('translit', 'core')))
{
	include_once cot_langfile('translit', 'core');
	$qs = strtr($qs, $cot_translitb);
}

// Results per page
$maxperpage = ($cfg['maxrowsperpage'] && is_numeric($cfg['maxrowsperpage']) && $cfg['maxrowsperpage'] > 0) ? $cfg['maxrowsperpage'] : 15;
list(	, $d) = cot_import_pagenav('d', $maxperpage);

// Tags displayed per page in standalone cloud
$perpage = $cfg['plugin']['tags']['perpage'];
list(	, $dt) = cot_import_pagenav('dt', $perpage);

// Array to register areas with tag functions provided
$tag_areas = array();

if (cot_module_active('page'))
{
	require_once cot_incfile('page', 'module');
	$tag_areas[] = 'pages';
}

if (cot_module_active('forums'))
{
	require_once cot_incfile('forums', 'module');
	$tag_areas[] = 'forums';
}

// Sorting order
$o = cot_import('order', 'P', 'ALP');
if (empty($o))
{
	$o = mb_strtolower($cfg['plugin']['tags']['sort']);
}
$tag_order = '';
$tag_orders = array('Title', 'Date', 'Category');
foreach ($tag_orders as $order)
{
	$ord = mb_strtolower($order);
	$selected = $ord == $o ? 'selected="selected"' : '';
	$tag_order .= cot_rc('input_option', array('value' => $ord, 'selected' => $selected, 'title' => $L[$order]));
}

/* == Hook for the plugins == */
foreach (cot_getextplugins('tags.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($cfg['plugin']['tags']['noindex'])
{
	$out['head'] .= $R['code_noindex'];
}

// the tag you are looking for
$qs_tag = htmlspecialchars(strip_tags($qs));
// current pagination page for uniqueness of meta tags
$qs_pag = $L['tags_All'] . ' ' . $sys['domain'] .= empty($dt) ? '' : ' - ' . mb_strtolower($L['Page']) . ' ' . preg_replace("/[^0-9]/", '', $sys['uri_curr']);
// meta title
$out['subtitle'] = empty($qs) ? $qs_pag : $L['tags_Search_tags'] . ': ' . $qs_tag;
// meta descriptions
$out['desc'] = empty($qs) ? $qs_pag . '. ' . cot_string_truncate($L['tags_Query_hint'], 143, false, true) : $L['tags_Search_tags'] . ' - ' . $qs_tag . '. ' .cot_string_truncate($L['tags_Query_hint'], 143, false, true);
// meta keywords
$out['keywords'] = empty($qs) ? preg_replace("/\W\s/u", "", mb_strtolower($qs_pag)) : mb_strtolower($qs_tag . ' ' . $L['tags_Search_tags']);

$t->assign(array(
	'TAGS_ACTION' => cot_url('plug', 'e=tags&a=' . $a),
	'TAGS_HINT' => $L['tags_Query_hint'],
	'TAGS_QUERY' => htmlspecialchars($qs),
	'TAGS_ORDER' => $tag_order
));

if ($a == 'pages' && cot_module_active('page'))
{
	if(empty($qs))
	{
		// Form and cloud
		cot_tag_search_form('pages');
	}
	else
	{
		// Search results
		cot_tag_search_pages($qs);
	}
}
elseif ($a == 'forums' && cot_module_active('forums'))
{
	if (empty($qs))
	{
		// Form and cloud
		cot_tag_search_form('forums');
	}
	else
	{
		// Search results
		cot_tag_search_forums($qs);
	}
}
elseif ($a == 'all')
{
	if (empty($qs))
	{
		// Form and cloud
		cot_tag_search_form('all');
	}
	else
	{
		// Search results
		foreach ($tag_areas as $area)
		{
			$tag_search_callback = 'cot_tag_search_' . $area;
			if (function_exists($tag_search_callback))
			{
				$tag_search_callback($qs);
			}
		}
	}
}
else
{
	/* == Hook for the plugins == */
	foreach (cot_getextplugins('tags.search.custom') as $pl)
	{
		include $pl;
	}
	/* ===== */
}

/**
 * Search by tag in pages
 *
 * @param string $query User-entered query string
 * @global CotDB $db
 */
function cot_tag_search_pages($query)
{
	global $db, $t, $L, $lang, $cfg, $usr, $qs, $d, $db_tag_references, $db_pages, $o, $row, $sys;

	if (!cot_module_active('page'))
	{
		return;
	}

	$query = cot_tag_parse_query($query, 'p.page_id');
	if (empty($query))
	{
		return;
	}

	$maxperpage = (cot::$cfg['maxrowsperpage'] && is_numeric(cot::$cfg['maxrowsperpage']) && cot::$cfg['maxrowsperpage'] > 0) ?
		cot::$cfg['maxrowsperpage'] : 15;

	$join_columns = '';
	$join_tables = '';
	$join_where = '';

	switch($o)
	{
		case 'title':
			$order = 'ORDER BY `page_title`';
			break;
		case 'date':
			$order = 'ORDER BY `page_date` DESC';
			break;
		case 'category':
			$order = 'ORDER BY `page_cat`';
			break;
		default:
			$order = '';
	}

	/* == Hook == */
	foreach (cot_getextplugins('tags.search.pages.query') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$totalitems = cot::$db->query("SELECT DISTINCT COUNT(*)
		FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
			ON r.tag_item = p.page_id $join_tables
		WHERE r.tag_area = 'pages' AND ($query) AND p.page_state = 0 $join_where")->fetchColumn();

	$sql = $db->query("SELECT DISTINCT p.* $join_columns
		FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
			ON r.tag_item = p.page_id $join_tables
		WHERE r.tag_area = 'pages' AND ($query) AND p.page_id IS NOT NULL AND p.page_state = 0 $join_where
		$order
		LIMIT $d, $maxperpage");

	$t->assign('TAGS_RESULT_TITLE', $L['tags_Found_in_pages']);
	$pcount = $sql->rowCount();

	/* == Hook : Part 1 == */
	$extp = cot_getextplugins('tags.search.pages.loop');
	/* ===== */

	if ($pcount > 0)
	{
		foreach ($sql->fetchAll() as $row)
		{
			if(($row['page_begin'] > 0 && $row['page_begin'] > $sys['now']) || ($row['page_expire'] > 0 && $sys['now'] > $row['page_expire']))
			{
				--$pcount;
				continue;
			}

			$tags = cot_tag_list($row['page_id']);
			$tag_list = '';
			$tag_i = 0;
			foreach ($tags as $tag)
			{
				$tag_t = cot::$cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
				$tag_u = cot::$cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
				$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
				if ($tag_i > 0) $tag_list .= ', ';
				$tag_list .= cot_rc_link(cot_url('plug', array('e' => 'tags', 'a' => 'pages', 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl)), htmlspecialchars($tag_t));
				$tag_i++;
			}

			$t->assign(cot_generate_pagetags($row, 'TAGS_RESULT_ROW_', cot::$cfg['page']['cat___default']['truncatetext']));
			$t->assign(array(
				//'TAGS_RESULT_ROW_URL' => empty($row['page_alias']) ? cot_url('page', 'c='.$row['page_cat'].'&id='.$row['page_id']) : cot_url('page', 'c='.$row['page_cat'].'&al='.$row['page_alias']),
				'TAGS_RESULT_ROW_TITLE' => htmlspecialchars($row['page_title']),
				'TAGS_RESULT_ROW_PATH' => cot_breadcrumbs(cot_structure_buildpath('page', $row['page_cat']), false),
				'TAGS_RESULT_ROW_TAGS' => $tag_list
			));
			/* == Hook : Part 2 == */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */
			$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_ROW');
		}
		$sql->closeCursor();
		$qs_u = cot::$cfg['plugin']['tags']['translit'] ? cot_translit_encode($qs) : $qs;
		$tl = $lang != 'en' && $qs_u != $qs ? 1 : null;
		$pagenav = cot_pagenav('plug', array('e' => 'tags', 'a' => 'pages', 't' => $qs_u, 'tl' => $tl), $d, $totalitems, $maxperpage);
		$t->assign(array(
			'TAGS_PAGEPREV' => $pagenav['prev'],
			'TAGS_PAGENEXT' => $pagenav['next'],
			'TAGS_PAGNAV' => $pagenav['main']
		));

		/* == Hook == */
		foreach (cot_getextplugins('tags.search.pages.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */
	}

	if($pcount == 0)
	{
		$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_NONE');
	}

	$t->parse('MAIN.TAGS_RESULT');
}

/**
 * Search by tag in forums
 *
 * @param string $query User-entered query string
 * @global CotDB $db
 */
function cot_tag_search_forums($query)
{
	global $db, $t, $L, $lang, $cfg, $usr, $qs, $d, $db_tag_references, $db_forum_topics, $o, $row;

	if (!cot_module_active('forums'))
	{
		return;
	}

	$query = cot_tag_parse_query($query, 't.ft_id');
	if (empty($query))
	{
		return;
	}

	$maxperpage = (cot::$cfg['maxrowsperpage'] && is_numeric(cot::$cfg['maxrowsperpage']) && cot::$cfg['maxrowsperpage'] > 0) ?
		cot::$cfg['maxrowsperpage'] : 15;

	$join_columns = '';
	$join_tables = '';
	$join_where = '';

	switch($o)
	{
		case 'title':
			$order = 'ORDER BY `ft_title`';
			break;
		case 'date':
			$order = 'ORDER BY `ft_updated` DESC';
			break;
		case 'category':
			$order = 'ORDER BY `ft_cat`';
			break;
		default:
			$order = '';
	}

	/* == Hook == */
	foreach (cot_getextplugins('tags.search.forums.query') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$totalitems = $db->query("SELECT DISTINCT COUNT(*)
		FROM $db_tag_references AS r LEFT JOIN $db_forum_topics AS t
			ON r.tag_item = t.ft_id $join_tables
		WHERE r.tag_area = 'forums' AND ($query) $join_where")->fetchColumn();

	$sql = $db->query("SELECT DISTINCT t.ft_id, t.ft_cat, t.ft_title $join_columns
		FROM $db_tag_references AS r LEFT JOIN $db_forum_topics AS t
			ON r.tag_item = t.ft_id $join_tables
		WHERE r.tag_area = 'forums' AND ($query) AND t.ft_id IS NOT NULL $join_where
		$order
		LIMIT $d, $maxperpage");

	$t->assign('TAGS_RESULT_TITLE', $L['tags_Found_in_forums']);
	if ($sql->rowCount() > 0)
	{
		while ($row = $sql->fetch())
		{
			$tags = cot_tag_list($row['ft_id'], 'forums');
			$tag_list = '';
			$tag_i = 0;
			foreach ($tags as $tag)
			{
				$tag_t = $cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
				$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
				$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
				if ($tag_i > 0) $tag_list .= ', ';
				$tag_list .= cot_rc_link(cot_url('plug', array('e' => 'tags', 'a' => 'forums', 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl)), htmlspecialchars($tag_t));
				$tag_i++;
			}
			$master = ($row['fs_masterid'] > 0) ? array($row['fs_masterid'], $row['fs_mastername']) : false;
			$t->assign(array(
				'TAGS_RESULT_ROW_URL' => cot_url('forums', 'm=posts&q='.$row['ft_id']),
				'TAGS_RESULT_ROW_TITLE' => htmlspecialchars($row['ft_title']),
				'TAGS_RESULT_ROW_PATH' => cot_breadcrumbs(cot_forums_buildpath($row['ft_cat']), false),
				'TAGS_RESULT_ROW_TAGS' => $tag_list
			));
			$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_ROW');
		}
		$sql->closeCursor();
		$qs_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($qs) : $qs;
		$tl = $lang != 'en' && $qs_u != $qs ? 1 : null;
		$pagenav = cot_pagenav('plug', array('e' => 'tags', 'a' => 'forums', 't' => $qs_u, 'tl' => $tl), $d, $totalitems, $maxperpage);
		$t->assign(array(
			'TAGS_PAGEPREV' => $pagenav['prev'],
			'TAGS_PAGENEXT' => $pagenav['next'],
			'TAGS_PAGNAV' => $pagenav['main']
		));
	}
	else
	{
		$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_NONE');
	}
	$t->parse('MAIN.TAGS_RESULT');
}
