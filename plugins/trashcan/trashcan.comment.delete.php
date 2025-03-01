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
use cot\services\ItemService;

defined('COT_CODE') or die('Wrong URL');

if (!Cot::$cfg['plugin']['trashcan']['trash_comment']) {
    return;
}

require_once cot_incfile('trashcan', ExtensionsDictionary::TYPE_PLUGIN);

if (empty($trashcan)) {
    $trashcan = TrashcanService::getInstance();
}

$isParentItemDeleted = ItemService::getInstance()->isRecentlyDeleted($comment['com_area'], $comment['com_code']);

if (!isset($commentParentTrashcanId) || !isset($commentParentTrashcanId[$comment['com_id']])) {
    $commentParentTrashcanId[$comment['com_id']] = $trashcan->getRecentlyPlacedId(
        $comment['com_area'],
        $comment['com_code']
    ) ?: 0;
}

// If the parent element is not removed to the trash, do not put the comment in the trash.
if ($isParentItemDeleted && $commentParentTrashcanId[$comment['com_id']] === 0) {
    return;
}

// @todo title on site's default language
TrashcanService::getInstance()->put(
    'comment',
    Cot::$L['comments_comment'] . " #" . $comment['com_id'] . " (" . $comment['com_author'] . ")",
    (string) $comment['com_id'],
    $comment,
    $commentParentTrashcanId[$comment['com_id']]
);

