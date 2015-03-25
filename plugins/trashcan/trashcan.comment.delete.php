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

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('trashcan', 'plug');
if ($cfg['plugin']['trashcan']['trash_comment'])
{
	cot_trash_put('comment', $L['Comment']." #".$id." (".$row['com_author'].")", $id, $row);
}
