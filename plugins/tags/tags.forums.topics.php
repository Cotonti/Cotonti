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
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'])
{
	require_once cot_incfile('tags', 'plug');
	$item_id = $row['ft_id'];

	if (!isset($tags_rowset_list))
	{
		// Load tags for all entries with 1 query
		$rowset_copy = $sql_forums_rowset;
		reset($rowset_copy);
		$tag_items = array();
		foreach ($rowset_copy as $t_row)
		{
			$tag_items[] = $t_row['ft_id'];
		}
		unset($rowset_copy);
		$tags_rowset_list = cot_tag_list($tag_items, 'forums');
	}

	$tags = isset($tags_rowset_list[$item_id]) ? $tags_rowset_list[$item_id] : array();
	if (count($tags) > 0)
	{
		$tc_html = $L['Tags'] . ': ';
		$tag_i = 0;
		foreach ($tags as $tag)
		{
			$tag_t = $cfg['plugin']['tags']['title'] ? htmlspecialchars(cot_tag_title($tag)) : htmlspecialchars($tag);
			$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
			$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
			if ($tag_i > 0) $tc_html .= ', ';
			$tc_html .= cot_rc('tags_link_tag', array(
				'url' => cot_url('plug', array('e' => 'tags', 'a' => 'forums', 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl)),
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
