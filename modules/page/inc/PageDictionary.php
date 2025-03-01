<?php
/**
 * Page dictionary
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\page\inc;

defined('COT_CODE') or die('Wrong URL');

class PageDictionary
{
    public const SOURCE_PAGE = 'page';

    /**
     * Published
     */
    public const STATE_PUBLISHED = 0;

    /**
     * Waiting for approve by admin (moderator)
     */
    public const STATE_PENDING = 1;

    /**
     * Draft
     */
    public const STATE_DRAFT = 2;
}