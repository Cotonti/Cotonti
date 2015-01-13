<?php
/**
 * Hits API
 *
 * @package Hits
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

cot::$db->registerTable('stats');

/*
 * =============================== Statistics API =============================
*/

/**
 * Creates new stats parameter
 *
 * @param string $name Parameter name
 * @global CotDB $db
 */
function cot_stat_create($name)
{
	global $db, $db_stats;
	$db->insert($db_stats, array('stat_name' => $name, 'stat_value' => 1));
}

/**
 * Returns statistics parameter
 *
 * @param string $name Parameter name
 * @return int
 * @global CotDB $db
 */
function cot_stat_get($name)
{
	global $db, $db_stats;

	$sql = $db->query("SELECT stat_value FROM $db_stats where stat_name=".$db->quote($name)." LIMIT 1");
	return ($sql->rowCount() > 0) ? (int) $sql->fetchColumn() : FALSE;
}

/**
 * Increments stats
 *
 * @param string $name Parameter name
 * @param int $value Increment step
 * @global CotDB $db
 */
function cot_stat_inc($name, $value = 1)
{
	global $db, $db_stats;
	$db->query("UPDATE $db_stats SET stat_value=stat_value+$value WHERE stat_name=".$db->quote($name));
}

/**
 * Inserts new stat or increments value if it already exists
 *
 * @param string $name Parameter name
 * @param int $value Increment step
 * @global CotDB $db
 */
function cot_stat_update($name, $value = 1)
{
	global $db, $db_stats;
	$db->query("INSERT INTO $db_stats (stat_name, stat_value)
		VALUES ('".$db->prep($name)."', 1)
		ON DUPLICATE KEY UPDATE stat_value=stat_value+$value");
}
