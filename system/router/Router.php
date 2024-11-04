<?php

declare(strict_types=1);

namespace cot\router;

use Cot;
use cot\controllers\BaseController;
use cot\dictionaries\CotontiDictionary;
use cot\exceptions\NotFoundHttpException;

class Router
{
    public function route(): ?Route
    {
        $extensionCode = isset($_GET['e']) ? cot_import('e', 'G', 'ALP') : null;
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

        $m = cot_import('m', 'G', 'ALP', 24);
        $n = cot_import('n', 'G', 'ALP', 24);
        $a = cot_import('a', 'G', 'ALP', 24);

       // $extensionCode = $this->getExtensionCodeFromRequest();
        $controllerClass = null;
        $actionId = null;

        if ($extensionCode === null) {
            // It can be core controller or an "index" module
            if ($m !== null || $a !== null) {
                $controllerClass = $this->getControllerClass($m ?? 'cotonti');
                if ($controllerClass !== null) {
                    $actionId = $this->getActionName($a, $controllerClass);
                    if ($m !== null && $actionId === null) {
                        throw new NotFoundHttpException(
                            'Action "'. $actionId . '" not found in controller "'
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

                // 'index' module
                $extensionCode = 'index';
//                define('COT_MODULE', true);
//                $env['type'] = 'module';
//                $env['ext'] = 'index';
            }
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
            $found = true;
        }
        if (
            file_exists(Cot::$cfg['plugins_dir'] . '/' . $extensionCode)
            && cot_plugin_active($extensionCode)
        ) {
            $pluginFound = true;
            $found = true;
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
            Cot::$env['type'] = 'module';
            Cot::$env['location'] = $extensionCode;
            define('COT_MODULE', true);
        } elseif ($pluginFound) {
            Cot::$env['type'] = 'plug';
            Cot::$env['location'] = 'plugins';
            define('COT_PLUG', true);
        } else {
            // @todo translate
            throw new NotFoundHttpException("Extension '" . $extensionCode . "' not found");
        }

        $env['ext'] = $extensionCode;

        // Контроллер. Если нет - то хук и его обработчики
        // Или так: если popup или ajax - то хук и обработчики, иначе контроллер, если нет - то хук standalone/module и его обработчики

        $hook = $moduleFound ? 'module' : 'standalone';


        //            echo '<pre>';
//            var_dump($result);
//            echo '</pre>';
//            die;

        die('ddd');
    }

//    public function getExtensionCodeFromRequest(): ?string
//    {
//        $extensionCode = isset($_GET['e']) ? cot_import('e', 'G', 'ALP') : null;
//        $ajax = cot_import('r', 'G', 'ALP');
//        $popup = cot_import('o', 'G', 'ALP');
//        if (!$extensionCode) {
//            // Support for ajax and popup hooked plugins
//            if (!empty($ajax)) {
//                $extensionCode = $ajax;
//            } elseif (!empty($popup)) {
//                $extensionCode = $popup;
//            }
//        }
//        return $extensionCode;
//    }

    /**
     * @param string $controller Controller name from request
     * @param string|null $extensionCode
     * @param string|null $extensionType
     * @param bool $ifExists Check if class exists
     * @return ($ifExists is true ? ?string : string) Controller class name.
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
                $result .= 'plugins';
            } else {
                $result .= 'modules';
            }
            $result .= '\\' . $extensionCode . '\\';
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
    public function getActionName(string $action, string $controller): ?string
    {
        $actionName = 'action' . $this->prepareName($action);
        if (method_exists($controller, $actionName)) {
            // or may be use $action as is?
            return $actionName;
        }

        $actions = $controller::actions();
        if (array_key_exists($action, $actions)) {
            return $action;
        }

        if (!empty($controller::$defaultAction) && method_exists($controller, $controller::$defaultAction)) {
            return $controller::$defaultAction;
        }

        return null;
    }

    private function getRote(
        ?string $extensionCode = null,
        ?string $controller = null,
        ?string $action = null,
        string $extensionType = null
    ): ?string {
        if ($controller === null && $action === null) {
            return null;
        }

        if ($extensionCode === null) {
            // Process core controllers

        }
    }

    private function prepareName(string $name): string
    {
        return ucfirst(str_replace('-', '', ucwords($name, '-')));
    }
}