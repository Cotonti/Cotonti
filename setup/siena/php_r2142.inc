<?php
/**
 * r2142 Unified extension registry patch
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

global $db_core, $db_updates, $cfg;

// Add a new column to the registry
$db->query("ALTER TABLE `$db_core` ADD COLUMN `ct_plug` tinyint unsigned NOT NULL default 0");

// Copy installed plugin into $db_core
$res = $db->query("SELECT * FROM `$db_updates` WHERE upd_param LIKE '%.ver'");
foreach ($res->fetchAll() as $row)
{
	$name = mb_substr($row['upd_param'], 0, -4);
	if (file_exists($cfg['plugins_dir'] . "/$name/$name.setup.php"))
	{
		$info = cot_infoget($cfg['plugins_dir'] . "/$name/$name.setup.php");
		cot_extension_add($name, $info['Name'], $row['upd_value'], true);
	}
}

// Remove entries from updates table
$db->delete($db_updates, "upd_param LIKE '%.ver'");

?>
