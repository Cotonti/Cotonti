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
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

$indexpolls->assign('IPOLLS_COMMENTS', cot_comments_link('polls', 'id='.$poll_id, 'polls', $poll_id));
?>