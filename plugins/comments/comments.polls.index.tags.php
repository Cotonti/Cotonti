<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=polls.index.tags
Tags=polls.index.tpl:{IPOLLS_COMMENTS}
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

require_once cot_incfile('comments', 'plug');

$indexpolls->assign(array(
	'IPOLLS_COMMENTS' => cot_comments_link('polls', 'id='.$row_p['poll_id'], 'polls', $row_p['poll_id']),
	'IPOLLS_COMMENTS_COUNT' => cot_comments_count('polls', $row_p['poll_id'])
));
