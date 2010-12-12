<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=list.tags
Tags=list.tpl:{LIST_RATINGS},{LIST_RATINGS_DISPLAY}
[END_COT_EXT]
==================== */

/**
 * Rating tags for a page category
 *
 * @package ratings
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$t->assign(array(
	'LIST_RATINGS' => cot_ratings_link('page', 'c='.$c, 'page', $c),
	'LIST_RATINGS_DISPLAY' => cot_ratings_display('page', $c)
));

?>