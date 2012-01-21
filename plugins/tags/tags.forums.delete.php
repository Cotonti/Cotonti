<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.delete.done
[END_COT_EXT]
==================== */

/**
 * Removes tags linked to a forum post
 *
 * @package tags
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'] && cot_auth('plug', 'tags', 'W'))
{
	require_once cot_incfile('tags', 'plug');
	cot_tag_remove_all($q, 'forums');
}

?>