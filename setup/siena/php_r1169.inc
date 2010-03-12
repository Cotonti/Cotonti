<?php
/**
 * r1169: PFS module rollback (r923)
 */

defined('SED_CODE') && defined('COT_UPDATE') or die('Wrong URL');

$sql = sed_sql_query("SHOW COLUMNS FROM $db_pfs_folders WHERE Field = 'pff_parentid'");
if(sed_sql_numrows($sql) != 0)
{
	sed_sql_query("ALTER TABLE $db_pfs_folders DROP COLUMN pff_parentid");
	sed_sql_query("ALTER TABLE $db_pfs_folders DROP COLUMN pff_path");
	sed_sql_query("DELETE FROM $db_config WHERE config_name = 'flashupload'");
}
?>
