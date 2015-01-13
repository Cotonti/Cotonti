<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.main
[END_COT_EXT]
==================== */

/**
 * Generates keywords from topic tags
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'])
{
	require_once cot_incfile('tags', 'plug');
	$tags = cot_tag_list($q, 'forums');
	$tag_keywords = implode(', ', $tags);
	$out['keywords'] = $tag_keywords;
}
