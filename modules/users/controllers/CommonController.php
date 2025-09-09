<?php
/**
 * Users common controller
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\users\controllers;

use cot\controllers\BaseController;
use cot\modules\users\controllers\actions\getUsersAction;

class CommonController extends BaseController
{
    public static function actions(): array
    {
        return [
            'get-users' => getUsersAction::class,
        ];
    }
}