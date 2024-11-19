<?php

declare(strict_types=1);

namespace cot\controllers;

/**
 * @method run();
 */
abstract class BaseAction
{
    /**
     * This method is called before executing `run()`.
     *  You can override this method to do preparation work for the action run.
     *  If the method returns false, action will be canceled.
     *
     * @return bool whether to run the action.
     */
    public function beforeRun(): bool
    {
        return true;
    }

    /**
     * This method is called after executing `run()`.
     * You can override this method to perform post-processing after the action is executed.
     */
    public function afterRun(): void
    {
    }
}