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
if (cot::$cache && cot::$cache->mem) $cot_sections_vw = cot::$cache->mem->get('sections_wv', 'forums');
if (!$cot_sections_vw) {
	$sqltmp = cot::$db->query("SELECT online_subloc, COUNT(*) FROM $db_online WHERE online_location='Forums' GROUP BY online_subloc");
	while ($tmprow = $sqltmp->fetch()) {
		$cot_sections_vw[$tmprow['online_subloc']] = $tmprow['COUNT(*)'];
	}
	$sqltmp->closeCursor();

    if (cot::$cache && cot::$cache->mem) cot::$cache->mem->store('sections_vw', $cot_sections_vw, 'forums', 120);
}
