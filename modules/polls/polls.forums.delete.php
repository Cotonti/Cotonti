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
 *
 * @todo don't delete poll when topic deleting to trashcan.
 * @todo delete poll on topic deleting from trashcan
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('polls', 'module');

if (!empty($topicId)) {
    cot_poll_delete($topicId, 'forum');
} elseif (!empty($q)) {
    cot_poll_delete($q, 'forum');
}
