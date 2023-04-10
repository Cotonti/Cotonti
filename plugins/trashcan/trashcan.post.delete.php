<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.delete.done
[END_COT_EXT]
==================== */

/**
 * Trashcan delete post
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('trashcan', 'plug');

if (Cot::$cfg['plugin']['trashcan']['trash_forum']) {
	cot_trash_put('forumpost', Cot::$L['forums_post'] . " #" . $p . " from topic #" . $q, $p, $row);
}
