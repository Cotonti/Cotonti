<?php

declare(strict_types=1);

namespace cot\controllers;

use BadMethodCallException;
use cot\exceptions\InvalidConfigException;

abstract class BaseController
{
    /**
     * @var ?string the ID of the action that is used when the action ID is not specified
     * in the request. Defaults to 'index'.
     */
    public static $defaultAction = 'index';

    /**
     * Declares external actions for the controller.
     *
     * This method can be overridden to declare external actions.
     * The method returns an array, the keys of which are the IDs of the actions, and the values are the corresponding
     * names of the action classes. For example:
     * ```php
     * return [
     *   'action1' => 'cot\modules\someext\controllers\Action1',
     *   'action2' => [
     *     'class' => 'cot\modules\someext\controllers\Action1',
     *     'argument1' => 'value1',
     *     'argument2' => 'value2',
     *   ],
     * ];
     * ```
     * @return array<string, class-string|array<string, class-string|string>>
     */
    public static function actions(): array
    {
        return [];
    }

    /**
     * This method is called before executing `runAction()`.
     * You can override this method to do preparation work for the action run.
     * If the method returns false, action will be canceled.
     *
     * @return bool whether to run the action.
     */
    public function beforeAction(): bool
    {
        return true;
    }

    /**
     * This method is called after executing `runAction()`.
     * You can override this method to perform post-processing after the action is executed.
     */
    public function afterAction(?string $result): ?string
    {
        return $result;
    }

    public function runAction(string $actionId): ?string
    {
        if (!$this->beforeAction()) {
            return null;
        }

        $actionName = 'action' . static::prepareName($actionId);
        if (method_exists($this, $actionName)) {
            $result = $this->$actionName();
        } elseif (array_key_exists($actionId, static::actions())) {
            $result = $this->runStandAloneAction($actionId);
        } else {
            throw new BadMethodCallException(
                'Action "' . $actionId . '" not found in controller "' . get_class($this) . '"'
            );
        }

        return $this->afterAction($result);
    }

    protected function runStandAloneAction(string $actionId): ?string
    {
        $actions = static::actions();
        if (!array_key_exists($actionId, $actions)) {
            throw new BadMethodCallException(
                'Action "' . $actionId . '" not found in controller "' . get_class($this) . '"'
            );
        }

        if (isset($actions[$actionId]) && !isset($actions[$actionId]['class'])) {
            throw new InvalidConfigException(
                'Action configuration must be an array containing a "class" element.'
            );
        }

        $actionClassName = is_string($actions[$actionId]) ? $actions[$actionId] : $actions[$actionId]['class'];

        if (!method_exists($actionClassName, 'run')) {
            throw new InvalidConfigException($actionClassName . ' must define a "run()" method.');
        }

        $arguments = [];
        if (is_array($actions[$actionId]) && count($actions[$actionId]) > 1) {
            $arguments = $actions[$actionId];
            unset($arguments['class']);
        }

        /** @var BaseAction $action */
        $action = new $actionClassName();

        if ($action->beforeRun()) {
            $result = call_user_func_array([$action, 'run'], $arguments);
            $action->afterRun();

            return $result;
        }

        return null;
    }

    /**
     * Converts id string to CamelCase
     * Example: post-comment becomes PostComment
     */
    public static function prepareName(string $name, string $separator = '-'): string
    {
        return ucfirst(str_replace($separator, '', ucwords($name, '-')));
    }
}