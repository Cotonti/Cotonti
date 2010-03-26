<?PHP
/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * MySQL database driver
 *
 * @package Cotonti
 * @version 0.0.3
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

/**
 * Returns number of rows affected by last query
 *
 * @param resource $conn Custom connection handle
 * @return int
 */
function sed_sql_affectedrows($conn = null)
{
	// FIXME unsupported by PDO
	global $sed_dbc, $sed_sql_affectedrows;
	return $sed_sql_affectedrows;
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
	return is_null($conn) ? $sed_dbc->lastInserdId() : $conn->lastInserdId();
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
	$sed_sql_affectedrows = $result->rowCount();
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

/**
 * Returns number of rows in a table
 *
 * @param string $table Table name
 * @param PDO $conn Custom connection handle
 * @return int
 */
function sed_sql_rowcount($table, $conn = null)
{
	global $sed_dbc;
	$conn = is_null($conn) ? $sed_dbc : $conn;
	$res = $conn->query("SELECT COUNT(*) FROM `$table`");
	return (int) $res->fetchColumn();
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
 * @param PDO $conn Custom connection handle
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
			$vals .= $conn->quote($val).",";
		}

	}
	if(!empty($keys) && !empty($vals))
	{
		$keys = mb_substr($keys, 0, -1);
		$vals = mb_substr($vals, 0, -1);
		return $conn->exec("INSERT INTO `$table_name` ($keys) VALUES ($vals)");
	}
	return 0;
}

/**
 * Performs simple SQL DELETE query and returns number of removed items.
 *
 * @param string $table_name Table name
 * @param string $condition Body of WHERE clause
 * @param PDO $conn Custom connection handle
 * @return int
 */
function sed_sql_delete($table_name, $condition = '', $conn = null)
{
	global $sed_dbc;
	$conn = is_null($conn) ? $sed_dbc : $conn;
	if(empty($condition))
	{
		return $conn->exec("DELETE FROM $table_name");
	}
	else
	{
		return $conn->exec("DELETE FROM $table_name WHERE $condition");
	}
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
 * @param PDO $conn Custom connection handle
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
			$upd .= $conn->quote($val).",";
		}

	}
	if(!empty($upd))
	{
		$upd = mb_substr($upd, 0, -1);
		return $conn->exec("UPDATE $table_name SET $upd $condition");
	}
	return 0;
}

?>