<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.delete.done
[END_COT_EXT]
==================== */

use cot\modules\page\inc\PageDictionary;

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $id Deleting page id
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

cot_comments_remove(PageDictionary::SOURCE_PAGE, $id);
