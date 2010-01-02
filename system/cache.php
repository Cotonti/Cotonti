<?php
/**
 * Cache subsystem library
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2009-2010
 * @license BSD
 */

/**
 * Stores the list of advanced cachers provided by the host
 * @var array
 */
$cache_drivers = array();

/**
 * Abstract class containing code common for all cache drivers
 * @author trustmaster
 */
abstract class Cache_driver
{
	/**
	 * Clears all cache entries served by current driver
	 * @param string $realm Cache realm name, to clear specific realm only
	 * @return bool
	 */
	abstract public function clear($realm = 'cot');

	/**
	 * Checks if an object is stored in cache
	 * @param string $id Object identifier
	 * @param string $realm Cache realm
	 * @return bool
	 */
	abstract public function exists($id, $realm = 'cot');

	/**
	 * Returns value of cached image
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 * @return mixed
	 */
	abstract public function get($id, $realm = 'cot');

	/**
	 * Removes object image from cache
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 * @return bool
	 */
	abstract public function remove($id, $realm = 'cot');

	/**
	 * Stores data as object image in cache
	 * @param string $id Object identifier
	 * @param mixed $data Object value
	 * @param string $realm Realm name
	 * @param int $ttl Time to live, 0 for unlimited
	 * @return bool
	 */
	abstract public function store($id, $data, $realm = 'cot', $ttl = 0);
}

/**
 * Persistent cache driver that writes all entries back on script termination.
 * Persistent cache drivers work slower but guarantee long-term data consistency.
 */
abstract class Writeback_cache_driver extends Cache_driver
{
	/**
	 * Values for delayed writeback to persistent cache
	 * @var array
	 */
	protected $writeback_data = array();
	/**
	 * Keys that are to be removed
	 */
	protected $removed_data = array();

	/**
	 * Writes modified entries back to persistent storage
	 */
	abstract public function  __destruct();

	public function remove($id, $realm = 'cot')
	{
		$this->removed_data[] = array('id' => $id, 'realm' => $realm);
	}

	public function store($id, $data, $realm = 'cot', $ttl = 0)
	{
		$this->writeback_data[] = array('id' => $id, 'data' => $data, 'realm' =>  $realm, 'ttl' => $ttl);
	}
}

/**
 * Temporary cache driver is fast in-memory cache. It usually works faster and provides
 * automatic garbage collection, but it doesn't save data if PHP stops whatsoever.
 */
abstract class Temporary_cache_driver extends Cache_driver
{
	/**
	 * Increments counter value
	 * @param string $id Counter identifier
	 * @param string $realm Realm name
	 * @param int $value Increment value
	 * return int Result value
	 */
	public function inc($id, $realm = 'cot', $value = 1)
	{
		$res = $this->get($id, $realm);
		$res += $value;
		$this->store($id, $res, $realm);
		return $res;
	}

	/**
	 * Decrements counter value
	 * @param string $id Counter identifier
	 * @param string $realm Realm name
	 * @param int $value Increment value
	 * return int Result value
	 */
	public function dec($id, $realm = 'cot', $value = 1)
	{
		$res = $this->get($id, $realm);
		$res -= $value;
		$this->store($id, $res, $realm);
		return $res;
	}

	/**
	 * Gets a size limit from php.ini
	 * @param string $name INI setting name
	 * @return int Number of bytes
	 */
	protected function get_ini_size($name)
	{
		$ini = ini_get($name);
		$suffix = strtoupper(substr($ini, -1));
		$prefix = substr($ini, 0, -1);
		switch ($suffix)
		{
			case 'K':
				return ((int) $prefix) * 1024;
				break;
			case 'M':
				return ((int) $prefix) * 1048576;
				break;
			case 'G':
				return ((int) $prefix) * 1073741824;
				break;
			default:
				return (int) $ini;
		}
	}
}

/**
 * A persistent cache using local file system tree. It does not use multilevel structure
 * or lexicograph search, so it may slow down when your cache grows very big.
 * But normally it is very fast reads.
 * @author trustmaster
 */
class File_cache extends Cache_driver
{
	/**
	 * Cache root directory
	 * @var string
	 */
	private $dir;

	/**
	 * Cache storage object constructor
	 * @param string $dir Cache root directory. System default will be used if empty.
	 * @return File_cache
	 */
	public function __construct($dir = '')
	{
		global $cfg;
		if (empty($dir)) $dir = $cfg['cache_dir'];

		if (file_exists($dir) && is_writeable($dir))
		{
			$this->dir = $dir;
		}
		else
		{
			throw new Exception('Cache directory ' . $dir . ' is not writeable!');
		}
	}

	/**
	 * @see Cache_driver::clear()
	 */
	public function clear($realm = 'cot')
	{
		if (empty($realm))
		{
			$dp = opendir($this->dir);
			while ($f = readdir($dp))
			{
				$dname = $this->dir . '/' . $f;
				if ($f[0] != '.' && is_dir($dname))
				{
					$this->clear($f);
				}
			}
			closedir($dp);
		}
		else
		{
			$dp = opendir($this->dir . '/' . $realm);
			while ($f = readdir($dp))
			{
				$fname = $this->dir . '/' . $realm . '/' . $f;
				if (is_file($fname))
				{
					unlink($fname);
				}
			}
			closedir($dp);
		}
		return TRUE;
	}

	/**
	 * @see Cache_driver::exists()
	 */
	public function exists($id, $realm = 'cot')
	{
		return file_exists($this->dir . '/' . $realm . '/' . $id);
	}

	/**
	 * @see Cache_driver::get()
	 */
	public function get($id, $realm = 'cot')
	{
		if ($this->exists($id, $realm))
		{
			return unserialize(file_get_contents($this->dir . '/' . $realm . '/' . $id));
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * @see Cache_driver::remove()
	 */
	public function remove($id, $realm = 'cot')
	{
		if ($this->exists($id, $realm))
		{
			unlink($this->dir . '/' . $realm . '/' . $id);
			return true;
		}
		else return false;
	}

	/**
	 * @param int $ttl Unsupported by this driver
	 * @see Cache_driver::store()
	 */
	public function store($id, $data, $realm = 'cot', $ttl = 0)
	{
		if (!file_exists($this->dir . '/' . $realm))
		{
			mkdir($this->dir . '/' . $realm);
		}
		file_put_contents($this->dir . '/' . $realm . '/' . $id, serialize($data));
		return true;
	}
}

$cache_drivers[] = 'MySQL_cache';
/**
 * A very popular caching solution using MySQL as a storage. It is quite slow compared to
 * File_cache but may be considered more reliable.
 * @author trustmaster
 */
class MySQL_cache extends Writeback_cache_driver
{
	/**
	 * Prefetched data to avoid duplicate queries
	 * @var array
	 */
	private $buffer = array();

	/**
	 * Performs pre-load actions
	 */
	public function __construct()
	{
		// TODO use probability
		$this->gc();
	}

	/**
	 * Saves all modified data with one query
	 */
	public function  __destruct()
	{
		global $db_cache, $sys;
		if (count($this->removed_data) > 0)
		{
			$q = "DELETE FROM $db_cache WHERE";
			$i = 0;
			foreach ($this->removed_data as $entry)
			{
				$c_name = sed_sql_prep($entry['id']);
				$c_realm = sed_sql_prep($entry['realm']);
				$or = $i == 0 ? '' : ' OR';
				$q .= $or . " (c_name = '$c_name' AND c_realm = '$c_realm')";
			}
			sed_sql_query($q);
		}
		if (count($this->writeback_data) > 0)
		{
			$q = "INSERT INTO $db_cache (c_name, c_realm, c_expire, c_value) VALUES ";
			$i = 0;
			foreach ($this->writeback_data as $entry)
			{
				$c_name = sed_sql_prep($entry['id']);
				$c_realm = sed_sql_prep($entry['realm']);
				$c_expire = $entry['ttl'] > 0 ? $sys['now'] + $entry['ttl'] : 0;
				$c_value = sed_sql_prep(serialize($entry['data']));
				$comma = $i == 0 ? '' : ',';
				$q .= $comma . "('$c_name', '$c_realm', $c_expire, '$c_value')";
			}
			$q .= " ON DUPLICATE KEY UPDATE c_value=VALUES(c_value), c_expire=VALUES(c_expire)";
			sed_sql_query($q);
		}
	}

	/**
	 * @see Cache_driver::clear()
	 */
	public function clear($realm = 'cot')
	{
		global $db_cache;
		if (empty($realm))
		{
			sed_sql_query("TRUNCATE $db_cache");
		}
		else
		{
			sed_sql_query("DELETE FROM $db_cache WHERE c_realm = '$realm'");
		}
		$this->buffer = array();
		return TRUE;
	}

	/**
	 * @see Cache_driver::exists()
	 */
	public function exists($id, $realm = 'cot')
	{
		global $db_cache;
		$sql = sed_sql_query("SELECT c_value FROM $db_cache WHERE c_realm = '$realm' AND c_name = $id");
		$res = sed_sql_numrows($sql) == 1;
		if ($res)
		{
			$this->buffer[$realm][$id] = unserialize(sed_sql_result($sql, 0, 0));
		}
		return $res;
	}

	/**
	 * Garbage collector function. Removes cache entries which are not valid anymore.
	 * @return int Number of entries removed
	 */
	private function gc()
	{
		global $db_cache, $sys;
		sed_sql_query("DELETE FROM $db_cache WHERE c_expire > 0 AND c_expire < " . $sys['now']);
		return sed_sql_affectedrows();
	}

	/**
	 * @see Cache_driver::get()
	 */
	public function get($id, $realm = 'cot')
	{
		global $db_cache;
		if(!$this->exists($id, $realm))
		{
			return $this->buffer[$realm][$id];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Removes item immediately, avoiding writeback.
	 * @param string $id Item identifirer
	 * @param string $realm Cache realm
	 * @return bool
	 * @see Cache_driver::remove()
	 */
	public function remove_now($id, $realm = 'cot')
	{
		global $db_cache;
		sed_sql_query("DELETE FROM $db_cache WHERE c_realm = '$realm' AND c_id = $id");
		unset($this->buffer[$realm][$id]);
		return sed_sql_affectedrows() == 1;
	}

	/**
	 * Writes item to cache immediately, avoiding writeback.
	 * @param string $id Object identifier
	 * @param mixed $data Object value
	 * @param string $realm Realm name
	 * @param int $ttl Time to live, 0 for unlimited
	 * @return bool
	 * @see Cache_driver::store()
	 */
	public function store_now($id, $data, $realm = 'cot', $ttl = 0)
	{
		global $db_cache;
		$c_name = sed_sql_prep($id);
		$c_realm = sed_sql_prep($realm);
		$c_expire = $entry['ttl'] > 0 ? $sys['now'] + $ttl : 0;
		$c_value = sed_sql_prep(serialize($data));
		sed_sql_query("INSERT INTO $db_cache (c_name, c_realm, c_expire, c_value)
			VALUES ('$c_name', '$c_realm', $c_expire, '$c_value')");
		$this->buffer[$realm][$id] = $data;
		return sed_sql_affectedrows() == 1;
	}
}

if (extension_loaded('memcache'))
{
	$cache_drivers[] = 'Memcache_driver';

	/**
	 * Memcache distributed persistent cache driver implementation. Give it a higher priority
	 * if a cluster of webservers is used and Memcached is running via TCP/IP between them.
	 * In other circumstances this only should be used if no APC/eAccelerator/XCache available,
	 * keeping in mind that File_cache might be still faster.
	 * @author trustmaster
	 */
	class Memcache_driver extends Temporary_cache_driver
	{
		/**
		 * PHP Memcache instance
		 * @var Memcache
		 */
		protected $memcache = NULL;
		/**
		 * Compression flag
		 * @var int
		 */
		protected $compressed = true;

		/**
		 * Creates an object and establishes Memcached server connection
		 * @param string $host Memcached host
		 * @param int $port Memcached port
		 * @param bool $persistent Use persistent connection
		 * @param bool $compressed Use compression
		 * @return Mem_cache
		 */
		public function __construct($host = 'localhost', $port = 11211, $persistent = true, $compressed = true)
		{
			$this->memcache = new Memcache;
			$this->memcache->addServer($host, $port, $persistent);
			$this->compressed = $compressed ? MEMCACHE_COMPRESSED : 0;
		}

		/**
		 * @see Cache_driver::clear()
		 */
		public function clear($realm = 'cot')
		{
			if (empty($realm))
			{
				return $this->memcache->flush();
			}
			else
			{
				// TODO implement exact realm cleanup
				return FALSE;
			}
		}

		/**
		 * @see Cache_driver::exists()
		 */
		public function exists($id, $realm = 'cot')
		{
			return $this->memcache->get($realm . '/' . $id, $this->compressed) !== FALSE;
		}

		/**
		 * @see Cache_driver::get()
		 */
		public function get($id, $realm = 'cot')
		{
			return $this->memcache->get($realm . '/' . $id, $this->compressed);
		}

		/**
		 * @see Cache_driver::remove()
		 */
		public function remove($id, $realm = 'cot')
		{
			return $this->memcache->delete($realm . '/' . $id);
		}

		/**
		 * @see Cache_driver::store()
		 */
		public function store($id, $data, $realm = 'cot', $ttl = 0)
		{
			return $this->memcache->set($realm . '/' . $id, $data, $this->compressed, $ttl);
		}

		/**
		 * @see Temporary_cache_driver::inc()
		 */
		public function inc($id, $realm = 'cot', $value = 1)
		{
			if ($this->compressed == MEMCACHE_COMPRESSED)
			{
				return parent::inc($id, $realm, $value);
			}
			else
			{
				return $this->memcache->increment($realm . '/' . $id, $value);
			}
		}

		/**
		 * @see Temporary_cache_driver::dec()
		 */
		public function dec($id, $realm = 'cot', $value = 1)
		{
			if ($this->compressed == MEMCACHE_COMPRESSED)
			{
				return parent::dec($id, $realm, $value);
			}
			else
			{
				return $this->memcache->decrement($realm . '/' . $id, $value);
			}
		}

		/**
		 * Returns number of bytes available
		 * @return int
		 */
		protected function get_available_size()
		{
			$info = $this->memcache->getstats();
			return $info['limit_maxbytes'] - $info['bytes'];
		}

		/**
		 * Returns number of bytes occupied
		 * @return int
		 */
		protected function get_occupied_size()
		{
			$info = $this->memcache->getstats();
			return $info['bytes'];
		}

		/**
		 * Returns maximum variable cache capacity
		 * @return int
		 */
		protected function get_max_size()
		{
			$info = $this->memcache->getstats();
			return $info['limit_maxbytes'];
		}
	}
}

if (extension_loaded('apc'))
{
	$cache_drivers[] = 'APC_driver';

	/**
	 * Accelerated PHP Cache driver implementation. This should be used as default cacher
	 * on APC-enabled hosts.
	 * @author trustmaster
	 */
	class APC_driver extends Temporary_cache_driver
	{
		/**
		 * @see Cache_driver::clear()
		 */
		public function clear($realm = 'cot')
		{
			if (empty($realm))
			{
				return apc_clear_cache();
			}
			else
			{
			// TODO implement exact realm cleanup
				return FALSE;
			}
		}

		/**
		 * @see Cache_driver::exists()
		 */
		public function exists($id, $realm = 'cot')
		{
			return apc_fetch($realm . '/' . $id) !== FALSE;
		}

		/**
		 * @see Cache_driver::get()
		 */
		public function get($id, $realm = 'cot')
		{
			return unserialize(apc_fetch($realm . '/' . $id));
		}

		/**
		 * @see Cache_driver::remove()
		 */
		public function remove($id, $realm = 'cot')
		{
			return apc_delete($realm . '/' . $id);
		}

		/**
		 * @see Cache_driver::store()
		 */
		public function store($id, $data, $realm = 'cot', $ttl = 0)
		{
			return apc_store($realm . '/' . $id, serialize($data), $ttl);
		}

		/**
		 * Returns number of bytes available
		 * @return int
		 */
		protected function get_available_size()
		{
			$info = apc_sma_info();
			return $info['avail_mem'];
		}

		/**
		 * Returns number of bytes occupied
		 * @return int
		 */
		protected function get_occupied_size()
		{
			// unreliable
			return $this->get_max_size() - $this->get_available_size();
		}

		/**
		 * Returns maximum variable cache capacity
		 * @return int
		 */
		protected function get_max_size()
		{
			// possibly only compiler-related
			return ini_get('apc.shm_segments') * ini_get('apc.shm_size');
		}
	}
}

if (extension_loaded('eaccelerator'))
{
	$cache_drivers[] = 'eAccelerator_driver';

	/**
	 * eAccelerator driver implementation. This should be used as default cacher
	 * on hosts providing eAccelerator.
	 * @author trustmaster
	 */
	class eAccelerator_driver extends Temporary_cache_driver
	{
		/**
		 * @see Cache_driver::clear()
		 */
		public function clear($realm = 'cot')
		{
			if (empty($realm))
			{
				eaccelerator_clear();
				return TRUE;
			}
			else
			{
			// TODO implement exact realm cleanup
				return FALSE;
			}
		}

		/**
		 * @see Cache_driver::exists()
		 */
		public function exists($id, $realm = 'cot')
		{
			return !is_null(eaccelerator_get($realm . '/' . $id));
		}

		/**
		 * @see Cache_driver::get()
		 */
		public function get($id, $realm = 'cot')
		{
			return eaccelerator_get($realm . '/' . $id);
		}

		/**
		 * @see Cache_driver::remove()
		 */
		public function remove($id, $realm = 'cot')
		{
			return eaccelerator_rm($realm . '/' . $id);
		}

		/**
		 * @see Cache_driver::store()
		 */
		public function store($id, $data, $realm = 'cot', $ttl = 0)
		{
			return eaccelerator_put($realm . '/' . $id, $data, $ttl);
		}

		/**
		 * Returns number of bytes available
		 * @return int
		 */
		protected function get_available_size()
		{
			$info = eaccelerator_info();
			return $info['memorySize'] - $info['memoryAllocated'];
		}

		/**
		 * Returns number of bytes occupied
		 * @return int
		 */
		protected function get_occupied_size()
		{
			$info = eaccelerator_info();
			return $info['memoryAllocated'];
		}

		/**
		 * Returns maximum variable cache capacity
		 * @return int
		 */
		protected function get_max_size()
		{
			$info = eaccelerator_info();
			return $info['memorySize'];
		}

		protected function get_keys()
		{
			return eaccelerator_list_keys();
		}
	}
}

if (extension_loaded('xcache'))
{
	$cache_drivers[] = 'Xcache_driver';

	/**
	 * XCache variable cache driver. It should be used on hosts that use XCache for
	 * PHP acceleration and variable cache.
	 * @author trustmaster
	 */
	class Xcache_driver extends Temporary_cache_driver
	{
		/**
		 * @see Cache_driver::clear()
		 */
		public function clear($realm = 'cot')
		{
			if (empty($realm))
			{
				return xcache_unset_by_prefix('');
			}
			else
			{
				return xcache_unset_by_prefix($realm . '/');
			}
		}

		/**
		 * @see Cache_driver::exists()
		 */
		public function exists($id, $realm = 'cot')
		{
			return xcache_isset($realm . '/' . $id);
		}

		/**
		 * @see Cache_driver::get()
		 */
		public function get($id, $realm = 'cot')
		{
			return xcache_get($realm . '/' . $id);
		}

		/**
		 * @see Cache_driver::remove()
		 */
		public function remove($id, $realm = 'cot')
		{
			return xcache_unset($realm . '/' . $id);
		}

		/**
		 * @see Cache_driver::store()
		 */
		public function store($id, $data, $realm = 'cot', $ttl = 0)
		{
			return xcache_set($realm . '/' . $id, $data, $ttl);
		}

		/**
		 * @see Temporary_cache_driver::inc()
		 */
		public function inc($id, $realm = 'cot', $value = 1)
		{
			return xcache_inc($realm . '/' . $id, $value);
		}

		/**
		 * @see Temporary_cache_driver::dec()
		 */
		public function dec($id, $realm = 'cot', $value = 1)
		{
			return xcache_dec($realm . '/' . $id, $value);
		}

		/**
		 * Returns maximum variable cache capacity
		 * @return int
		 */
		protected function get_max_size()
		{
			return $this->get_ini_size('xcache.var_size');
		}
	}
}

/**
 * Multi-layer universal cache controller for Cotonti
 */
class Cache
{
	/**
	 * Persistent cache underlayer driver
	 * @var Cache_driver
	 */
	private $disk;
	/**
	 * Intermediate shared (memory) driver
	 * @var Cache_driver
	 */
	private $shared;
	/**
	 * Event bindings
	 * @var array
	 */
	private $bindings;

	/**
	 * Initializes controller components
	 */
	public function  __construct()
	{
		global $cfg, $cache_drivers;
		$selected = count($cache_drivers) == 2 ? $cache_drivers[1] : $cfg['cache']['shared_drv'];
		$this->shared = in_array($selected, $cache_drivers) ?
				new $selected() : new MySQL_cache();
		$this->disk = new File_cache($cfg['cache_dir']);
		if ($this->disk->exists('cache_bindings', 'system'))
		{
			$this->bindings = $this->disk->get('cache_bindings', 'system');
		}
		else
		{
			$this->resync_bindings();
		}
	}

	/**
	 * Performs actions before script termination
	 */
	public function  __destruct()
	{
		
	}

	/**
	 * Rereads bindings from database
	 */
	private function resync_bindings()
	{
		global $db_cache_bindings;
		$this->bindings = array();
		$sql = sed_sql_query("SELECT * FROM `$db_cache_bindings`");
		while ($row = sed_sql_fetchassoc($sql))
		{
			$this->bindings[$row['c_event']][] = array('id' => $row['c_id'], 'realm' => $row['c_realm']);
		}
		sed_sql_freeresult($sql);
		$this->disk->store('cache_bindings', $this->bindings, 'system');
	}

	/**
	 * Binds an event to automatic cache field invalidation
	 * @param string $event Event name
	 * @param string $id Cache entry id
	 * @param string $realm Cache realm name
	 * @return bool TRUE on success, FALSE on error
	 */
	public function bind($event, $id, $realm = 'cot')
	{
		global $db_cache_bindings;
		$c_event = sed_sql_prep($event);
		$c_id = sed_sql_prep($id);
		$c_realm = sed_sql_prep($realm);
		sed_sql_query("INSERT INTO `$db_cache_bindings` (c_event, c_id, c_realm)
			VALUES ('$c_event', '$c_id', '$c_realm')");
		$res = sed_sql_affectedrows() == 1;
		if ($res)
		{
			$this->resync_bindings();
		}
		return $res;
	}

	/**
	 * Binds multiple cache fields to events, all represented as an associative array
	 *
	 * @param array $bindings An indexed array of bindings.
	 * Each binding is an associative array with keys: event, realm, id.
	 * @return int Number of bindings added
	 */
	public function bind_array($bindings)
	{
		global $db_cache_bindings;
		$q = "INSERT INTO `$db_cache_bindings` (c_event, c_id, c_realm) VALUES ";
		$i = 0;
		foreach ($bindings as $entry)
		{
			$c_event = sed_sql_prep($entry['event']);
			$c_id = sed_sql_prep($entry['id']);
			$c_realm = sed_sql_prep($entry['realm']);
			$comma = $i == 0 ? '' : ',';
			$q .= $comma . "('$c_event', '$c_id', '$c_realm')";
		}
		sed_sql_query($q);
		$res = sed_sql_affectedrows();
		if ($res > 0)
		{
			$this->resync_bindings();
		}
		return $res;
	}

	/**
	 * Checks if an object is stored in cache
	 * @param string $id Object identifier
	 * @param string $realm Cache realm
	 * @return bool
	 */
	public function exists($id, $realm = 'cot')
	{
		return $this->shared->exists($id, $realm);
	}

	/**
	 * Checks if an object is stored in disk cache
	 * @param string $id Object identifier
	 * @param string $realm Cache realm
	 * @return bool
	 */
	public function exists_disk($id, $realm = 'cot')
	{
		return $this->disk->exists($id, $realm);
	}

	/**
	 * Gets the object from cache. Attempts searching it top-down, from current process
	 * memory downto persistent cache layer. Applies LRU policy to fit cache size limits.
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 * @return mixed Cached item value or NULL if the item was not found in cache
	 * @see Cache::set(), Cache::get_disk(), Cache::get_shared()
	 */
	public function get($id, $realm = 'cot')
	{
		return $this->shared->get($id, $realm);
	}

	/**
	 * Gets an object directly from disk, avoiding the shared memory.
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 * @return mixed Cached item value or NULL if the item was not found in cache
	 */
	public function get_disk($id, $realm = 'cot')
	{
		return $this->disk->get($id, $realm);
	}

	/**
	 * Stores data as object image in cache. Writes through both shared memory and disk cache.
	 * @param string $id Object identifier
	 * @param mixed $data Object value
	 * @param string $realm Realm name
	 * @param int $ttl Time to live, 0 for unlimited
	 * @return bool
	 */
	public function set($id, $data, $realm = 'cot', $ttl = 0)
	{
		return $this->shared->store($id, $data, $realm, $ttl);
	}

	/**
	 * Stores disk-only cache entry. Use it for large objects, which you don't want to put
	 * into memory cache.
	 * @param string $id Object identifier
	 * @param mixed $data Object value
	 * @param string $realm Realm name
	 * @return bool
	 */
	public function set_disk($id, $data, $realm = 'cot')
	{
		return $this->disk->store($id, $data, $realm);
	}

	/**
	 * Invalidates cache cells which were binded to the event.
	 * @param string $event Event name
	 * @return int Number of cells cleaned
	 */
	public function trigger($event)
	{
		$cnt = 0;
		foreach ($this->bindings[$event] as $cell)
		{
			$this->remove($cell['id'], $cell['realm']);
			$cnt++;
		}
		return $cnt;
	}

	/**
	 * Removes cache image of the object.
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 */
	public function remove($id, $realm = 'cot')
	{
		$this->shared->remove($id, $realm);
	}

	/**
	 * Removes disk cache image of the object.
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 */
	public function remove_disk($id, $realm = 'cot')
	{
		$this->disk->remove($id, $realm);
	}

	/**
	 * Removes event/cache bindings
	 * @param string $realm Realm name (required)
	 * @param string $id Object identifier. Optional, if not specified, all bindings from the realm are removed.
	 * @return int Number of bindings removed
	 */
	public function unbind($realm, $id = '')
	{
		global $db_cache_bindings;
		$c_realm = sed_sql_prep($realm);
		$q = "DELETE FROM `$db_cache_bindings` WHERE c_realm = '$c_realm'";
		if (!empty($id))
		{
			$c_id = sed_sql_prep($id);
			$q .= " AND c_id = '$c_id'";
		}
		sed_sql_query($q);
		$res = sed_sql_affectedrows();
		if ($res > 0)
		{
			$this->resync_bindings();
		}
		return $res;
	}
}
?>