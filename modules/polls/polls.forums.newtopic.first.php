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
 * @package polls
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');
$poll = trim(cot_import('poll_text', 'P', 'HTM'));

if(!empty($poll))
{
	cot_poll_check();
}

?>