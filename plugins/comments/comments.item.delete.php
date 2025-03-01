<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=item.delete
[END_COT_EXT]
==================== */

declare(strict_types=1);

/**
 * Removes comments linked to the item
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $source
 * @var int|string $sourceId
 */

use cot\plugins\comments\inc\CommentsControlService;
use cot\plugins\comments\inc\CommentsDictionary;
use cot\plugins\comments\inc\CommentsService;

defined('COT_CODE') or die('Wrong URL');

if (
    $source === CommentsDictionary::SOURCE_COMMENT
    || !CommentsService::getInstance()->isNeedToProcessItemDelete($source)
) {
    return;
}

CommentsControlService::getInstance()->deleteBySourceId($source, (string) $sourceId);
