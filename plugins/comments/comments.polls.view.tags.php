<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=polls.view.tags
Tags=polls.tpl:{POLLS_COMMENTS},{POLLS_COMMENTS_LNK},{POLLS_COMMENTS_COUNT}
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
 */

use cot\modules\polls\inc\PollsDictionary;
use cot\plugins\comments\inc\CommentsService;
use cot\plugins\comments\inc\CommentsWidget;

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

if ($t->hasTag('POLLS_COMMENTS') || $t->hasTag('POLLS_COMMENTS_DISPLAY')) {
    $t->assign([
        'POLLS_COMMENTS_LINK' => cot_commentsLink('polls', ['id' => $id], PollsDictionary::SOURCE_POLL, $id),
        'POLLS_COMMENTS_COUNT' => CommentsService::getInstance()->getCount(PollsDictionary::SOURCE_POLL, $id),
        'POLLS_COMMENTS' => (new CommentsWidget(
            [
                'source' => PollsDictionary::SOURCE_POLL,
                'sourceId' => $id,
                'extensionCode' => 'polls',
            ]
        ))->run(),
    ]);
}
