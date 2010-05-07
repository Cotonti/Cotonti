<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.add
File=tags.page.add
Hooks=page.add.add.done
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Adds tags for a new page
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
	$item_id = sed_sql_result(sed_sql_query("SELECT LAST_INSERT_ID()"), 0, 0);
	$rtags = sed_import('rtags', 'P', 'TXT');
	$tags = sed_tag_parse($rtags);
	$cnt = 0;
	foreach ($tags as $tag)
	{
		sed_tag($tag, $item_id);
		$cnt++;
		if ($cfg['plugin']['tags']['limit'] > 0 && $cnt == $cfg['plugin']['tags']['limit'])
		{
			break;
		}
	}
}

?>