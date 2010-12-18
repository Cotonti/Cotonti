<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.add
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Koradhil, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$nhidden = cot_import('nhidden', 'P', 'BOL');

if($grp_id)
{
	$db->update($db_groups, array('grp_hidden' => (int)$nhidden),  "grp_id = ".(int)$grp_id);
}

$cache && $cache->db->remove('hiddenusers', 'system');

?>