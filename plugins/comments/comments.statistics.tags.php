<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=statistics.tags
Tags=statistics.tpl:{STATISTICS_TOTALDBCOMMENTS}
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

$totaldbcomments = $db->countRows($db_com);
$t->assign(array(
	'STATISTICS_TOTALDBCOMMENTS' => $totaldbcomments
));
