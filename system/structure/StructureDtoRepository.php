<?php
/**
 * Structure Dto Repository
 *
 * @see ItemDto
 * @package Structure
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\structure;

use Cot;
use cot\dto\ItemDto;
use cot\repositories\BaseRepository;

class StructureDtoRepository extends BaseRepository
{
    /**
     * @var mixed
     */
    private static $cacheById;

    public static function getTableName(): string
    {
        return Cot::$db->structure;
    }

    /**
     * Fetches comment from DB
     * @param int $id Comment ID
     * @param bool $useCache Use one time session cache
     * @return ?array
     */
    public function getById(int $id, bool $withFullItemData = false, bool $useCache = true): ?ItemDto
    {
        if ($id < 1) {
            return null;
        }

        if ($useCache && isset(self::$cacheById[$id])) {
            return self::$cacheById[$id] !== false ? self::$cacheById[$id] : null;
        }

        $condition = 'structure_id = :id';
        $params = ['id' => $id];

        $results = $this->getDtoByCondition($condition, $params, null, null, null, $withFullItemData);
        $result = !empty($results) ? reset($results) : null;

        self::$cacheById[$id] = !empty($result) ? $result : false;

        return $result;
    }

    /**
     * @return list<ItemDto>
     * @todo hooks
     */
    public function getDtoByCondition(
        $condition,
        array $params = [],
        $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        bool $withFullItemData = false
    ): array {
        // for include files
        global $L, $R, $Ls;

        $categories = StructureRepository::getInstance()->getByCondition(
            $condition,
            $params,
            $orderBy,
            $limit,
            $offset
        );

        if (empty($categories)) {
            return [];
        }

        /* === Hook === */
        foreach (cot_getextplugins('structure.getItemDtoList.main') as $pl) {
            include $pl;
        }
        /* ===== */

        $structureHelper = StructureHelper::getInstance();

        $result = [];

        foreach ($categories as $categoryData) {
            // @todo use structure helper to prepare data
            $category = Cot::$structure[$categoryData['structure_area']][$categoryData['structure_code']];

            $dto = new ItemDto(
                StructureDictionary::SOURCE_CATEGORY,
                $category['id'],
                '',
                Cot::$L['Category'],
                $category['title'],
                $category['desc'],
                $structureHelper->getUrl($categoryData['structure_area'], $categoryData['structure_code'])
            );

            if ($withFullItemData) {
                $dto->data = $category;
            }

            $result[$category['id']] = $dto;
        }

        /* === Hook === */
        foreach (cot_getextplugins('comments.getItemDtoList.done') as $pl) {
            include $pl;
        }
        /* ===== */

        return $result;
    }

    /**
     * @return list<ItemDto>
     */
    public function getByCondition(
        $condition,
        array $params = [],
        $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return $this->getDtoByCondition($condition, $params, $orderBy, $limit, $offset);
    }
}