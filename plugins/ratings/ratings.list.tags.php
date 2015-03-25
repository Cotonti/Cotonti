<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.tags
Tags=page.list.tpl:{LIST_RATINGS_DISPLAY},{LIST_RATINGS_AVERAGE},{LIST_RATINGS_COUNT}
[END_COT_EXT]
==================== */

/**
 * Rating tags for a page category
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('ratings', 'plug');

list ($ratings_display, $ratings_average, $ratings_count) = cot_ratings_display('page', $c, $c);

$t->assign(array(
	'LIST_RATINGS_DISPLAY' => $ratings_display,
	'LIST_RATINGS_AVERAGE' => $ratings_average,
	'LIST_RATINGS_COUNT'   => $ratings_count
));
