<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags
Tags=index.tpl:{INDEX_TAG_CLOUD},{INDEX_TAG_CLOUD_ALL_LINK}
[END_COT_EXT]
==================== */

/**
 * Tag clouds for index page
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (cot::$cfg['plugin']['tags']['pages'] || cot::$cfg['plugin']['tags']['forums']) {
	require_once cot_incfile('tags', 'plug');

	$limit = cot::$cfg['plugin']['tags']['lim_index'] == 0 ? null : (int) cot::$cfg['plugin']['tags']['lim_index'];
	$tcloud = cot_tag_cloud(cot::$cfg['plugin']['tags']['index'], cot::$cfg['plugin']['tags']['order'], $limit);
	$tc_html = cot::$R['tags_code_cloud_open'];
	$tag_count = 0;
	foreach ($tcloud as $tag => $cnt) {
		$tag_count++;
		$tag_t = cot::$cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
		$tag_u = cot::$cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
		$tl = ($lang != 'en' && $tag_u != $tag) ? 1 : null;
		foreach ($tc_styles as $key => $val) {
			if ($cnt <= $key) {
				$dim = $val;
				break;
			}
		}
		$tc_html .= cot_rc('tags_link_cloud_tag', array(
			'url' => cot_url(
                'plug',
                array('e' => 'tags', 'a' => cot::$cfg['plugin']['tags']['index'], 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl)
            ),
			'tag_title' => htmlspecialchars($tag_t),
			'dim' => $dim
		));
	}

	$tc_html .= cot::$R['tags_code_cloud_close'];
	$tc_html = ($tag_count > 0) ? $tc_html : cot::$L['tags_Tag_cloud_none'];
	$t->assign('INDEX_TAG_CLOUD', $tc_html);
	if (cot::$cfg['plugin']['tags']['more'] && $limit > 0 && $tag_count == $limit) {
		$t->assign('INDEX_TAG_CLOUD_ALL_LINK', cot_rc('tags_code_cloud_more',
			array('url' => cot_url('plug', 'e=tags&a=' . cot::$cfg['plugin']['tags']['index']))));
	}
}
