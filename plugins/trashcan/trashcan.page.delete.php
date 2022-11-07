<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.delete.done
[END_COT_EXT]
==================== */

/**
 * Trashcan delete page
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (cot::$cfg['plugin']['trashcan']['trash_page']) {
	global $L;

    require_once cot_incfile('trashcan', 'plug');

	cot_trash_put('page', cot::$L['Page'] . " #" . $id . " " . $rpage['page_title'], $id, $rpage);
}
