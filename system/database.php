<?php
/**
 * PDO-based database layer
 *
 * @see http://www.php.net/manual/en/book.pdo.php
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright (c) Cotonti Team 2010-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Cotonti Database Connection class.
 * A compact extension to standard PHP PDO class with slight Cotonti-specific needs,
 * handy functions and query builder.
 *
 * @see http://www.php.net/manual/en/class.pdo.php
 *
 * @property-read int $affectedRows Number of rows affected by the most recent query
 * @property-read int $count Total query count
 * @property-read int $timeCount Total query execution time
 */
class CotDB extends PDO {
	/**
	 * Number of rows affected by the most recent query
	 * @var int
	 */
	private $_affected_rows = 0;

	/**
	 * Total query count
	 * @var int
	 */
	private $_count = 0;

	/**
	 * Prepare statements by itself. Used with MySQL client API versions prior to 5.1
	 * @var bool
	 */
	private $_prepare_itself = false;

	/**
	 * Total query execution time
	 * @var int
	 */
	private $_tcount = 0;

	/**
	 * Timer start microtime
	 * @var string
	 */
	private $_xtime = 0;

	/**
	 * Creates a PDO instance to represent a connection to the requested database.
	 *
	 * @param string $dsn The Data Source Name, or DSN, contains the information required to connect to the database.
	 * @param string $username The user name for the DSN string.
	 * @param string $passwd The password for the DSN string.
	 * @param array $options A key=>value array of driver-specific connection options.
	 * @see http://www.php.net/manual/en/pdo.construct.php
	 */
	public function  __construct($dsn, $username, $passwd, $options = array())
	{
		global $cfg;
		if (!empty($cfg['mysqlcharset']) && version_compare(PHP_VERSION, '5.3.0', '!='))
		{
			$collation_query = "SET NAMES '{$cfg['mysqlcharset']}'";
			if (!empty($cfg['mysqlcollate']) )
			{
				$collation_query .= " COLLATE '{$cfg['mysqlcollate']}'";
			}
			$options[PDO::MYSQL_ATTR_INIT_COMMAND] = $collation_query;
		}
		parent::__construct($dsn, $username, $passwd, $options);
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if (version_compare($this->getAttribute(PDO::ATTR_CLIENT_VERSION), '5.1.0', '<'))
		{
			$this->_prepare_itself = true;
		}
	}

	/**
	 * Provides access to properties
	 * @param string $name Property name
	 * @return mixed Property value
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'affectedRows':
				return $this->_affected_rows;
				break;
			case 'count':
				return $this->_count;
				break;
			case 'timeCount':
				return $this->_tcount;
				break;
			default:
				return null;
		}
	}

	/**
	 * Binds parameters to a statement
	 *
	 * @param PDOStatement $statement PDO statement
	 * @param array $parameters Array of parameters, numeric or associative
	 */
	private function _bindParams($statement, $parameters)
	{
		$is_numeric = is_int(key($parameters));
		foreach ($parameters as $key => $val)
		{
			$type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
			$is_numeric ? $statement->bindValue($key + 1, $val, $type) : $statement->bindValue($key, $val, $type);
		}
	}

	/**
	 * Parses PDO exception message and returns its components and status
	 *
	 * @param PDOException $e PDO Exception
	 * @param string $err_code Output error code parameter
	 * @param string $err_message Output error message parameter
	 * @return bool TRUE for error cases, FALSE for notifications and warnings
	 */
	private function _parseError(PDOException $e, &$err_code, &$err_message)
	{
		$pdo_message = $e->getMessage();
		if (preg_match('#SQLSTATE\[(\w+)\].*?: (.*)#', $pdo_message, $matches))
		{
            $err_code = $matches[1];
            $err_message = $matches[2];
        }
		else
		{
			$err_code = $e->getCode();
			$err_message = $pdo_message;
		}
		return $err_code > '02';
	}

	/**
	 * Prepares a parametrized query on client side
	 *
	 * @param string $query Query being prepared
	 * @param array $parameters Associative or numeric array of parameters
	 * @return string Array with placeholders substituted
	 */
	private function _prepare($query, $parameters = array())
	{
		if (count($parameters) > 0)
		{
			foreach ($parameters as $key => $val)
			{
				$placeholder = is_int($key) ? '?' : ':' . $key;
				$value = is_int($val) ? $val : $this->quote($val);
				$query = preg_replace('`' . preg_quote($placeholder) . '`', $value, $query, 1);
			}
		}
		return $query;
	}

	/**
	 * Starts query execution timer
	 */
	private function _startTimer()
	{
		global $cfg;
		$this->_count++;
		if ($cfg['showsqlstats'] || $cfg['debug_mode'])
		{
			$this->_xtime = microtime();
		}
	}

	/**
	 * Stops query execution timer
	 */
	private function _stopTimer($query)
	{
		global $cfg, $usr, $sys;
		if ($cfg['showsqlstats'] || $cfg['debug_mode'])
		{
			$ytime = microtime();
			$xtime = explode(' ',$this->_xtime);
			$ytime = explode(' ',$ytime);
			$this->_tcount += $ytime[1] + $ytime[0] - $xtime[1] - $xtime[0];
			if ($cfg['devmode'] || $cfg['debug_mode'])
			{
				$calls = '';
				$bt = debug_backtrace();
				for ($i = sizeof($bt)-1; $i > 0; $i--)
				{
					$call = (($bt[$i]['object'] && $bt[$i]['class']) ? $bt[$i]['class'].$bt[$i]['type'] : '').$bt[$i]['function'].'();';
					$calls .= (empty($calls)?'':"\n → ").basename($bt[$i]['file']).' ['.$bt[$i]['line'].']: '.$call;
				}
				$sys['devmode']['queries'][] = array ($this->_count, $ytime[1] + $ytime[0] - $xtime[1] - $xtime[0], $query, $calls);
				$sys['devmode']['timeline'][] = $xtime[1] + $xtime[0] - $sys['starttime'];
			}
		}
	}

	/**
	 * Returns total number of records contained in a table
	 * @param string $table_name Table name
	 * @return int
	 */
	public function countRows($table_name)
	{
		return $this->query("SELECT COUNT(*) FROM `$table_name`")->fetchColumn();
	}

	/**
	 * Performs simple SQL DELETE query and returns number of removed items.
	 *
	 * @param string $table_name Table name
	 * @param string $condition Body of WHERE clause
	 * @param array $parameters Array of statement input parameters, see http://www.php.net/manual/en/pdostatement.execute.php
	 * @return int Number of records removed on success or FALSE on error
	 */
	public function delete($table_name, $condition = '', $parameters = array())
	{
		$query = empty($condition) ? "DELETE FROM `$table_name`" : "DELETE FROM `$table_name` WHERE $condition";
		if (!is_array($parameters))
		{
			$parameters = array($parameters);
		}
		$this->_startTimer();
		try
		{
			if (count($parameters) > 0)
			{
				if ($this->_prepare_itself)
				{
					$res = $this->exec($this->_prepare($query, $parameters));
				}
				else
				{
					$stmt = $this->prepare($query);
					$this->_bindParams($stmt, $parameters);
					$stmt->execute();
					$res = $stmt->rowCount();
				}
			}
			else
			{
				$res = $this->exec($query);
			}
		}
		catch (PDOException $err)
		{
			if ($this->_parseError($err, $err_code, $err_message))
			{
				cot_diefatal('SQL error ' . $err_code . ': ' . $err_message);
			}
		}
		$this->_stopTimer($query);
		return $res;
	}

	/**
	 * Checks if a field exists in a table
	 *
	 * @param string $table_name Table name
	 * @param string $field_name Field name
	 * @return bool TRUE if the field exists, FALSE otherwise
	 */
	function fieldExists($table_name, $field_name)
	{
		return $this->query("SHOW COLUMNS FROM `$table_name` WHERE Field = " . $this->quote($field_name))->rowCount() == 1;
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
	 * @param bool $insert_null Insert SQL NULL for empty values rather than ignoring them.
	 * @return int The number of affected records
	 */
	public function insert($table_name, $data, $insert_null = false)
	{
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
					if ($j > 0) $vals .= ',';
					if (!$keys_built)
					{
						if ($j > 0) $keys .= ',';
						$keys .= "`$key`";
					}
					if (is_null($val) && $insert_null)
					{
						$vals .= 'NULL';
					}
					elseif ($val === 'NOW()')
					{
						$vals .= 'NOW()';
					}
					elseif (is_int($val) || is_float($val))
					{
						$vals .= $val;
					}
					else
					{
						$vals .= $this->quote($val);
					}
					$j++;
				}
			}
			$vals .= ')';
			$keys_built = true;
		}
		if (!empty($keys) && !empty($vals))
		{
			$query = "INSERT INTO `$table_name` ($keys) VALUES $vals";
			$this->_startTimer();
			try
			{
				$res = $this->exec($query);
			}
			catch (PDOException $err)
			{
				if ($this->_parseError($err, $err_code, $err_message))
				{
					cot_diefatal('SQL error ' . $err_code . ': ' . $err_message);
				}
			}
			$this->_stopTimer($query);
			return $res;
		}
		return 0;
	}

	public function prep($str)
	{
		return preg_replace("#^'(.*)'\$#", '$1', $this->quote($str));
	}

	/**
	 * Runs an SQL script containing multiple queries.
	 *
	 * @param string $script SQL script body, containing formatted queries separated by semicolons and newlines
	 * @param resource $conn Custom connection handle
	 * @return string Error message if an error occurs or empty string on success
	 */
	public function runScript($script)
	{
		global $db_x;
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
				$result = $this->query($query);
				if (!$result)
				{
					return $this->error . '<br />' . htmlspecialchars($query) . '<hr />';
				}
				elseif ($result instanceof PDOStatement)
				{
					$result->closeCursor();
				}
			}
		}
		return '';
	}

	/**
	 * 1) If called with one parameter:
	 * Works like PDO::query()
	 * Executes an SQL statement in a single function call, returning the result set (if any) returned by the statement as a PDOStatement object.
	 * 2) If called with second parameter as array of input parameter bindings:
	 * Works like PDO::prepare()->execute()
	 * Prepares an SQL statement and executes it.
	 * @see http://www.php.net/manual/en/pdo.query.php
	 * @see http://www.php.net/manual/en/pdo.prepare.php
	 * @param string $query The SQL statement to prepare and execute.
	 * @param array $parameters An array of values to be binded as input parameters to the query. PHP int parameters will beconsidered as PDO::PARAM_INT, others as PDO::PARAM_STR.
	 * @return PDOStatement
	 */
	public function query($query, $parameters = array())
	{
		if (!is_array($parameters))
		{
			$parameters = array($parameters);
		}
		$this->_startTimer();
		try
		{
			if (count($parameters) > 0)
			{
				if ($this->_prepare_itself)
				{
					$result = parent::query($this->_prepare($query, $parameters));
				}
				else
				{
					$result = parent::prepare($query);
					$this->_bindParams($result, $parameters);
					$result->execute();
				}
			}
			else
			{
				$result = parent::query($query);
			}
		}
		catch (PDOException $err)
		{
			if ($this->_parseError($err, $err_code, $err_message))
			{
				cot_diefatal('SQL error ' . $err_code . ': ' . $err_message);
			}
		}
		$this->_stopTimer($query);
		// In Cotonti we use PDO::FETCH_ASSOC by default to save memory
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$this->_affected_rows = $result->rowCount();
		return $result;
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
	 * @param array $parameters Array of statement input parameters, see http://www.php.net/manual/en/pdostatement.execute.php
	 * @param bool $update_null Nullify cells which have null values in the array. By default they are skipped
	 * @return int The number of affected records or FALSE on error
	 */
	public function update($table_name, $data, $condition ='', $parameters = array(), $update_null = false)
	{
		if(!is_array($data))
		{
			return 0;
		}
		$upd = '';
		if (!is_array($parameters))
		{
			$parameters = array($parameters);
		}
		if ($this->_prepare_itself && !empty($condition) && count($parameters) > 0)
		{
			$condition = $this->_prepare($condition, $parameters);
			$parameters = array();
		}
		$condition = empty($condition) ? '' : 'WHERE '.$condition;
		foreach ($data as $key => $val)
		{
			if (is_null($val) && !$update_null)
			{
				continue;
			}
			$upd .= "`$key`=";
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
				$upd .= $this->quote($val) . ',';
			}

		}
		if (!empty($upd))
		{
			$upd = mb_substr($upd, 0, -1);
			$query = "UPDATE `$table_name` SET $upd $condition";
			$this->_startTimer();
			try
			{
				if (count($parameters) > 0)
				{
					$stmt = $this->prepare($query);
					$this->_bindParams($stmt, $parameters);
					$stmt->execute();
					$res = $stmt->rowCount();
				}
				else
				{
					$res = $this->exec($query);
				}
			}
			catch (PDOException $err)
			{
				if ($this->_parseError($err, $err_code, $err_message))
				{
					cot_diefatal('SQL error ' . $err_code . ': ' . $err_message);
				}
			}
			$this->_stopTimer($query);
			return $res;
		}
		return 0;
	}
}

?>