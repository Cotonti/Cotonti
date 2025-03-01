<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comment.delete.done
[END_COT_EXT]
==================== */

/**
 * Trashcan delete page
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var array $comment Comment data
 */

declare(strict_types=1);

use cot\extensions\ExtensionsDictionary;
use cot\plugins\trashcan\inc\TrashcanService;

defined('COT_CODE') or die('Wrong URL');

if (!Cot::$cfg['plugin']['trashcan']['trash_comment']) {
    return;
}

require_once cot_incfile('trashcan', ExtensionsDictionary::TYPE_PLUGIN);

if (empty($trashcan)) {
    $trashcan = TrashcanService::getInstance();
}

if (!isset($commentTrashcanId) || !isset($commentTrashcanId[$comment['com_id']])) {
    $commentTrashcanId[$comment['com_id']] = $trashcan->getRecentlyPlacedId(
        $comment['com_area'],
        $comment['com_code']
    ) ?: 0;
}

// @todo title on site's default language
TrashcanService::getInstance()->put(
    'comment',
    Cot::$L['comments_comment'] . " #" . $comment['com_id'] . " (" . $comment['com_author'] . ")",
    (string) $comment['com_id'],
    $comment,
    $commentTrashcanId[$comment['com_id']]
);

