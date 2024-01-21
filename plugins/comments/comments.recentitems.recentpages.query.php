<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=recentitems.recentpages.query
[END_COT_EXT]
==================== */

/**
 * Joins into the main recentitems query
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

global $L;

require_once cot_incfile('comments', 'plug');

$joinColumns[] = '(SELECT COUNT(*) FROM ' . Cot::$db->quoteT(Cot::$db->com)
    . " WHERE com_area = 'page' AND com_code = p.page_id) AS com_count";
