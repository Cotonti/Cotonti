<?php
/**
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\traits;

trait GetInstanceTrait
{
    private static $classInstance = null;

    public static function getInstance(): self
    {
        if (static::$classInstance === null) {
            static::$classInstance = new static();
        }

        return static::$classInstance;
    }
}