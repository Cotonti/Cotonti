<?php

namespace cot\controllers\actions\ServerEvents;

use Cot;
use cot\controllers\BaseAction;
use cot\exceptions\NotFoundHttpException;
use cot\serverEvents\repositories\ServerEventsRepository;
use cot\serverEvents\ServerEventsDictionary;
use cot\serverEvents\ServerEventService;
use cot\serverEvents\ServerEventsObserverService;
use Temporary_cache_driver;

class ServerEventsAjaxAction extends BaseAction
{
    private const OLD_OBSERVERS_CHECK_KEY = 'SEOldObserversKey';

    /**
     * @var ?ServerEventsObserverService
     */
    private $observerService;

    /**
     * @var ServerEventService
     */
    private $eventService;

    /**
     * @var ServerEventsRepository
     */
    private $eventsRepository;

    public function __construct()
    {
        $this->observerService = ServerEventsObserverService::getInstance();
        $this->eventService = ServerEventService::getInstance();
        $this->eventsRepository = ServerEventsRepository::getInstance();
    }

    public function run(): string
    {
        if (Cot::$usr['id'] === 0) {
            throw new NotFoundHttpException();
        }

        $this->clearOldObservers();

        $token = $this->observerService->register(Cot::$usr['id']);

        $result = ['events' => []];

        $events = $this->eventsRepository->getForObserver(Cot::$usr['id'], $token);
        if ($events === []) {
            return $this->result($result);
        }

        $lastId = 0;
        foreach ($events as $event) {
            $result['events'][] = $event->toArray();

            if ($event->id > $lastId) {
                $lastId = $event->id;
            }
        }
        $this->observerService->setLastEventId($token, $lastId);
        $this->eventService->deleteByUserId(Cot::$usr['id']);

        return $this->result($result);
    }

    public function result($data): string
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        return json_encode($data);
    }

    private function clearOldObservers(): void
    {
        $lastCleared = $this->getGetLastOldObserversClearedTime();

        if (
            $lastCleared === 0
            || (Cot::$sys['now'] - $lastCleared) < ServerEventsDictionary::CLEAR_OLD_OBSERVERS_PERIOD
        ) {
            return;
        }

        $this->observerService->clearOld();
    }

    private function getGetLastOldObserversClearedTime(): int
    {
        $result = 0;

        $cache = $this->getCache();
        if (!empty($cache)) {
            $result = $cache->get(self::OLD_OBSERVERS_CHECK_KEY);
            if (!$result) {
                $result = 0;
            }
        }

        return $result;
    }

    /**
     * @return Temporary_cache_driver|null
     */
    private function getCache()
    {
        if (!empty(Cot::$cache) && !empty(Cot::$cache->mem)) {
            return Cot::$cache->mem;
        }
        return null;
    }
}