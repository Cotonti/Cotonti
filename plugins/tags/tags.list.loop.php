<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.loop
Tags=page.list.tpl:{LIST_ROW_TAGS_ROW_TAG},{LIST_ROW_TAGS_ROW_URL},{LIST_ROW_NO_TAGS}
[END_COT_EXT]
==================== */

/**
 * Displays tags in list row
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
		$rowset_copy = $sqllist_rowset;
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
		$tag_i = 0;
		foreach ($tags as $tag)
		{
			$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
			$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
			$t->assign(array(
				'LIST_ROW_TAGS_ROW_TAG' => $cfg['plugin']['tags']['title'] ? htmlspecialchars(cot_tag_title($tag)) : htmlspecialchars($tag),
				'LIST_ROW_TAGS_ROW_URL' => cot_url('plug', array('e' => 'tags', 'a' => 'pages', 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl))
			));
			$t->parse('MAIN.LIST_ROW.LIST_ROW_TAGS_ROW');
			$tag_i++;
		}
	}
	else
	{
		$t->assign(array(
			'LIST_ROW_NO_TAGS' => $L['tags_Tag_cloud_none']
		));
		$t->parse('MAIN.LIST_ROW.PAGE_NO_TAGS');
	}
}
