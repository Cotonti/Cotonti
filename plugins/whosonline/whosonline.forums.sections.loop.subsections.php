<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.sections.loop.sections
Tags=forums.sections.tpl:{FORUMS_SECTIONS_ROW_VIEWERS}
[END_COT_EXT]
==================== */

/**
 * Forums sections online users display
 *
 * @package WhosOnline
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// So this code should be moved there too and use 'forums.sections.loop.subsections' hook
$cot_sections_vw_cur = 0;
if (!empty($cot_sections_vw) && isset($cot_sections_vw[cot::$structure['forums'][$y]['title']])) {
    $cot_sections_vw_cur = (int) $cot_sections_vw[cot::$structure['forums'][$y]['title']];
}

$t->assign(array(
    'FORUMS_SECTIONS_ROW_VIEWERS' => $cot_sections_vw_cur
));
