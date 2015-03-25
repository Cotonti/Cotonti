<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=trashcan.api
[END_COT_EXT]
==================== */

/**
 * Trash can functions for i18n
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('i18n', 'plug');

// Register restoration table
$trash_types['i18n_page'] = $db_i18n_pages;

// Actually no functions are required so far
