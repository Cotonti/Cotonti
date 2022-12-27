<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home
[END_COT_EXT]
==================== */

/**
 * Trashcan delete old
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (cot::$cfg['plugin']['trashcan']['trash_prunedelay'] > 0) {
    require_once cot_incfile('trashcan', 'plug');

	$timeago = cot::$sys['now'] - (cot::$cfg['plugin']['trashcan']['trash_prunedelay'] * 86400);

    $sqlToPrune = cot::$db->query('SELECT tr_id FROM ' . cot::$db->quoteTableName(cot::$db->trash) .
        ' WHERE tr_date < ?', $timeago);

    $pruned = 0;
    while ($itemToPrune = $sqlToPrune->fetchColumn()) {
        $pruned++;
        cot_trash_delete($itemToPrune);
    }
    $sqlToPrune->closeCursor();

	if ($pruned > 0) {
        /** @todo translate */
        $prumedMsg = $pruned . ' old item(s) removed from the trashcan, older than ' .
            cot::$cfg['plugin']['trashcan']['trash_prunedelay'] . ' days';

        cot_message($prumedMsg);

		cot_log($prumedMsg, 'adm');
	}
}
