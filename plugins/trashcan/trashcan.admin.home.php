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

require_once cot_incfile('trashcan', 'plug');
if ($cfg['plugin']['trashcan']['trash_prunedelay'] > 0)
{
	$timeago = $sys['now'] - ($cfg['plugin']['trashcan']['trash_prunedelay'] * 86400);
	$sqltmp = $db->delete($db_trash, "tr_date < $timeago");
	$deleted = $db->affectedRows;
	if ($deleted > 0)
	{
		cot_log($deleted.' old item(s) removed from the trashcan, older than '.$cfg['plugin']['trashcan']['trash_prunedelay'].' days', 'adm');
	}
}
