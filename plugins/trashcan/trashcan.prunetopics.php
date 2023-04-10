<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.functions.prunetopics
[END_COT_EXT]
==================== */

/**
 * Trashcan delete post
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $topicId topic id
 * @var array $topic topic data
 */

defined('COT_CODE') or die('Wrong URL');


if (Cot::$cfg['plugin']['trashcan']['trash_forum']) {
	// We are inside cot_forum_prunetopics() function, so need some globals
	global $trash_types, $db_trash, $db_x;
	require_once cot_incfile('trashcan', 'plug');

    // Add topic to trash
    $parentTrashId = cot_trash_put('forumtopic', Cot::$L['Topic'] . ' #' . $topicId, $topicId, $topic);

    // And all it's posts
    $postsSql = Cot::$db->query(
        'SELECT * FROM ' . Cot::$db->quoteTableName(Cot::$db->forum_posts) . ' WHERE fp_topicid = ?',
        [$topicId]
    );
    while ($post = $postsSql->fetch()) {
        cot_trash_put(
            'forumpost',
            Cot::$L['forums_post'] . " #" . $post['fp_id'] . " from topic #" . $topicId,
            $post['fp_id'],
            $post,
            $parentTrashId
        );
    }
    $postsSql->closeCursor();
}
