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
 * @package tags
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'] || $cfg['plugin']['tags']['forums'])
{
	require_once cot_incfile('tags', 'plug');
	$limit = $cfg['plugin']['tags']['lim_index'] == 0 ? null : (int) $cfg['plugin']['tags']['lim_index'];
	$tcloud = cot_tag_cloud($cfg['plugin']['tags']['index'], $cfg['plugin']['tags']['order'], $limit);
	$tc_html = $R['tags_code_cloud_open'];
	$tag_count = 0;
	foreach ($tcloud as $tag => $cnt)
	{
		$tag_count++;
		$tag_t = $cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
		$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
		$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
		foreach ($tc_styles as $key => $val)
		{
			if ($cnt <= $key)
			{
				$dim = $val;
				break;
			}
		}
		$tc_html .= cot_rc('tags_link_cloud_tag', array(
			'url' => cot_url('plug', array('e' => 'tags', 'a' => $cfg['plugin']['tags']['index'], 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl)),
			'tag_title' => htmlspecialchars($tag_t),
			'dim' => $dim
		));
	}

	$tc_html .= $R['tags_code_cloud_close'];
	$tc_html = ($tag_count > 0) ? $tc_html : $L['tags_Tag_cloud_none'];
	$t->assign('INDEX_TAG_CLOUD', $tc_html);
	if ($cfg['plugin']['tags']['more'] && $limit > 0 && $tag_count == $limit)
	{
		$t->assign('INDEX_TAG_CLOUD_ALL_LINK', cot_rc('tags_code_cloud_more',
			array('url' => cot_url('plug', 'e=tags&a='.$cfg['plugin']['tags']['index']))));
	}
}

?>