<?php
/**
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\pm\services;

use Cot;

class LangService
{
    /**
     * Loads and returns the language file for the specified language as an array
     * @param string $code Part name (area code or plugin name)
     * @param string $type Part type: 'plug', 'module' or 'core'
     * @param string $lang Set this to override global $lang
     * @param ?string $defaultLang Default (fallback) language code
     * @return array
     */
    public static function load(string $code, string $type, string $lang, ?string $defaultLang = null): array
    {
        if ($defaultLang === null) {
            $defaultLang = Cot::$cfg['defaultlang'];
        }
        $langFile = cot_langfile($code, $type, $defaultLang, $lang);
        if (!$langFile)  {
            $langFile = cot_langfile($code, $type, 'en', 'en');
        }
        if (!$langFile)  {
            return [];
        }
        include $langFile;

        /** @var array $L */

        return $L;
    }
}