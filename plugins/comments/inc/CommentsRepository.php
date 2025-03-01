<?php
/**
 * Comments system for Cotonti
 * Repository
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\inc;

use Cot;
use cot\repositories\BaseRepository;

class CommentsRepository extends BaseRepository
{
    private static $cacheById = [];
    private static $cacheCount = [];

    public static function getTableName(): string
    {
        if (empty(Cot::$db->com)) {
            Cot::$db->registerTable('com');
        }
        return Cot::$db->com;
    }

    /**
     * Fetches comment from DB
     * @param int $id Comment ID
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

        $condition = 'com_id = :commentId';
        $params = ['commentId' => $id];

        $results = $this->getByCondition($condition, $params);
        $result = !empty($results) ? $results[0] : null;

        self::$cacheById[$id] = !empty($result) ? $result : false;

        return $result;
    }

    public function getCountBySource(string $source, bool $useCache = true): int
    {
        if ($source === '') {
            return 0;
        }

        if ($useCache && isset(self::$cacheCount[$source])) {
            return self::$cacheCount[$source];
        }

        $query = 'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(self::getTableName()) . ' WHERE com_area = :source';
        $params = ['source' => $source];

        $result = Cot::$db->query($query, $params)->fetchColumn();

        self::$cacheCount[$source] = $result;

        return $result;
    }

    protected function afterFetch(array $item): array
    {
        $item['com_id'] = (int) $item['com_id'];
        $item['com_authorid'] = (int) $item['com_authorid'];
        $item['com_date'] = (int) $item['com_date'];

        return $item;
    }
}