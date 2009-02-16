<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.forums.topics.php
Version=0.0.2
Updated=2008-dec-22
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums
File=tags.forums
Hooks=forums.sections.tags
Tags=forums.sections.tpl:{FORUMS_SECTIONS_TAG_CLOUD},{FORUMS_SECTIONS_TOP_TAG_CLOUD}
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['forums'])
{
	require_once(sed_langfile('tags'));
	require_once $cfg['plugins_dir'].'/tags/inc/config.php';
	// Get all subcategories
	$limit = $cfg['plugin']['tags']['lim_forums'] == 0 ? null : (int) $cfg['plugin']['tags']['lim_forums'];
	$tcloud = sed_tag_cloud('forums', $cfg['plugin']['tags']['order'], $limit);
	$tc_html = '<link rel="stylesheet" type="text/css" href="'.$cfg['plugins_dir'].'/tags/style.css" />
		<div class="tag_cloud">';
	foreach($tcloud as $tag => $cnt)
	{
		$tag_count++;
		$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
		$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
		$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
		foreach($tc_styles as $key => $val)
		{
			if($cnt <= $key)
			{
				$dim = $val;
				break;
			}
		}
		$tc_html .= '<a href="'.sed_url('plug', 'e=tags&a=forums&t='.$tag_u.$tl).'" class="'.$dim.'">'.sed_cc($tag_t).'</a> ';
	}
	$tc_html .= '</div>';
	$tc_html = ($tag_count > 0) ? $tc_html : $L['tags_Tag_cloud_none'];
	$t->assign(array(
	'FORUMS_SECTIONS_TOP_TAG_CLOUD' => $L['tags_Tag_cloud'],
	'FORUMS_SECTIONS_TAG_CLOUD' => $tc_html
	));
}
?>