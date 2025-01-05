<?php
/**
 * @package Extensions
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\extensions;

defined('COT_CODE') or die('Wrong URL.');

use Cot;
use cot\traits\GetInstanceTrait;

class ExtensionsHelper
{
    use GetInstanceTrait;

    public function getTitle(string $extensionCode, ?string $extensionType = null, ? string $lang = null): string
    {
        global $cot_modules, $cot_plugins_enabled;

        if ($extensionType === null) {
            $extensionType = ExtensionsService::getInstance()->getType($extensionCode);
        }

        // Some extension files depends on main lang file. For example: PFS
        $L = Cot::$L;

        unset($L[$extensionCode . '_title'], $L['plu_title'], $L['info_name']);

        $langFile = cot_langfile($extensionCode, $extensionType, 'en', $lang);
        if (!empty($langFile) && file_exists($langFile)) {
            include $langFile;
        }

        if (!empty($L[$extensionCode . '_title'])) {
            return $L[$extensionCode . '_title'];
        }

        if (!empty($L['plu_title'])) {
            return $L['plu_title'];
        }

        if (!empty($L['info_name'])) {
            return $L['info_name'];
        }

        if (
            $extensionType === ExtensionsDictionary::TYPE_MODULE
            && isset($cot_modules[$extensionCode])
        ) {
            return $cot_modules[$extensionCode]['title'];
        }

        if (
            $extensionType === ExtensionsDictionary::TYPE_PLUGIN
            && isset($cot_plugins_enabled[$extensionCode])
        ) {
            return $cot_plugins_enabled[$extensionCode]['title'];
        }

        $extensionDirectory = $extensionType === ExtensionsDictionary::TYPE_MODULE
            ? Cot::$cfg['modules_dir']
            : Cot::$cfg['plugins_dir'];

        $setupFile = $extensionDirectory . '/' . $extensionCode . '/' . $extensionCode . '.setup.php';
        $exists = file_exists($setupFile);
        if ($exists) {
            $info = cot_infoget($setupFile, 'COT_EXT');
            if (!empty($info['Name'])) {
                return $info['Name'];
            }
        }

        return $extensionCode;
    }

    public function getDescription(string $extensionCode, ?string $extensionType = null, ? string $lang = null): string
    {
        if ($extensionType === null) {
            $extensionType = ExtensionsService::getInstance()->getType($extensionCode);
        }

        // Some extension files depends on main lang file. For example: PFS
        $L = Cot::$L;

        unset($L[$extensionCode . '_description'], $L['info_desc']);

        $langFile = cot_langfile($extensionCode, $extensionType, 'en', $lang);
        if (!empty($langFile) && file_exists($langFile)) {
            include $langFile;
        }

        if (!empty($L[$extensionCode . '_description'])) {
            return $L[$extensionCode . '_description'];
        }

        if (!empty($L['info_desc'])) {
            return $L['info_desc'];
        }

        $extensionDirectory = $extensionType === ExtensionsDictionary::TYPE_MODULE
            ? Cot::$cfg['modules_dir']
            : Cot::$cfg['plugins_dir'];

        $setupFile = $extensionDirectory . '/' . $extensionCode . '/' . $extensionCode . '.setup.php';
        $exists = file_exists($setupFile);
        if ($exists) {
            $info = cot_infoget($setupFile, 'COT_EXT');
            if (!empty($info['Description'])) {
                return $info['Description'];
            }
        }

        return '';
    }
}