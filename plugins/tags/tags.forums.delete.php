<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.delete.done
[END_COT_EXT]
==================== */

/**
 * Removes tags linked to a forum post
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'] && cot_auth('plug', 'tags', 'W'))
{
	require_once cot_incfile('tags', 'plug');
	cot_tag_remove_all($q, 'forums');
}
