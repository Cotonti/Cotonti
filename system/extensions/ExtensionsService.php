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
use cot\router\Router;
use cot\traits\GetInstanceTrait;

/**
 * Extensions Service
 * @package Extensions
 */
class ExtensionsService
{
    use GetInstanceTrait;

    public function getTitle(string $extensionCode, ?string $extensionType = null, ? string $lang = null): string
    {
        global $cot_modules, $cot_plugins_enabled;

        if ($extensionType === null) {
            $extensionType = $this->getType($extensionCode);
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
            $extensionType = $this->getType($extensionCode);
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

    /**
     * @return array<string, array{code: string, title: string, version: string}>
     */
    public function getModulesList(): array
    {
        global $cot_modules;
        return $cot_modules;
    }

    /**
     * @return array<string, array{code: string, title: string, version: string}>
     */
    public function getPluginsList(): array
    {
        global $cot_plugins_enabled;
        return $cot_plugins_enabled;
    }


    /**
     * Get extension type: module or plugin
     * @return string ExtensionsDictionary::EXTENSION_TYPE_MODULE or ExtensionsDictionary::EXTENSION_TYPE_PLUGIN
     *   or NULL if extension is not found or not installed
     */
    public function getType(string $extensionCode): ?string
    {
        $moduleFound = false;
        $pluginFound = false;
        if (
            file_exists(Cot::$cfg['modules_dir'] . '/' . $extensionCode)
            && $this->isInstalled($extensionCode, ExtensionsDictionary::TYPE_MODULE)
        ) {
            $moduleFound = true;
        }

        if (
            file_exists(Cot::$cfg['plugins_dir'] . '/' . $extensionCode)
            && $this->isInstalled($extensionCode, ExtensionsDictionary::TYPE_PLUGIN)
        ) {
            $pluginFound = true;
        }

        if ($moduleFound && $pluginFound) {
            // Need to query the db to check which one is installed
            $result = Cot::$db->query(
                'SELECT ct_plug FROM ' . Cot::$db->core . ' WHERE ct_code = ? LIMIT 1',
                $extensionCode
            )->fetch();

            if (!$result) {
                $moduleFound = $pluginFound = false;
            } elseif ($result['ct_plug']) {
                $moduleFound = false;
            } else {
                $pluginFound = false;
            }
        }

        if ($moduleFound) {
            return ExtensionsDictionary::TYPE_MODULE;
        }

        if ($pluginFound) {
            return ExtensionsDictionary::TYPE_PLUGIN;
        }

        return null;
    }

    /**
     * Get extension's admin page url if exists
     */
    public function getPublicPageUrl(string $extensionCode, ?string $extensionType = null): ?string
    {
        if ($extensionType === null) {
            $extensionType = $this->getType($extensionCode);
        }

        // Hook handler can set it to NULL or STRING
        $result = false;

        /* === Hook === */
        foreach (cot_getextplugins('extensionService.getPublicPageUrl') as $pl) {
            include $pl;
        }
        /* ===== */

        if ($result !== false) {
            return $result;
        }

        if (!$this->isInstalled($extensionCode, $extensionType)) {
            return null;
        }

        $hook = $extensionType === ExtensionsDictionary::TYPE_MODULE ? 'module' : 'standalone';

        if (!empty(cot_getextplugins($hook, true, $extensionCode))) {
            return cot_url($extensionCode);
        }

        $router = Router::getInstance();

        $defaultControllerClass = $router->getControllerClass('index', $extensionCode, $extensionType);
        if ($defaultControllerClass === null) {
            return null;
        }

        if ($router->getActionName(null, $defaultControllerClass) !== null) {
            return cot_url($extensionCode);
        }

        return null;
    }

    /**
     * Get extension's admin page url if exists
     */
    public function getAdminPageUrl(string $extensionCode, ?string $extensionType = null): ?string
    {
        if ($extensionType === null) {
            $extensionType = $this->getType($extensionCode);
        }

        // Hook handler can set it to NULL or STRING
        $result = false;

        /* === Hook === */
        foreach (cot_getextplugins('extensionService.hasPublicPage') as $pl) {
            include $pl;
        }
        /* ===== */

        if ($result !== false) {
            return $result;
        }

        if (!$this->isInstalled($extensionCode, $extensionType)) {
            return null;
        }

        $hooks = ['admin'];
        if ($extensionType === ExtensionsDictionary::TYPE_PLUGIN) {
            $hooks[] = 'tools';
        }

        foreach ($hooks as $hook) {
            if (!empty(cot_getextplugins($hook, true, $extensionCode))) {
                return $hook === 'tools'
                    ? cot_url('admin', ['m' => 'other', 'p' => $extensionCode])
                    : cot_url('admin', ['m' => $extensionCode]);
            }
        }

        $router = Router::getInstance();

        $defaultControllerClass = $router->getControllerClass('index', $extensionCode, $extensionType, true);
        if ($defaultControllerClass === null) {
            return null;
        }

        if ($router->getActionName(null, $defaultControllerClass) !== null) {
            return cot_url('admin', ['m' => $extensionCode]);
        }

        return null;
    }

    /**
     * Get extension's rights (ACL) settings url
     */
    public function getRightsUrl(string $extensionCode, ?string $extensionType = null): ?string
    {
        if (!$this->isInstalled($extensionCode, $extensionType)) {
            return null;
        }

        if ($extensionType === ExtensionsDictionary::TYPE_MODULE) {
            return cot_url('admin', ['m' => 'rightsbyitem', 'ic' => $extensionCode, 'io' => 'a']);
        }

        return cot_url(
            'admin',
            ['m' => 'rightsbyitem', 'ic' => ExtensionsDictionary::TYPE_PLUGIN, 'io' => $extensionCode]
        );
    }

    /**
     * Checks if an extension is currently installed and active
     */
    public function isInstalled(string $extensionCode, ?string $extensionType = null, $refreshData = false): bool
    {
        global $cot_modules, $cot_plugins_enabled;

        if ($refreshData) {
            $data = Cot::$db->query(
                'SELECT ct_code, ct_plug, ct_title, ct_version FROM ' . Cot::$db->core .  ' WHERE ct_code = :code ',
                ['code' => $extensionCode]
            )->fetch();

            $installed = !empty($data);

            if (!$installed) {
                unset($cot_modules[$extensionCode], $cot_plugins_enabled[$extensionCode]);
            } elseif ($data['ct_plug']) {
                $cot_plugins_enabled[$data['ct_code']] = [
                    'code' => $data['ct_code'],
                    'title' => $data['ct_title'],
                    'version' => $data['ct_version'],
                ];
            } else {
                $cot_modules[$data['ct_code']] = [
                    'code' => $data['ct_code'],
                    'title' => $data['ct_title'],
                    'version' => $data['ct_version'],
                ];
            }

            return $installed;
        }

        if ($extensionType === null) {
            $extensionType = $this->getType($extensionCode);
        }

        if ($extensionType === null) {
            return false;
        }

        if ($extensionType === ExtensionsDictionary::TYPE_MODULE) {
            return isset($cot_modules[$extensionCode]);
        }

        return isset($cot_plugins_enabled[$extensionCode]);
    }

    /**
     * Checks if a module is currently installed and active
     * @see ExtensionsService::isInstalled()
     */
    public function isModuleActive(string $extensionCode): bool
    {
        return $this->isInstalled($extensionCode, ExtensionsDictionary::TYPE_MODULE);
    }

    /**
     * Checks if a plugin is currently installed and active
     * @see ExtensionsService::isInstalled()
     */
    public function isPluginActive(string $extensionCode): bool
    {
        return $this->isInstalled($extensionCode, ExtensionsDictionary::TYPE_PLUGIN);
    }
}