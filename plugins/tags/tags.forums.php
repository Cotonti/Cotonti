<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.sections.tags
Tags=forums.sections.tpl:{FORUMS_SECTIONS_TAG_CLOUD},{FORUMS_SECTIONS_TOP_TAG_CLOUD}
[END_COT_EXT]
==================== */

/**
 * Forum tag cloud
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'])
{
	require_once $cfg['system_dir'] . '/tags.php';
	require_once sed_langfile('tags', 'plug');
	require_once $cfg['plugins_dir'].'/tags/inc/config.php';
	require_once $cfg['plugins_dir'].'/tags/inc/resources.php';
	// Get all subcategories
	$limit = $cfg['plugin']['tags']['lim_forums'] == 0 ? null : (int) $cfg['plugin']['tags']['lim_forums'];
	$tcloud = sed_tag_cloud('forums', $cfg['plugin']['tags']['order'], $limit);
	$tc_html = $R['tags_code_cloud_open'];
	foreach ($tcloud as $tag => $cnt)
	{
		$tag_count++;
		$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
		$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
		$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
		foreach ($tc_styles as $key => $val)
		{
			if ($cnt <= $key)
			{
				$dim = $val;
				break;
			}
		}
		$tc_html .= sed_rc('tags_link_cloud_tag', array(
			'url' => sed_url('plug', 'e=tags&a=forums' . $tl . '&t=' . $tag_u),
			'tag_title' => htmlspecialchars($tag_t),
			'dim' => $dim
		));
	}
	if ($cfg['plugin']['tags']['more'] && $limit > 0)
	{
		$tc_html .= sed_rc('tags_code_cloud_more', array('url' => sed_url('plug', 'e=tags&a=forums')));
	}
	$tc_html .= $R['tags_code_cloud_close'];
	$tc_html = ($tag_count > 0) ? $tc_html : $L['tags_Tag_cloud_none'];
	$t->assign(array(
		'FORUMS_SECTIONS_TOP_TAG_CLOUD' => $L['tags_Tag_cloud'],
		'FORUMS_SECTIONS_TAG_CLOUD' => $tc_html
	));
}

?>