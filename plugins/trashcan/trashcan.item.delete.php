<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=item.delete
Order=3
[END_COT_EXT]
==================== */

/**
 * Trashcan on item delete
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $source
 * @var int $sourceId
 * @var int $deletedToTrashcanId
 */

declare(strict_types=1);

use cot\plugins\trashcan\inc\TrashcanService;

$deletedToTrashcanId = TrashcanService::getInstance()->getRecentlyPlacedId($source, (string) $sourceId);
