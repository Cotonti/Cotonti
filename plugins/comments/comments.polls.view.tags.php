<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=polls.view.tags
Tags=polls.tpl:{POLLS_COMMENTS},{POLLS_COMMENTS_DISPLAY}
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

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

if ($t->hasTag('POLLS_COMMENTS') || $t->hasTag('POLLS_COMMENTS_DISPLAY')) {
    $t->assign([
        'POLLS_COMMENTS' => cot_comments_link('polls', 'id=' . $id, PollsDictionary::SOURCE_POLL, $id),
        'POLLS_COMMENTS_COUNT' => cot_comments_count(PollsDictionary::SOURCE_POLL, $id),
        'POLLS_COMMENTS_DISPLAY' => cot_comments_display(PollsDictionary::SOURCE_POLL, $id)
    ]);
}
