<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.add.done,i18n.page.add.done
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

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'] && cot_auth('plug', 'tags', 'W'))
{
	cot_require('tags', true);
	// I18n
	if ($cot_current_hook == 'i18n.page.add.done')
	{
		$tags_extra = array('tag_locale' => $i18n_locale);
		$item_id = $id;
	}
	else
	{
		$tags_extra = null;
		$item_id = $db->query("SELECT LAST_INSERT_ID()")->fetchColumn();
	}
	
	$rtags = cot_import('rtags', 'P', 'TXT');
	$tags = cot_tag_parse($rtags);
	$cnt = 0;
	foreach ($tags as $tag)
	{
		cot_tag($tag, $item_id, 'pages', $tags_extra);
		$cnt++;
		if ($cfg['plugin']['tags']['limit'] > 0 && $cnt == $cfg['plugin']['tags']['limit'])
		{
			break;
		}
	}
}

?>