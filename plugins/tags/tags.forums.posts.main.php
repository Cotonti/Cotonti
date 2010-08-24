<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.main
[END_COT_EXT]
==================== */

/**
 * Generates keywords from topic tags
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'])
{
	sed_require('tags', true);
	$tags = sed_tag_list($q, 'forums');
	$tag_keywords = implode(', ', $tags);
	$out['keywords'] = $tag_keywords;
}

?>