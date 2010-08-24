<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=indexpolls.get_polls.tags
Tags=indexpolls.tpl:{IPOLLS_COMMENTS}
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

defined('SED_CODE') or die('Wrong URL');

sed_require('comments', true);

$indexpolls->assign('IPOLLS_COMMENTS', sed_comments_link('polls', 'id='.$poll_id, 'polls', $poll_id));
?>