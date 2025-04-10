<?php

declare(strict_types=1);

namespace cot\services;

use cot\dto\ItemDto;
use cot\structure\StructureDictionary;
use cot\structure\StructureDtoRepository;
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
     * @var array Objects recently placed in the trash
     */
    private static $recentlyDeleted = [];

    /**
     * @param list<int|string> $sourceIds
     * @return array<int, ItemDto>
     */
    public function getItems(string $source, array $sourceIds, bool $withFullItemData = false): array
    {
        if ($source === StructureDictionary::SOURCE_CATEGORY) {
            return $this->getStructureItems($sourceIds, $withFullItemData);
        }

        $result = [];

        /* === Hook === */
        foreach (cot_getextplugins('item.getItems') as $pl) {
            include $pl;
        }
        /* ===== */

        return $result;
    }

    /**
     * @param int|string $sourceId
     */
    public function get(string $source, $sourceId, bool $withFullItemData = false): ?ItemDto
    {
        $item = $this->getItems($source, [$sourceId], $withFullItemData);
        return $item[$sourceId] ?? null;
    }

    /**
     * Get categories DTOs
     * @param list<int|string> $sourceIds
     * @return array<int, ItemDto>
     */
    private function getStructureItems(array $sourceIds, bool $withFullItemData = false): array
    {
        $categoryIds = [];
        foreach ($sourceIds as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $categoryIds[] = $id;
            }
        }
        $categoryIds = array_unique($categoryIds);

        $condition = 'structure_id IN (' . implode(',', $categoryIds) . ')';

        $dtoList = StructureDtoRepository::getInstance()->getDtoByCondition(
            $condition,
            [], 'structure_id DESC',
            null,
            null,
            $withFullItemData
        );

        $result = [];

        foreach ($dtoList as $dto) {
            $result[$dto->id] = $dto;
        }

        /* === Hook === */
        foreach (cot_getextplugins('structure.item.getItems.done') as $pl) {
            include $pl;
        }
        /* ===== */

        return $result;
    }

    /**
     * @param string $source
     * @param int|string $sourceId
     * @return bool
     */
    public function isRecentlyDeleted(string $source, $sourceId): bool
    {
        return isset(self::$recentlyDeleted[$source])
            && is_array(self::$recentlyDeleted[$source])
            && in_array($sourceId, self::$recentlyDeleted[$source]);
    }

    /**
     * Delete item event
     * @var string $source Deleting source code
     * @var int|string $sourceId Deleting source item ID
     */
    public function onDelete(string $source, $sourceId): void
    {
        self::$recentlyDeleted[$source][] = $sourceId;

        // If Trashcan plugin is installed and item deleted to trashcan - trashcan record ID
        $deletedToTrashcanId = 0;

        /* === Hook === */
        foreach (cot_getextplugins('item.delete') as $pl) {
            include $pl;
        }
        /* ===== */
    }
}