<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.update
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

$rhidden = cot_import('rhidden', 'P', 'BOL');

if(!empty($rtitle))
{
	$db->query("UPDATE $db_groups SET grp_hidden = ".(int)$rhidden." WHERE grp_id = ".(int)$g);
}

?>