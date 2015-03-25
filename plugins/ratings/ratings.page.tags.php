<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.tags
Tags=page.tpl:{PAGE_RATINGS_DISPLAY},{PAGE_RATINGS_AVERAGE},{PAGE_RATINGS_COUNT}
[END_COT_EXT]
==================== */

/**
 * Displays page ratings
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('ratings', 'plug');

list ($ratings_display, $ratings_average, $ratings_count) = cot_ratings_display('page', $pag['page_id'], $pag['page_cat']);

$t->assign(array(
	'PAGE_RATINGS_DISPLAY' => $ratings_display,
	'PAGE_RATINGS_AVERAGE' => $ratings_average,
	'PAGE_RATINGS_COUNT'   => $ratings_count
));
