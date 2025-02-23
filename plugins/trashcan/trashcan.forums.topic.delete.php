<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topic.delete
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
 *
 * @todo Log message on default language.
 */

use cot\modules\forums\inc\ForumsDictionary;
use cot\plugins\trashcan\inc\TrashcanService;

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['trashcan']['trash_forum']) {
	// We are inside ForumsTopicsControlService::deleteByData() method, so need some globals

	global $trash_types;

	require_once cot_incfile('trashcan', 'plug');

    $trashcan = TrashcanService::getInstance();

    // Add topic to trash
    $parentTrashId = $trashcan->put(ForumsDictionary::SOURCE_TOPIC, Cot::$L['Topic'] . ' #' . $topicId, (string) $topicId, $topic);
}
