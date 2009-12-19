<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.newtopic
File=tags.forums.newtopic
Hooks=forums.newtopic.newtopic.done
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Part of plug tags
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright All rights reserved. 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W'))
{
	$item_id = $q;
	$rtags = sed_import('rtags', 'P', 'TXT');
	$tags = sed_tag_parse($rtags);
	$cnt = 0;
	foreach($tags as $tag)
	{
		sed_tag($tag, $item_id, 'forums');
		$cnt++;
		if($cfg['plugin']['tags']['limit'] > 0 && $cnt == $cfg['plugin']['tags']['limit'])
		{
			break;
		}
	}
}

?>