<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.update.first
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package polls
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');
$poll = trim(cot_import('poll_text', 'P', 'HTM'));
$poll_id = cot_import('poll_id','P','TXT');

if(!empty($poll) && $poll_id)
{
	cot_poll_check();
}

?>