<?php
/**
 * @package Extensions
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\extensions;

/**
 * Extensions Dictionary
 * @package Extensions
 */
class ExtensionsDictionary
{
    /**
     * Special core type
     */
    public const TYPE_CORE = 'core';

    /**
     * Extension types
     */
    public const TYPE_MODULE = 'module';
    public const TYPE_PLUGIN = 'plug';
}