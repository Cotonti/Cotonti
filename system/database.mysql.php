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
 * @return int
 */
function sed_sql_affectedrows()
{
	return mysql_affected_rows();
}

/**
 * Closes database connection
 *
 * @return int
 */
function sed_sql_close()
{
	return mysql_close();
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
	if (!empty($cfg['mysqlcharset']))
	{
		@mysql_query("SET NAMES {$cfg['mysqlcharset']}", $connection);
	}
	$select = @mysql_select_db($db, $connection) or sed_diefatal('Could not select the database !<br />Please check your settings in the file datas/config.php<br />'.'MySQL error : '.sed_sql_error());
	return $connection;
}

/**
 * Returns last error number
 *
 * @return int
 */
function sed_sql_errno()
{
	return mysql_errno();
}

/**
 * Returns last SQL error message
 *
 * @return string
 */
function sed_sql_error()
{
	return mysql_error();
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
 * @return int
 */
function sed_sql_insertid()
{
	return mysql_insert_id();
}

/**
 * Returns list of tables for a database. Use sed_sql_fetcharray() to get table names from result
 *
 * @param string $db_name Database name
 * @return resource
 */
function sed_sql_listtables($db_name)
{
	return mysql_list_tables($db_name);
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
 * @return string
 */
function sed_sql_prep($str)
{
	return mysql_real_escape_string($str);
}

/**
 * Executes SQL query
 *
 * @global $sys
 * @global $cfg
 * @global $usr
 * @param string $query SQL query
 * @return resource
 */
function sed_sql_query($query)
{
	global $sys, $cfg, $usr;
	$sys['qcount']++;
	$xtime = microtime();
	$result = mysql_query($query) OR sed_diefatal('SQL error : '.sed_sql_error());
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
 * @return int
 */
function sed_sql_rowcount($table)
{
	$sqltmp = sed_sql_query("SELECT COUNT(*) FROM $table");
	return (int) mysql_result($sqltmp, 0, 0);
}
?>
