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
	require_once $cfg['system_dir'] . '/tags.php';
	require_once sed_langfile('tags', 'plug');
	$item_id = $pag['page_id'];
	$tags = sed_tag_list($item_id);
	$tag_keywords = implode(', ', $tags);
	$out['keywords'] = $tag_keywords;
}

?>