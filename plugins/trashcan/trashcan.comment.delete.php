<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.delete
[END_COT_EXT]
==================== */

/**
 * Trashcan delete page
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\plugins\trashcan\inc\TrashcanService;

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('trashcan', 'plug');

if (Cot::$cfg['plugin']['trashcan']['trash_comment']) {
    TrashcanService::getInstance()->put('comment', Cot::$L['comments_comment'] . " #" . $id . " (" . $row['com_author'].")", (string) $id, $row);
}
