<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Loads required data
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Load structure i18n
$cache && $i18n_structure = $cache->db->get('structure', 'i18n');

if (!$i18n_structure)
{
	cot_i18n_load_structure();
	$cache && $cache->db->store('structure', $i18n_structure, 'i18n');
}
