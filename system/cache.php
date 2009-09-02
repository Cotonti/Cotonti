<?php
/**
 * Cache subsystem library
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2009
 * @license BSD
 */

/**
 * Stores the list of advanced cachers provided by the host
 * @var array
 */
$cache_drivers = array();

/**
 * An interface provided by all cache drivers
 * @author trustmaster
 */
interface Cache_interface
{
	/**
	 * Clears all cache entries served by current driver
	 * @param $realm string Cache realm name, to clear specific realm only
	 * @return void
	 */
	public function clear($realm = 'cot');

	/**
	 * Checks if an object is stored in cache
	 * @param $id string Object identifier
	 * @param $realm string Cache realm
	 * @return bool
	 */
	public function exists($id, $realm = 'cot');

	/**
	 * Returns value of cached image
	 * @param $id string Object identifier
	 * @param $realm string Realm name
	 * @return mixed
	 */
	public function get($id, $realm = 'cot');

	/**
	 * Removes object image from cache
	 * @param $id string Object identifier
	 * @param $realm string Realm name
	 * @return bool
	 */
	public function remove($id, $realm = 'cot');

	/**
	 * Stores data as object image in cache
	 * @param $id string Object identifier
	 * @param $data mixed Object value
	 * @param $realm string Realm name
	 * @return bool
	 */
	public function store($id, $data, $realm = 'cot');

	/**
	 * Standard event trigger function. It is called when some data has been changed
	 * and the cache image needs to be refreshed. It calls an event handler (if exists),
	 * a callback function, and updates the cache with its return value.
	 * @param $event string Event name, used as a part of callback name
	 * @param $id int Object identifier
	 * @param $realm string Cache realm
	 * @return void
	 */
	public function trigger($event, $id = 0, $realm = 'cot');
}

/**
 * Abstract class containing code common for all cache drivers
 * @author trustmaster
 */
abstract class Cache_driver
{
	public function trigger($event, $id = 0, $realm = 'cot')
	{
		$func = $realm . '_' . $event;
		if (function_exists($func))
		{
			$data = $func($id);
			if (is_null($data))
			{
				$this->remove($id, $realm);
			}
			else
			{
				$this->store($id, $data, $realm);
			}
		}
	}
}

/**
 * A non-prersistent cache implementation (example) using PHP arrays.
 * Normally you don't need to use it, because direct array access is faster.
 * @author trustmaster
 */
class Array_cache extends Cache_driver implements Cache_interface
{
	/**
	 * An associative array containing all the cache data
	 * @var array
	 */
	private $data = array();

	public function clear($realm = 'cot')
	{
		if (empty($realm))
		{
			$realms = array_keys($this->data);
			foreach ($realms as $realm)
			{
				$this->data[$realm] = array();
			}
		}
		else
		{
			$this->data[$realm] = array();
		}
	}

	public function exists($id, $realm = 'cot')
	{
		return isset($this->data[$realm][$id]);
	}

	public function get($id, $realm = 'cot')
	{
		return $this->data[$realm][$id];
	}

	public function remove($id, $realm = 'cot')
	{
		unset($this->data[$realm][$id]);
		return true;
	}

	public function store($id, $data, $realm = 'cot')
	{
		$this->data[$realm][$id] = $data;
		return true;
	}
}

/**
 * A persistent cache using local file system tree. It does not use multilevel structure
 * or lexicograph search, so it may slow down when your cache grows very big.
 * But normally it is very fast reads.
 * @author trustmaster
 */
class File_cache extends Cache_driver implements Cache_interface
{
	/**
	 * Cache root directory
	 * @var string
	 */
	private $dir;

	/**
	 * Cache storage object constructor
	 * @param $dir Cache root directory. System default will be used if empty.
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
	}

	public function exists($id, $realm = 'cot')
	{
		return file_exists($this->dir . '/' . $realm . '/' . $id);
	}

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

	public function remove($id, $realm = 'cot')
	{
		if ($this->exists($id, $realm))
		{
			unlink($this->dir . '/' . $realm . '/' . $id);
			return true;
		}
		else return false;
	}

	public function store($id, $data, $realm = 'cot')
	{
		if (!file_exists($this->dir . '/' . $realm))
		{
			mkdir($this->dir . '/' . $realm);
		}
		file_put_contents($this->dir . '/' . $realm . '/' . $id, serialize($data));
		return true;
	}
}

/**
 * A very popular caching solution using MySQL as a storage. It is quite slow compared to
 * File_cache but may be considered more reliable.
 * @author trustmaster
 */
class Query_cache extends Cache_driver implements Cache_interface
{
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
	}

	public function exists($id, $realm = 'cot')
	{
		global $db_cache;
		$res = sed_sql_query("SELECT COUNT(*) FROM $db_cache WHERE c_realm = '$realm' AND c_id = $id");
		return (bool) sed_sql_result($res, 0, 0) == 1;
	}

	/**
	 * Garbage collector function. Removes cache entries which are not valid anymore.
	 * @return int Number of entries removed
	 */
	public function gc()
	{
		global $db_cache;
		sed_sql_query("DELETE FROM $db_cache WHERE c_expire > 0 AND c_expire < " . time());
		return sed_sql_affectedrows();
	}

	public function get($id, $realm = 'cot')
	{
		global $db_cache;
		$res = sed_sql_query("SELECT c_value FROM $db_cache WHERE c_realm = '$realm' AND c_id = $id");
		return unserialize(sed_sql_result($res, 0, 0));
	}

	public function remove($id, $realm = 'cot')
	{
		global $db_cache;
		sed_sql_query("DELETE FROM $db_cache WHERE c_realm = '$realm' AND c_id = $id");
		return sed_sql_affectedrows() == 1;
	}

	public function store($id, $data, $realm = 'cot', $ttl = 0)
	{
		global $db_cache;
		$data = sed_sql_prep(serialize($data));
		$expire = $ttl > 0 ? time() + $ttl : 0;
		sed_sql_query("INSERT INTO $db_cache (c_realm, c_id, c_expire, c_value)
			VALUES ('$realm', $id, $ttl, '$data')");
		return sed_sql_affectedrows() == 1;
	}
}

if (extension_loaded('memcache'))
{
	$cache_drivers[] = 'Mem_cache';

	/**
	 * Memcache distributed persistent cache driver implementation. Give it a higher priority
	 * if a cluster of webservers is used and Memcached is running via TCP/IP between them.
	 * In other circumstances this only should be used if no APC/eAccelerator/XCache available,
	 * keeping in mind that File_cache might be still faster.
	 * @author trustmaster
	 */
	class Mem_cache extends Cache_driver implements Cache_interface
	{
		/**
		 * PHP Memcache instance
		 * @var Memcache
		 */
		protected $memcache = NULL;

		/**
		 * Creates an object and establishes Memcached server connection
		 * @param $host string Memcached host
		 * @param $port int Memcached port
		 * @param $persistent bool Use persistent connection
		 * @return Mem_cache
		 */
		public function __construct($host = 'localhost', $port = 11211, $persistent = true)
		{
			$this->memcache = new Memcache;
			$this->memcache->addServer($host, $port, $persistent);
		}

		public function clear($realm = 'cot')
		{
			if (empty($realm))
			{
				return $this->memcache->flush();
			}
			else
			{
			// TODO implement exact realm cleanup
			}
		}

		public function exists($id, $realm = 'cot')
		{
			return $this->memcache->get($realm . '/' . $id, MEMCACHE_COMPRESSED) !== FALSE;
		}

		public function get($id, $realm = 'cot')
		{
			return $this->memcache->get($realm . '/' . $id, MEMCACHE_COMPRESSED);
		}

		public function remove($id, $realm = 'cot')
		{
			return $this->memcache->delete($realm . '/' . $id);
		}

		public function store($id, $data, $realm = 'cot', $ttl = 0)
		{
			return $this->memcache->add($realm . '/' . $id, $data, MEMCACHE_COMPRESSED, $ttl);
		}
	}
}

if (extension_loaded('apc'))
{
	$cache_drivers[] = 'APC_cache';

	/**
	 * Accelerated PHP Cache driver implementation. This should be used as default cacher
	 * on APC-enabled hosts.
	 * @author trustmaster
	 */
	class APC_cache extends Cache_driver implements Cache_interface
	{
		public function clear($realm = 'cot')
		{
			if (empty($realm))
			{
				return apc_clear_cache();
			}
			else
			{
			// TODO implement exact realm cleanup
			}
		}

		public function exists($id, $realm = 'cot')
		{
			return apc_fetch($realm . '/' . $id) !== FALSE;
		}

		public function get($id, $realm = 'cot')
		{
			return apc_fetch($realm . '/' . $id);
		}

		public function remove($id, $realm = 'cot')
		{
			return apc_delete($realm . '/' . $id);
		}

		public function store($id, $data, $realm = 'cot', $ttl = 0)
		{
			return apc_store($realm . '/' . $id, $data, $ttl);
		}
	}
}

if (extension_loaded('eaccelerator'))
{
	$cache_drivers[] = 'eAccelerator_cache';

	/**
	 * eAccelerator driver implementation. This should be used as default cacher
	 * on hosts providing eAccelerator.
	 * @author trustmaster
	 */
	class eAccelerator_cache extends Cache_driver implements Cache_interface
	{
	// TODO page cache support

		public function clear($realm = 'cot')
		{
			if (empty($realm))
			{
				eaccelerator_clear();
			}
			else
			{
			// TODO implement exact realm cleanup
			}
		}

		public function exists($id, $realm = 'cot')
		{
			return !is_null(eaccelerator_get($realm . '/' . $id));
		}

		public function get($id, $realm = 'cot')
		{
			return eaccelerator_get($realm . '/' . $id);
		}

		public function remove($id, $realm = 'cot')
		{
			return eaccelerator_rm($realm . '/' . $id);
		}

		public function store($id, $data, $realm = 'cot', $ttl = 0)
		{
			return eaccelerator_put($realm . '/' . $id, $data, $ttl);
		}
	}
}

if (extension_loaded('xcache'))
{
	$cache_drivers[] = 'X_cache';

	/**
	 * XCache variable cache driver. It should be used on hosts that use XCache for
	 * PHP acceleration and variable cache.
	 * @author trustmaster
	 */
	class X_cache extends Cache_driver implements Cache_interface
	{
		public function clear($realm = 'cot')
		{
			if (empty($realm))
			{
				xcache_clear_cache();
			}
			else
			{
			// TODO implement exact realm cleanup
			}
		}

		public function exists($id, $realm = 'cot')
		{
			return xcache_isset($realm . '/' . $id);
		}

		public function get($id, $realm = 'cot')
		{
			return xcache_get($realm . '/' . $id);
		}

		public function remove($id, $realm = 'cot')
		{
			return xcache_unset($realm . '/' . $id);
		}

		public function store($id, $data, $realm = 'cot', $ttl = 0)
		{
			return xcache_set($realm . '/' . $id, $data, $ttl);
		}
	}
}

?>