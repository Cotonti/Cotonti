<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=input
Order=10
[END_COT_EXT]
==================== */

/**
 * Who's online (part 1)
 *
 * @package whosonline
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('whosonline', 'plug');

if ($usr['id'] > 0)
{
	$sql = $db->query("SELECT * FROM $db_online WHERE online_userid=".$usr['id']);

	if ($sql->rowCount() == 1)
	{
		$online_row = $sql->fetch();
		$online_count = 1;
		$sys['online_location'] = $online_row['online_location'];
		$sys['online_subloc'] = $online_row['online_subloc'];
	}
	$sql->closeCursor();
}
else
{
	$sql = $db->query("SELECT * FROM $db_online WHERE online_ip='".$usr['ip']."' LIMIT 1");

	if ($sql->rowCount() > 0)
	{
		$online_row = $sql->fetch();
		$sys['online_location'] = $online_row['online_location'];
		$sys['online_subloc'] = $online_row['online_subloc'];
	}
	$sql->closeCursor();
}

?>
