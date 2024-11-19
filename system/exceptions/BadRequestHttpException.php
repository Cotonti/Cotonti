<?php

declare(strict_types=1);

namespace cot\exceptions;

/**
 * "Bad Request" HTTP exception with status code 400.
 * @see https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.1
 */
class BadRequestHttpException extends HttpException
{
    protected $code = 400;
}