<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.emptytopicdel, forums.functions.prunetopics
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('polls', 'module');

cot_poll_delete($q, 'forum');
