<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=system/functions/database.lib.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=Functions
[END_SED]
==================== */

/**
 * @package Seditio-N
 * @version 0.0.1
 * @copyright Partial copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

/**
 * Returns number of rows affected by last query
 *
 * @param resource $conn Custom connection handle
 * @return int
 */
function sed_sql_affectedrows($conn = null)
{
	global $sed_dbc;
	return is_null($conn) ? mysql_affected_rows($sed_dbc) : mysql_affected_rows($conn);
}

/**
 * Closes database connection
 *
 * @param resource $conn Custom connection handle
 * @return int
 */
function sed_sql_close($conn = null)
{
	global $sed_dbc;
	return is_null($conn) ? mysql_close($sed_dbc) : mysql_close($conn);
}

/**
 * Connects to the database and returns connection handle
 *
 * @global $cfg
 * @param string $host Host name
 * @param string $user User name
 * @param string $pass Password
 * @param string $db Database name
 * @return resource
 */
function sed_sql_connect($host, $user, $pass, $db)
{
	global $cfg;
	$connection = @mysql_connect($host, $user, $pass) or sed_diefatal('Could not connect to database !<br />Please check your settings in the file datas/config.php<br />'.'MySQL error : '.sed_sql_error());
	if (version_compare(mysql_get_server_info($connection), '4.1.0', '>=') && !empty($cfg['mysqlcharset']))
	{
		$collation_query = "SET NAMES '{$cfg['mysqlcharset']}'";
		if (!empty($cfg['mysqlcollate']) )
		{
			$collation_query .= " COLLATE '{$cfg['mysqlcollate']}'";
		}
		@mysql_query($collation_query, $connection);
	}
	$select = @mysql_select_db($db, $connection) or sed_diefatal('Could not select the database !<br />Please check your settings in the file datas/config.php<br />'.'MySQL error : '.sed_sql_error());
	return $connection;
}

/**
 * Returns last error number
 *
 * @param resource $conn Custom connection handle
 * @return int
 */
function sed_sql_errno($conn = null)
{
	global $sed_dbc;
	return is_null($conn) ? mysql_errno($sed_dbc) : mysql_errno($conn);
}

/**
 * Returns last SQL error message
 *
 * @param resource $conn Custom connection handle
 * @return string
 */
function sed_sql_error($conn = null)
{
	global $sed_dbc;
	return is_null($conn) ? mysql_error($sed_dbc) : mysql_error($conn);
}

/**
 * Fetches result row as mixed numeric/associative array
 *
 * @param resource $res Query result
 * @return array
 */
function sed_sql_fetcharray($res)
{
	return mysql_fetch_array($res);
}

/**
 * Returns result row as associative array
 *
 * @param resource $res Query result
 * @return array
 */
function sed_sql_fetchassoc($res)
{
	return mysql_fetch_assoc($res);
}

/**
 * Returns result row as numeric array
 *
 * @param resource $res Query result
 * @return array
 */
function sed_sql_fetchrow($res)
{
	return mysql_fetch_row($res);
}

/**
 * Frees result resources
 *
 * @param resource $res Query result
 * @return bool
 */
function sed_sql_freeresult($res)
{
	return mysql_free_result($res);
}

/**
 * Returns ID of last INSERT query
 *
 * @param resource $conn Custom connection handle
 * @return int
 */
function sed_sql_insertid($conn = null)
{
	global $sed_dbc;
	return is_null($conn) ? mysql_insert_id($sed_dbc) : mysql_insert_id($conn);
}

/**
 * Returns list of tables for a database. Use sed_sql_fetcharray() to get table names from result
 *
 * @param string $db_name Database name
 * @param resource $conn Custom connection handle
 * @return resource
 */
function sed_sql_listtables($db_name, $conn = null)
{
	global $sed_dbc;
	return is_null($conn) ? mysql_list_tables($db_name, $sed_dbc) : mysql_list_tables($db_name, $conn);
}

/**
 * Returns number of rows in result set
 *
 * @param resource $res Query result
 * @return int
 */
function sed_sql_numrows($res)
{
	return mysql_num_rows($res);
}

/**
 * Escapes potentially insecure sequences in string
 *
 * @param string $str
 * @param resource $conn Custom connection handle
 * @return string
 */
function sed_sql_prep($str, $conn = null)
{
	global $sed_dbc;
	return is_null($conn) ? mysql_real_escape_string($str, $sed_dbc) : mysql_real_escape_string($str, $conn);
}

/**
 * Executes SQL query
 *
 * @global $sys
 * @global $cfg
 * @global $usr
 * @param string $query SQL query
 * @param resource $conn Custom connection handle
 * @return resource
 */
function sed_sql_query($query, $conn = null)
{
	global $sys, $cfg, $usr, $sed_dbc;
	$conn = is_null($conn) ? $sed_dbc : $conn;
	$sys['qcount']++;
	$xtime = microtime();
	$result = mysql_query($query, $conn) OR sed_diefatal('SQL error : '.sed_sql_error($conn));
	$ytime = microtime();
	$xtime = explode(' ',$xtime);
	$ytime = explode(' ',$ytime);
	$sys['tcount'] = $sys['tcount'] + $ytime[1] + $ytime[0] - $xtime[1] - $xtime[0];
	if ($cfg['devmode'])
	{
		$sys['devmode']['queries'][] = array ($sys['qcount'], $ytime[1] + $ytime[0] - $xtime[1] - $xtime[0], $query);
		$sys['devmode']['timeline'][] = $xtime[1] + $xtime[0] - $sys['starttime'];
	}
	return $result;
}

/**
 * Fetches a single cell from result
 *
 * @param resource $res Result set
 * @param int $row Row number
 * @param mixed $col Column name or index (null-based)
 * @return mixed
 */
function sed_sql_result($res, $row, $col)
{
	return mysql_result($res, $row, $col);
}

/**
 * Returns number of rows in a table
 *
 * @param string $table Table name
 * @param resource $conn Custom connection handle
 * @return int
 */
function sed_sql_rowcount($table, $conn = null)
{
	global $sed_dbc;
	$conn = is_null($conn) ? $sed_dbc : $conn;
	$sqltmp = sed_sql_query("SELECT COUNT(*) FROM $table", $conn);
	return (int) mysql_result($sqltmp, 0, 0);
}

/**
 * Performs SQL INSERT on simple data array. Array keys must match table keys, optionally you can specify
 * key prefix as third parameter. Strings get quoted and escaped automatically.
 * Ints and floats must be typecasted.
 * You can use special values in the array:
 * - PHP NULL => SQL NULL
 * - 'NOW()' => SQL NOW()
 * The number of affected records is returned.
 *
 * @param string $table_name Table name
 * @param array $data Associative array containing data for insertion.
 * @param string $prefix Optional key prefix, e.g. 'page_' prefix will result into 'page_name' key.
 * @param resource $conn Custom connection handle
 * @return int
 */
function sed_sql_insert($table_name, $data, $prefix = '', $conn = null)
{
	global $sed_dbc;
	$conn = is_null($conn) ? $sed_dbc : $conn;
	if(!is_array($data))
	{
		return 0;
	}
	$keys = '';
	$vals = '';
	foreach($data as $key => $val)
	{
		$keys .= "`{$prefix}$key`,";
		if(is_null($val))
		{
			$vals .= 'NULL,';
		}
		elseif($val === 'NOW()')
		{
			$vals .= 'NOW(),';
		}
		elseif(is_int($val) || is_float($val))
		{
			$vals .= $val.',';
		}
		else
		{
			$vals .= "'".mysql_real_escape_string($val, $conn)."',";
		}

	}
	if(!empty($keys) && !empty($vals))
	{
		$keys = mb_substr($keys, 0, -1);
		$vals = mb_substr($vals, 0, -1);
		sed_sql_query("INSERT INTO `$table_name` ($keys) VALUES ($vals)", $conn);
		return sed_sql_affectedrows($conn);
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
function sed_sql_delete($table_name, $condition = '', $conn = null)
{
	global $sed_dbc;
	$conn = is_null($conn) ? $sed_dbc : $conn;
	if(empty($condition))
	{
		sed_sql_query("DELETE FROM $table_name", $conn);
	}
	else
	{
		sed_sql_query("DELETE FROM $table_name WHERE $condition", $conn);
	}
	return sed_sql_affectedrows($conn);
}

/**
 * Performs SQL UPDATE with simple data array. Array keys must match table keys, optionally you can specify
 * key prefix as fourth parameter. Strings get quoted and escaped automatically.
 * Ints and floats must be typecasted.
 * You can use special values in the array:
 * - PHP NULL => SQL NULL
 * - 'NOW()' => SQL NOW()
 * The number of affected records is returned.
 *
 * @param string $table_name Table name
 * @param string $condition Body of SQL WHERE clause
 * @param array $data Associative array containing data for insertion.
 * @param string $prefix Optional key prefix, e.g. 'page_' prefix will result into 'page_name' key.
 * @param resource $conn Custom connection handle
 * @return int
 */
function sed_sql_update($table_name, $condition, $data, $prefix = '', $conn = null)
{
	global $sed_dbc;
	$conn = is_null($conn) ? $sed_dbc : $conn;
	if(!is_array($data))
	{
		return 0;
	}
	$upd = '';
	$condition = empty($condition) ? '' : 'WHERE '.$condition;
	foreach($data as $key => $val)
	{
		$upd .= "`{$prefix}$key`=";
		if(is_null($val))
		{
			$upd .= 'NULL,';
		}
		elseif($val === 'NOW()')
		{
			$upd .= 'NOW(),';
		}
		elseif(is_int($val) || is_float($val))
		{
			$upd .= $val.',';
		}
		else
		{
			$upd .= "'".mysql_real_escape_string($val, $conn)."',";
		}

	}
	if(!empty($upd))
	{
		$upd = mb_substr($upd, 0, -1);
		sed_sql_query("UPDATE $table_name SET $upd $condition", $conn);
		return sed_sql_affectedrows($conn);
	}
	return 0;
}
?>
