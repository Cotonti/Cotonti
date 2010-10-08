<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home
[END_COT_EXT]
==================== */

/**
 * Trashcan delete old
 *
 * @package trash
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_require('trashcan', true);
if ($cfg['plugin']['trashcan']['trash_prunedelay'] > 0)
{
	$timeago = $sys['now_offset'] - ($cfg['trash_prunedelay'] * 86400);
	$sqltmp = cot_db_query("DELETE FROM $db_trash WHERE tr_date<$timeago");
	$deleted = cot_db_affectedrows();
	if ($deleted > 0)
	{
		cot_log($deleted.' old item(s) removed from the trashcan, older than '.$cfg['trash_prunedelay'].' days', 'adm');
	}
}

?>