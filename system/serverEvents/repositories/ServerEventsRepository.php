<?php

declare(strict_types=1);

namespace cot\serverEvents\repositories;

use Cot;
use cot\serverEvents\ServerEventMessageDto;
use cot\traits\GetInstanceTrait;

/**
 * Server Events repository
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @todo use more fast adapters then MySQL. Redis?
 */
class ServerEventsRepository
{
    use GetInstanceTrait;

    /**
     * @return ServerEventMessageDto[]
     */
    public function getForObserver(int $userId, string $token): array
    {
        $table = Cot::$db->quoteTableName(Cot::$db->server_events);
        $observerTable = Cot::$db->quoteTableName(Cot::$db->server_events_observers);

        $observer = Cot::$db->query(
            'SELECT updated_at, last_event_id FROM ' . $observerTable
            . ' WHERE user_id = :userId AND token = :token',
            ['userId' => $userId, 'token' => $token]
        )->fetch();

        if (empty($observer)) {
            return [];
        }

        $condition = "{$table}.user_id = :userId AND {$table}.created_at >= '{$observer['updated_at']}'";
        if ($observer['last_event_id'] > 0) {
            $condition .= " AND {$table}.id > {$observer['last_event_id']}";
        }

        $sql = 'SELECT * FROM ' . Cot::$db->server_events . " WHERE {$condition} ORDER BY {$table}.created_at ASC";
        $data = Cot::$db->query($sql, [':userId' => $userId]);
        if (empty($data)) {
            return [];
        }

        $result = [];
        foreach ($data as $row) {
            $result[] = ServerEventMessageDto::fromArray($row);
        }
        return $result;
    }
}