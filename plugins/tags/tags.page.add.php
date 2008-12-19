<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.page.add.php
Version=0.0.2
Updated=2008-jan-07
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.add
File=tags.page.add
Hooks=page.add.add.done
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	$item_id = sed_sql_result(sed_sql_query("SELECT LAST_INSERT_ID()"), 0, 0);
	$rtags = sed_import('rtags', 'P', 'TXT');
	$tags = sed_tag_parse($rtags);
	$cnt = 0;
	foreach($tags as $tag)
	{
		sed_tag($tag, $item_id);
		$cnt++;
		if($cfg['plugin']['tags']['limit'] > 0 && $cnt == $cfg['plugin']['tags']['limit'])
		{
			break;
		}
	}
}
?>