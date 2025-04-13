<?php
/**
 * Comments system for Cotonti
 * Admin controller
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\controllers\admin;

use cot\controllers\BaseController;
use cot\plugins\comments\controllers\admin\actions\IndexAction;

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

class IndexController extends BaseController
{
    public static function actions(): array
    {
        return [
            'index' => IndexAction::class,
        ];
    }
}