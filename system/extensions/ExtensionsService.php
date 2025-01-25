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

    /**
     * Get default action for extension if exists
     * @return ?array{controller: class-string, action: string}
     */
    public function getDefaultAction(
        string $extensionCode,
        ?string $extensionType = null,
        bool $isAdminPart = false
    ): ?array {
        $router = Router::getInstance();

        if ($extensionType === null) {
            $extensionType = $this->getType($extensionCode);
        }

        $result = [
            'controller' => $router->getControllerClass(
                ExtensionsDictionary::DEFAULT_CONTROLLER_ID,
                $extensionCode,
                $extensionType,
                $isAdminPart
            ),
        ];

        if ($result['controller'] === null) {
            return null;
        }

        $result['action'] = $router->getActionName(null, $result['controller']);
        if ($result['action'] === null) {
            return null;
        }

        return $result;
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
     * @return string ExtensionsDictionary::TYPE_MODULE or ExtensionsDictionary::TYPE_PLUGIN
     *   or NULL if extension is not found or not installed
     */
    public function getType(string $extensionCode): ?string
    {
        $moduleFound = false;
        $pluginFound = false;
        if (file_exists(Cot::$cfg['modules_dir'] . '/' . $extensionCode)) {
            $moduleFound = true;
        }

        if (file_exists(Cot::$cfg['plugins_dir'] . '/' . $extensionCode)) {
            $pluginFound = true;
        }

        if ($moduleFound && $pluginFound) {
            if ($this->isModuleActive($extensionCode)) {
                return ExtensionsDictionary::TYPE_MODULE;
            }

            if ($this->isPluginActive($extensionCode)) {
                return ExtensionsDictionary::TYPE_PLUGIN;
            }

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
        $result = '__default__';

        /* === Hook === */
        foreach (cot_getextplugins('extensionService.getPublicPageUrl') as $pl) {
            include $pl;
        }
        /* ===== */

        if ($result !== '__default__') {
            if ($result === false) {
                return null;
            }
            return $result;
        }

        if (!$this->isActive($extensionCode, $extensionType)) {
            return null;
        }

        $hook = $extensionType === ExtensionsDictionary::TYPE_MODULE ? 'module' : 'standalone';

        if (!empty(cot_getextplugins($hook, true, $extensionCode))) {
            return cot_url($extensionCode);
        }

        if ($this->getDefaultAction($extensionCode, $extensionType) !== null) {
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
        $result = '__default__';

        /* === Hook === */
        foreach (cot_getextplugins('extensionService.hasPublicPage') as $pl) {
            include $pl;
        }
        /* ===== */

        if ($result !== '__default__') {
            if ($result === false) {
                return null;
            }
            return $result;
        }

        if (!$this->isActive($extensionCode, $extensionType)) {
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

        if ($this->getDefaultAction($extensionCode, $extensionType, true) !== null) {
            return cot_url('admin', ['m' => $extensionCode]);
        }

        return null;
    }

    /**
     * Get extension's rights (ACL) settings url
     */
    public function getRightsUrl(string $extensionCode, ?string $extensionType = null): ?string
    {
        if (!$this->isActive($extensionCode, $extensionType)) {
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
     * Checks if an extension is currently installed
     * @todo to ExtensionsControlService
     */
    public function isInstalled(string $extensionCode): bool
    {
        $cnt = cot::$db->query(
            'SELECT COUNT(*) FROM ' . Cot::$db->core . ' WHERE ct_code = :code',
            ['code' => $extensionCode]
        )->fetchColumn();

        return $cnt > 0;
    }

    /**
     * Checks if an extension is currently installed and active
     */
    public function isActive(string $extensionCode, ?string $extensionType = null, $refreshData = false): bool
    {
        global $cot_modules, $cot_plugins_enabled;

        if ($refreshData) {
            $data = ExtensionsRepository::getInstance()->getByCode($extensionCode, $extensionType, true);
            $active = !empty($data);

            if (!$active) {
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

            return $active;
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
     * @see ExtensionsService::isActive()
     */
    public function isModuleActive(string $extensionCode): bool
    {
        return $this->isActive($extensionCode, ExtensionsDictionary::TYPE_MODULE);
    }

    /**
     * Checks if a plugin is currently installed and active
     * @see ExtensionsService::isActive()
     */
    public function isPluginActive(string $extensionCode): bool
    {
        return $this->isActive($extensionCode, ExtensionsDictionary::TYPE_PLUGIN);
    }
}