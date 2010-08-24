<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.add.done
[END_COT_EXT]
==================== */

/**
 * Adds tags for a new page
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	sed_require('tags', true);
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