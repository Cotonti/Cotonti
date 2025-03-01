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
 * @var bool $isFirstPost
 * @var int $q topic id
 * @var XTemplate $t
 */

use cot\modules\forums\inc\ForumsDictionary;

defined('COT_CODE') or die('Wrong URL');

if (
    $isFirstPost
    && Cot::$usr['isadmin']
    && cot_poll_edit_form($q, $t, 'MAIN.POLL', ForumsDictionary::SOURCE_TOPIC)
) {
    $t->parse('MAIN.POLL');
}
