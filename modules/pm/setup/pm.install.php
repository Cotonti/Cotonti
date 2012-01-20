<?php
/**
 * Installation handler
 *
 * @package pm
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2011-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

global $db_users;

// Add users fields if missing
$dbres = $db->query("SHOW COLUMNS FROM `$db_users` WHERE `Field` = 'user_pmnotify'");
if ($dbres->rowCount() == 0)
{
	$db->query("ALTER TABLE `$db_users` ADD COLUMN `user_pmnotify` TINYINT UNSIGNED NOT NULL DEFAULT 0");
}
$dbres->closeCursor();

$dbres = $db->query("SHOW COLUMNS FROM `$db_users` WHERE `Field` = 'user_newpm'");
if ($dbres->rowCount() == 0)
{
	$db->query("ALTER TABLE `$db_users` ADD COLUMN `user_newpm` TINYINT UNSIGNED NOT NULL DEFAULT 0");
}
$dbres->closeCursor();

?>
