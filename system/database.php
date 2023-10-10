<?php
/**
 * PDO-based database layer
 *
 * @see http://www.php.net/manual/en/book.pdo.php
 *
 * @package API - Database
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Cotonti Database Connection class.
 * A compact extension to standard PHP PDO class with slight Cotonti-specific needs,
 * handy functions and query builder.
 *
 * @see http://www.php.net/manual/en/class.pdo.php
 *
 * @property-read string $auth 'cot_auth' table name
 * @property-read string $cache 'cot_cache' table name
 * @property-read string $cache_bindings 'cot_cache_bindings' table name
 * @property-read string $core 'cot_core' table name
 * @property-read string $config 'cot_config' table name
 * @property-read string $groups 'cot_groups' table name
 * @property-read string $groups_users 'cot_groups_users' table name
 * @property-read string $logger 'cot_logger' table name
 * @property-read string $online 'cot_online' table name
 * @property-read string $extra_fields 'cot_extra_fields' table name
 * @property-read string $plugins 'cot_plugins' table name
 * @property-read string $structure 'cot_structure' table name
 * @property-read string $updates 'cot_updates' table name
 * @property-read string $users 'cot_users' table name
 *
 * @property-read int $affectedRows Number of rows affected by the most recent query
 * @property-read int $count Total query count
 * @property-read int $timeCount Total query execution time
 */
class CotDB
{
    protected $tableQuoteCharacter = '`';
    protected $columnQuoteCharacter = '`';

    /**
     * The PDO connection to database
     * @var \PDO
     */
    protected $adapter;

    /**
     * The database connection configuration options.
     * @var array{
     *     adapter: string,
     *     host: string,
     *     port?: int,
     *     tablePrefix?: string,
     *     user: string,
     *     password: string,
     *     dbName?: string,
     *     charset?: string,
     *     collate?: string,
     *     options?: array<int, string>
     * }
     */
    protected $config;

    /**
     * @var int the default fetch mode for this connection.
     * In Cotonti we use PDO::FETCH_ASSOC by default to save memory
     * @see https://www.php.net/manual/en/pdostatement.setfetchmode.php
     */
    //public $fetchMode = \PDO::FETCH_ASSOC;

    /**
     * The table prefix for the connection.
     * @var string
     */
    protected $tablePrefix = '';

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
	 * Total query execution time in microseconds as string
	 * @var numeric-string
	 */
	private $_tcount = '0';

	/**
	 * Timer start microtime
	 * @var string
	 */
	private $_xtime = '0';

	/**
	 * Table names registry
	 * @var array
	 */
	private $_tables = array();

    /**
     * It is used in runScript() only
     * @var string
     */
    public $error = '';

	/**
	 * Creates a PDO instance to represent a connection to the requested database.
	 *
	 * @param array{
     *     adapter: string,
     *     host: string,
     *     port?: int,
     *     tablePrefix?: string,
     *     user: string,
     *     password: string,
     *     dbName?: string,
     *     charset?: string,
     *     collate?: string,
     *     options?: array<int, string>
     * } $config The Data Source Name, or DSN, contains the information required to connect to the database.
     * Where array keys are:
     *  'adapter' => 'mysql' db type
     *  'user' The user name for the DSN string.
     *  'password' The password for the DSN string.
     *  'dbName' Optional. Database name
     *  'options' A key=>value array of driver-specific connection options.
	 */
    public function  __construct($config)
	{
        $this->config = $config;
        if (!empty($config['tablePrefix'])) {
            $this->tablePrefix = $config['tablePrefix'];
        }

        $this->adapter = static::connect();
	}

    /**
     * Connect to Data Base
     * @return \PDO
     *
     * @see http://www.php.net/manual/en/pdo.construct.php
     * @todo when multiple connections to databases will be implemented, use connection registry
     */
    protected function connect()
    {
        if (!empty($this->config['charset'])) {
            $collation_query = "SET NAMES '{$this->config['charset']}'";
            if (!empty($this->config['collate'])) {
                $collation_query .= " COLLATE '{$this->config['collate']}'";
            }
            $this->config['options'][PDO::MYSQL_ATTR_INIT_COMMAND] = $collation_query;
        }

        $this->config['adapter'] = !empty($this->config['adapter']) ? $this->config['adapter'] : 'mysql';

        $port = empty($this->config['port']) ? '' : ';port=' . $this->config['port'];
        $dsn = $this->config['adapter'] . ':host=' . $this->config['host'] . $port;
        if (!empty($this->config['dbName'])) {
            $dsn .= ';dbname=' . $this->config['dbName'];
        }

        $this->config['options'] = !empty($this->config['options']) ? $this->config['options'] : null;
        $adapter = new \PDO($dsn, $this->config['user'], $this->config['password'], $this->config['options']);

        $adapter->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if (!method_exists($adapter, 'prepare')) {
			$this->_prepare_itself = true;
		}

        return $adapter;
    }

    /**
     * @return array{
     *     adapter: string,
     *     host: string,
     *     port?: int,
     *     tablePrefix?: string,
     *     user: string,
     *     password: string,
     *     dbName?: string,
     *     charset?: string,
     *     collate?: string,
     *     options?: array<int, string>
     * }
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        return $this->adapter;
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
				return isset($this->_tables[$name]) ? $this->_tables[$name] : null;
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
		foreach ($parameters as $key => $val) {
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
		if (preg_match('#SQLSTATE\[(\w+)\].*?: (.*)#', $pdo_message, $matches)) {
			$err_code = $matches[1];
			$err_message = $matches[2];

        } else {
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
		if (count($parameters) > 0) {
			foreach ($parameters as $key => $val) {
				$placeholder = is_int($key) ? '?' : ':' . $key;
				$value = is_int($val) ? $val : $this->quote($val);
				$pos = strpos($query, $placeholder);
				if ($pos !== false) {
					$query = substr_replace($query, $value, $pos, strlen($placeholder));
				}
			}
		}
		return $query;
	}

	/**
	 * Starts query execution timer
	 */
	private function _startTimer()
	{
        // if config is not loaded yet, save stats just in case
		$showStats = !isset(\Cot::$cfg['showsqlstats']) || \Cot::$cfg['showsqlstats'];

		$this->_count++;
		if ($showStats || \Cot::$cfg['debug_mode']) {
			$this->_xtime = microtime();
		}
	}

	/**
	 * Stops query execution timer
	 */
	private function _stopTimer($query)
	{
        // if config is not loaded yet, save stats just in case
        $showStats = !isset(\Cot::$cfg['showsqlstats']) || \Cot::$cfg['showsqlstats'];
        $devMode = !isset(\Cot::$cfg['devmode']) || \Cot::$cfg['devmode'];

		if ($showStats || \Cot::$cfg['debug_mode']) {
			$now = microtime();
			$xtime = explode(' ', $this->_xtime);
			$ytime = explode(' ', $now);

            $startTime = bcadd($xtime[1], $xtime[0], 8);
            $stopTime = bcadd($ytime[1], $ytime[0], 8);
            $executionTime = bcsub($stopTime, $startTime, 8);

			$this->_tcount = bcadd($this->_tcount, $executionTime, 8);
			if ($devMode || \Cot::$cfg['debug_mode']) {
				$calls = '';
				$bt = debug_backtrace();
				for ($i = sizeof($bt) - 1; $i > 0; $i--) {
				    $object = !empty($bt[$i]['object']);
					$call = (($object && $bt[$i]['class']) ? $bt[$i]['class'] . $bt[$i]['type'] : '') . $bt[$i]['function'] . '();';
					$calls .= (empty($calls) ? '' : "\n → ")
                        . (!empty($bt[$i]['file']) ? basename($bt[$i]['file']) : '-')
                        . ' [' . (!empty($bt[$i]['line']) ? $bt[$i]['line'] : '-') . ']: '
                        . $call;
				}

                \Cot::$sys['devmode']['queries'][] = [$this->_count, $executionTime, $query, $calls];
                \Cot::$sys['devmode']['timeline'][] = bcsub($startTime, \Cot::$sys['starttime'], 8);
			}
		}
	}

    /**
     * 1) If called with one parameter:
     * Works like PDO::query()
     * Executes an SQL statement in a single function call, returning the result set (if any) returned by the statement
     *     as a PDOStatement object.
     * 2) If called with second parameter as array of input parameter bindings:
     * Works like PDO::prepare()->execute()
     * Prepares an SQL statement and executes it.
     * @see http://www.php.net/manual/en/pdo.query.php
     * @see http://www.php.net/manual/en/pdo.prepare.php
     * @param string $query The SQL statement to prepare and execute.
     * @param array|string|int|float $parameters An array of values to be binded as input parameters to the query.
     *     PHP int parameters will beconsidered as PDO::PARAM_INT, others as PDO::PARAM_STR.
     * @param int $mode Fetch mode. See https://www.php.net/manual/ru/pdo.constants.php
     *      In Cotonti we use PDO::FETCH_ASSOC by default to save memory
     *
     * @return PDOStatement
     */
    public function query($query, $parameters = [], $mode = PDO::FETCH_ASSOC)
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }
        $this->_startTimer();
        $result = null;
        try {
            if (count($parameters) > 0) {
                if ($this->_prepare_itself) {
                    $result = $this->adapter->query($this->_prepare($query, $parameters));
                } else {
                    $result = $this->adapter->prepare($query);
                    $this->_bindParams($result, $parameters);
                    $result->execute();
                }
            } else {
                $result = $this->adapter->query($query, $mode);
            }
        } catch (\PDOException $err) {
            /**
             * @todo it should be optional. Sometimes we don't need to catch Exception here, but in another place
             * @see plugins/trashcan/inc/trashcan.functions.php:122
             */
            if ($this->_parseError($err, $err_code, $err_message)) {
                cot_diefatal('SQL error ' . $err_code . ': ' . $err_message);
            }
        }
        $this->_stopTimer($query);
        if (!empty($result)) {
            $result->setFetchMode($mode);
            $this->_affected_rows = $result->rowCount();
        }
        return $result;
    }

	/**
	 * Returns total number of records contained in a table
	 * @param string $tableName Table name
	 * @return int
	 */
	public function countRows($tableName)
	{
		return $this->query('SELECT COUNT(*) FROM ' . $this->quoteTableName($tableName))->fetchColumn();
	}

	/**
	 * Performs simple SQL DELETE query and returns number of removed items.
	 *
	 * @param string $tableName Table name
	 * @param string $condition Body of WHERE clause
	 * @param array $parameters Array of statement input parameters, see http://www.php.net/manual/en/pdostatement.execute.php
	 * @return int Number of records removed on success or FALSE on error
	 */
	public function delete($tableName, $condition = '', $parameters = [])
	{
        $query = 'DELETE FROM ' . $this->quoteTableName($tableName);
        if (!empty($condition)) {
            $query .=  ' WHERE ' . $condition;
        }

		if (!is_array($parameters)) {
			$parameters = array($parameters);
		}

        $res = 0;
		$this->_startTimer();
		try {
			if (count($parameters) > 0) {
				if ($this->_prepare_itself) {
					$res = $this->adapter->exec($this->_prepare($query, $parameters));
				} else {
					$stmt = $this->adapter->prepare($query);
					$this->_bindParams($stmt, $parameters);
					$stmt->execute();
					$res = $stmt->rowCount();
				}
			} else {
				$res = $this->adapter->exec($query);
			}

        } catch (PDOException $err) {
			if ($this->_parseError($err, $err_code, $err_message)) {
				cot_diefatal('SQL error ' . $err_code . ': ' . $err_message);
			}
		}
		$this->_stopTimer($query);

		return $res;
	}

    /**
     * Checks if a table exists in schema
     *
     * @param string $tableName Table name
     * @return bool TRUE if the table exists, FALSE otherwise
     */
    public function tableExists($tableName)
    {
        if (mb_strpos($tableName, '.') !== false) {
            list($schema, $tableName) = explode('.', $tableName);
        } else {
            $schema = $this->config['dbName'];
        }
        $query = 'SELECT * FROM ' . $this->quoteTableName('information_schema.tables')
            . ' WHERE ' . $this->quoteColumnName('table_schema') . ' = :schema AND ' . $this->quoteColumnName('table_name')
            . ' = :table LIMIT 1';

        return $this->query($query, ['schema' => $schema, 'table' => $tableName])->rowCount() === 1;
    }

	/**
	 * Checks if a field exists in a table
	 *
	 * @param string $tableName Table name
	 * @param string $fieldName Field name
	 * @return bool TRUE if the field exists, FALSE otherwise
	 */
    public function fieldExists($tableName, $fieldName)
    {
		return $this->query("SHOW COLUMNS FROM " . $this->quoteTableName($tableName) .
                " WHERE Field = " . $this->quote($fieldName))->rowCount() == 1;
	}

	/**
	* Checks if an index with the same index name or column order exists
	*
	* @param string $tableName Table name
	* @param string $index_name Index/Key name
	* @param string[]|string $indexColumns Either a string for a single column name or an array for single/multiple
     *     columns. No column check will be preformed if left empty.
	* @return bool TRUE if the index name or column order exists, FALSE otherwise
	*/
    public function indexExists($tableName, $index_name, $indexColumns = array())
	{
		if (empty($indexColumns)) {
			return (bool) $this->query('SHOW INDEXES FROM ' . $this->quoteTableName($tableName) .
                ' WHERE Key_name=' . $this->quote($index_name))->rowCount();
		}

		$existing_indexes = $this->query('SHOW INDEXES FROM ' . $this->quoteTableName($tableName))->fetchAll();
		if (!is_array($indexColumns)) {
            $indexColumns = array($indexColumns);
		}
		$exists = false;
		$index_list = array();
		foreach ($existing_indexes as $existing_index) {
			$index_list[$existing_index['Key_name']][$existing_index['Seq_in_index'] - 1] = $existing_index['Column_name'];
		}
		foreach ($index_list as $list_index => $list_columns) {
			if ($list_index == $index_name) {
				$exists = true;
				break;
			}
			if (
                count(array_diff_assoc($indexColumns, $list_columns)) === 0 &&
                count($indexColumns) === count($list_columns)
            ) {
				$exists = true;
				break;
			}
		}

		return $exists;
	}

	/**
	* Adds an index on a table
	*
	* @param string $tableName Table name
	* @param string $indexName Index/Key name
	* @param string[]|string $indexColumns Either a string for a single column name or an array for single/multiple
     *     columns. $indexName will be used if empty.
	* @return int Number of rows affected
	*/
    public function addIndex($tableName, $indexName, $indexColumns = array())
	{
		if (empty($indexColumns)) {
            $indexColumns = array($indexName);
		}
		if (!is_array($indexColumns)) {
            $indexColumns = array($indexColumns);
		}

        $quotedColumns = [];
        foreach ($indexColumns as $column) {
            $quotedColumns[] = $this->quoteColumnName($column);
        }

		return $this->query('ALTER TABLE ' . $this->quoteTableName($tableName) .
            ' ADD INDEX ' . $this->quoteColumnName($indexName) . ' (' . implode(',', $quotedColumns) . ')')
            ->rowCount();
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
	 * @param string $tableName Table name
	 * @param array $data Associative or 2D array containing data for insertion.
	 * @param bool $insertNull Insert SQL NULL for empty values rather than ignoring them.
	 * @param bool $ignore Ignore duplicate key errors on insert
	 * @param array $updateFields List of fields to be updated with ON DUPLICATE KEY UPDATE
	 * @return int The number of affected records
	 */
	public function insert($tableName, $data, $insertNull = false, $ignore = false, $updateFields = array())
	{
		if (!is_array($data)) {
			return 0;
		}
		$keys = '';
		$vals = '';

        // Check the array type
		$arr_keys = array_keys($data);
		$multiline = is_numeric($arr_keys[0]);

        // Build the query
		if ($multiline) {
			$rowset = &$data;
		} else {
			$rowset = [$data];
		}
		$keys_built = false;
		$cnt = count($rowset);
		for ($i = 0; $i < $cnt; $i++) {
			$vals .= ($i > 0) ? ',(' : '(';
			$j = 0;
			if (is_array($rowset[$i])) {
				foreach ($rowset[$i] as $key => $val) {
					if (is_null($val) && !$insertNull) {
						continue;
					}
					if ($j > 0) {
                        $vals .= ',';
                    }
					if (!$keys_built) {
						if ($j > 0) {
                            $keys .= ',';
                        }
						$keys .= $this->quoteColumnName($key);
					}
                    if (is_null($val) || $val === 'NULL') {
						$vals .= 'NULL';
					} elseif (is_bool($val)) {
						$vals .= $val ? 'TRUE' : 'FALSE';
					} elseif ($val === 'NOW()') {
						$vals .= 'NOW()';
					} elseif (is_int($val) || is_float($val)) {
						$vals .= $val;
					} else {
						$vals .= $this->quote($val);
					}
					$j++;
				}
			}
			$vals .= ')';
			$keys_built = true;
		}

        if (empty($keys) || empty($vals)) {
            return 0;
        }

        $ignore = $ignore ? 'IGNORE' : '';
        $query = "INSERT $ignore INTO " . $this->quoteTableName($tableName) . " ($keys) VALUES $vals";
        if (count($updateFields) > 0) {
            $query .= ' ON DUPLICATE KEY UPDATE';
            $j = 0;
            foreach ($updateFields as $key) {
                if ($j > 0) $query .= ',';
                $query .= ' ' . $this->quoteColumnName($key) . ' = VALUES(' . $this->quoteColumnName($key) . ')';
                $j++;
            }
        }
        $res = 0;
        $this->_startTimer();
        try {
            $res = $this->adapter->exec($query);
        } catch (\PDOException $err) {
            /**
             * @todo it should be optional. Sometimes we don't need to catch Exception here, but in another place
             * @see plugins/trashcan/inc/trashcan.functions.php:122
             */
            if ($this->_parseError($err, $err_code, $err_message)) {
                cot_diefatal('SQL error ' . $err_code . ': ' . $err_message);
            }
        }
        $this->_stopTimer($query);

        return $res;
	}

    /**
     * @param string $name Optional. PostgreSQL need it
     * @param string $pkey Optional. PostgreSQL need it
     * @return string
     *
     * For example, PDO_PGSQL requires you to specify the name of a sequence object for the name parameter.
     * "{$table_name}_{$pkey}_seq"
     *
     * @see https://www.php.net/manual/en/pdo.lastinsertid.php
     */
    public function lastInsertId($name = '', $pkey = '')
    {
        return $this->adapter->lastInsertId($name);
    }

    /**
     * Performs SQL UPDATE with simple data array. Array keys must match table keys, optionally you can specify
     * key prefix as fourth parameter. Strings get quoted and escaped automatically.
     * Ints and floats must be typecasted.
     * You can use special values in the array:
     * - PHP NULL => SQL NULL
     * - 'NOW()' => SQL NOW()
     *
     * @param string $tableName Table name
     * @param array $data Associative array containing data for update
     * @param string $condition Body of SQL WHERE clause
     * @param array $parameters Array of statement input parameters, see http://www.php.net/manual/en/pdostatement.execute.php
     * @param bool $updateNull Nullify cells which have null values in the array. By default they are skipped
     * @return int The number of affected records or FALSE on error
     */
    public function update($tableName, $data, $condition ='', $parameters = [], $updateNull = false)
    {
        if (!is_array($data)) {
            return 0;
        }
        $upd = '';
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }
        if ($this->_prepare_itself && !empty($condition) && count($parameters) > 0) {
            $condition = $this->_prepare($condition, $parameters);
            $parameters = [];
        }
        $condition = empty($condition) ? '' : 'WHERE ' . $condition;
        foreach ($data as $key => $val) {
            if (is_null($val) && !$updateNull) {
                continue;
            }
            $upd .= $this->quoteColumnName($key) . '=';
            if (is_null($val) || $val === 'NULL') {
                $upd .= 'NULL,';
            } elseif (is_bool($val)) {
                $upd .= $val ? 'TRUE,' : 'FALSE,';
            } elseif ($val === 'NOW()') {
                $upd .= 'NOW(),';
            } elseif (is_int($val) || is_float($val)) {
                $upd .= $val.',';
            } else {
                $upd .= $this->quote($val) . ',';
            }

        }
        if (!empty($upd)) {
            $upd = mb_substr($upd, 0, -1);
            $query = 'UPDATE ' . $this->quoteTableName($tableName) . " SET $upd $condition";
            $res = 0;
            $this->_startTimer();
            try {
                if (count($parameters) > 0) {
                    $stmt = $this->adapter->prepare($query);
                    $this->_bindParams($stmt, $parameters);
                    $stmt->execute();
                    $res = $stmt->rowCount();
                } else {
                    $res = $this->adapter->exec($query);
                }
            } catch (PDOException $err) {
                if ($this->_parseError($err, $err_code, $err_message)) {
                    cot_diefatal('SQL error ' . $err_code . ': ' . $err_message);
                }
            }
            $this->_stopTimer($query);

            return $res;
        }

        return 0;
    }

	/**
	 * Prepares a param for use in SQL query without wrapping it with quotes
	 * @param  string $str Param string
	 * @return string      Escaped param
	 */
	public function prep($str)
	{
        if (empty($str)) {
            return '';
        }

		return preg_replace("#^'(.*)'\$#", '$1', $this->quote((string) $str));
	}

	/**
	 * Registers an unprefixed table name in table names registry
	 * @param  string $table_name Table name without a prefix, e.g. 'pages'
     */
	public function registerTable($table_name)
	{
		if (!isset($GLOBALS['db_' . $table_name])) {
			$GLOBALS['db_' . $table_name] = $this->tablePrefix . $table_name;
		}
		$this->_tables[$table_name] = $GLOBALS['db_' . $table_name];
	}

	/**
	 * Runs an SQL script containing multiple queries.
	 *
     * Scripts should be created as before in MySQL format, with table prefix 'cot_'.
     * The necessary table prefix will be substituted automatically, the necessary quote characters too.
     *
	 * @param string $script SQL script body, containing formatted queries separated by semicolons and newlines
	 * @return string Error message if an error occurs or empty string on success
     * @todo process $this->tableQuoteCharacter
	 */
	public function runScript($script)
	{
        if (empty($script)) {
            return '';
        }

		// Remove comments
        $script = preg_replace('#^/\*.*?\*/#ms', "", $script);
		$script = preg_replace('#^--.*?$#m', '', $script);

		// Run queries separated by ; at the end of line
		$queries =  preg_split('#;\r?\n#', $script);
		foreach ($queries as $query) {
			$query = trim($query);
            if (empty($query)) {
                continue;
            }

            if (
                ($this->tablePrefix != 'cot_' || $this->tableQuoteCharacter != '`') &&
                preg_match_all('#`cot_(\w+)`#', $query, $matches)
            ) {
                foreach ($matches[0] as $key => $match) {
                    $tableName = isset($GLOBALS['db_' . $matches[1][$key]]) ?
                        $GLOBALS['db_' . $matches[1][$key]] : $this->tablePrefix . $matches[1][$key];
                    $query = str_replace($match, $this->quoteTableName($tableName), $query);
                }
            }

            if ($this->columnQuoteCharacter != '`') {
                $query = str_replace('`', $this->columnQuoteCharacter, $query);
            }

            $result = $this->query($query);
            if (!$result) {
                return $this->error . '<br />' . htmlspecialchars($query) . '<hr />';
            } elseif ($result instanceof PDOStatement) {
                $result->closeCursor();
            }
		}

		return '';
	}

    /**
     * Quotes a string value for use in a query.
     *
     * @param string|string[] $data string or strings array for quotting
     * @return string|string[] the properly quoted string or array of strings
     * @see http://php.net/manual/en/pdo.quote.php
     */
    public function quote($data)
    {
        if (is_string($data)) {
            if (($value = $this->adapter->quote($data)) !== false) {
                return $value;
            }

            // the driver doesn't support quote (e.g. oci)
            return "'" . addcslashes(str_replace("'", "''", $data), "\000\n\r\\\032") . "'";
        }

        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $str) {
            // Don't quote integers
            if (((string) ((int) $str)) != $str) {
                $data[$key] = $this->quote($str);
            }
        }

        return $data;
    }

    /**
     * Quotes a table name for use in a query.
     * If the table name contains schema prefix, the prefix will also be properly quoted.
     *
     * If the table name is already quoted or contains special characters including '(',
     * then this method will do nothing.
     *
     * @param string $name table name
     * @return string the properly quoted table name
     */
    public function quoteTableName($name)
    {
        if (empty($name)) {
            return '';
        }

        if (strncmp($name, '(', 1) === 0 && strpos($name, ')') === strlen($name) - 1) {
            return $name;
        }

        if (strpos($name, '.') === false) {
            return $this->quoteSimpleTableName($name);
        }

        $parts = explode('.', $name);
        foreach ($parts as $i => $part) {
            $parts[$i] = $this->quoteSimpleTableName($part);
        }

        return implode('.', $parts);
    }

    /**
     * Alias for self::quoteTableName()
     * Short name for ease of use
     * @param string $name table name
     * @return string the properly quoted table name
     */
    public function quoteT($name)
    {
        return $this->quoteTableName($name);
    }

    /**
     * Quotes a simple table name for use in a query.
     * A simple table name should contain the table name only without any schema prefix.
     * If the table name is already quoted, this method will do nothing.
     *
     * @param string $name table name
     * @return string the properly quoted table name
     */
    protected function quoteSimpleTableName($name)
    {
        if (is_string($this->tableQuoteCharacter)) {
            $startChar = $endChar = $this->tableQuoteCharacter;

        } else {
            list($startChar, $endChar) = $this->tableQuoteCharacter;
        }

        if (strpos($name, $startChar) !== false) return $name;

        return $startChar . $name . $endChar;
    }

    /**
     * Quotes a column name for use in a query.
     * If the column name contains prefix, the prefix will also be properly quoted.
     * If the column name is already quoted or contains special characters including '(', '[['
     * then this method will do nothing.
     *
     * @param string $name column name
     * @return string the properly quoted column name
     */
    public function quoteColumnName($name)
    {
        if (strpos($name, '(') !== false || strpos($name, '[[') !== false) {
            return $name;
        }

        if (($pos = strrpos($name, '.')) !== false) {
            $prefix = $this->quoteTableName(substr($name, 0, $pos)) . '.';
            $name = substr($name, $pos + 1);

        } else {
            $prefix = '';
        }

        return $prefix . $this->quoteSingleColumnName($name);
    }

    /**
     * Alias for self::quoteColumnName()
     * Short name for ease of use
     * @param string $name table name
     * @return string the properly quoted table name
     */
    public function quoteC($name)
    {
        return $this->quoteColumnName($name);
    }

    /**
     * Quotes a simple column name for use in a query.
     * A simple column name should contain the column name only without any prefix.
     * If the column name is already quoted or is the asterisk character '*', this method will do nothing.
     *
     * @param string $name column name
     * @return string the properly quoted column name
     */
    protected function quoteSingleColumnName($name)
    {
        if (is_string($this->tableQuoteCharacter)) {
            $startChar = $endChar = $this->columnQuoteCharacter;

        } else {
            list($startChar, $endChar) = $this->columnQuoteCharacter;
        }

        if ($name === '*' || strpos($name, $startChar) !== false) {
            return $name;
        }

        return $startChar . $name . $endChar;
    }
}
