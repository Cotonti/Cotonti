<?php
/**
 * Hits API
 *
 * @package Hits
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

Cot::$db->registerTable('stats');

/*
 * =============================== Statistics API =============================
*/

/**
 * Creates new stats parameter
 *
 * @param string $name Parameter name
 * @param int $value Parameter value
 */
function cot_stat_create($name, $value = 1)
{
    $value = (int) $value;
	Cot::$db->insert(Cot::$db->stats, ['stat_name' => $name, 'stat_value' => $value]);
}

/**
 * Returns statistics parameter
 *
 * @param string $name Parameter name
 * @return int|null
 */
function cot_stat_get($name)
{
	$sql = Cot::$db->query('SELECT stat_value FROM ' . Cot::$db->stats . ' WHERE stat_name = ? LIMIT 1', $name);
	return ($sql->rowCount() > 0) ? (int) $sql->fetchColumn() : null;
}

/**
 * Increments stats value
 *
 * @param string $name Parameter name
 * @param int $value Increment step
 * @param bool $createIfNotExists
 */
function cot_stat_inc($name, $value = 1, $createIfNotExists = false)
{
    if ($createIfNotExists && cot_stat_get($name) === null) {
        cot_stat_create($name, $value);
        return;
    }
    $value = (int) $value;
    cot::$db->query(
        'UPDATE ' . cot::$db->stats . " SET stat_value = stat_value + {$value} WHERE stat_name = ?",
        $name
    );
}

/**
 * Updates stat value
 *
 * @param string $name Parameter name
 * @param int $value Increment step
 * @param bool $createIfNotExists
 */
function cot_stat_update($name, $value = 1, $createIfNotExists = false)
{
    if ($createIfNotExists && cot_stat_get($name) === null) {
        cot_stat_create($name, $value);
        return;
    }
    $value = (int) $value;
    Cot::$db->update(Cot::$db->stats, ['stat_value' => $value], 'stat_name= ?', $name);
}

/**
 * Inserts new stat value or updates it if value is already exists
 *
 * @param string $name Parameter name
 * @param int $value Increment step
 */
function cot_stat_set($name, $value = 1)
{
    cot_stat_update($name, $value, true);
}