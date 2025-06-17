<?php
/**
 * Forums dictionary
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

namespace cot\modules\forums\inc;

defined('COT_CODE') or die('Wrong URL.');

class ForumsDictionary
{
    public const SOURCE_TOPIC = 'forumTopic';
    public const SOURCE_POST = 'forumPost';

    /**
     * Topic modes
     * 0 - Normal. Available for all
     * 1 - Private. Only moderators and the starter of the topic can read and reply
     */
    const TOPIC_MODE_NORMAL = 0;
    const TOPIC_MODE_PRIVATE = 1;
}