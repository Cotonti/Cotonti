<?php

declare(strict_types=1);

namespace cot\exceptions;

/**
 * NotFoundHttpException represents a "Not Found" HTTP exception with status code 404.
 */
class NotFoundHttpException extends HttpException
{
    protected $code = 404;
}