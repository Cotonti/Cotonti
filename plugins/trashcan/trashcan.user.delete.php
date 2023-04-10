<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.update.delete
[END_COT_EXT]
==================== */

/**
 * Trashcan delete user
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $id User id for delete
 * @var array<string, mixed> $urr User data
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('trashcan', 'plug');

if (Cot::$cfg['plugin']['trashcan']['trash_user']) {
	cot_trash_put('user', Cot::$L['User'] . ' #' . $id . ' ' . $urr['user_name'], $id, $urr);
}
