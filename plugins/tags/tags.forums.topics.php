<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.loop
Tags=forums.topics.tpl:{FORUMS_TOPICS_ROW_TAGS}
[END_COT_EXT]
==================== */

/**
 * Renders topic tags output
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'])
{
	require_once cot_incfile('tags', 'plug');
	$item_id = $row['ft_id'];
	$tags = cot_tag_list($item_id, 'forums');
	if (count($tags) > 0)
	{
		$tc_html = $L['Tags'] . ': ';
		$tag_i = 0;
		foreach ($tags as $tag)
		{
			$tag_t = $cfg['plugin']['tags']['title'] ? htmlspecialchars(cot_tag_title($tag)) : htmlspecialchars($tag);
			$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
			$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
			if ($tag_i > 0) $tc_html .= ', ';
			$tc_html .= cot_rc('tags_link_tag', array(
				'url' => cot_url('plug', 'e=tags&a=forums' . $tl . '&t=' . $tag_u),
				'tag_title' => $tag_t
			));
			$tag_i++;
		}
		$t->assign('FORUMS_TOPICS_ROW_TAGS', $tc_html);
	}
	else
	{
		//$tc_html = $L['tags_Tag_cloud_none'];
		$t->assign('FORUMS_TOPICS_ROW_TAGS', '');
	}
}
// TODO tag cloud with subforums support

?>