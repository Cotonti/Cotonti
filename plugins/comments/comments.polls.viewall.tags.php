<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=polls.viewall.tags
Tags=polls.tpl:{POLLS_COMMENTS},{POLLS_COMMENTS_COUNT}
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\modules\polls\inc\PollsDictionary;
use cot\plugins\comments\inc\CommentsService;

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

$t->assign([
    'POLLS_COMMENTS_LINK' => cot_commentsLink(
        'polls',
        ['id' => $row['poll_id']],
        PollsDictionary::SOURCE_POLL,
        $row['poll_id'],
    ),
    'POLLS_COMMENTS_COUNT' => CommentsService::getInstance()
        ->getCount(PollsDictionary::SOURCE_POLL, $row['poll_id']),
]);
