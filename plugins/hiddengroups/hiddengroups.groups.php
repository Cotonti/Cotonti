<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package HiddenGroups
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$sql = $db->query("SELECT * FROM $db_groups WHERE grp_disabled=0");
while ($row = $sql->fetch())
{
	$cot_groups[$row['grp_id']]['hidden'] = $row['grp_hidden'];
}
$sql->closeCursor();
