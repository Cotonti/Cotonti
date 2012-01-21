<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.update.done
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

if(!empty($poll) && $poll_id && $is_first_post && !cot_error_found())
{
	$number = cot_poll_save();
}

?>