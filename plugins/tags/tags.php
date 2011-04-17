<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Tag search
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster (Vladimir Sibirov)
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

$qs = cot_import('t', 'G', 'TXT');
if(empty($qs)) $qs = cot_import('t', 'P', 'TXT');

$tl = cot_import('tl', 'G', 'BOL');
if ($tl) 
{ 
	include_once cot_langfile('translit', 'core'); 
	$qs = strtr($qs, $cot_translitb); 
}

list($pg, $d, $durl) = cot_import_pagenav('d',  $cfg['maxrowsperpage']);
$dt = cot_import('dt', 'G', 'INT');
$perpage = $cfg['plugin']['tags']['perpage'];

require_once cot_incfile('page', 'module');
require_once cot_incfile('forums', 'module');

// Array to register areas with tag functions provided
$tag_areas = array('pages', 'forums');

// Sorting order
$o = cot_import('order', 'P', 'ALP');
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

$out['head'] .= $R['code_noindex'];
$out['subtitle'] = empty($qs) ? $L['Tags'] : htmlspecialchars(strip_tags($qs)) . ' - ' . $L['tags_Search_results'];

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
		$query = cot_tag_parse_query($qs);
		if(!empty($query))
		{
			cot_tag_search_pages($query);
		}
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
		$query = cot_tag_parse_query($qs);
		if(!empty($query))
		{
			cot_tag_search_forums($query);
		}
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
		$query = cot_tag_parse_query($qs);
		if(!empty($query))
		{
			foreach ($tag_areas as $area)
			{
				$tag_search_callback = 'cot_tag_search_' . $area;
				if (function_exists($tag_search_callback))
				{
					$tag_search_callback($query);
				}
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
 * @param string $query Search query as SQL condition
 */
function cot_tag_search_pages($query)
{
	global $db, $t, $L, $lang, $cfg, $usr, $qs, $d, $db_tag_references, $db_pages, $o, $row;

	if (!cot_module_active('page'))
	{
		return;
	}

	$totalitems = $db->query("SELECT DISTINCT COUNT(*)
		FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
			ON r.tag_item = p.page_id
		WHERE r.tag_area = 'pages' AND ($query) AND p.page_state = 0")->fetchColumn();
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

	$sql = $db->query("SELECT DISTINCT p.page_id, p.page_alias, p.page_title, p.page_cat $join_columns
		FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
			ON r.tag_item = p.page_id $join_tables
		WHERE r.tag_area = 'pages' AND ($query) AND p.page_id IS NOT NULL AND p.page_state = 0 $join_where
		$order
		LIMIT $d, {$cfg['maxrowsperpage']}");
	$t->assign('TAGS_RESULT_TITLE', $L['tags_Found_in_pages']);

	/* == Hook : Part 1 == */
	$extp = cot_getextplugins('tags.search.pages.loop');
	/* ===== */
	foreach ($sql->fetchAll() as $row)
	{
		$tags = cot_tag_list($row['page_id']);
		$tag_list = '';
		$tag_i = 0;
		foreach ($tags as $tag)
		{
			$tag_t = $cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
			$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
			$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
			if ($tag_i > 0) $tag_list .= ', ';
			$tag_list .= cot_rc_link(cot_url('plug', 'e=tags&a=pages&t='.$tag_u.$tl), htmlspecialchars($tag_t), 'rel="nofollow"');
			$tag_i++;
		}
		$t->assign(array(
			'TAGS_RESULT_ROW_URL' => empty($row['page_alias']) ? cot_url('page', 'id='.$row['page_id']) : cot_url('page', 'al='.$row['page_alias']),
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
	$pagenav = cot_pagenav('plug','e=tags&a=pages&t=' . urlencode($qs), $d, $totalitems, $cfg['maxrowsperpage']);
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

	$t->parse('MAIN.TAGS_RESULT');
}

/**
 * Search by tag in forums
 *
 * @param string $query Search query as SQL condition
 */
function cot_tag_search_forums($query)
{
	global $db, $t, $L, $lang, $cfg, $usr, $qs, $d, $db_tag_references, $db_forum_topics, $o, $row;

	if (!cot_module_active('forums'))
	{
		return;
	}

	$totalitems = $db->query("SELECT DISTINCT COUNT(*)
		FROM $db_tag_references AS r LEFT JOIN $db_forum_topics AS t
			ON r.tag_item = t.ft_id
		WHERE r.tag_area = 'forums' AND ($query)")->fetchColumn();
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
	$sql = $db->query("SELECT DISTINCT t.ft_id, t.ft_cat, t.ft_title
		FROM $db_tag_references AS r LEFT JOIN $db_forum_topics AS t
			ON r.tag_item = t.ft_id
		WHERE r.tag_area = 'forums' AND ($query) AND t.ft_id IS NOT NULL
		$order
		LIMIT $d, {$cfg['maxrowsperpage']}");
	$t->assign('TAGS_RESULT_TITLE', $L['tags_Found_in_forums']);
	while ($row = $sql->fetch())
	{
		$tags = cot_tag_list($row['ft_id'], 'forums');
		$tag_list = '';
		$tag_i = 0;
		foreach ($tags as $tag)
		{
			$tag_t = $cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
			$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
			$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
			if ($tag_i > 0) $tag_list .= ', ';
			$tag_list .= cot_rc_link(cot_url('plug', 'e=tags&a=forums&t='.$tag_u.$tl), htmlspecialchars($tag_t), 'rel="nofollow"');
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
	$pagenav = cot_pagenav('plug','e=tags&a=forums&t='.urlencode($qs), $d, $totalitems, $cfg['maxrowsperpage']);
	$t->assign(array(
		'TAGS_PAGEPREV' => $pagenav['prev'],
		'TAGS_PAGENEXT' => $pagenav['next'],
		'TAGS_PAGNAV' => $pagenav['main']
	));
	$t->parse('MAIN.TAGS_RESULT');
}

?>