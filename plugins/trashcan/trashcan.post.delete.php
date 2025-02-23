<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.post.delete.done
[END_COT_EXT]
==================== */

declare(strict_types=1);

/**
 * Trashcan delete post
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var array $forumPost Forums post data
 * @var int $trashcanId
 */

use cot\modules\forums\inc\ForumsDictionary;
use cot\plugins\trashcan\inc\TrashcanService;

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('trashcan', 'plug');

if (Cot::$cfg['plugin']['trashcan']['trash_forum']) {
    if (empty($trashcan)) {
        $trashcan = TrashcanService::getInstance();
    }

    if (!isset($topicTrashcanId) || !isset($topicTrashcanId[$forumPost['fp_topicid']])) {
        $topicTrashcanId[$forumPost['fp_topicid']] = $trashcan->getRecentlyPlacedId(
            ForumsDictionary::SOURCE_TOPIC,
            (string) $forumPost['fp_topicid']
        ) ?: 0;
    }

    $trashcanId = TrashcanService::getInstance()->put(
        ForumsDictionary::SOURCE_POST,
        Cot::$L['forums_post'] . " #" . $forumPost['fp_id'] . " from topic #" . $forumPost['fp_topicid'],
        (string) $forumPost['fp_id'],
        $forumPost,
        $topicTrashcanId[$forumPost['fp_topicid']]
    );
}
