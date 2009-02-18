<?PHP
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
 * Part of plug tags
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Trustmaster - Vladimir Sibirov
 * @copyright All rights reserved. 2008-2009
 * @license BSD
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	$rtags = sed_import('rtags', 'P', 'TXT');
	$tags = sed_tag_parse($rtags);
	$old_tags = sed_tag_list($id);
	$kept_tags = array();
	$new_tags = array();
	// Find new tags, count old tags that have been left
	$cnt = 0;
	foreach($tags as $tag)
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
	foreach($rem_tags as $tag)
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