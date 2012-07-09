<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.newtopic.tags
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

if ($cfg['forums']['cat_' . $s]['allowpolls'])
{
	cot_poll_edit_form('new', $t, 'MAIN.POLL');
	$t->parse('MAIN.POLL');
}

?>