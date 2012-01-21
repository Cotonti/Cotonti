<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
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

defined('COT_CODE') or die('Wrong URL.');

$sql = $db->query("SELECT * FROM $db_groups WHERE grp_disabled=0");
while ($row = $sql->fetch())
{
	$cot_groups[$row['grp_id']]['hidden'] = $row['grp_hidden'];
}
$sql->closeCursor();

?>