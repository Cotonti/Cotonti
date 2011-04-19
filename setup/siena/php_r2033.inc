<?php
/**
 * r2033: exclude modules and plugins directory from plugin paths
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

global $db_plugins, $cfg;

/**
 * Helper function to strip slashes from beginning and ending of the dir
 * @access private
 * @param string $dir Path
 * @return string
 */
function cot_r2033_strip_slashes($dir)
{
	if (strpos($dir, './') === 0)
	{
		$dir = substr($dir, 2);
	}
	if (strrpos($dir, '/') === strlen($dir) - 1)
	{
		$dir = substr($dir, 0, -1);
	}
	return $dir;
}

foreach (array($cfg['plugins_dir'], $cfg['modules_dir']) as $dir)
{
	$dir = $db->quote(cot_r2033_strip_slashes($dir));
	$db->query("UPDATE $db_plugins SET pl_file = SUBSTRING(pl_file, INSTR(pl_file, $dir) + LENGTH($dir) + 1) WHERE INSTR(pl_file, $dir)");
}
?>
