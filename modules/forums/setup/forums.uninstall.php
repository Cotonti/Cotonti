<?php
/**
 * Uninstallation handler
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

global $db_users;

// Remove forums columns from users table
$dbres = $db->query("SHOW COLUMNS FROM `$db_users` WHERE `Field` = 'user_postcount'");
if ($dbres->rowCount() == 1)
{
	$db->query("ALTER TABLE `$db_users` DROP COLUMN `user_postcount`");
}
$dbres->closeCursor();
