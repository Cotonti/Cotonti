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
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['forums']['cat_' . $s]['allowpolls'])
{
	cot_poll_edit_form('new', $t, 'MAIN.POLL');
	$t->parse('MAIN.POLL');
}
