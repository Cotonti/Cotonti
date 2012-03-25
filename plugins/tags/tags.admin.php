<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.page.loop
Tags=admin.page.inc.tpl:{ADMIN_TAGS_ROW_TAG},{ADMIN_TAGS_ROW_URL}
[END_COT_EXT]
==================== */

/**
 * Shows tags in page administration area
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
	$item_id = $row['page_id'];
	$tags = cot_tag_list($item_id);
	if (count($tags) > 0)
	{
		$tag_i = 0;
		foreach ($tags as $tag)
		{
			$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
			$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
			$t->assign(array(
				'ADMIN_TAGS_ROW_TAG' => $cfg['plugin']['tags']['title'] ? htmlspecialchars(cot_tag_title($tag)) : htmlspecialchars($tag),
				'ADMIN_TAGS_ROW_URL' => cot_url('plug', array('e' => 'tags', 'a' => 'pages', 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl))
			));
			$t->parse('PAGE.PAGE_ROW.ADMIN_TAGS_ROW');
			$tag_i++;
		}
	}
	else
	{
		$t->parse('PAGE.PAGE_ROW.ADMIN_NO_TAGS');
	}
}

?>