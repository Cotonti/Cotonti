<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=news.loop
Tags=news.tpl:{PAGE_TAGS_ROW_TAG},{PAGE_TAGS_ROW_URL},{PAGE_TAGS_ROW_TAG_COUNT}
[END_COT_EXT]
==================== */

/**
 * Displays tags in news entry
 *
 * @package tags
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'])
{
	require_once cot_incfile('tags', 'plug');
	if (cot_plugin_active('i18n') && $i18n_enabled && $i18n_notmain)
	{
		$tags_extra = array('tag_locale' => $i18n_locale);
	}
	else
	{
		$tags_extra = null;
	}
	$item_id = $pag['page_id'];
	
	if (!isset($tags_rowset_list))
	{
		// Load tags for all entries with 1 query
		$rowset_copy = $sql_rowset;
		reset($rowset_copy);
		$tag_items = array();
		foreach ($rowset_copy as $t_row)
		{
			$tag_items[] = $t_row['page_id'];
		}
		unset($rowset_copy);
		$tags_rowset_list = cot_tag_list($tag_items, 'pages', $tags_extra);
	}

	$tags = isset($tags_rowset_list[$item_id]) ? $tags_rowset_list[$item_id] : array();
	if (count($tags) > 0)
	{
		$tag_ii = 0;
		foreach ($tags as $tag)
		{
			$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
			$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
			$news->assign(array(
				'PAGE_TAGS_ROW_TAG' => $cfg['plugin']['tags']['title'] ? htmlspecialchars(cot_tag_title($tag)) : htmlspecialchars($tag),
				'PAGE_TAGS_ROW_TAG_COUNT' => $tag_ii,
				'PAGE_TAGS_ROW_URL' => cot_url('plug', array('e' => 'tags', 'a' => 'pages', 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl))
			));
			$news->parse('NEWS.PAGE_ROW.PAGE_TAGS.PAGE_TAGS_ROW');
			$tag_ii++;
		}
		$news->parse('NEWS.PAGE_ROW.PAGE_TAGS');
	}
	else
	{
		$news->parse('NEWS.PAGE_ROW.PAGE_NO_TAGS');
	}
}
?>
