<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.edit
File=tags.page.edit
Hooks=page.edit.update.done
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Updates page tags
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	require_once $cfg['system_dir'] . '/tags.php';
	$rtags = sed_import('rtags', 'P', 'TXT');
	$tags = sed_tag_parse($rtags);
	$old_tags = sed_tag_list($id);
	$kept_tags = array();
	$new_tags = array();
	// Find new tags, count old tags that have been left
	$cnt = 0;
	foreach ($tags as $tag)
	{
		$p = array_search($tag, $old_tags);
		if($p !== false)
		{
			$kept_tags[] = $old_tags[$p];
			$cnt++;
		}
		else
		{
			$new_tags[] = $tag;
		}
	}
	// Remove old tags that have been removed
	$rem_tags = array_diff($old_tags, $kept_tags);
	foreach ($rem_tags as $tag)
	{
		sed_tag_remove($tag, $id);
	}
	// Add new tags
	$ncnt = count($new_tags);
	if ($cfg['plugin']['tags']['limit'] > 0
		&& $ncnt > $cfg['plugin']['tags']['limit'] - $cnt)
	{
		$lim = $cfg['plugin']['tags']['limit'] - $cnt;
	}
	else
	{
		$lim = $ncnt;
	}
	for ($i = 0; $i < $lim; $i++)
	{
		sed_tag($new_tags[$i], $id);
	}
}

?>