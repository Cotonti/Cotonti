<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.main
[END_COT_EXT]
==================== */

/**
 * Generates page keywords
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'])
{
	sed_require('tags', true);
	$item_id = $pag['page_id'];
	$tags = sed_tag_list($item_id);
	$tag_keywords = implode(', ', $tags);
	$out['keywords'] = $tag_keywords;
}

?>