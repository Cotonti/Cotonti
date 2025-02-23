<?php
/**
 * Forums posts service
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\forums\inc;

use Cot;
use cot\traits\GetInstanceTrait;

defined('COT_CODE') or die('Wrong URL.');

class ForumsPostsService
{
    use GetInstanceTrait;

    public function canEdit(array $postData, $isUserAdmin = false): bool
    {
        return Cot::$usr['id'] > 0
        && (
            $isUserAdmin
            || (
                (int) $postData['fp_posterid'] === Cot::$usr['id']
                && (
                    Cot::$cfg['forums']['edittimeout'] == 0
                    || Cot::$sys['now'] - $postData['fp_creation'] < Cot::$cfg['forums']['edittimeout'] * 3600
                )
            )
        );
    }
}