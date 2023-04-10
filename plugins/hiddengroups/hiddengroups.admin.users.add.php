<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.add
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package HiddenGroups
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $grp_id Group ID
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$rgroups['grp_hidden'] = cot_import('rhidden', 'P', 'BOL');

if ($grp_id) {
	Cot::$db->update(Cot::$db->groups, ['grp_hidden' => (int) $rgroups['grp_hidden']], 'grp_id = ?', $grp_id);
}

if (!empty(Cot::$cache) && !empty(Cot::$cache->db)) {
    Cot::$cache->db->remove('cot_hiddenusers', 'system');
}