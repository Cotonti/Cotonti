<?php

namespace cot\serverEvents;

use Cot;
use cot\serverEvents\repositories\ServerEventsObserversRepository;
use cot\traits\GetInstanceTrait;
use BadMethodCallException;

/**
 * Server Event observer service
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class ServerEventsObserverService
{
    use GetInstanceTrait;

    /**
     * Check if user is still connected.
     *
     * After selecting the active user from the database, we can save the current time to the observer table in the
     * 'last_seen' field.
     * Or ping the client.
     * But, this is not necessary. If we disconnect the active user, the browser will reconnect if necessary.
     */
    public function isConnected(int $userId, ?string $token): bool
    {
        $sessionsCount = ServerEventsObserversRepository::getInstance()->getCountByUserId($userId, $token);
        return $sessionsCount > 0;
    }

    public function register(int $userId): ?string
    {
        if ($userId === 0) {
            throw new BadMethodCallException();
        }

        $token = $this->getTokenForCurrentUser();

        $nowDate = date('Y-m-d H:i:s', Cot::$sys['now']);

        $existingObserver = ServerEventsObserversRepository::getInstance()->getByUserId($userId, $token);
        if ($existingObserver !== null) {
            Cot::$db->update(
                Cot::$db->server_events_observers,
                ['updated_at' => $nowDate],
                "id = {$existingObserver['id']}"
            );
            return $existingObserver['token'];
        }

        $data = [
            'user_id' => $userId,
            'token' => $token,
            'created_at' => $nowDate,
            'updated_at' => $nowDate,
            'last_event_id' => 0,
        ];

        Cot::$db->insert(Cot::$db->server_events_observers, $data);

        return $token;
    }

    public function setLastEventId(string $token, int $eventId): bool
    {
        $result = Cot::$db->update(
            Cot::$db->server_events_observers,
            ['last_event_id' => $eventId],
            'token = :token',
            ['token' => $token]
        );

        return $result > 0;
    }

    public function remove(int $userId, string $token): void
    {
        if ($userId === 0) {
            throw new BadMethodCallException();
        }

        Cot::$db->delete(
            Cot::$db->server_events_observers,
            'user_id = :userId AND token = :token',
            ['userId' => $userId, 'token' => $token]
        );

        ServerEventService::getInstance()->deleteByUserId($userId);
    }

    public function clearOld(): void
    {
        $expireDate = date('Y-m-d H:i:s', time() - ServerEventsDictionary::OBSERVER_EXPIRE_IN);

        Cot::$db->delete(
            Cot::$db->server_events_observers,
            "updated_at <= '{$expireDate}'"
        );

        ServerEventService::getInstance()->clearExpired();
    }

    public function getTokenForCurrentUser(): string
    {
        return session_id();
    }
}