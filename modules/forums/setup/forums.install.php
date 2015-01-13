<?php
/**
 * Installation handler
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

global $db_users;

// Add users fields if missing
$dbres = $db->query("SHOW COLUMNS FROM `$db_users` WHERE `Field` = 'user_postcount'");
if ($dbres->rowCount() == 0)
{
	$db->query("ALTER TABLE `$db_users` ADD COLUMN `user_postcount` INT DEFAULT 0");
}
$dbres->closeCursor();
