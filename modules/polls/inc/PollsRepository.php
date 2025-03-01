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

    protected function afterFetch(array $item): array
    {
        $item['poll_id'] = (int) $item['poll_id'];
        $item['poll_creationdate'] = (int) $item['poll_creationdate'];

        return $item;
    }
}