<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.editpost
File=tags.forums.editpost
Hooks=forums.editpost.update.done
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

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W') && $is_first_post)
{
	$rtags = sed_import('rtags', 'P', 'TXT');
	$tags = sed_tag_parse($rtags);
	$old_tags = sed_tag_list($q, 'forums');
	$kept_tags = array();
	$new_tags = array();
	// Find new tags, count old tags that have been left
	$cnt = 0;
	foreach($tags as $tag)
	{
		$ps = array_search($tag, $old_tags);
		if($ps !== false)
		{
			$kept_tags[] = $old_tags[$ps];
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
		sed_tag_remove($tag, $q, 'forums');
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
	for($i = 0; $i < $lim; $i++)
	{
		sed_tag($new_tags[$i], $q, 'forums');
	}
}

?>