<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=news.first
[END_COT_EXT]
==================== */

/**
 * Joins into the main news query
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

global $db_com;

require_once cot_incfile('comments', 'plug');

$news_join_columns .= ", (SELECT COUNT(*) FROM `$db_com` WHERE com_area = 'page' AND com_code = p.page_id) AS com_count";
