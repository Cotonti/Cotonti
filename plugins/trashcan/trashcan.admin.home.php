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

if (
    Cot::$cfg['plugin']['trashcan']['trash_prunedelay'] > 0 &&
    (empty($_SESSION['trashcanAutoPruned']) || $_SESSION['trashcanAutoPruned'] < date('Y-m-d'))
) {
    require_once cot_incfile('trashcan', 'plug');

	$timeago = Cot::$sys['now'] - (Cot::$cfg['plugin']['trashcan']['trash_prunedelay'] * 86400);

    $sqlToPrune = Cot::$db->query('SELECT tr_id FROM ' . Cot::$db->quoteTableName(Cot::$db->trash) .
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
            Cot::$cfg['plugin']['trashcan']['trash_prunedelay'] . ' days';

        cot_message($prumedMsg);
		cot_log($prumedMsg, 'trashcan', 'prune', 'done');
	}

    $_SESSION['trashcanAutoPruned'] = date('Y-m-d');
}
