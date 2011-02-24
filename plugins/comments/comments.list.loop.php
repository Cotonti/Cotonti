<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.loop
Tags=page.list.tpl:{LIST_ROW_COMMENTS}
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

$page_urlp = empty($pag['page_alias']) ? cot_url('page', 'id='.$pag['page_id']) : cot_url('page', 'al='.$pag['page_alias']);
$t->assign(array(
	'LIST_ROW_COMMENTS' => cot_comments_link('page', $page_urlp, 'page', $pag['page_id'], $c),
	'LIST_ROW_COMMENTS_COUNT' => cot_comments_count('page', $pag['page_id'])
));

?>