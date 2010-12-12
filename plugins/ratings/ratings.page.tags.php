<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.tags
Tags=page.tpl:{PAGE_RATINGS},{PAGE_RATINGS_DISPLAY},{PAGE_RATINGS_COUNT}
[END_COT_EXT]
==================== */

/**
 * Displays page ratings
 *
 * @package ratings
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('ratings', 'plug');

$page_urlp = empty($pag['page_alias']) ? 'id='.$pag['page_id'] : 'al='.$pag['page_alias'];
$t->assign(array(
	'PAGE_RATINGS' => cot_ratings_link('page', $page_urlp, 'page', $pag['page_id'], $pag['page_cat']),
	'PAGE_RATINGS_DISPLAY' => cot_ratings_display('page', $pag['page_id'], $pag['page_cat']),
	'PAGE_RATINGS_COUNT' => cot_ratings_count('page', $pag['page_id'])
));

?>