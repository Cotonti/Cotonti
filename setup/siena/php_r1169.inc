<?php
/**
 * r1169: PFS module rollback (r923)
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

$sql = $db->query("SHOW COLUMNS FROM $db_pfs_folders WHERE Field = 'pff_parentid'");
if($sql->rowCount() != 0)
{
	$db->query("ALTER TABLE $db_pfs_folders DROP COLUMN pff_parentid");
	$db->query("ALTER TABLE $db_pfs_folders DROP COLUMN pff_path");
	$db->query("DELETE FROM $db_config WHERE config_name = 'flashupload'");
}
?>
