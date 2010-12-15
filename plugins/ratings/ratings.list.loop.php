<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=list.loop
Tags=page.list.tpl:{LIST_ROW_RATINGS}
[END_COT_EXT]
==================== */

/**
 * Displays page rating in pages list
 *
 * @package ratings
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('ratings', 'plug');

$t->assign('LIST_ROW_RATINGS', cot_ratings_link('page', $page_urlp, 'page', $pag['page_id'], $c));

?>