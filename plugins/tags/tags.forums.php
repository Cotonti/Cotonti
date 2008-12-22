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
	file_exists($cfg['plugins_dir']."/tags/lang/tags.$lang.lang.php") ? require_once($cfg['plugins_dir']."/tags/lang/tags.$lang.lang.php") : require_once($cfg['plugins_dir'].'/tags/lang/tags.en.lang.php');
	// Get all subcategories
	$tcloud = sed_tag_cloud('forums', $cfg['plugin']['tags']['order']);
	$tc_html = '<ul class="tag_cloud">';
	foreach($tcloud as $tag => $cnt)
	{
		$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
		$tc_html .= '<li value="'.$cnt.'"><a href="'.sed_url('plug', 'e=tags&a=forums&t='.urlencode($tag)).'">'.sed_cc($tag_t).'</a> </li>';
	}
	$tc_html .= '</ul><script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/jquery.tagcloud.js"></script><script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/set.js"></script>';

	$t->assign(array(
	'FORUMS_SECTIONS_TOP_TAG_CLOUD' => $L['Tag_cloud'],
	'FORUMS_SECTIONS_TAG_CLOUD' => $tc_html
	));
}
?>