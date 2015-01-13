<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.newtopic.newtopic.first
Tags=
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

if(!empty($poll))
{
	cot_poll_check();
}
