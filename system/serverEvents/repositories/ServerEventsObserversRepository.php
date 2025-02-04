<?php

declare(strict_types=1);

namespace cot\serverEvents\repositories;

use Cot;
use cot\repositories\BaseRepository;

defined('COT_CODE') or die('Wrong URL');

/**
 * Server Event observers repository
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class ServerEventsObserversRepository extends BaseRepository
{
    public static function getTableName(): string
    {
        if (empty(Cot::$db->server_events_observers)) {
            Cot::$db->registerTable('server_events_observers');
        }
        return Cot::$db->server_events_observers;
    }

    public function getByUserId(int $userId, ?string $token = null): ?array
    {
        $condition = ['user_id = :userId'];
        $params = ['userId' => $userId];

        if (!empty($token)) {
            $condition[] = 'token = :token';
            $params['token'] = $token;
        }

        $result = $this->getByCondition($condition, $params, 'created_at DESC');
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

        $sqlWhere = 'WHERE ' . $this->prepareCondition($condition);

        $sql = "SELECT COUNT(*) FROM $table $sqlWhere";

        $result = Cot::$db->query($sql, $params)->fetchColumn();

        return (int) $result;
    }

    protected function afterFetch(array $item): array
    {
        $item['id'] = (int) $item['id'];
        $item['user_id'] = (int) $item['user_id'];
        $item['last_event_id'] = (int) $item['last_event_id'];

        return $item;
    }
}