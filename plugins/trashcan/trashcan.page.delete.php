<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.admin.delete.done, page.edit.delete.done
[END_COT_EXT]
==================== */

/**
 * Trashcan delete page
 *
 * @package trashcan
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
cot_require('trashcan', true);
if ($cfg['plugin']['trashcan']['trash_page'])
{
	cot_trash_put('page', $L['Page']." #".$id." ".$row['page_title'], $id, $row);

}

?>