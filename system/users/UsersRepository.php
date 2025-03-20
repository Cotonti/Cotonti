<?php

declare(strict_types=1);

namespace cot\users;

use Cot;
use cot\repositories\BaseRepository;

defined('COT_CODE') or die('Wrong URL');

/**
 * Users repository
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class UsersRepository extends BaseRepository
{
    private static $cacheById = [];

    public static function getTableName(): string
    {
        return Cot::$db->users;
    }

    /**
     * Fetches user entry from DB
     * @param ?int $id User ID
     * @param bool $useCache Use one time session cache
     * @return ?array
     */
    public function getById(?int $id, bool $useCache = true): ?array
    {
        if ($id === null && Cot::$usr['id'] > 0) {
            $id = Cot::$usr['id'];
        }
        if (empty($id) || $id < 1) {
            return null;
        }

        if ($useCache && isset(self::$cacheById[$id])) {
            return self::$cacheById[$id] !== false ? self::$cacheById[$id] : null;
        }

        if ($id === Cot::$usr['id'] && $useCache) {
            $result = Cot::$usr['profile'];
        } else {
            $condition = 'user_id = :userId';
            $params = ['userId' => $id];

            $results = $this->getByCondition($condition, $params);
            $result = !empty($results) ? $results[0] : null;
        }

        self::$cacheById[$id] = !empty($result) ? $result : false;

        return $result;
    }

    /**
     * @param int $groupId
     * @param $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return list<array<string, int|string>> Requested items data
     */
    public function getByGroup(int $groupId, $orderBy = null, ?int $limit = 1000, ?int $offset = null): array
    {
        $condition = 'user_maingrp = :groupId OR user_id IN (SELECT gru_userid FROM '
            . Cot::$db->quoteT(Cot::$db->groups_users) . ' WHERE gru_groupid = :groupId)';
        $params = ['groupId' => $groupId];

        return $this->getByCondition($condition, $params, $orderBy, $limit, $offset);
    }

    protected function afterFetch(array $item): array
    {
        $item['user_id'] = (int) $item['user_id'];
        $item['user_maingrp'] = (int) $item['user_maingrp'];
        $item['user_banexpire'] = (int) $item['user_banexpire'];
        $item['user_regdate'] = (int) $item['user_regdate'];
        $item['user_lastlog'] = (int) $item['user_lastlog'];
        $item['user_lastvisit'] = (int) $item['user_lastvisit'];
        $item['user_logcount'] = (int) $item['user_logcount'];
        $item['user_sidtime'] = (int) $item['user_sidtime'];

        return $item;
    }
}