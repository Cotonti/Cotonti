<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=recentitems.recentpages.tags
Tags=recentitems.tpl:{PAGE_ROW_COMMENTS}
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

cot_require('comments', true);

$page_urlp = empty($pag['page_alias']) ? 'id='.$pag['page_id'] : 'al='.$pag['page_alias'];
$recentitems->assign(array(
	'PAGE_ROW_COMMENTS' => cot_comments_link('page', $page_urlp, 'page', $pag['page_id'], $pag['page_cat'])
));

?>