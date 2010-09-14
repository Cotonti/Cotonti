<?php

/**
 * Common database abstraction layer functions
 *
 * @package Cotonti
 * @version 0.7.0
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Returns number of rows in a table
 *
 * @param string $table Table name
 * @param resource $conn Custom connection handle
 * @return int
 */
function cot_db_rowcount($table, $conn = null)
{
	global $cot_dbc;
	$conn = is_null($conn) ? $cot_dbc : $conn;
	$sqltmp = cot_db_query("SELECT COUNT(*) FROM $table", $conn);
	return (int) cot_db_result($sqltmp);
}

/**
 * Runs an SQL script containing multiple queries.
 *
 * @param string $script SQL script body, containing formatted queries separated by semicolons and newlines
 * @param resource $conn Custom connection handle
 * @return string Error message if an error occurs or empty string on success
 */
function cot_db_runscript($script, $conn = null)
{
	global $cot_dbc, $db_x;
	$conn = is_null($conn) ? $cot_dbc : $conn;

	$error = '';
	// Remove comments
	$script = preg_replace('#^/\*.*?\*/#m', '', $script);
	$script = preg_replace('#^--.*?$#m', '', $script);
	// Run queries separated by ; at the end of line
	$queries =  preg_split('#;\r?\n#', $script);
	foreach ($queries as $query)
	{
		$query = trim($query);
		if (!empty($query))
		{
			if ($db_x != 'cot_')
			{
				$query = str_replace('`cot_', '`'.$db_x, $query);
			}
			$result = @cot_db_query($query, $conn);
			if (!$result)
			{
				return cot_db_error($conn) . '<br />' . htmlspecialchars($query) . '<hr />';
			}
		}
	}
	return '';
}

/**
 * Performs SQL INSERT on simple data array. Array keys must match table keys, optionally you can specify
 * key prefix as third parameter. Strings get quoted and escaped automatically.
 * Ints and floats must be typecasted.
 * You can use special values in the array:
 * - PHP NULL => SQL NULL
 * - 'NOW()' => SQL NOW()
 * Performs single row INSERT if $data is an associative array,
 * performs multi-row INSERT if $data is a 2D array (numeric => assoc)
 *
 * @param string $table_name Table name
 * @param array $data Associative or 2D array containing data for insertion.
 * @param string $prefix Optional key prefix, e.g. 'page_' prefix will result into 'page_name' key.
 * @param resource $conn Custom connection handle
 * @return int The number of affected records
 */
function cot_db_insert($table_name, $data, $prefix = '', $conn = null)
{
	global $cot_dbc;
	$conn = is_null($conn) ? $cot_dbc : $conn;
	if (!is_array($data))
	{
		return 0;
	}
	$keys = '';
	$vals = '';
	// Check the array type
	$arr_keys = array_keys($data);
	$multiline = is_numeric($arr_keys[0]);
	// Build the query
	if ($multiline)
	{
		$rowset = &$data;
	}
	else
	{
		$rowset = array($data);
	}
	$keys_built = false;
	$cnt = count($rowset);
	for ($i = 0; $i < $cnt; $i++)
	{
		$vals .= ($i > 0) ? ',(' : '(';
		$j = 0;
		if (is_array($rowset[$i]))
		{
			foreach ($rowset[$i] as $key => $val)
			{
				if (is_null($val))
				{
					continue;
				}
				if ($j > 0) $vals .= ',';
				if (!$keys_built)
				{
					if ($j > 0) $keys .= ',';
					$keys .= "`{$prefix}$key`";
				}
				if ($val === 'NOW()')
				{
					$vals .= 'NOW()';
				}
				elseif (is_int($val) || is_float($val))
				{
					$vals .= $val;
				}
				else
				{
					$vals .= "'".cot_db_prep($val, $conn)."'";
				}
				$j++;
			}
		}
		$vals .= ')';
		$keys_built = true;
	}
	if (!empty($keys) && !empty($vals))
	{
		cot_db_query("INSERT INTO `$table_name` ($keys) VALUES $vals", $conn);
		return cot_db_affectedrows($conn);
	}
	return 0;
}

/**
 * Performs simple SQL DELETE query and returns number of removed items.
 *
 * @param string $table_name Table name
 * @param string $condition Body of WHERE clause
 * @param resource $conn Custom connection handle
 * @return int
 */
function cot_db_delete($table_name, $condition = '', $conn = null)
{
	global $cot_dbc;
	$conn = is_null($conn) ? $cot_dbc : $conn;
	if (empty($condition))
	{
		cot_db_query("DELETE FROM $table_name", $conn);
	}
	else
	{
		cot_db_query("DELETE FROM $table_name WHERE $condition", $conn);
	}
	return cot_db_affectedrows($conn);
}

/**
 * Performs SQL UPDATE with simple data array. Array keys must match table keys, optionally you can specify
 * key prefix as fourth parameter. Strings get quoted and escaped automatically.
 * Ints and floats must be typecasted.
 * You can use special values in the array:
 * - PHP NULL => SQL NULL
 * - 'NOW()' => SQL NOW()
 *
 * @param string $table_name Table name
 * @param array $data Associative array containing data for update
 * @param string $condition Body of SQL WHERE clause
 * @param string $prefix Optional key prefix, e.g. 'page_' prefix will result into 'page_name' key
 * @param bool $update_null Nullify cells which have null values in the array. By default they are skipped
 * @param resource $conn Custom connection handle
 * @return int The number of affected records
 */
function cot_db_update($table_name, $data, $condition, $prefix = '', $update_null = false, $conn = null)
{
	global $cot_dbc;
	$conn = is_null($conn) ? $cot_dbc : $conn;
	if(!is_array($data))
	{
		return 0;
	}
	$upd = '';
	$condition = empty($condition) ? '' : 'WHERE '.$condition;
	foreach ($data as $key => $val)
	{
		if (is_null($val) && !$update_null)
		{
			continue;
		}
		$upd .= "`{$prefix}$key`=";
		if (is_null($val))
		{
			$upd .= 'NULL,';
		}
		elseif ($val === 'NOW()')
		{
			$upd .= 'NOW(),';
		}
		elseif (is_int($val) || is_float($val))
		{
			$upd .= $val.',';
		}
		else
		{
			$upd .= "'".cot_db_prep($val, $conn)."',";
		}

	}
	if (!empty($upd))
	{
		$upd = mb_substr($upd, 0, -1);
		cot_db_query("UPDATE $table_name SET $upd $condition", $conn);
		return cot_db_affectedrows($conn);
	}
	return 0;
}

?>
