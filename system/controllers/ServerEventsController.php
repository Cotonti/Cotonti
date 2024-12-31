<?php

declare(strict_types=1);

namespace cot\controllers;

use cot\controllers\actions\ServerEvents\IndexAction;

defined('COT_CODE') or die('Wrong URL.');

/**
 * Server Event controller
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events
 */
class ServerEventsController extends BaseController
{
    public static function actions(): array
    {
        return [
            'index' => IndexAction::class,
        ];
    }
}
