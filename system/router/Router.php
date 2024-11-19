<?php

declare(strict_types=1);

namespace cot\router;

use Cot;
use cot\controllers\BaseController;
use cot\exceptions\NotFoundHttpException;
use cot\extensions\ExtensionsDictionary;
use cot\extensions\ExtensionsService;
use cot\traits\GetInstanceTrait;

class Router
{
    use GetInstanceTrait;

    /**
     * Routing for site's public area (front-end)
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

        $n = cot_import('n', 'G', 'ALP', 24);
        $a = cot_import('a', 'G', 'ALP', 24);

        if ($extensionCode === null) {
            // It can be core controller or an "index" module
            if ($n !== null || $a !== null) {
                $route = $this->processController(null, $n, $a, null);
                if ($route) {
                    return $route;
                }
            }

            /**
             * In the site's public area, if the request does not contain the extension id or the system
             * controller id or action id, the "index" module will be executed
             */
            $extensionCode = 'index';
        }

         if (!preg_match('`^\w+$`', $extensionCode)) {
            // @todo error message ?
            throw new NotFoundHttpException();
        }

        $extensionType = ExtensionsService::getInstance()->getType($extensionCode);
        if ($extensionType === null) {
            throw new NotFoundHttpException();
        }

        // Extension's controller
        $route = $this->processController($extensionCode, $n, $a, $extensionType);
        if ($route === null) {
            // Extension's include files
            $route = $this->processFrontIncludeFiles(
                $extensionCode,
                $extensionType,
                $ajax !== null,
                $popup !== null
            );
        }

        if ($route === null) {
            throw new NotFoundHttpException();
        }

        Cot::$env['ext'] = $extensionCode;
        Cot::$env['type'] = $extensionType;

        if ($extensionType === ExtensionsDictionary::TYPE_MODULE) {
            Cot::$env['location'] = $extensionCode;
            define('COT_MODULE', true);
        } elseif ($extensionType === ExtensionsDictionary::TYPE_PLUGIN) {
            Cot::$env['location'] = 'plugins';
            define('COT_PLUG', true);
        }

        return $route;
    }

    public function routeAdmin(): ?Route
    {
        $extensionCode = cot_import('m', 'G', 'ALP', 24);
        $requestControllerId = cot_import('n', 'G', 'ALP', 24);
        $requestActionId = cot_import('a', 'G', 'ALP', 24);
        $subPart = cot_import('s', 'G', 'ALP', 24);

        // Standard admin include files
        $route = $this->processAdminCoreIncludeFiles($extensionCode, $subPart, $requestControllerId, $requestActionId);
        if ($route !== null) {
            return $route;
        }

        // Core controllers
        if ($extensionCode === null) {
            return $this->processController(null, $requestControllerId, $requestActionId, null, true);
        }

        $extensionType = ExtensionsService::getInstance()->getType($extensionCode);
        if ($extensionType === null) {
            throw new NotFoundHttpException();
        }

        // Module's controller
        $route = $this->processController(
            $extensionCode,
            $requestControllerId,
            $requestActionId,
            $extensionType,
            true
        );
        if ($route === null) {
            // Modules include files
            $route = $this->processAdminExtensionIncludeFiles($extensionCode, $extensionType, 'admin');
        }

        if ($route === null) {
            throw new NotFoundHttpException();
        }

        Cot::$env['ext'] = $extensionCode;
        Cot::$env['type'] = $extensionType;

        return $route;
    }

    /**
     * Route for "admin.other" admin part (https://domain.tld/admin.php?m=other)
     * Plugins only
     */
    public function routeAdminOther(?string $extensionCode): Route
    {
        if ($extensionCode === null) {
            $extensionCode = cot_import('p', 'G', 'ALP', 24);
        }
        if ($extensionCode === null) {
            throw new NotFoundHttpException();
        }

        $extensionType = ExtensionsDictionary::TYPE_PLUGIN;

        $requestControllerId = cot_import('n', 'G', 'ALP', 24);
        $requestActionId = cot_import('a', 'G', 'ALP', 24);

        $route = $this->processController(
            $extensionCode,
            $requestControllerId,
            $requestActionId,
            $extensionType,
            true
        );
        if ($route === null) {
            $route = $this->processAdminExtensionIncludeFiles($extensionCode, $extensionType, 'tools');
        }

        if ($route === null) {
            throw new NotFoundHttpException();
        }

        Cot::$env['ext'] = $extensionCode;
        Cot::$env['type'] = $extensionType;

        return $route;
    }

    /**
     * @throws NotFoundHttpException
     */
    private function processController(
        ?string $extensionCode,
        ?string $requestControllerId, // $_GET['n']
        ?string $requestActionId, // $_GET['a']
        ?string $extensionType,
        ?bool $isAdmin = false
    ): ?Route {
        $controllerId = $requestControllerId;
        if ($controllerId === null) {
            if ($extensionCode !== null) {
                // Extensions can have a default IndexController
                $controllerId = 'index';
            } elseif (!$isAdmin) {
                // Site's amin area core have a default MainController
                $controllerId = 'main';
            }
        }
        if ($controllerId === null) {
            return null;
        }
        $controller = $this->getController($controllerId, $extensionCode, $extensionType, $isAdmin);
        if ($controller === null) {
            if ($isAdmin && (empty($extensionCode) || $extensionCode === ExtensionsDictionary::TYPE_CORE)) {
                $controllerClass = $this->getControllerClass(
                    $controllerId,
                    $extensionCode,
                    $extensionType,
                    $isAdmin,
                    false
                );
                throw new NotFoundHttpException('Controller "' . $controllerClass . '" not found"');
            }
            return null;
        }

        $actionId = $this->getActionName($requestActionId, get_class($controller));
        if ($actionId === null) {
            if ($extensionCode === null && $requestControllerId === null && !$isAdmin) {
                return null;
            }

            $actionName = $requestActionId;
            if (empty($actionName) && !empty($controller::$defaultAction)) {
                $actionName = $controller::$defaultAction;
            }
            throw new NotFoundHttpException(
                'Action "' . $actionName . '" not found in controller "'
                . get_class($controller) . '"'
            );
        }

        $route = new Route();
        $route->extensionCode = $extensionCode;
        $route->extensionType = $extensionType;
        $route->controller = $controller;
        $route->action = $actionId;

        return $route;

    }

    /**
     * @param string $controllerId Controller id
     * @param ?string $extensionCode
     * @param ?string $extensionType ExtensionsDictionary::EXTENSION_TYPE_MODULE or ExtensionsDictionary::EXTENSION_TYPE_PLUGIN,
     *    NULL for core
     */
    public function getController(
        string $controllerId,
        ?string $extensionCode = null,
        ?string $extensionType = null,
        ?bool $isAdmin = false
    ): ?BaseController {
        $className = $this->getControllerClass($controllerId, $extensionCode, $extensionType, $isAdmin);
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
     * @param string $controllerId Controller Id
     * @param ?string $extensionCode
     * @param ?string $extensionType ExtensionsDictionary ::EXTENSION_TYPE_MODULE or ExtensionsDictionary::EXTENSION_TYPE_PLUGIN,
     *     NULL for core
     * @param bool $ifExists Check if class exists
     * @return ($ifExists is true ? ?class-string<BaseController> : class-string<BaseController>) Controller class name.
     */
    public function getControllerClass(
        string $controllerId,
        ?string $extensionCode = null,
        ?string $extensionType = null,
        ?bool $isAdmin = false,
        bool $ifExists = true
    ): ?string {
        $result = '\\cot\\';
        if ($extensionCode !== null) {
            if ($extensionType === ExtensionsDictionary::TYPE_PLUGIN) {
                $result .= 'plugins'. '\\';
            } elseif ($extensionType === ExtensionsDictionary::TYPE_MODULE) {
                $result .= 'modules'. '\\';
            }
            $result .= $extensionCode . '\\';
        } elseif ($isAdmin) {
            $result .= 'admin'. '\\';
        }

        $result .=  'controllers\\';

        if ($extensionCode !== null && $isAdmin) {
            $result .= 'admin'. '\\';
        }

        $result .=  $this->prepareName($controllerId) . 'Controller';

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

    private function processFrontIncludeFiles(
        string $extensionCode,
        string $extensionType,
        bool $isAjax,
        bool $isPopup
    ): ?Route
    {
        if ($isPopup) {
            $hook = 'popup';
        } elseif ($isAjax) {
            $hook = 'ajax';
        } else {
            $hook = $extensionType === ExtensionsDictionary::TYPE_MODULE ? 'module' : 'standalone';
        }

        $route = new Route();
        $route->extensionCode = $extensionCode;
        $route->extensionType = $extensionType;

        $route->includeFiles = cot_getextplugins($hook, true, $extensionCode, $extensionType);

        if ($route->includeFiles === []) {
            return null;
        }

        return $route;
    }

    private function processAdminCoreIncludeFiles(
        ?string $adminPart, // $_GET['m']
        ?string $subPart, // $_GET['a']
        ?string $requestControllerId, // $_GET['n']
        ?string $requestActionId // $_GET['a']
    ): ?Route {
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
            'users',
        ];

        $includeFile = null;
        if ($adminPart !== null) {
            $includeFile = $adminPart;
        //} elseif ($requestControllerId === null && $requestActionId === null) {
        } elseif ($requestControllerId === null) {
            // If the controller ID is not present in the request, it is the admin home page
            $includeFile = 'home';
        }

        if ($includeFile === null) {
            return null;
        }

        if (!empty($subPart)) {
            $includeFile = $includeFile . '.' . $subPart;
        }
        $standardIncFile = cot_incfile('admin', 'module', $includeFile);
        if (in_array($includeFile, $standardAdmin) && file_exists($standardIncFile)) {
            $route = new Route();
            $route->includeFiles = [$standardIncFile];
            return $route;
        }

        return null;
    }

    private function processAdminExtensionIncludeFiles(string $extensionCode, string $extensionType, ?string $hook = null): ?Route
    {
        if ($hook === null) {
            if ($extensionType === ExtensionsDictionary::TYPE_MODULE) {
                $hook = 'admin';
            } elseif ($extensionType === ExtensionsDictionary::TYPE_PLUGIN) {
                $hook = 'tools';
            }
        }
        if ($hook === null) {
            return null;
        }

        $route = new Route();
        $route->extensionCode = $extensionCode;
        $route->extensionType = $extensionType;
        $route->includeFiles = cot_getextplugins($hook, true, $extensionCode, $extensionType);

        if ($route->includeFiles !== []) {
            return $route;
        }

        return null;
    }

    private function prepareName(string $name): string
    {
        return BaseController::prepareName($name);
    }
}