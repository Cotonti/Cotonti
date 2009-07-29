<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.posts
File=tags.forums.posts
Hooks=forums.posts.tags
Tags=forums.posts.tpl:{FORUMS_POSTS_TAGS}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Part of plug tags
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Trustmaster - Vladimir Sibirov
 * @copyright All rights reserved. 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['forums'])
{
	require_once(sed_langfile('tags'));
	$tags = sed_tag_list($q, 'forums');
	if(count($tags) > 0)
	{
		$tc_html = $L['Tags'] . ':';
		foreach($tags as $tag)
		{
			$tag_t = $cfg['plugin']['tags']['title'] ? htmlspecialchars(sed_tag_title($tag)) : htmlspecialchars($tag);
			$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
			$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
			$tc_html .= ' <a href="'.sed_url('plug', 'e=tags&a=forums&t='.$tag_u.$tl).'">'.$tag_t.'</a>,';
		}
		$tc_html = mb_substr($tc_html, 0, -1);
		$t->assign('FORUMS_POSTS_TAGS', $tc_html);
	}
	else
	{
		//$tc_html = $L['tags_Tag_cloud_none'];
		$t->assign('FORUMS_POSTS_TAGS', '');
	}
}

?>
