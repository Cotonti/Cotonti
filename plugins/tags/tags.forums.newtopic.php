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
 * Adds tags when creating a new topic
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W'))
{
	require_once $cfg['system_dir'] . '/tags.php';
	$item_id = $q;
	$rtags = sed_import('rtags', 'P', 'TXT');
	$tags = sed_tag_parse($rtags);
	$cnt = 0;
	foreach ($tags as $tag)
	{
		sed_tag($tag, $item_id, 'forums');
		$cnt++;
		if ($cfg['plugin']['tags']['limit'] > 0 && $cnt == $cfg['plugin']['tags']['limit'])
		{
			break;
		}
	}
}

?>