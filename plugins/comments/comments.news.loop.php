<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=news.loop
Tags=news.tpl:{PAGE_ROW_COMMENTS}
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$page_urlp = empty($pag['page_alias']) ? 'c='.$pag['page_cat'].'&id='.$pag['page_id'] : 'c='.$pag['page_cat'].'&al='.$pag['page_alias'];
$news->assign(array(
	'PAGE_ROW_COMMENTS' => cot_comments_link('page', $page_urlp, 'page', $pag['page_id'], $pag['page_cat'], $pag),
	'PAGE_ROW_COMMENTS_COUNT' => cot_comments_count('page', $pag['page_id'], $pag)
));
