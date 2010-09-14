<?php

/**
 * MySQL database driver
 *
 * @package Cotonti
 * @version 0.7.0
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Returns number of rows affected by last query
 *
 * @param resource $conn Custom connection handle
 * @return int
 */
function cot_db_affectedrows($conn = null)
{
	global $cot_dbc;
	return is_null($conn) ? mysql_affected_rows($cot_dbc) : mysql_affected_rows($conn);
}

/**
 * Closes database connection
 *
 * @param resource $conn Custom connection handle
 * @return int
 */
function cot_db_close($conn = null)
{
	global $cot_dbc;
	return is_null($conn) ? mysql_close($cot_dbc) : mysql_close($conn);
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
function cot_db_connect($host, $user, $pass, $db)
{
	global $cfg;
	$connection = @mysql_connect($host, $user, $pass);
	if (!$connection && !defined('COT_INSTALL'))
	{
		cot_diefatal('Could not connect to database !<br />Please check your settings in the file datas/config.php<br />'.'MySQL error : '.cot_db_error()); // TODO: Need translate
		if (!version_compare(mysql_get_server_info($connection), '4.1.0', '>='))
		{
			cot_diefatal('Cotonti system requirements: MySQL 4.1 or above.'); // TODO: Need translate
		}
	}
	elseif (!$connection)
	{
		// Used by installer to identify where the problem was
		return 1;
	}

	if (!empty($cfg['mysqlcharset']))
	{
		$collation_query = "SET NAMES '{$cfg['mysqlcharset']}'";
		if (!empty($cfg['mysqlcollate']) )
		{
			$collation_query .= " COLLATE '{$cfg['mysqlcollate']}'";
		}
		@mysql_query($collation_query, $connection);
	}
	$select = @mysql_select_db($db, $connection);
	if (!$select && !defined('COT_INSTALL'))
	{
		cot_diefatal('Could not select the database !<br />Please check your settings in the file datas/config.php<br />'.'MySQL error : '.cot_db_error()); // TODO: Need translate
	}
	elseif (!$select)
	{
		// Used by installer to identify where the problem was
		return 2;
	}
	return $connection;
}

/**
 * Returns last error number
 *
 * @param resource $conn Custom connection handle
 * @return int
 */
function cot_db_errno($conn = null)
{
	global $cot_dbc;
	return is_null($conn) ? mysql_errno($cot_dbc) : mysql_errno($conn);
}

/**
 * Returns last SQL error message
 *
 * @param resource $conn Custom connection handle
 * @return string
 */
function cot_db_error($conn = null)
{
	global $cot_dbc;
	return is_null($conn) ? mysql_error($cot_dbc) : mysql_error($conn);
}

/**
 * Fetches result row as mixed numeric/associative array
 *
 * @param resource $res Query result
 * @return array
 */
function cot_db_fetcharray($res)
{
	return mysql_fetch_array($res);
}

/**
 * Returns result row as associative array
 *
 * @param resource $res Query result
 * @return array
 */
function cot_db_fetchassoc($res)
{
	return mysql_fetch_assoc($res);
}

/**
 * Returns result row as numeric array
 *
 * @param resource $res Query result
 * @return array
 */
function cot_db_fetchrow($res)
{
	return mysql_fetch_row($res);
}

/**
 * Returns number of records total for last query with SQL_CALC_FOUND_ROWS
 *
 * @param resource $conn Custom connection
 * @return int
 */
function cot_db_foundrows($conn = NULL)
{
	return (int) cot_db_result(cot_db_query('SELECT FOUND_ROWS()'), 0, 0);
}

/**
 * Frees result resources
 *
 * @param resource $res Query result
 * @return bool
 */
function cot_db_freeresult($res)
{
	return mysql_free_result($res);
}

/**
 * Returns ID of last INSERT query
 *
 * @param resource $conn Custom connection handle
 * @return int
 */
function cot_db_insertid($conn = null)
{
	global $cot_dbc;
	return is_null($conn) ? mysql_insert_id($cot_dbc) : mysql_insert_id($conn);
}

/**
 * Returns list of tables for a database. Use cot_db_fetcharray() to get table names from result
 *
 * @param string $db_name Database name
 * @param resource $conn Custom connection handle
 * @return resource
 */
function cot_db_listtables($db_name, $conn = null)
{
	global $cot_dbc;
	return is_null($conn) ? mysql_list_tables($db_name, $cot_dbc) : mysql_list_tables($db_name, $conn);
}

/**
 * Returns number of rows in result set
 *
 * @param resource $res Query result
 * @return int
 */
function cot_db_numrows($res)
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
function cot_db_prep($str, $conn = null)
{
	global $cot_dbc;
	return is_null($conn) ? mysql_real_escape_string($str, $cot_dbc) : mysql_real_escape_string($str, $conn);
}

/**
 * Executes SQL query
 *
 * @global $sys
 * @global $cfg
 * @global $cot_dbc
 * @param string $query SQL query
 * @param resource $conn Custom connection handle
 * @return resource
 */
function cot_db_query($query, $conn = null)
{
	global $sys, $cfg, $cot_dbc;
	$conn = is_null($conn) ? $cot_dbc : $conn;
	$sys['qcount']++;
	$xtime = microtime();
	$result = mysql_query($query, $conn);
	if (!$result && !defined('COT_INSTALL'))
	{
		cot_diefatal('SQL error : '.cot_db_error($conn));
	}
	elseif (!$result)
	{
		return;
	}
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
function cot_db_result($res, $row = 0, $col = 0)
{
	return mysql_result($res, $row, $col);
}

require_once $cfg['system_dir'] . '/database.common.php';

?>