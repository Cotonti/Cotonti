<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=item.delete
[END_COT_EXT]
==================== */

declare(strict_types=1);

/**
 * Removes polls linked to the item
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $source
 * @var int|string $sourceId
 */

use cot\modules\polls\inc\PollsControlService;
use cot\modules\polls\inc\PollsDictionary;
use cot\modules\polls\inc\PollsService;

defined('COT_CODE') or die('Wrong URL');

if (
    $source === PollsDictionary::SOURCE_POLL
    || !PollsService::getInstance()->isNeedToProcessItemDelete($source)
) {
    return;
}

PollsControlService::getInstance()->deleteBySourceId($source, (string) $sourceId);
