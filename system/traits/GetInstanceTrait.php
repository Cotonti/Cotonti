<?php
/**
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\traits;

trait GetInstanceTrait
{
    private static $classInstances = [];

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        $class = static::class;
        if (!isset(self::$classInstances[$class])) {
            self::$classInstances[$class] = new static();
        }
        return self::$classInstances[$class];
    }
}