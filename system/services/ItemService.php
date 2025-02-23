<?php

declare(strict_types=1);

namespace cot\services;

use cot\dto\ItemDto;
use cot\extensions\ExtensionsService;
use cot\plugins\trashcan\inc\TrashcanService;
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
        foreach (cot_getextplugins('item.getItems') as $pl) {
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
     * @var int|string $sourceId Deleting source item ID
     */
    public function onDelete(string $source, $sourceId): void
    {
        // If Trashcan plugin is installed and item deleted to trashcan - trashcan record ID
        $deletedToTrashcanId = 0;

        /* === Hook === */
        foreach (cot_getextplugins('item.delete') as $pl) {
            include $pl;
        }
        /* ===== */
    }
}