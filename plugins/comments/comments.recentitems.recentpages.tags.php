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
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $recentItems
 * @var array<string, mixed> $pag Page data
 */

defined('COT_CODE') or die('Wrong URL');

$page_urlp = empty($pag['page_alias']) ? 'id=' . $pag['page_id'] : 'al=' . $pag['page_alias'];
$recentItems->assign([
	'PAGE_ROW_COMMENTS' => cot_comments_link(
        'page',
        $page_urlp,
        'page',
        $pag['page_id'],
        $pag['page_cat'],
        $pag
    ),
	'PAGE_ROW_COMMENTS_COUNT' => cot_comments_count('page', $pag['page_id'], $pag),
]);
