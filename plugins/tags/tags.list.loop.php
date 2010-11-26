<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=list.loop
File=tags.list.loop
Hooks=list.loop
Tags=list.tpl:{LIST_ROW_TAGS_ROW_TAG},{LIST_ROW_TAGS_ROW_URL},{LIST_ROW_NO_TAGS}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Displays page tags in list loop
 *
 * @package Cotonti
 * @version 0.6.12
 * @author Trustmaster - Vladimir Sibirov
 * @copyright All rights reserved. 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['pages'])
{
	require_once sed_langfile('tags');
	$item_id = $pag['page_id'];
	$tags = sed_tag_list($item_id);
	if (count($tags) > 0)
	{
		$tag_i = 0;
		foreach ($tags as $tag)
		{
			$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
			$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
			$t->assign(array(
				'LIST_ROW_TAGS_ROW_TAG' => $cfg['plugin']['tags']['title'] ? htmlspecialchars(sed_tag_title($tag)) : htmlspecialchars($tag),
				'LIST_ROW_TAGS_ROW_URL' => sed_url('plug', 'e=tags&a=pages'.$tl.'&t='.$tag_u)
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
?>
