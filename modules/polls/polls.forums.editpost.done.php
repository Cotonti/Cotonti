<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.update.done
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
 * @var int $q topic ID
 */

use cot\modules\forums\inc\ForumsDictionary;

defined('COT_CODE') or die('Wrong URL');

if (!empty($poll) && $isFirstPost && !cot_error_found()) {
	$number = cot_poll_save(ForumsDictionary::SOURCE_TOPIC, $q);
}
