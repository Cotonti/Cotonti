<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.delete
[END_COT_EXT]
==================== */

declare(strict_types=1);

use cot\modules\users\inc\UsersDictionary;
use cot\plugins\trashcan\inc\TrashcanService;

/**
 * Trashcan delete user
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $id User id for delete
 * @var array<string, mixed> $userData User data
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('trashcan', 'plug');

if (!Cot::$cfg['plugin']['trashcan']['trash_user']) {
    return;
}

$trashcanId = TrashcanService::getInstance()->put(
    UsersDictionary::SOURCE_USER,
    Cot::$L['User'] . ' #' . $id . ' ' . $userData['user_name'],
    (string) $id,
    $userData
);
