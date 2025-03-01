<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=polls.functions.delete
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @todo remove after implement #1826 for polls
 */

use cot\modules\polls\inc\PollsDictionary;
use cot\plugins\comments\inc\CommentsControlService;

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

CommentsControlService::getInstance()->deleteBySourceId(PollsDictionary::SOURCE_POLL, (string) $id2);
