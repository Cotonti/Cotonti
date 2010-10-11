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
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

$qs = cot_import('t', 'G', 'TXT');
if(empty($qs)) $qs = cot_import('t', 'P', 'TXT');

$tl = cot_import('tl', 'G', 'BOL');
if($tl) $qs = strtr($qs, $cot_translitb);

$d = (int) cot_import('d', 'G', 'INT');
$perpage = $cfg['plugin']['tags']['perpage'];

cot_require('tags', true);

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

if ($a == 'pages')
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
elseif ($a == 'forums')
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

?>