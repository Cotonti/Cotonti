<?php

declare(strict_types=1);

namespace cot\router;

use Cot;
use cot\controllers\BaseController;
use cot\dictionaries\CotontiDictionary;
use cot\exceptions\NotFoundHttpException;

class Router
{
    /**
     * Routing for public part (front-end)
     */
    public function route(): ?Route
    {
        $extensionCode = isset($_GET['e']) ? cot_import('e', 'G', 'ALP') : null;
        if ($extensionCode === 'admin') {
            throw new NotFoundHttpException();
        }

        $ajax = cot_import('r', 'G', 'ALP');
        $popup = cot_import('o', 'G', 'ALP');
        if (!$extensionCode) {
            // Support for ajax and popup hooked plugins
            if (!empty($ajax)) {
                $extensionCode = $ajax;
            } elseif (!empty($popup)) {
                $extensionCode = $popup;
            }
        }

        // @todo в админке $m содержит $extensionCode
        // @todo использовать $n для единообразия
        $m = cot_import('m', 'G', 'ALP', 24);
        $n = cot_import('n', 'G', 'ALP', 24);
        $a = cot_import('a', 'G', 'ALP', 24);

        $actionId = null;

        if ($extensionCode === null) {
            // It can be core controller or an "index" module
            if ($m !== null || $a !== null) {
                // @todo use self::getController()
                $controllerClass = $this->getControllerClass($m ?? 'main');
                if ($controllerClass !== null) {
                    $actionId = $this->getActionName($a, $controllerClass);
                    if ($m !== null && $actionId === null) {
                        throw new NotFoundHttpException(
                            'Action "' . $actionId . '" not found in controller "'
                            . $controllerClass . '"'
                        );
                    } elseif ($actionId === null) {
                        $controllerClass = null;
                        unset($controller);
                    }
                }

                if ($controllerClass !== null) {
                    $route = new Route();
                    $route->controller = new $controllerClass();
                    $route->action = $actionId;

                    return $route;
                }
            }
                // 'index' module
                $extensionCode = 'index';
        }

         if (!preg_match('`^\w+$`', $extensionCode)) {
            // @todo error message ?
            throw new NotFoundHttpException();
        }

        $moduleFound = false;
        $pluginFound = false;
        if (
            file_exists(Cot::$cfg['modules_dir'] . '/' . $extensionCode)
            && cot_module_active($extensionCode)
        ) {
            $moduleFound = true;
        }
        if (
            file_exists(Cot::$cfg['plugins_dir'] . '/' . $extensionCode)
            && cot_plugin_active($extensionCode)
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
            Cot::$env['type'] = CotontiDictionary::EXTENSION_TYPE_MODULE;
            Cot::$env['location'] = $extensionCode;
            define('COT_MODULE', true);
        } elseif ($pluginFound) {
            Cot::$env['type'] = CotontiDictionary::EXTENSION_TYPE_PLUGIN;
            Cot::$env['location'] = 'plugins';
            define('COT_PLUG', true);
        } else {
            // @todo translate
            throw new NotFoundHttpException("Extension '" . $extensionCode . "' not found");
        }

        Cot::$env['ext'] = $extensionCode;

        if ($popup !== null) {
            $hook = $popup;
        } elseif ($ajax !== null) {
            $hook = $ajax;
        } else {
            $hook = $moduleFound ? 'module' : 'standalone';
        }

        $route = new Route();

        $route->includeFiles = cot_getextplugins($hook, true, $extensionCode, Cot::$env['type']);

        if ($route->includeFiles !== []) {
            return $route;
        }

        $controller = $this->getController($m ?? 'index', $extensionCode);
        if ($controller === null) {
            // @todo error message ?
            throw new NotFoundHttpException();
        }

        $controllerClass = get_class($controller);
        $actionId = $this->getActionName($a, $controllerClass);
        if ($actionId === null) {
            throw new NotFoundHttpException(
                // @todo translate
                'Action "' . $a . '" not found in controller "' . $controllerClass . '"'
            );
        }

        $route->controller = $controller;
        $route->action = $actionId;

        return $route;
    }

    public function routeAdmin(): ?Route
    {
        $standardAdmin = [
            'cache.disk',
            'cache',
            'config',
            'extrafields',
            'extensions',
            'home',
            'infos',
            'log',
            'other',
            'phpinfo',
            'rights',
            'rightsbyitem',
            'structure',
            'urls',
            'users'
        ];

        $extensionCode = cot_import('m', 'G', 'ALP', 24);
        $controllerId = cot_import('n', 'G', 'ALP', 24);
        $actionId = cot_import('a', 'G', 'ALP', 24);
        $s = cot_import('s', 'G', 'ALP', 24);

        // Standard admin include files
        $includeFile = null;
        if ($extensionCode !== null) {
            $includeFile = $extensionCode;
        } elseif ($controllerId === null && $actionId === null) {
            $includeFile = 'home';
        }
        if ($includeFile !== '') {
            if (!empty($s)) {
                $includeFile = $includeFile . '.' . $s;
            }
            $standardIncFile = cot_incfile('admin', 'module', $includeFile);
            if (in_array($includeFile, $standardAdmin) && file_exists($standardIncFile)) {
                $route = new Route();
                $route->includeFiles = [$standardIncFile];
                return $route;
            }
        }

        // Core controllers
        if ($extensionCode === null && $controllerId !== null) {
            $controller = $this->getController($m ?? 'main', 'admin');
            var_dump($controller);
        }

        // Проверять права на администрирование расширения?
        // Extensions include files

        // Extensions controllers
        die;
    }

    /**
     * @param string $controllerName Controller name from request
     * @param ?string $extensionCode
     * @param ?string $extensionType
     */
    public function getController(
        string $controllerName,
        ?string $extensionCode = null,
        ?string $extensionType = null
    ): ?BaseController {
        $className = $this->getControllerClass($controllerName, $extensionCode, $extensionType);
        if ($className === null) {
            return null;
        }

        $controller = new $className();
        if (!($controller instanceof BaseController)) {
            return null;
        }

        return $controller;
    }

    /**
     * @param string $controller Controller name from request
     * @param ?string $extensionCode
     * @param ?string $extensionType
     * @param bool $ifExists Check if class exists
     * @return ($ifExists is true ? ?string : string) Controller class name.
     *
     * @todo попробовать получить с фронта доступ к админ контроллеру и прикрыть лавочку
     */
    public function getControllerClass(
        string $controller,
        ?string $extensionCode = null,
        ?string $extensionType = null,
        bool $ifExists = true
    ): ?string {
        $result = '\\cot\\';
        if ($extensionCode !== null) {
            if ($extensionType === CotontiDictionary::EXTENSION_TYPE_PLUGIN) {
                $result .= 'plugins'. '\\';
            } elseif ($extensionType === CotontiDictionary::EXTENSION_TYPE_MODULE) {
                $result .= 'modules'. '\\';
            }
            $result .= $extensionCode . '\\';
        }
        $result .=  'controllers\\' . $this->prepareName($controller) . 'Controller';

        if ($ifExists) {
            return class_exists($result) ? $result : null;
        }

        return $result;
    }

    /**
     * @param class-string<BaseController> $controller
     */
    public function getActionName(?string $action, string $controller): ?string
    {
        if ($action !== null) {
            $actionName = 'action' . $this->prepareName($action);
            if (method_exists($controller, $actionName)) {
                return $action;
            }

            $actions = $controller::actions();
            if (array_key_exists($action, $actions)) {
                return $action;
            }

            return null;
        }

        if (!empty($controller::$defaultAction)) {
            return $this->getActionName($controller::$defaultAction, $controller);
        }

        return null;
    }

    private function prepareName(string $name): string
    {
        return BaseController::prepareName($name);
    }
}