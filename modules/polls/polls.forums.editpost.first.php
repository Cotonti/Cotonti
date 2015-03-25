<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.update.first
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');
$poll = trim(cot_import('poll_text', 'P', 'HTM'));
$poll_id = cot_import('poll_id','P','TXT');

if(!empty($poll) && $poll_id)
{
	cot_poll_check();
}
