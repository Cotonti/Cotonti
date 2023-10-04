<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.sections.main
[END_COT_EXT]
==================== */

/**
 * Forums viewers
 *
 * @package WhosOnline
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$cot_sections_vw = null;
if (Cot::$cache && Cot::$cache->mem) {
    $cot_sections_vw = Cot::$cache->mem->get('sections_wv', 'forums');
}
if (!$cot_sections_vw) {
	$sqltmp = Cot::$db->query('SELECT online_subloc, COUNT(*) FROM ' . Cot::$db->online . " WHERE online_location='Forums' GROUP BY online_subloc");
	while ($tmprow = $sqltmp->fetch()) {
        if (empty($cot_sections_vw)) {
            $cot_sections_vw = [];
        }
		$cot_sections_vw[$tmprow['online_subloc']] = $tmprow['COUNT(*)'];
	}
	$sqltmp->closeCursor();

    if (Cot::$cache && Cot::$cache->mem) {
        Cot::$cache->mem->store('sections_vw', $cot_sections_vw, 'forums', 120);
    }
}
