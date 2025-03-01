<?php
/**
 * Polls Repository
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\polls\inc;

use Cot;
use cot\repositories\BaseRepository;

class PollsRepository extends BaseRepository
{
    private static $cacheById = [];
    private static $cacheCount = [];

    public static function getTableName(): string
    {
        if (empty(Cot::$db->polls)) {
            Cot::$db->registerTable('polls');
        }
        return Cot::$db->polls;
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

        $condition = 'poll_id = :pollId';
        $params = ['pollId' => $id];

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

        $query = 'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(self::getTableName()) . ' WHERE poll_type = :source';
        $params = ['source' => $source];

        $result = Cot::$db->query($query, $params)->fetchColumn();

        self::$cacheCount[$source] = $result;

        return $result;
    }

    /**
     * @return list<array>
     */
    public function getBySourceId(string $source, string $sourceId): ?array
    {
        $condition = 'poll_type = :source AND poll_code = :sourceId';
        $params = ['source' => $source, 'sourceId' => $sourceId];

        return $this->getByCondition($condition, $params);
    }

    protected function afterFetch(array $item): array
    {
        $item['poll_id'] = (int) $item['poll_id'];
        $item['poll_creationdate'] = (int) $item['poll_creationdate'];

        return $item;
    }
}