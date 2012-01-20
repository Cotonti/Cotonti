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
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if ($is_first_post && $usr['isadmin'] && cot_module_active('polls') && cot_poll_edit_form($q, $t, 'MAIN.POLL', 'forum'))
{
    $t->parse('MAIN.POLL');
}

?>