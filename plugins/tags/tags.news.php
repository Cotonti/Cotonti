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
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

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
	$tags = cot_tag_list($item_id, 'pages', $tags_extra);
	if (count($tags) > 0)
	{
		$tag_ii = 0;
		foreach ($tags as $tag)
		{
			$tag_u = cot_urlencode($tag, $cfg['plugin']['tags']['translit']);
			$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
			$news->assign(array(
				'PAGE_TAGS_ROW_TAG' => $cfg['plugin']['tags']['title'] ? htmlspecialchars(cot_tag_title($tag)) : htmlspecialchars($tag),
				'PAGE_TAGS_ROW_TAG_COUNT' => $tag_ii,
				'PAGE_TAGS_ROW_URL' => cot_url('plug', 'e=tags&a=pages&t=' . $tag_u . $tl)
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
