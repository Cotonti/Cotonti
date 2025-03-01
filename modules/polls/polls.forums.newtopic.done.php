<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.newtopic.newtopic.done
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

use cot\modules\forums\inc\ForumsDictionary;

defined('COT_CODE') or die('Wrong URL');

if (!empty($poll)) {
	cot_poll_save(ForumsDictionary::SOURCE_TOPIC, $q);
}
