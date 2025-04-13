<?php
/**
 * Comments system for Cotonti
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\inc;

defined('COT_CODE') or die('Wrong URL');

class CommentsDictionary
{
    public const SOURCE_COMMENT = 'comment';

    public const EVENT_CREATE = 'create';
    public const EVENT_UPDATE = 'update';
}