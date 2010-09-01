<?PHP

/**
 * PHP Data Objects (PDO) database driver
 *
 * @package Cotonti
 * @version 0.7.0
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

/**
 * Returns number of rows affected by last query
 *
 * @param PDO $conn Custom connection handle
 * @return int
 */
function sed_sql_affectedrows($conn = null)
{
	global $sed_dbc, $sed_sql_affectedrows;
	if (is_null($conn)) $conn = $sed_dbc;
	return $sed_sql_affectedrows[spl_object_hash($conn)];
}

/**
 * Closes database connection
 *
 * @param PDO $conn Custom connection handle
 */
function sed_sql_close($conn = null)
{
	global $sed_dbc;
	is_null($conn) ? $sed_dbc = null : $conn = null;
}

/**
 * Connects to the database and returns connection handle
 *
 * @global $cfg
 * @param string $host Host name
 * @param string $user User name
 * @param string $pass Password
 * @param string $db Database name
 * @return PDO
 */
function sed_sql_connect($host, $user, $pass, $db)
{
	global $cfg;
	$pdo_options = array(PDO::ATTR_PERSISTENT => true);
	if (!empty($cfg['mysqlcharset']))
	{
		$collation_query = "SET NAMES '{$cfg['mysqlcharset']}'";
		if (!empty($cfg['mysqlcollate']) )
		{
			$collation_query .= " COLLATE '{$cfg['mysqlcollate']}'";
		}
		$pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = $collation_query;
	}
	try
	{
		$connection = new PDO("mysql:host=$host;dbname=$db", $user, $pass, $pdo_options);
	}
	catch (PDOException $e)
	{
		sed_diefatal('Could not connect to database !<br />
			Please check your settings in the file datas/config.php<br />
			MySQL error : '.$e->getMessage());
	}
	return $connection;
}

/**
 * Returns last error number
 *
 * @param PDO $conn Custom connection handle
 * @return int
 */
function sed_sql_errno($conn = null)
{
	global $sed_dbc;
	$info = is_null($conn) ? $sed_dbc->errorInfo() : $conn->errorInfo();
	return $info[1];
}

/**
 * Returns last SQL error message
 *
 * @param PDO $conn Custom connection handle
 * @return string
 */
function sed_sql_error($conn = null)
{
	global $sed_dbc;
	$info = is_null($conn) ? $sed_dbc->errorInfo() : $conn->errorInfo();
	return $info[2];
}

/**
 * Fetches result row as mixed numeric/associative array
 *
 * @param PDOStatement $res Query result
 * @return array
 */
function sed_sql_fetcharray($res)
{
	return $res->fetch();
}

/**
 * Returns result row as associative array
 *
 * @param PDOStatement $res Query result
 * @return array
 */
function sed_sql_fetchassoc($res)
{
	return $res->fetch(PDO::FETCH_ASSOC);
}

/**
 * Returns result row as numeric array
 *
 * @param PDOStatement $res Query result
 * @return array
 */
function sed_sql_fetchrow($res)
{
	return $res->fetch(PDO::FETCH_NUM);
}

/**
 * Returns number of records total for last query with SQL_CALC_FOUND_ROWS
 *
 * @param PDO $conn Custom connection
 * @return int
 */
function sed_sql_foundrows($conn = NULL)
{
	return (int) sed_sql_result(sed_sql_query('SELECT FOUND_ROWS()'), 0, 0);
}

/**
 * Frees result resources
 *
 * @param PDOStatement $res Query result
 */
function sed_sql_freeresult($res)
{
	$res = null;
}

/**
 * Returns ID of last INSERT query
 *
 * @param PDO $conn Custom connection handle
 * @return int
 */
function sed_sql_insertid($conn = null)
{
	global $sed_dbc;
	return is_null($conn) ? $sed_dbc->lastInsertId() : $conn->lastInsertId();
}

/**
 * Returns list of tables for a database. Use sed_sql_fetcharray() to get table names from result
 *
 * @param string $db_name Database name
 * @param PDO $conn Custom connection handle
 * @return PDOStatement
 */
function sed_sql_listtables($db_name, $conn = null)
{
	global $sed_dbc;
	if (is_null($conn)) $conn = $sed_dbc;
	return $conn->query("SHOW TABLES");
}

/**
 * Returns number of rows in result set
 *
 * @param PDOStatement $res Query result
 * @return int
 */
function sed_sql_numrows($res)
{
	return $res->rowCount();
}

/**
 * Escapes potentially insecure sequences in string
 *
 * @param string $str
 * @param PDO $conn Custom connection handle
 * @return string
 */
function sed_sql_prep($str, $conn = null)
{
	global $sed_dbc;
	$conn = is_null($conn) ? $sed_dbc : $conn;
	return preg_replace("#^'(.*)'\$#", '$1', $conn->quote($str));
}

/**
 * Executes SQL query
 *
 * @global $sys
 * @global $cfg
 * @global $usr
 * @param string $query SQL query
 * @param PDO $conn Custom connection handle
 * @return PDOStatement
 */
function sed_sql_query($query, $conn = null)
{
	global $sys, $cfg, $usr, $sed_dbc, $sed_sql_affectedrows;
	$conn = is_null($conn) ? $sed_dbc : $conn;
	$sys['qcount']++;
	$xtime = microtime();
	$result = $conn->query($query) OR sed_diefatal('SQL error : '.sed_sql_error($conn));
	$sed_sql_affectedrows[spl_object_hash($conn)] = $result->rowCount();
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
 * @param PDOStatement $res Result set
 * @param int $row Row number
 * @param mixed $col Column name or index (null-based)
 * @return mixed
 */
function sed_sql_result($res, $row = 0, $col = 0)
{
	$r = $res->fetch(PDO::FETCH_BOTH, PDO::FETCH_ORI_ABS, $row);
	return $r[$col];
}

require_once $cfg['system_dir'] . '/database.common.php';

?>