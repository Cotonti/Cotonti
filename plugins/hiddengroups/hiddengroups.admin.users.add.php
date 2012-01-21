<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.add
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package hiddengroups
 * @version 1.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$rgroups['grp_hidden'] = cot_import('rhidden', 'P', 'BOL');

if($grp_id)
{
	$db->update($db_groups, array('grp_hidden' => (int)$rgroups['grp_hidden']), "grp_id = ".(int)$grp_id);
}

$cache && $cache->db->remove('cot_hiddenusers', 'system');

?>