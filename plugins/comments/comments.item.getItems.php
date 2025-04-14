<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=item.getItems
[END_COT_EXT]
==================== */

declare(strict_types = 1);

use cot\dto\ItemDto;
use cot\extensions\ExtensionsDictionary;
use cot\plugins\comments\inc\CommentsDictionary;
use cot\plugins\comments\inc\CommentsDtoRepository;

defined('COT_CODE') or die('Wrong URL');

/**
 * Comments system for Cotonti
 * Get items
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $source
 * @var list<int|numeric-string> $sourceIds
 * @var bool $withFullItemData
 * @var list<ItemDto> $result
 */

if ($source !== CommentsDictionary::SOURCE_COMMENT || empty($sourceIds)) {
    return;
}

// for include files
global $L, $R, $Ls, $db_com;

require_once cot_incfile('comments', ExtensionsDictionary::TYPE_PLUGIN);

$commentsIds = [];
foreach ($sourceIds as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $commentsIds[] = $id;
    }
}
$commentsIds = array_unique($commentsIds);

$condition = 'com_id IN (' . implode(',', $commentsIds) . ')';

/* === Hook === */
foreach (cot_getextplugins('comments.item.getItems.main') as $pl) {
    include $pl;
}
/* ===== */

$dtoList = CommentsDtoRepository::getInstance()->getDtoByCondition(
    $condition,
    [],
    'com_id DESC',
    null,
    null,
    $withFullItemData
);
foreach ($dtoList as $dto) {
    $result[$dto->id] = $dto;
}

/* === Hook === */
foreach (cot_getextplugins('comments.item.getItems.done') as $pl) {
    include $pl;
}
/* ===== */
