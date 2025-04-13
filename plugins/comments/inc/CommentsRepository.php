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

    /**
     * @return list<array>
     */
    public function getBySourceId(
        string $source,
        ?string $sourceId,
        $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): ?array {
        $condition = ['com_area = :source'];
        $params = ['source' => $source];
        if ($sourceId !== null) {
            $condition[] = 'com_code = :sourceId';
            $params['sourceId'] = $sourceId;
        }

        return $this->getByCondition($condition, $params, $orderBy, $limit, $offset);
    }

    /**
     * @param int|string|null $sourceId
     */
    public function getCountBySourceId(string $source, $sourceId = null, bool $useCache = true): int
    {
        if ($source === '') {
            return 0;
        }

        $key = $source . '_' . ((string) $sourceId);

        if ($useCache && isset(self::$cacheCount[$key])) {
            return self::$cacheCount[$key];
        }

        $condition = ' WHERE com_area = :source';
        $params = ['source' => $source];
        if ($sourceId !== null) {
            $condition .= ' AND com_code = :sourceId';
            $params['sourceId'] = $sourceId;
        }

        $query = 'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(self::getTableName()) . $condition;

        $result = Cot::$db->query($query, $params)->fetchColumn();

        self::$cacheCount[$key] = $result;

        return $result;
    }

    public function afterFetch(array $item): array
    {
        $item['com_id'] = (int) $item['com_id'];
        $item['com_authorid'] = (int) $item['com_authorid'];
        $item['com_date'] = (int) $item['com_date'];

        return $item;
    }
}