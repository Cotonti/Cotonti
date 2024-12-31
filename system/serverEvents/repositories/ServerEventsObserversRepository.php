<?php

declare(strict_types=1);

namespace cot\serverEvents\repositories;

use Cot;
use cot\traits\GetInstanceTrait;

/**
 * Server Event observers repository
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class ServerEventsObserversRepository
{
    use GetInstanceTrait;

    public function getByUserId(int $userId, ?string $token = null): ?array
    {
        $condition = ['user_id = :userId'];
        $params = ['userId' => $userId];

        if (!empty($token)) {
            $condition[] = 'token = :token';
            $params['token'] = $token;
        }

        $result = $this->getByCondition($condition, $params);
        if (empty($result)) {
            return null;
        }

        return array_shift($result);
    }

    /**
     * Get user sessions count
     */
    public function getCountByUserId(int $userId, ?string $token = null): int
    {
        $table = Cot::$db->quoteTableName(Cot::$db->server_events_observers);

        $condition = ['user_id = :userId'];
        $params = ['userId' => $userId];

        if (!empty($token)) {
            $condition[] = 'token = :token';
            $params['token'] = $token;
        }

        $sqlWhere = $this->prepareCondition($condition);

        $sql = "SELECT COUNT(*) FROM $table $sqlWhere";

        $result = Cot::$db->query($sql, $params)->fetchColumn();

        return (int) $result;
    }

    /**
     * @return array<int, array<int|string>> Return server event observers data
     */
    public function getByCondition(array $condition, array $params = []): array
    {
        $table = Cot::$db->quoteTableName(Cot::$db->server_events_observers);

        $sqlWhere = $this->prepareCondition($condition);

        $sql = "SELECT {$table}.* "
            . " FROM {$table} "
            . $sqlWhere . " ORDER BY {$table}.created_at DESC";

        $items = Cot::$db->query($sql, $params)->fetchAll();
        if (empty($items)) {
            return [];
        }

        $result = [];
        foreach ($items as $item) {
            $item = $this->castAttributes($item);
            $result[$item['id']] = $item;
        }

        return $result;
    }

    private function prepareCondition(array $condition): string
    {
        if ($condition !== []) {
            return ' WHERE (' . implode(') AND (', $condition) . ')';
        }

        return '';
    }

    private function castAttributes(array $item): array
    {
        $item['id'] = (int) $item['id'];
        $item['user_id'] = (int) $item['user_id'];
        $item['last_event_id'] = (int) $item['last_event_id'];

        return $item;
    }
}