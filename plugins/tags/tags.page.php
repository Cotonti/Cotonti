<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.tags
Tags=page.tpl:{PAGE_TAGS_ROW_TAG},{PAGE_TAGS_ROW_URL}
[END_COT_EXT]
==================== */

/**
 * Renders page tags output
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'])
{
	if (!isset($tags))
	{
		require_once $cfg['system_dir'] . '/tags.php';
		require_once sed_langfile('tags', 'plug');
		$item_id = $pag['page_id'];
		$tags = sed_tag_list($item_id);
	}
	if (count($tags) > 0)
	{
		$tag_i = 0;
		foreach ($tags as $tag)
		{
			$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
			$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
			$t->assign(array(
				'PAGE_TAGS_ROW_TAG' => $cfg['plugin']['tags']['title'] ? htmlspecialchars(sed_tag_title($tag)) : htmlspecialchars($tag),
				'PAGE_TAGS_ROW_URL' => sed_url('plug', 'e=tags&a=pages'.$tl.'&t='.$tag_u)
			));
			$t->parse('MAIN.PAGE_TAGS_ROW');
			$tag_i++;
		}
	}
	else
	{
		$t->assign(array(
			'PAGE_NO_TAGS' => $L['tags_Tag_cloud_none']
		));
		$t->parse('MAIN.PAGE_NO_TAGS');
	}
}

?>