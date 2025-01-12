<?php

declare(strict_types=1);

namespace cot\serverEvents;

defined('COT_CODE') or die('Wrong URL');

/**
 * Server Event dictionary
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class ServerEventsDictionary
{
    /**
     * Clear old observers every
     * 1 hour
     */
    public const CLEAR_OLD_OBSERVERS_PERIOD = 3600;

    /**
     * Check if user still connected
     * 10 minutes
     */
    public const CHECK_CONNECTION_PERIOD = 600;

    /**
     * Observers older than this value will be deleted
     * 1 day (24 hours)
     */
    public const OBSERVER_EXPIRE_IN = 86400;

    /**
     * Events older than this value will be deleted
     * 5 minutes
     */
    public const EVENT_EXPIRE_IN = 300;
}