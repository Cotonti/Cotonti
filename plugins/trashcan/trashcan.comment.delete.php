<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.update.delete
[END_COT_EXT]
==================== */

/**
 * Trashcan delete page
 *
 * @package trash
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
cot_require('trashcan', true);
if ($cfg['plugin']['trashcan']['trash_comment'])
{
	cot_trash_put('comment', $L['Comment']." #".$id." (".$row['com_author'].")", $id, $row);
}

?>