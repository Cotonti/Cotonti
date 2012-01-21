<?php

/**
 * Hidden groups
 *
 * @package hiddengroups
 * @version 1.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

global $db_groups;

$dbres = $db->query("SHOW COLUMNS FROM `$db_groups` WHERE `Field` = 'grp_hidden'");
if ($dbres->rowCount() == 0)
{
	$db->query("ALTER TABLE `$db_groups` ADD COLUMN `grp_hidden` TINYINT NOT NULL DEFAULT '0' AFTER `grp_disabled`");
}
$dbres->closeCursor();

$cache && $cache->db->remove('cot_groups', 'system');

?>