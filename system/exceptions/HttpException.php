<?php

declare(strict_types=1);

namespace cot\exceptions;

use Exception;

/**
 * HttpException represents an exception caused by an improper request of the end-user.
 *
 * HttpException can be differentiated via its $code property value which
 * keeps a standard HTTP status code (e.g. 404, 500). Error handlers may use this status code
 * to decide how to format the error page.
 *
 * Throwing an HttpException like in the following example will result in the 404 page to be displayed.
 *
 * ```php
 * if ($item === null) { // item does not exist
 *     throw new \cot\exceptions\HttpException('The requested Item could not be found.', 404);
 * }
 * ```
 */
class HttpException extends Exception
{
}