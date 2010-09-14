<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.tags
Tags=
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package polls
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if ($is_first_post && $usr['isadmin'] && !$cfg['disable_polls'] && cot_poll_edit_form($q, $t, 'MAIN.POLL', 'forum'))
{
    	$t->parse("MAIN.POLL");
}

?>