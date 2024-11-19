<?php

declare(strict_types=1);

namespace cot\exceptions;

/**
 * "Forbidden" HTTP exception with status code 403.
 */
class ForbiddenHttpException extends HttpException
{
    protected $code = 403;
}