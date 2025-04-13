<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.loop
Tags=page.list.tpl:{LIST_ROW_COMMENTS_LINK},{LIST_ROW_COMMENTS_COUNT}
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 * @var array $pag Page data
 * @var string $c Category code
 */

use cot\modules\page\inc\PageDictionary;
use cot\plugins\comments\inc\CommentsService;

defined('COT_CODE') or die('Wrong URL');

$commentsPageUrlParams = empty($pag['page_alias'])
    ? ['c' => $pag['page_cat'], 'id' => $pag['page_id']]
    : ['c' => $pag['page_cat'], 'al' => $pag['page_alias']];

$t->assign([
    'LIST_ROW_COMMENTS_LINK' => cot_commentsLink(
        'page',
        $commentsPageUrlParams,
        PageDictionary::SOURCE_PAGE,
        $pag['page_id'],
        $c,
        $pag
    ),
    'LIST_ROW_COMMENTS_COUNT' => CommentsService::getInstance()
        ->getCount(PageDictionary::SOURCE_PAGE, $pag['page_id'], $pag),
]);
