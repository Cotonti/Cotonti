<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');
require_once cot_incfile('forms');

$item = cot_import('item', 'G', 'TXT');
$cat = cot_import('cat', 'G', 'TXT');
$area = cot_import('area', 'G', 'ALP');

// Check if comments are enabled for specific category/item
cot_block(!empty($area) && !empty($item) && cot_comments_enabled($area, $cat, $item));

cot_sendheaders();

echo cot_comments_display($area, $item, $cat);