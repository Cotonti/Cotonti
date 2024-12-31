<?php

namespace cot\serverEvents;

use Cot;
use cot\serverEvents\repositories\ServerEventsObserversRepository;
use cot\traits\GetInstanceTrait;
use BadMethodCallException;

/**
 * Server Event service
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class ServerEventService
{
    use GetInstanceTrait;

    public function createForUser(int $userId, string $eventName, array $data = []): bool
    {
        if ($userId < 1) {
            throw new BadMethodCallException();
        }

        $sessionsCount = ServerEventsObserversRepository::getInstance()->getCountByUserId($userId);

        // This user in not listening for any events
        if ($sessionsCount === 0) {
            return false;
        }

        $data = [
            'user_id' => $userId,
            'event' => $eventName,
            'data' => json_encode($data),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $result = Cot::$db->insert(Cot::$db->server_events, $data);

        return $result > 0;
    }

    /**
     * Delete unneeded user's events data
     */
    public function deleteByUserId(int $userId): void
    {
        if ($userId === 0) {
            throw new BadMethodCallException();
        }

        $table = Cot::$db->quoteTableName(Cot::$db->server_events);
        $observersTable = Cot::$db->quoteTableName(Cot::$db->server_events_observers);

        $expiredCondition = "{$table}.created_at <= '"
            . date('Y-m-d H:i:s', time() - ServerEventsDictionary::EVENT_EXPIRE_IN) . "'";

        $sessionsCount = ServerEventsObserversRepository::getInstance()->getCountByUserId($userId);

        $params = ['userId' => $userId];

        if ($sessionsCount === 0) {
            // No user's connections left. Delete all his events data
            $condition = "{$table}.user_id = :userId OR ({$expiredCondition})";
        } else {
            $lastData = Cot::$db->query(
                "SELECT MIN(last_event_id) as last_event_id, MIN(created_at) as min_created_at "
                . " FROM {$observersTable} WHERE user_id = :userId GROUP BY user_id",
                ['userId' => $userId]
            )->fetch();

            if (empty($lastData)) {
                return;
            }

            $deleteConditions = [
                "{$table}.created_at <= '{$lastData['min_created_at']}'",
            ];
            if ($lastData['last_event_id'] > 0) {
                $deleteConditions[] = "{$table}.last_event_id <= {$lastData['last_event_id']}";
            }

            $condition = "({$table}.user_id = :userId AND (" . implode(' OR ', $deleteConditions) . ")) "
            . " OR ({$expiredCondition})";
        }

        Cot::$db->delete(Cot::$db->server_events, $condition, $params);
    }

    public function clearExpired(): void
    {
        $table = Cot::$db->quoteTableName(Cot::$db->server_events);
        $observersTable = Cot::$db->quoteTableName(Cot::$db->server_events_observers);

        $condition = "{$table}.created_at <= '"
            . date('Y-m-d H:i:s', time() - ServerEventsDictionary::EVENT_EXPIRE_IN) . "' "
            . " OR {$table}.user_id NOT IN (SELECT DISTINCT user_id FROM {$observersTable})";

        Cot::$db->delete(Cot::$db->server_events, $condition);
    }
}