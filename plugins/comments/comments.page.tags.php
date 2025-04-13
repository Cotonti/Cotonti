<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.tags
Tags=page.tpl:{PAGE_COMMENTS},{PAGE_COMMENTS_LINK},{PAGE_COMMENTS_COUNT},{PAGE_COMMENTS_RSS}
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
 */

use cot\modules\page\inc\PageDictionary;
use cot\plugins\comments\inc\CommentsService;
use cot\plugins\comments\inc\CommentsWidget;

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

if ($t->hasTag('PAGE_COMMENTS') || $t->hasTag('PAGE_COMMENTS_LINK')) {
    $t->assign([
        'PAGE_COMMENTS_LINK' => cot_commentsLink(
            'page',
            $pageurl_params,
            PageDictionary::SOURCE_PAGE,
            $pag['page_id'],
            $pag['page_cat'],
            $pag
        ),
        'PAGE_COMMENTS' => (new CommentsWidget(
            [
                'source' => PageDictionary::SOURCE_PAGE,
                'sourceId' => $pag['page_id'],
                'extensionCode' => 'page',
                'categoryCode' => $pag['page_cat'],
            ]
        ))->run(),
        'PAGE_COMMENTS_COUNT' => CommentsService::getInstance()
            ->getCount(PageDictionary::SOURCE_PAGE, $pag['page_id'], $pag),
        'PAGE_COMMENTS_RSS' => cot_url('rss', ['m' => 'comments', 'id' => $pag['page_id']]),
    ]);
}
