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
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($is_first_post && $usr['isadmin'] && cot_module_active('polls') && cot_poll_edit_form($q, $t, 'MAIN.POLL', 'forum'))
{
    $t->parse('MAIN.POLL');
}
