<?php

declare(strict_types=1);

namespace cot;

use cot\exceptions\BadRequestHttpException;
use cot\exceptions\ForbiddenHttpException;
use cot\exceptions\HttpException;
use cot\exceptions\NotFoundHttpException;
use cot\traits\GetInstanceTrait;
use Throwable;

defined('COT_CODE') or die('Wrong URL');

class ErrorHandler
{
    use GetInstanceTrait;

    public function handle(Throwable $exception): bool
    {
        // cot class can be not initialised yet
        global $cfg, $usr;

        $logException = true;
        if (
            $exception instanceof ForbiddenHttpException
            || $exception instanceof NotFoundHttpException
            || $exception instanceof BadRequestHttpException
        ) {
            $logException = false;
        }

        if ($logException) {
            error_log((string) $exception);
        }

        if ($exception instanceof ForbiddenHttpException) {
            cot_block(false);
        }

        if ($exception instanceof HttpException) {
            $displayErrors = $cfg['display_errors'] || in_array(COT_GROUP_SUPERADMINS, $usr['groups']);

            cot_die_message(
                $exception->getCode(),
                true,
                '',
                $displayErrors ? $exception->getMessage() : ''
            );
        }

        $this->displayError($exception);

        return true;
    }

    /**
     * Show exception to the user and terminates script execution
     */
    public function displayError(Throwable $exception): void
    {
        // cot class can be not initialised yet
        global $cfg, $usr;

        $displayErrors = $cfg['display_errors'] || in_array(COT_GROUP_SUPERADMINS, $usr['groups']);

        if ($displayErrors) {
            $mainTitle = $cfg['maintitle'] ?? $cfg['mainurl'];
            $message_body = '<p><em>' . @date('Y-m-d H:i') . '</em></p>';
            $message_body .= '<p>' . $exception->getMessage() . ' in ' . $exception->getFile() . ':'
                . $exception->getLine() . '</p>';
            $message_body .= '<pre style="overflow: auto">' . $exception->getTraceAsString() . '</pre>';
            $message_body .= '<hr /><a href="' . $cfg['mainurl'] . '">' . $mainTitle . '</a>';
            cot_die_message(500, true, '', $message_body);
        } else {
            cot_die_message(503, true);
        }
    }
}