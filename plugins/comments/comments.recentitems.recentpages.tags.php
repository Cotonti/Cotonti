<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=recentitems.recentpages.tags
Tags=recentitems.pages.tpl:{PAGE_ROW_COMMENTS_LINK},{PAGE_ROW_COMMENTS_COUNT}
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

use cot\plugins\comments\inc\CommentsService;

defined('COT_CODE') or die('Wrong URL');

$commentsPageUrlParams = empty($pag['page_alias'])
    ? ['c' => $pag['page_cat'], 'id' => $pag['page_id']]
    : ['c' => $pag['page_cat'], 'al' => $pag['page_alias']];

$recentItems->assign([
    'PAGE_ROW_COMMENTS_LINK' => cot_commentsLink(
        'page',
        $commentsPageUrlParams,
        'page',
        $pag['page_id'],
        $pag['page_cat'],
        $pag
    ),
    'PAGE_ROW_COMMENTS_COUNT' => CommentsService::getInstance()->getCount('page', $pag['page_id'], $pag),
]);
