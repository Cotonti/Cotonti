<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=trashcan.api
[END_COT_EXT]
==================== */

/**
 * Trash can functions for comments
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

// Register restoration table
$trash_types['comment'] = $db_com;
