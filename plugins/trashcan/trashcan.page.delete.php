<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.delete.done
[END_COT_EXT]
==================== */

/**
 * Trashcan delete page
 *
 * @package trashcan
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('trashcan', 'plug');
if ($cfg['plugin']['trashcan']['trash_page'])
{
	global $L;
	cot_trash_put('page', $L['Page']." #".$id." ".$rpage['page_title'], $id, $rpage);
}

?>