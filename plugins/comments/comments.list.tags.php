<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.tags
Tags=page.list.tpl:{LIST_COMMENTS},{LIST_COMMENTS_LINK},{LIST_COMMENTS_COUNT}
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 * Comments for category
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 * @var string $c Category code
 */

use cot\plugins\comments\inc\CommentsService;
use cot\plugins\comments\inc\CommentsWidget;
use cot\structure\StructureDictionary;

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

if ($t->hasTag('LIST_COMMENTS') || $t->hasTag('LIST_COMMENTS_LINK')) {
    $t->assign([
        'LIST_COMMENTS_LINK' => cot_commentsLink(
            'page',
            ['c' => $c],
            StructureDictionary::SOURCE_CATEGORY,
            Cot::$structure['page'][$c]['id'],
            $c,
            Cot::$structure['page'][$c]
        ),
        'LIST_COMMENTS_COUNT' => CommentsService::getInstance()->getCount(
            StructureDictionary::SOURCE_CATEGORY,
            Cot::$structure['page'][$c]['id']
        ),
        'LIST_COMMENTS' => (new CommentsWidget(
            [
                'source' => StructureDictionary::SOURCE_CATEGORY,
                'sourceId' => Cot::$structure['page'][$c]['id'],
                'extensionCode' => 'page',
                'categoryCode' => $c,
            ]
        ))->run(),
    ]);
}
