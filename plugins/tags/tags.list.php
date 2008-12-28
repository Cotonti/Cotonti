<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.list.php
Version=121
Updated=2008-dec-19
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=list
File=tags.list
Hooks=list.tags
Tags=list.tpl:{LIST_TAG_CLOUD},{LIST_TOP_TAG_CLOUD}
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['pages'])
{
	require_once(sed_langfile('tags'));
	// Get all subcategories
	$tc_cats = array("'$c'");
	$tc_path = $sed_cat[$c]['path'] . '.';
	foreach($sed_cat as $key => $val)
	{
		if(mb_strstr($val['path'], $tc_path))
		{
			$tc_cats[] = "'$key'";
		}
	}
	$tc_cats = implode(',', $tc_cats);

	// Get all pages from all subcategories and all tags with counts for them
	$limit = $cfg['plugin']['tags']['lim_pages'] == 0 ? '' : ' LIMIT ' . $cfg['plugin']['tags']['lim_pages'];
	$order = $cfg['plugin']['tags']['order'] == 'tag' ? '`tag`' : '`cnt` DESC';
	$tc_res = sed_sql_query("SELECT r.tag AS tag, COUNT(r.tag_item) AS cnt
		FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
		ON r.tag_item = p.page_id
		WHERE r.tag_area = 'pages' AND p.page_cat IN ($tc_cats)
		GROUP BY r.tag
		ORDER BY $order $limit");
	$tc_html = '<ul class="tag_cloud">';
	while($tc_row = sed_sql_fetchassoc($tc_res))
	{
		$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tc_row['tag']) : $tc_row['tag'];
		$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
		$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
		$tc_html .= '<li value="'.$tc_row['cnt'].'"><a href="'.sed_url('plug', 'e=tags&a=pages&t='.$tag_u.$tl).'">'.sed_cc($tag_t).'</a> </li>';
	}
	sed_sql_freeresult($tc_res);
	$tc_html .= '</ul><script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/jquery.tagcloud.js"></script><script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/set.js"></script>';


	$t->assign(array(
	'LIST_TOP_TAG_CLOUD' => $L['Tag_cloud'],
	'LIST_TAG_CLOUD' => $tc_html
	));
}
?>