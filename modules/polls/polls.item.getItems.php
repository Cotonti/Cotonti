<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=item.getItems
[END_COT_EXT]
==================== */

declare(strict_types = 1);

use cot\dto\ItemDto;
use cot\modules\polls\inc\PollsDictionary;
use cot\modules\polls\inc\PollsHelper;
use cot\modules\polls\inc\PollsRepository;

defined('COT_CODE') or die('Wrong URL');

/**
 * Polls
 * Get items
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $source
 * @var list<int|numeric-string> $sourceIds
 * @var bool $withFullItemData
 * @var list<ItemDto> $result
 */

if ($source !== PollsDictionary::SOURCE_POLL || empty($sourceIds)) {
    return;
}

$pollsIds = [];
foreach ($sourceIds as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $pollsIds[] = $id;
    }
}
$pollsIds = array_unique($pollsIds);

$condition = 'poll_id IN (' . implode(',', $pollsIds) . ')';
$polls = PollsRepository::getInstance()->getByCondition($condition);

$pollsHelper = PollsHelper::getInstance();

foreach ($polls as $poll) {
    $dto = new ItemDto(
        PollsDictionary::SOURCE_POLL,
        $poll['poll_id'],
        'polls',
        Cot::$L['Poll'],
        $poll['poll_text'],
        '',
        $pollsHelper->getUrl($poll),
        null
    );
    if ($withFullItemData) {
        $dto->data = $poll;
    }
    $result[$dto->id] = $dto;
}


/* === Hook === */
foreach (cot_getextplugins('polls.item.getItems.done') as $pl) {
    include $pl;
}
/* ===== */
