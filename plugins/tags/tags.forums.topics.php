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
Part=forums.topics
File=tags.forums.topics
Hooks=forums.topics.loop
Tags=forums.topics.tpl:{FORUMS_TOPICS_ROW_TAGS}
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['forums'])
{
	file_exists($cfg['plugins_dir']."/tags/lang/tags.$lang.lang.php") ? require_once($cfg['plugins_dir']."/tags/lang/tags.$lang.lang.php") : require_once($cfg['plugins_dir'].'/tags/lang/tags.en.lang.php');
	$item_id = $row['ft_id'];
	$tags = sed_tag_list($item_id, 'forums');
	if(count($tags) > 0)
	{
		$tc_html = $L['Tags'] . ':';
		foreach($tags as $tag)
		{
			$tag_t = $cfg['plugin']['tags']['title'] ? sed_cc(sed_tag_title($tag)) : sed_cc($tag);
			$tc_html .= ' <a href="'.sed_url('plug', 'e=tags&a=forums&t='.urlencode($tag)).'">'.$tag_t.'</a>,';
		}
		$tc_html = mb_substr($tc_html, 0, -1);
	}
	else
	{
		$tc_html = '';
	}
	$t->assign('FORUMS_TOPICS_ROW_TAGS', $tc_html);
}

// TODO tag cloud with subforums support
?>