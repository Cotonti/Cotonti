<?php

declare(strict_types=1);

namespace cot\structure;

use Cot;
use cot\repositories\BaseRepository;
use cot\traits\GetInstanceTrait;

defined('COT_CODE') or die('Wrong URL');

/**
 * Structure repository
 * @package Structure
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class StructureRepository extends BaseRepository
{
    use GetInstanceTrait;

    private static $cacheById = [];

    public static function getTableName(): string
    {
        return Cot::$db->structure;
    }

    /**
     * Fetches structure entry from DB
     * @param int $id Category ID
     * @param bool $useCache Use one time session cache
     * @return ?array
     */
    public function getById(int $id, bool $useCache = true): ?array
    {
        if ($id < 1) {
            return null;
        }

        if ($useCache && isset(self::$cacheById[$id])) {
            return self::$cacheById[$id] !== false ? self::$cacheById[$id] : null;
        }

        $condition = 'structure_id = :structureId';
        $params = ['structureId' => $id];

        $results = $this->getByCondition($condition, $params);
        $result = !empty($results) ? $results[0] : null;

        self::$cacheById[$id] = !empty($result) ? $result : false;

        return $result;
    }

    protected function afterFetch(array $item): array
    {
        $item['structure_id'] = (int) $item['structure_id'];
        $item['structure_count'] = (int) $item['structure_count'];

        return $item;
    }
}