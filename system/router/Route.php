<?php

namespace cot\router;

use cot\controllers\BaseController;

class Route
{
    /**
     * @var BaseController
     */
    public $controller = null;

    /**
     * @var string
     */
    public $action = null;

    public $includeFiles = [];

//    /**
//     * @var string
//     */
//    public $extensionType;
//
//    /**
//     * @var string
//     */
//    public $extensionCode;
}