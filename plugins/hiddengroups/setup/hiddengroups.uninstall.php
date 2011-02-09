<?php

/**
 * Hidden groups
 *
 * @package hiddengroups
 * @version 1.0
 * @author Koradhil, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$dbres = $db->query("SHOW COLUMNS FROM `$db_groups` WHERE `Field` = 'grp_hidden'");
if ($dbres->rowCount() == 1)
{
	$db->query("ALTER TABLE `$db_groups` DROP COLUMN `grp_hidden`");
}
$dbres->closeCursor();

$cache && $cache->db->remove('cot_groups', 'system');

?>