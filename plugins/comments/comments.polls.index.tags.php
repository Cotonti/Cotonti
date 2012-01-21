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
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

$indexpolls->assign(array(
	'IPOLLS_COMMENTS' => cot_comments_link('polls', 'id='.$row_p['poll_id'], 'polls', $row_p['poll_id']),
	'IPOLLS_COMMENTS_COUNT' => cot_comments_count('polls', $row_p['poll_id'])
));
?>