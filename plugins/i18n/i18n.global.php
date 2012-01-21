<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Loads required data
 *
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

// Load structure i18n
$cache && $i18n_structure = $cache->db->get('structure', 'i18n');

if (!$i18n_structure)
{
	cot_i18n_load_structure();
	$cache && $cache->db->store('structure', $i18n_structure, 'i18n');
}

?>
