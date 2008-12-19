<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.page.edit.php
Version=0.0.2
Updated=2008-dec-19
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.edit
File=tags.page.edit
Hooks=page.edit.update.done
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	$rtags = sed_import('rtags', 'P', 'TXT');
	$tags = sed_tag_parse($rtags);
	$old_tags = sed_tag_list($id);
	$new_tags = array();
	// Find new tags, count old tags that have been left
	$cnt = 0;
	foreach($tags as $tag)
	{
		if($p = array_search($tag, $old_tags) !== false)
		{
			unset($old_tags[$p]);
			$cnt++;
		}
		else
		{
			$new_tags[] = $tag;
		}
	}
	// Remove old tags that have been removed
	foreach($old_tags as $tag)
	{
		sed_tag_remove($tag, $id);
	}
	// Add new tags
	$lim = $cfg['plugin']['tags']['limit'] > 0 ? $cfg['plugin']['tags']['limit'] - $cnt : count($new_tags);
	for($i = 0; $i < $lim; $i++)
	{
		sed_tag($new_tags[$i], $id);
	}
}
?>