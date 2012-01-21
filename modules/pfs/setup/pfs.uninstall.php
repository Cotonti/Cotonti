<?php
/**
 * Uninstallation handler
 *
 * @package pfs
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2011-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

global $db_groups;

// Remove PM columns from groups table
$dbres = $db->query("SHOW COLUMNS FROM `$db_groups` WHERE `Field` = 'grp_pfs_maxfile'");
if ($dbres->rowCount() == 1)
{
	$db->query("ALTER TABLE `$db_groups` DROP COLUMN `grp_pfs_maxfile`");
}
$dbres->closeCursor();

$dbres = $db->query("SHOW COLUMNS FROM `$db_groups` WHERE `Field` = 'grp_pfs_maxtotal'");
if ($dbres->rowCount() == 1)
{
	$db->query("ALTER TABLE `$db_groups` DROP COLUMN `grp_pfs_maxtotal`");
}
$dbres->closeCursor();

?>
