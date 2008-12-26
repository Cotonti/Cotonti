<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.index.php
Version=0.0.2
Updated=2008-dec-23
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=index
File=tags.index
Hooks=index.tags
Tags=index.tpl:{INDEX_TAG_CLOUD},{INDEX_TOP_TAG_CLOUD}
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['pages'])
{
	require_once(sed_langfile('tags'));
	$tcloud = sed_tag_cloud('pages', $cfg['plugin']['tags']['order']);
	$tc_html = '<ul class="tag_cloud">';
	foreach($tcloud as $tag => $cnt)
	{
		$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
		$tc_html .= '<li value="'.$cnt.'"><a href="'.sed_url('plug', 'e=tags&a=pages&t='.urlencode($tag)).'">'.sed_cc($tag_t).'</a> </li>';
	}
	$tc_html .= '</ul><script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/jquery.tagcloud.js"></script><script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/set.js"></script>';
	$t->assign(array(
	'INDEX_TAG_CLOUD' => $tc_html,
	'INDEX_TOP_TAG_CLOUD' => $L['Tag_cloud']
	));
}
?>