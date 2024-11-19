<?php

declare(strict_types=1);

namespace cot\router;

use cot\controllers\BaseController;

class Route
{
    /**
     * @var ?BaseController
     */
    public $controller = null;

    /**
     * @var ?string
     */
    public $action = null;

    /**
     * @var list<string>
     */
    public $includeFiles = [];

    /**
     * @var ?string
     */
    public $extensionType = null;

    /**
     * @var ?string
     */
    public $extensionCode = null;
}