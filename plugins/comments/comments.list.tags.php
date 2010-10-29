<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=list.tags
Tags=list.tpl:{LIST_COMMENTS},{LIST_COMMENTS_DISPLAY}
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$t->assign(array(
	'LIST_COMMENTS' => cot_comments_link('page', 'c='.$c, 'page', $c),
	'LIST_COMMENTS_DISPLAY' => cot_comments_display('page', $c)
));

?>