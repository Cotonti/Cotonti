<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.tags
Tags=forums.posts.tpl:{FORUMS_POSTS_TAGS}
[END_COT_EXT]
==================== */

/**
 * Forum post tags output
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\modules\forums\inc\ForumsDictionary;

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['tags']['forums']) {
	if (!isset($tags)) {
		require_once cot_incfile('tags', 'plug');
		$tags = cot_tag_list($q, ForumsDictionary::SOURCE_TOPIC);
	}
	if (count($tags) > 0) {
		$tc_html = Cot::$L['Tags'] . ': ';
		$tag_i = 0;
		foreach ($tags as $tag) {
			$tag_t = Cot::$cfg['plugin']['tags']['title'] ? htmlspecialchars(cot_tag_title($tag)) : htmlspecialchars($tag);
			$tag_u = Cot::$cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
			$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
			if ($tag_i > 0) $tc_html .= ', ';
			$tc_html .= cot_rc('tags_link_tag', array(
				'url' => cot_url('plug', array('e' => 'tags', 'a' => 'forums', 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl)),
				'tag_title' => $tag_t
			));
			$tag_i++;
		}
		$t->assign('FORUMS_POSTS_TAGS', $tc_html);
	} else {
		//$tc_html = $L['tags_Tag_cloud_none'];
		$t->assign('FORUMS_POSTS_TAGS', '');
	}
}
