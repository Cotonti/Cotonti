<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=polls.viewall.tags
Tags=polls.tpl:{POLLS_COMMENTS}
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

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

$t->assign([
	'POLLS_COMMENTS' => cot_comments_link(
        'polls',
        'id=' . $row['poll_id'],
        PollsDictionary::SOURCE_POLL,
        $row['poll_id']
    ),
	'POLLS_COMMENTS_COUNT' => cot_comments_count(PollsDictionary::SOURCE_POLL, $row['poll_id'])
]);
