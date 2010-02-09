<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.topics
File=tags.forums.topics
Hooks=forums.topics.loop
Tags=forums.topics.tpl:{FORUMS_TOPICS_ROW_TAGS}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Renders topic tags output
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['forums'])
{
	require_once $cfg['system_dir'] . '/tags.php';
	require_once sed_langfile('tags');
	require_once $cfg['plugins_dir'].'/tags/inc/resources.php';
	$item_id = $row['ft_id'];
	$tags = sed_tag_list($item_id, 'forums');
	if(count($tags) > 0)
	{
		$tc_html = $L['Tags'] . ': ';
		$tag_i = 0;
		foreach($tags as $tag)
		{
			$tag_t = $cfg['plugin']['tags']['title'] ? htmlspecialchars(sed_tag_title($tag)) : htmlspecialchars($tag);
			$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
			$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
			if ($tag_i > 0) $tc_html .= ', ';
			$tc_html .= sed_rc('tags_link_tag', array(
				'url' => sed_url('plug', 'e=tags&a=forums' . $tl . '&t=' . $tag_u),
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