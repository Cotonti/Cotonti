<?php

declare(strict_types=1);

namespace cot\services;

use cot\dto\ItemDto;
use cot\traits\GetInstanceTrait;

defined('COT_CODE') or die('Wrong URL');

/**
 * Items service
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class ItemService
{
    use GetInstanceTrait;

    /**
     * @param list<int> $sourceIds
     * @return array<int, ItemDto>
     */
    public function getItems(string $source, array $sourceIds, bool $withFullItemData = false): array
    {
        $result = [];

        /* === Hook === */
        foreach (cot_getextplugins('itemService.getItems') as $pl) {
            include $pl;
        }
        /* ===== */

        return $result;
    }

    public function get(string $source, int $sourceId, bool $withFullItemData = false): ?ItemDto
    {
        $item = $this->getItems($source, [$sourceId], $withFullItemData);
        return $item[$sourceId] ?? null;
    }

    /**
     * Delete item event
     * @var string $source Deleting source code
     * @var int $sourceId Deleting source item ID
     * @var int $deletedToTrashcanId If item deleted to trashcan - trashcan record ID
     */
    public function onDelete(string $source, int $sourceId, int $deletedToTrashcanId = 0): void
    {
        /* === Hook === */
        foreach (cot_getextplugins('itemService.deleteItem') as $pl) {
            include $pl;
        }
        /* ===== */
    }
}