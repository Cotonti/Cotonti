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
 *
 * @var bool $is_first_post
 * @var int $q topic id
 * @var XTemplate $t
 */

defined('COT_CODE') or die('Wrong URL');

if (
    $is_first_post
    && Cot::$usr['isadmin']
    && cot_poll_edit_form($q, $t, 'MAIN.POLL', 'forum')
) {
    $t->parse('MAIN.POLL');
}
