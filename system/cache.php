<?php
/**
 * Cache subsystem library
 *
 * @package API - Cache
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Stores the list of advanced cachers provided by the host
 * @var array
 */
$cot_cache_drivers = [];

/**
 * Default cache realm
 */
const COT_DEFAULT_REALM = 'cot';

/**
 * Default time to live for temporary cache objects
 */
const COT_DEFAULT_TTL = 3600;
/**
 * Default cache type, uneffective
 */
const COT_CACHE_TYPE_ALL = 0;
/**
 * Disk cache type
 */
const COT_CACHE_TYPE_DISK = 1;
/**
 * Database cache type
 */
const COT_CACHE_TYPE_DB = 2;
/**
 * Shared memory cache type
 */
const COT_CACHE_TYPE_MEMORY = 3;
/**
 * Static cache type
 */
const COT_CACHE_TYPE_STATIC = 4;

/**
 * Default cache type
 */
const COT_CACHE_TYPE_DEFAULT = COT_CACHE_TYPE_DB;

/**
 * Abstract class containing code common for all cache drivers
 */
abstract class Cache_driver
{
	/**
	 * Clears all cache entries served by current driver
	 * @param string $realm Cache realm name, to clear specific realm only
	 * @return bool
	 */
	abstract public function clear($realm = COT_DEFAULT_REALM);

	/**
	 * Checks if an object is stored in cache
	 * @param string $id Object identifier
	 * @param string $realm Cache realm
	 * @return bool
	 */
	abstract public function exists($id, $realm = COT_DEFAULT_REALM);

	/**
	 * Returns value of cached image
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 * @return mixed Cached item value or NULL if the item was not found in cache
	 */
	abstract public function get($id, $realm = COT_DEFAULT_REALM);

	/**
	 * Removes object image from cache
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 * @return bool
	 */
	abstract public function remove($id, $realm = COT_DEFAULT_REALM);
}

/**
 * Static cache is used to store large amounts of rarely modified data
 */
abstract class Static_cache_driver extends Cache_driver
{
	/**
	 * Stores data as object image in cache
	 * @param string $id Object identifier
	 * @param mixed $data Object value
	 * @param string $realm Realm name
	 * @return bool
	 */
	abstract public function store($id, $data, $realm = COT_DEFAULT_REALM);
}

/**
 * Dynamic cache is used to store data that is not too large
 * and is modified more or less frequently
 */
abstract class Dynamic_cache_driver extends Cache_driver
{
	/**
	 * Stores data as object image in cache
	 * @param string $id Object identifier
	 * @param mixed $data Object value
	 * @param string $realm Realm name
	 * @param int $ttl Time to live, 0 for unlimited
	 * @return bool
	 */
	abstract public function store($id, $data, $realm = COT_DEFAULT_REALM, $ttl = COT_DEFAULT_TTL);
}

/**
 * Persistent cache driver that writes all entries back on script termination.
 * Persistent cache drivers work slower but guarantee long-term data consistency.
 */
abstract class Writeback_cache_driver extends Dynamic_cache_driver
{
	/**
	 * Values for delayed writeback to persistent cache
	 * @var array
	 */
	protected $writeback_data = [];
	/**
	 * Keys that are to be removed
	 */
	protected $removed_data = [];

	/**
	 * Writes modified entries back to persistent storage
	 */
	abstract public function flush();

	/**
	 * Removes cache image of the object from the database
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 */
	public function remove($id, $realm = COT_DEFAULT_REALM)
	{
		$this->removed_data[] = ['id' => $id, 'realm' => $realm];
	}

	/**
	 * Removes item immediately, avoiding writeback.
	 * @param string $id Item identifier
	 * @param string $realm Cache realm
	 * @return bool
	 * @see Cache_driver::remove()
	 */
	abstract public function remove_now($id, $realm = COT_DEFAULT_REALM);

	/**
	 * Stores data as object image in cache
	 * @param string $id Object identifier
	 * @param mixed $data Object value
	 * @param string $realm Realm name
	 * @param int $ttl Time to live, 0 for unlimited
	 * @return bool
	 * @see Cache_driver::store()
	 */
	public function store($id, $data, $realm = COT_DEFAULT_REALM, $ttl = 0)
	{
		$this->writeback_data[] = ['id' => $id, 'data' => $data, 'realm' =>  $realm, 'ttl' => $ttl];
		return true;
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
	abstract public function store_now($id, $data, $realm = COT_DEFAULT_REALM, $ttl = COT_DEFAULT_TTL);
}

/**
 * Query cache drivers are driven by database
 */
abstract class Db_cache_driver extends Writeback_cache_driver
{
	/**
	 * Loads all variables from a specified realm(s) into the global scope
	 * @param mixed $realm Realm name or array of realm names
	 * @return int Number of items loaded
	 */
	abstract public function get_all($realm = COT_DEFAULT_REALM);
}

/**
 * Temporary cache driver is fast in-memory cache. It usually works faster and provides
 * automatic garbage collection, but it doesn't save data if PHP stops whatsoever.
 * Use it for individual frequently modified variables.
 */
abstract class Temporary_cache_driver extends Dynamic_cache_driver
{
	/**
	 * Increments counter value
	 * @param string $id Counter identifier
	 * @param string $realm Realm name
	 * @param int $value Increment value
	 * return int Result value
	 */
	public function inc($id, $realm = COT_DEFAULT_REALM, $value = 1)
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
	public function dec($id, $realm = COT_DEFAULT_REALM, $value = 1)
	{
		$res = $this->get($id, $realm);
		$res -= $value;
		$this->store($id, $res, $realm);
		return $res;
	}

	/**
	 * Returns information about memory usage if available.
	 * Possible keys: available, occupied, max.
	 * If the driver cannot provide a value, it sets it to -1.
	 * @return array Associative array containing information
	 */
	abstract public function get_info();

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
 */
class File_cache extends Static_cache_driver
{
	/**
	 * Cache root directory
	 * @var string
	 */
	private $dir;

    /**
     * Cache storage object constructor
     * @param string $dir Cache root directory. System default will be used if empty.
     * @throws Exception
     * @return File_cache
     */
	public function __construct($dir = '')
	{
		global $cfg;
		if (empty($dir)) $dir = $cfg['cache_dir'];

        if (!empty($dir) && !file_exists($dir)) mkdir($dir, 0755, true);

		if (file_exists($dir) && is_writeable($dir))
		{
			$this->dir = $dir;
		}
		else
		{
			throw new Exception('Cache directory '.$dir.' is not writeable!'); // TODO: Need translate
		}
	}

	/**
	 * @see Cache_driver::clear()
	 */
	public function clear($realm = '', $exceptRealms = ['assets', 'static', 'htmlpurifier', 'templates'])
	{
        if (!empty($realm)) {
            $directory = $this->dir . '/' . $realm;
            if (is_dir($this->dir . '/' . $realm)) {
                return $this->doClear($directory, true, true);
            }

            return true;
        }

        if (is_dir($this->dir)) {
            $this->doClear(Cot::$cfg['cache_dir'], false);

            $directory = opendir($this->dir);
            while ($f = readdir($directory)) {
                $dname = $this->dir . '/' . $f;
                if (
                    is_dir($dname)
                    && $f[0] !== '.'
                    && (empty($exceptRealms) || !in_array($f, $exceptRealms))
                ) {
                    $this->clear($f);
                }
            }
            closedir($directory);

        }
        return true;
	}

    /**
     * Clears disk cache directory
     * @param string $directory Directory name
     * @param bool $clearSubDirectories true when enter subdirectories, otherwise false
     * @param bool $deleteDirectory true when remove directory, otherwise false
     * @return bool
     */
    private function doClear($directory, $clearSubDirectories = true, $deleteDirectory = false)
    {
        if (!is_dir($directory) || !is_writable($directory)) {
            return false;
        }

        $glob = glob("$directory/*");
        if (is_array($glob)) {
            foreach ($glob as $f) {
                if (
                    is_file($f)
                    && !in_array($f, [$this->dir . '/index.html', $this->dir . '/.htaccess'])
                ) {
                    @unlink($f);
                } elseif (is_dir($f) && $clearSubDirectories) {
                    $this->doClear($f, true, true);
                }
            }
        }

        if ($this->dir !== $directory && $deleteDirectory) {
            @rmdir($directory);
        }

        return true;
    }

	/**
	 * Checks if an object is stored in disk cache
	 * @param string $id Object identifier
	 * @param string $realm Cache realm
	 * @param int $ttl Lifetime in seconds, 0 means unlimited
	 * @return bool
	 */
	public function exists($id, $realm = COT_DEFAULT_REALM, $ttl = 0)
	{
		$filename = $this->dir.'/'.$realm.'/'.$id;
		return file_exists($filename) && ($ttl == 0 || time() - filemtime($filename) < $ttl);
	}

	/**
	 * Gets an object directly from disk
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 * @param int $ttl Lifetime in seconds, 0 means unlimited
	 * @return mixed Cached item value or NULL if the item was not found in cache
	 */
	public function get($id, $realm = COT_DEFAULT_REALM, $ttl = 0)
	{
		if ($this->exists($id, $realm, $ttl))
		{
			return unserialize(file_get_contents($this->dir.'/'.$realm.'/'.$id));
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Removes cache image of the object from disk
	 * @param string $id Object identifier
	 * @param string $realm Realm name
	 */
	public function remove($id, $realm = COT_DEFAULT_REALM)
	{
		if ($this->exists($id, $realm))
		{
			unlink($this->dir.'/'.$realm.'/'.$id);
			return true;
		}
		else return false;
	}

	/**
	 * Stores disk cache entry
	 * @param string $id Object identifier
	 * @param mixed $data Object value
	 * @param string $realm Realm name
	 * @return bool
	 */
	public function store($id, $data, $realm = COT_DEFAULT_REALM)
	{
		if (!file_exists($this->dir.'/'.$realm))
		{
			mkdir($this->dir.'/'.$realm);
		}
		file_put_contents($this->dir.'/'.$realm.'/'.$id, serialize($data));
		return true;
	}
}

/**
 * A cache that stores entire page outputs. Disk-based.
 */
class Page_cache
{
	/**
     * Cache root
	 * @var string
	 */
	private $dir;

	/**
     * Relative page (item) path
	 * @var string
	 */
	private $path;

	/**
	 * Short file name
     * @var string
     */
	private $name;

	/**
	 * Parameters to exclude
     * @var string[]
     */
	private $excl;

	/**
	 * Filename extension
     * @var string
	 */
	private $ext;

	/**
	 * Full path to page cache image
     * @var string
     * @todo relative to $this->dir
	 */
	private $filename = null;

	/**
	 * Directory permissions
     * @var string
	 */
	private $perms;

    /**
     * @var bool
     */
    private $enabled = false;

	/**
	 * Constructs controller object and sets basic configuration
	 * @param string $dir Cache directory
	 * @param int $perms Octal permission mask for cache directories
	 */
	public function __construct($dir, $perms = 0777)
	{
		$this->dir = rtrim($dir, '\\/');
		$this->perms = $perms;
	}

    /**
     * Initializes actual page cache
     * @param string $path Page path string
     * @param string $name Short name for the cache file
     * @param array $exclude A list of GET params to be excluded from consideration
     * @param string $ext File extension
     */
    public function init($path, $name, $exclude = [], $ext = '')
    {
        $this->path = $path;
        $this->name = $name;
        $this->excl = $exclude;
        $this->ext = $ext;
        $this->enabled = true;

        $filename = $this->path . '/' . $this->name;
        $args = [];
        foreach ($_GET as $key => $val) {
            if (!in_array($key, $this->excl)) {
                $args[$key] = $val;
            }
        }
        ksort($args);
        if (count($args) > 0) {
            $hashkey = serialize($args);
            $filename .= '_' . md5($hashkey) . sha1($hashkey);
        }
        if (!empty($this->ext)) {
            $filename .= '.' . $this->ext;
        }

        $this->filename = $filename;
    }

    /**
     * Initializes actual page cache by given page uri
     * @param string $uri Relative page uri (relative to $sys['abs_url'])
     * @param string $name Short name for the cache file
     * @param array $exclude A list of GET params to be excluded from consideration
     * @param string $ext File extension
     */
    public function initByUri($uri, $name, $exclude = [], $ext = '')
    {
        $path = $this->getPathByUri($uri);
        if (empty($path)) {
            return;
        }
        $this->init($path, $name, $exclude, $ext);
    }

    /**
     * @param string $uri Relative page uri (relative to $sys['abs_url'])
     * @return string
     */
    protected function getPathByUri($uri)
    {
        $parsedUrl = cot_parse_url($uri);
        $get = [];
        if (!empty($parsedUrl['query'])) {
            $parsedUrl['query'] = str_replace('&amp;', '&', $parsedUrl['query']);
            parse_str($parsedUrl['query'], $get);
        }

        if (function_exists('cot_staticCacheGetPathByUri')) {
            $path = cot_staticCacheGetPathByUri($parsedUrl);
            return !empty($path) ? $path : 'index';
        }

        if (!empty($get['e'])) {
            $path = preg_replace('#\W#', '', $get['e']);
        } elseif ($parsedUrl['path'] !== '/') {
            $parsedUrl['path'] = rawurldecode($parsedUrl['path']);
            $path = trim($parsedUrl['path'], '/');
            // Trim last uri part. It can be page alias or id
//            $lastPosition = mb_strrpos($path, '/');
//            if ($lastPosition > 0) {
//                $path = mb_substr($path, 0, $lastPosition);
//            }
        }

        if (!empty($path)) {
            $c = isset($get['c']) ? trim($get['c']) : null;
            if (!empty($c)) {
                $path .= '/' . $c;
            }
        } else {
            $path = 'index';
        }

        return $path;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return ?string
     */
    public function getFileName()
    {
        return $this->filename;
    }

    public function disable()
    {
        $this->enabled = false;
    }

	/**
	 * Removes an item and/or all contained items and cache files
	 * @param string $path Item path
     * @param bool $withSubDirectories Remove all contained items (subdirectories)
	 * @return int Number of files removed
	 */
	public function clear($path, $withSubDirectories = false)
	{
        $directory = $this->dir;
        if (!empty($path)) {
            $directory .= '/' . rtrim($path, '\\/');
        }
		return $this->removeDir($directory, $withSubDirectories);
	}

    /**
     * Removes an item and all contained items and cache files by given page uri
     * @param string $uri Relative page uri (relative to $sys['abs_url'])
     * @param bool $withSubDirectories Remove all contained items (subdirectories)
     * @return int Number of files removed
     */
    public function clearByUri($uri, $withSubDirectories = false)
    {
        $path = $this->getPathByUri($uri);
        if (empty($path)) {
            return 0;
        }

        return $this->clear($path, $withSubDirectories);
    }

	/**
	 * Reads the page cache object from disk and sends it to output.
	 * If the cache object does not exist, then just calculates the path
	 * for a following write() call.
	 */
	public function read()
	{
        $fileFullName = $this->dir. '/' . $this->filename;
		if (file_exists($fileFullName)) {
			// Browser cache headers
            $filemtime = filemtime($fileFullName);
			$etag = md5($fileFullName . filesize($fileFullName) . $filemtime);
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                // convert to unix timestamp
                $if_modified_since = strtotime(preg_replace('#;.*$#', '',
                    stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE'])));
            } else {
                $if_modified_since = false;
            }
            if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
                $if_none_match = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
                if ($if_none_match == $etag && $if_modified_since >= $filemtime) {
                    $protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
                    header($protocol . ' 304 Not Modified');
                    header("Etag: $etag");
                    exit;
                }
            }
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $filemtime));
			header("ETag: $etag");
			header('Expires: Mon, 01 Apr 1974 00:00:00 GMT');
			header('Cache-Control: must-revalidate, proxy-revalidate');
			// Page output
			header('Content-Type: text/html; charset=UTF-8');
			if (@strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === FALSE) {
				readgzfile($fileFullName);
			} else {
				header('Content-Encoding: gzip');
				echo file_get_contents($fileFullName);
			}
			exit;
		}
	}

	/**
	 * Writes output buffer contents to a cache image file
	 */
	public function write()
	{
        if (!$this->enabled || empty($this->filename)) {
            return;
        }
        if (!file_exists($this->dir . '/' . $this->path)) {
            mkdir($this->dir . '/' . $this->path, $this->perms, true);
        }
        file_put_contents($this->dir . '/' . $this->filename, gzencode(cot_outputFilters(ob_get_contents())));
	}

	/**
	 * Removes a directory with all its contents recursively
	 * @param string $path Directory path
     * @param bool $withSubDirectories Remove subdirectories
	 * @return int Number of items removed
	 */
	private function removeDir($path, $withSubDirectories = false)
	{
		$cnt = 0;
		if (is_dir($path)) {
			$dp = opendir($path);
			while ($f = readdir($dp)) {
				$fpath = $path . '/' . $f;
				if (is_dir($fpath) && $f != '.' && $f != '..' && $withSubDirectories) {
					$cnt += $this->removeDir($fpath, true);
				} elseif (is_file($fpath)) {
					unlink($fpath);
					$cnt++;
				}
			}
			closedir($dp);
			rmdir($path);
		}
		return ++$cnt;
	}
}

/**
 * A very popular caching solution using MySQL as a storage. It is quite slow compared to
 * File_cache but may be considered more reliable.
 */
class MySQL_cache extends Db_cache_driver
{
	/**
	 * Prefetched data to avoid duplicate queries
	 * @var array
	 */
	private $buffer = [];

	/**
	 * Performs pre-load actions
	 */
	public function __construct()
	{
		// 10% GC probability
		if (mt_rand(1, 10) == 5)
		{
			$this->gc();
		}
	}

	/**
	 * Enforces flush()
	 */
	public function __destruct()
	{
		$this->flush();
	}

	/**
	 * Saves all modified data with one query
	 */
	public function flush()
	{
		global $db, $db_cache, $sys;
		if (count($this->removed_data) > 0)
		{
			$q = "DELETE FROM $db_cache WHERE";
			$i = 0;
			foreach ($this->removed_data as $entry)
			{
				$c_name = $db->quote($entry['id']);
				$c_realm = $db->quote($entry['realm']);
				$or = $i == 0 ? '' : ' OR';
				$q .= $or." (c_name = $c_name AND c_realm = $c_realm)";
				$i++;
			}
			$this->removed_data = [];
			$db->query($q);
		}
		if (count($this->writeback_data) > 0)
		{
			$q = "INSERT INTO $db_cache (c_name, c_realm, c_expire, c_value) VALUES ";
			$i = 0;
			foreach ($this->writeback_data as $entry)
			{
				$c_name = $db->quote($entry['id']);
				$c_realm = $db->quote($entry['realm']);
				$c_expire = $entry['ttl'] > 0 ? $sys['now'] + $entry['ttl'] : 0;
				$c_value = $db->quote(serialize($entry['data']));
				$comma = $i == 0 ? '' : ',';
				$q .= $comma."($c_name, $c_realm, $c_expire, $c_value)";
				$i++;
			}
			$this->writeback_data = [];
			$q .= " ON DUPLICATE KEY UPDATE c_value=VALUES(c_value), c_expire=VALUES(c_expire)";
			$db->query($q);
		}
	}

	/**
	 * @see Cache_driver::clear()
	 */
	public function clear($realm = '')
	{
		global $db, $db_cache;

		if (empty($realm)) {
			$db->query("TRUNCATE $db_cache");
		} else {
			$db->query("DELETE FROM $db_cache WHERE c_realm = " . $db->quote($realm));
		}
		$this->buffer = [];

		return true;
	}

	/**
	 * @see Cache_driver::exists()
	 */
	public function exists($id, $realm = COT_DEFAULT_REALM)
	{
		global $db, $db_cache;
		if (isset($this->buffer[$realm][$id]))
		{
			return true;
		}
		$sql = $db->query("SELECT c_value FROM $db_cache WHERE c_realm = ".$db->quote($realm)." AND c_name = ".$db->quote($id));
		$res = $sql->rowCount() == 1;
		if ($res)
		{
			$this->buffer[$realm][$id] = unserialize($sql->fetchColumn());
		}
		return $res;
	}

	/**
	 * Garbage collector function. Removes cache entries which are not valid anymore.
	 * @return int Number of entries removed
	 */
	private function gc()
	{
		global $db, $db_cache, $sys;
		$db->query("DELETE FROM $db_cache WHERE c_expire > 0 AND c_expire < ".$sys['now']);
		return $db->affectedRows;
	}

	/**
	 * @see Cache_driver::get()
	 */
	public function get($id, $realm = COT_DEFAULT_REALM)
	{
		if($this->exists($id, $realm))
		{
			return $this->buffer[$realm][$id];
		}
		else
		{
			return null;
		}
	}

	/**
     * @inheritdoc
	 * @see Db_cache_driver::get_all()
	 */
	public function get_all($realm = COT_DEFAULT_REALM)
	{
        $where = '';
        if (!empty($realm)) {
            if (is_array($realm)) {
                $realm = array_map(
                    function($value)
                    {
                        return \Cot::$db->quote($value);
                    },
                    $realm
                );
                $where = 'c_realm IN (' . implode(', ', $realm) . ')';
            } else {
                $where = 'c_realm = ' . \Cot::$db->quote($realm);
            }
        }

        // c_auto is never written
        // $sql = $db->query("SELECT c_name, c_value FROM `$db_cache` WHERE c_auto=1 AND $r_where");

        if ($where !== '') {
            $where .= ' OR ';
        }
        $where .= 'c_auto = 1';

        $sql = \Cot::$db->query('SELECT c_name, c_value FROM ' . \Cot::$db->quoteTableName(\Cot::$db->cache) . " WHERE $where");
		$i = 0;
		while ($row = $sql->fetch()) {
			global ${$row['c_name']};
			${$row['c_name']} = unserialize($row['c_value']);
			$i++;
		}
		$sql->closeCursor();

		return $i;
	}

	/**
	 * @see Writeback_cache_driver::remove_now()
	 */
	public function remove_now($id, $realm = COT_DEFAULT_REALM)
	{
		global $db, $db_cache;
		$db->query("DELETE FROM $db_cache WHERE c_realm = ".$db->quote($realm)." AND c_name = ".$db->quote($id));
		unset($this->buffer[$realm][$id]);
		return $db->affectedRows == 1;
	}

	/**
	 * Stores data as object image in cache
	 * @param string $id Object identifier
	 * @param mixed $data Object value
	 * @param string $realm Realm name
	 * @param int $ttl Time to live, 0 for unlimited
	 * @return bool
	 * @see Cache_driver::store()
	 */
	public function store($id, $data, $realm = COT_DEFAULT_REALM, $ttl = 0)
	{
		// Check data length
		if ($data) {
            /**
             *       Type | Maximum length
             * MEDIUMTEXT |    16,777,215 (224−1) bytes = 16 MiB
             * LONGTEXT   | 4,294,967,295 (232−1) bytes =  4 GiB
             */

            // MySQL max MEDIUMTEXT size
			if (strlen(\Cot::$db->prep(serialize($data))) > 16777215) {
				return false;
			}
		}
		return parent::store($id, $data, $realm, $ttl);
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
	public function store_now($id, $data, $realm = COT_DEFAULT_REALM, $ttl = COT_DEFAULT_TTL)
	{
		global $db, $db_cache, $sys;
		$c_name = $db->quote($id);
		$c_realm = $db->quote($realm);
		$c_expire = $ttl > 0 ? $sys['now'] + $ttl : 0;
		$c_value = $db->quote(serialize($data));
		$db->query("INSERT INTO $db_cache (c_name, c_realm, c_expire, c_value)
			VALUES ($c_name, $c_realm, $c_expire, $c_value)");
		$this->buffer[$realm][$id] = $data;
		return $db->affectedRows == 1;
	}
}

if (extension_loaded('memcache'))
{
	$cot_cache_drivers[] = 'Memcache_driver';

	/**
	 * Memcache distributed persistent cache driver implementation. Give it a higher priority
	 * if a cluster of webservers is used and Memcached is running via TCP/IP between them.
	 * In other circumstances this only should be used if no APC/XCache available,
	 * keeping in mind that File_cache might be still faster.
	 * @author Cotonti Team
	 */
	class Memcache_driver extends Temporary_cache_driver
	{
		/**
		 * PHP Memcache instance
		 * @var Memcache
		 */
		protected $memcache = NULL;

		/**
		 * Creates an object and establishes Memcached server connection
		 * @param string $host Memcached host
		 * @param int $port Memcached port
		 * @param bool $persistent Use persistent connection
		 * @return Memcache_driver
		 */
		public function __construct($host = 'localhost', $port = 11211, $persistent = true)
		{
            if(empty($host)) $host = 'localhost';
            if(empty($port)) $port = 11211;
			$this->memcache = new Memcache;
			$this->memcache->addServer($host, $port, $persistent);
		}

        /**
         * Make unique key for one of different sites on one memcache pool
         * @param $key
         * @return string
         */
        public static function createKey($key) {
            if (is_array($key))  $key = serialize($key);
            return md5(Cot::$cfg['site_id'].$key);
        }

		/**
		 * @see Cache_driver::clear()
		 */
		public function clear($realm = '')
		{
			if (empty($realm))
			{
				return $this->memcache->flush();
			}
			else
			{
				// FIXME implement exact realm cleanup (not yet provided by Memcache)
				return $this->memcache->flush();
			}
		}

		/**
		 * @see Temporary_cache_driver::dec()
		 */
		public function dec($id, $realm = COT_DEFAULT_REALM, $value = 1)
		{
            $id = self::createKey($id);
			return $this->memcache->decrement($realm.'/'.$id, $value);
		}

		/**
		 * @see Cache_driver::exists()
		 */
		public function exists($id, $realm = COT_DEFAULT_REALM)
		{
            $id = self::createKey($id);
			return $this->memcache->get($realm.'/'.$id) !== FALSE;
		}

		/**
		 * @see Cache_driver::get()
		 */
		public function get($id, $realm = COT_DEFAULT_REALM)
		{
            $id = self::createKey($id);
			return $this->memcache->get($realm.'/'.$id);
		}

		/**
		 * @see Temporary_cache_driver::get_info()
		 */
		public function get_info()
		{
			$info = $this->memcache->getstats();
			if(empty($info)) {
			    return [
                    'available' => 0,
                    'max' => 0,
                    'occupied' => 0
                ];
            }
			return [
				'available' => $info['limit_maxbytes'] - $info['bytes'],
				'max' => $info['limit_maxbytes'],
				'occupied' => $info['bytes']
			];
		}

		/**
		 * @see Temporary_cache_driver::inc()
		 */
		public function inc($id, $realm = COT_DEFAULT_REALM, $value = 1)
		{
            $id = self::createKey($id);
			return $this->memcache->increment($realm.'/'.$id, $value);
		}

		/**
		 * @see Cache_driver::remove()
		 */
		public function remove($id, $realm = COT_DEFAULT_REALM)
		{
            $id = self::createKey($id);
			return $this->memcache->delete($realm.'/'.$id);
		}

		/**
		 * @see Dynamic_cache_driver::store()
		 */
		public function store($id, $data, $realm = COT_DEFAULT_REALM, $ttl = COT_DEFAULT_TTL)
		{
            $id = self::createKey($id);
			return $this->memcache->set($realm.'/'.$id, $data, 0, $ttl);
		}
	}
}

if (extension_loaded('apc'))
{
	$cot_cache_drivers[] = 'APC_driver';

	/**
	 * Accelerated PHP Cache driver implementation. This should be used as default cacher
	 * on APC-enabled hosts.
	 * @author Cotonti Team
	 */
	class APC_driver extends Temporary_cache_driver
	{
		/**
		 * @see Cache_driver::clear()
		 */
		public function clear($realm = '')
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
		public function exists($id, $realm = COT_DEFAULT_REALM)
		{
			return apc_fetch($realm.'/'.$id) !== FALSE;
		}

		/**
		 * @see Cache_driver::get()
		 */
		public function get($id, $realm = COT_DEFAULT_REALM)
		{
			return unserialize(apc_fetch($realm.'/'.$id));
		}

		/**
		 * @see Temporary_cache_driver::get_info()
		 */
		public function get_info()
		{
			$info = apc_sma_info();
			$max = ini_get('apc.shm_segments') * ini_get('apc.shm_size') * 1024 * 1024;
			$occupied = $max - $info['avail_mem'];
			return [
				'available' => $info['avail_mem'],
				'max' => $max,
				'occupied' => $occupied
			];
		}

		/**
		 * @see Cache_driver::remove()
		 */
		public function remove($id, $realm = COT_DEFAULT_REALM)
		{
			return apc_delete($realm.'/'.$id);
		}

		/**
		 * @see Dynamic_cache_driver::store()
		 */
		public function store($id, $data, $realm = COT_DEFAULT_REALM, $ttl = COT_DEFAULT_TTL)
		{
			// Protect from exhausted memory
			$info = $this->get_info();
			if ($info['available'] < $info['max'] * 0.2)
			{
				$this->clear();
			}
			return apc_store($realm.'/'.$id, serialize($data), $ttl);
		}
	}
}

if (extension_loaded('xcache'))
{
	$cot_cache_drivers[] = 'Xcache_driver';

	/**
	 * XCache variable cache driver. It should be used on hosts that use XCache for
	 * PHP acceleration and variable cache.
	 * @author Cotonti Team
	 */
	class Xcache_driver extends Temporary_cache_driver
	{
		/**
		 * @see Cache_driver::clear()
		 */
		public function clear($realm = '')
		{
            if(!function_exists('xcache_unset_by_prefix')) {
                function xcache_unset_by_prefix($prefix) {
                    // Since we can't clear targetted cache, we'll clear all. :(
                    xcache_clear_cache(XC_TYPE_VAR, 0);
                }
            }

            if (empty($realm)) {
                xcache_unset_by_prefix('');

            } else {
                xcache_unset_by_prefix($realm.'/');
            }

            return true;
		}

		/**
		 * @see Cache_driver::exists()
		 */
		public function exists($id, $realm = COT_DEFAULT_REALM)
		{
			return xcache_isset($realm.'/'.$id);
		}

		/**
		 * @see Temporary_cache_driver::dec()
		 */
		public function dec($id, $realm = COT_DEFAULT_REALM, $value = 1)
		{
			return xcache_dec($realm.'/'.$id, $value);
		}

		/**
		 * @see Cache_driver::get()
		 */
		public function get($id, $realm = COT_DEFAULT_REALM)
		{
			return unserialize(xcache_get($realm.'/'.$id));
		}

		/**
		 * @see Temporary_cache_driver::get_info()
		 */
		public function get_info()
		{
			return [
				'available' => -1,
				'max' => $this->get_ini_size('xcache.var_size'),
				'occupied' => -1
			];
		}

		/**
		 * @see Temporary_cache_driver::inc()
		 */
		public function inc($id, $realm = COT_DEFAULT_REALM, $value = 1)
		{
			return xcache_inc($realm.'/'.$id, $value);
		}

		/**
		 * @see Cache_driver::remove()
		 */
		public function remove($id, $realm = COT_DEFAULT_REALM)
		{
			return xcache_unset($realm.'/'.$id);
		}

		/**
		 * @see Dynamic_cache_driver::store()
		 */
		public function store($id, $data, $realm = COT_DEFAULT_REALM, $ttl = COT_DEFAULT_TTL)
		{
			return xcache_set($realm.'/'.$id, serialize($data), $ttl);
		}
	}
}

/**
 * Multi-layer universal cache controller for Cotonti
 *
 * @property-read bool $mem_available Memory storage availability flag
 */
class Cache
{
	/**
	 * Persistent cache underlayer driver.
	 * Stores disk-only cache entries. Use it for large objects, which you don't want to put
	 * into memory cache.
	 * @var File_cache
	 */
	public $disk;
	/**
	 * Intermediate database cache driver.
	 * It is recommended to use memory cache for particular objects rather than DB cache.
	 * @var Db_cache_driver
	 */
	public $db;
	/**
	 * Mutable top-layer shared memory driver.
	 * Is FALSE if memory cache is not available
	 * @var Temporary_cache_driver
	 */
	public $mem;

	/**
	 * Static cache driver. For caching entire site pages.
	 * @var Page_cache
	 */
	public $static;

	/**
	 * Event bindings
	 * @var array
	 */
	private $bindings;
	/**
	 * A flag to apply binding changes before termination
	 * @var bool
	 */
	private $resync_on_exit = false;
	/**
	 * Selected memory driver
	 * @var string
	 */
	private $selected_drv = '';

	/**
	 * Initializes Page cache for early page caching
	 */
	public function  __construct()
	{
		global $cfg;

		$this->static = new Page_cache($cfg['cache_dir'] . '/static', $cfg['dir_perms']);
	}

	/**
	 * Performs actions before script termination
	 */
	public function  __destruct()
	{
		if ($this->resync_on_exit)
		{
			$this->resync_bindings();
		}
	}

	/**
	 * Property handler
	 * @param string $name Property name
	 * @return mixed Property value
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'mem_available':
				return $this->mem !== FALSE;
			break;

			case 'mem_driver':
				return $this->selected_drv;
			break;

			default:
				return null;
			break;
		}
	}

    /**
     * Initializes the rest Cache components when the sources are available
     */
    public function init()
    {
        global $cfg, $cot_cache_autoload, $cot_cache_drivers, $cot_cache_bindings, $env;

        $this->disk = new File_cache(Cot::$cfg['cache_dir']);
		$this->db = new MySQL_cache();
        $defaultRealms = ['system', 'cot'];
        if (!empty(Cot::$env['ext'])) {
            $defaultRealms[] = Cot::$env['ext'];
        }
		$cot_cache_autoload = !empty($cot_cache_autoload) && is_array($cot_cache_autoload)
			? array_merge($defaultRealms, $cot_cache_autoload)
			: $defaultRealms;

		$this->db->get_all($cot_cache_autoload);

        Cot::$cfg['cache_drv'] .= '_driver';
		if (in_array(Cot::$cfg['cache_drv'], $cot_cache_drivers)) {
			$selected = Cot::$cfg['cache_drv'];
		}
		if (!empty($selected)) {
            $cfg['cache_drv_host'] = !empty($cfg['cache_drv_host']) ? $cfg['cache_drv_host'] : null;
            $cfg['cache_drv_port'] = !empty($cfg['cache_drv_port']) ? $cfg['cache_drv_port'] : null;
            /** @var Temporary_cache_driver $mem */
			$mem = new $selected($cfg['cache_drv_host'], $cfg['cache_drv_port']);
			// Some drivers may be enabled but without variable cache
			$info = $mem->get_info();
			if ($info['max'] > 1024)
			{
				$this->mem = $mem;
				$this->selected_drv = $selected;
			}
		} else {
			$this->mem = false;
		}

        if (!$cot_cache_bindings) {
			$this->resync_bindings();
		} else {
			unset($cot_cache_bindings);
		}
    }

	/**
	 * Rereads bindings from database
	 */
	private function resync_bindings()
	{
		// global $db, $db_cache_bindings;
		$this->bindings = [];
		// $sql = $db->query("SELECT * FROM `$db_cache_bindings`");
		// while ($row = $sql->fetch())
		// {
		// 	$this->bindings[$row['c_event']][] = ['id' => $row['c_id'], 'realm' => $row['c_realm']];
		// }
		// $sql->closeCursor();
		// $this->db->store('cot_cache_bindings', $this->bindings, 'system');
	}

	/**
	 * Binds an event to automatic cache field invalidation
	 * @param string $event Event name
	 * @param string $id Cache entry id
	 * @param string $realm Cache realm name
	 * @param int $type Storage type, one of COT_CACHE_TYPE_* values
	 * @return bool TRUE on success, FALSE on error
	 */
	public function bind($event, $id, $realm = COT_DEFAULT_REALM, $type = COT_CACHE_TYPE_DEFAULT)
	{
		global $db, $db_cache_bindings;
		$c_event = $db->quote($event);
		$c_id = $db->quote($id);
		$c_realm = $db->quote($realm);
		$c_type = (int) $type;
		$db->query("INSERT INTO `$db_cache_bindings` (c_event, c_id, c_realm, c_type)
			VALUES ($c_event, $c_id, $c_realm, $c_type)");
		$res = $db->affectedRows == 1;
		if ($res)
		{
			$this->resync_on_exit = true;
		}
		return $res;
	}

	/**
	 * Binds multiple cache fields to events, all represented as an associative array
	 * Binding keys:
	 * event - name of the event the field is binded to
	 * id - cache object id
	 * realm - cache realm name
	 * type - cache storage type, one of COT_CACHE_TYPE_* constants
	 * @param array $bindings An indexed array of bindings.
	 * Each binding is an associative array with keys: event, realm, id, type.
	 * @return int Number of bindings added
	 */
	public function bind_array($bindings)
	{
		global $db, $db_cache_bindings;
		$q = "INSERT INTO `$db_cache_bindings` (c_event, c_id, c_realm, c_type) VALUES ";
		$i = 0;
		foreach ($bindings as $entry)
		{
			$c_event = $db->prep($entry['event']);
			$c_id = $db->prep($entry['id']);
			$c_realm = $db->prep($entry['realm']);
			$c_type = (int) $entry['type'];
			$comma = $i == 0 ? '' : ',';
			$q .= $comma."('$c_event', '$c_id', '$c_realm', $c_type)";
		}
		$db->query($q);
		$res = $db->affectedRows;
		if ($res > 0)
		{
			$this->resync_on_exit = true;
		}
		return $res;
	}

	/**
	 * Clears all cache entries
	 * @param int|int[] $type Cache storage type:
	 * COT_CACHE_TYPE_ALL, COT_CACHE_TYPE_DB, COT_CACHE_TYPE_DISK, COT_CACHE_TYPE_MEMORY.
	 * @return bool
	 */
	public function clear($type = COT_CACHE_TYPE_ALL)
	{
		$res = true;

        $cacheTypesToClear = is_array($type) ? $type : [$type];
        if (in_array(COT_CACHE_TYPE_ALL, $cacheTypesToClear)) {
            // Clear All Caches
            if ($this->mem) {
                $res &= $this->mem->clear();
            }
            $res &= $this->db->clear();
            $res &= $this->disk->clear();
            $res &= $this->static->clear('', true);

            return $res;
        }

        if (in_array(COT_CACHE_TYPE_DB, $cacheTypesToClear)) {
            $res &= $this->db->clear();
        }

        if (in_array(COT_CACHE_TYPE_DISK, $cacheTypesToClear)) {
            $res &= $this->disk->clear();
        }

        if (in_array(COT_CACHE_TYPE_MEMORY, $cacheTypesToClear)) {
            if ($this->mem) {
                $res &= $this->mem->clear();
            }
        }

        if (in_array(COT_CACHE_TYPE_STATIC, $cacheTypesToClear)) {
            $res &= $this->static->clear('', true);
        }

		return $res;
	}

	/**
	 * Clears cache in specific realm
	 * @param string $realm Realm name
	 * @param int $type Cache storage type:
	 * COT_CACHE_TYPE_ALL, COT_CACHE_TYPE_DB, COT_CACHE_TYPE_DISK, COT_CACHE_TYPE_MEMORY.
	 */
	public function clear_realm($realm = COT_DEFAULT_REALM, $type = COT_CACHE_TYPE_ALL)
	{
		switch ($type)
		{
			case COT_CACHE_TYPE_DB:
				$this->db->clear($realm);
			break;

			case COT_CACHE_TYPE_DISK:
				$this->disk->clear($realm);
			break;

			case COT_CACHE_TYPE_MEMORY:
				if ($this->mem) {
					$this->mem->clear($realm);
				}
			break;

			case COT_CACHE_TYPE_STATIC:
                // @todo
				$this->static->clear($realm);
			break;

			default:
				if ($this->mem) {
					$this->mem->clear($realm);
				}
				$this->db->clear($realm);
				$this->disk->clear($realm);
				$this->static->clear($realm);
		}
	}

	/**
	 * Returns information about memory driver usage
	 * @return array Usage information
	 */
	public function get_info()
	{
		if ($this->mem)
		{
			return $this->mem->get_info();
		}
		else
		{
			return [];
		}
	}

	/**
	 * Invalidates cache cells which were binded to the event.
	 * @param string $event Event name
	 * @return int Number of cells cleaned
	 */
	public function trigger($event)
	{
		$cnt = 0;
		if (isset($this->bindings[$event]) && count($this->bindings[$event]) > 0)
		{
			foreach ($this->bindings[$event] as $cell)
			{
				switch ($cell['type'])
				{
					case COT_CACHE_TYPE_DISK:
						$this->disk->remove($cell['id'], $cell['realm']);
					break;

					case COT_CACHE_TYPE_DB:
						$this->db->remove($cell['id'], $cell['realm']);
					break;

					case COT_CACHE_TYPE_MEMORY:
						if ($this->mem)
						{
							$this->mem->remove($cell['id'], $cell['realm']);
						}
					break;

					case COT_CACHE_TYPE_STATIC:
                        // @todo
						$this->static->clear($cell['realm'] . '/' . $cell['id']);
					break;

					default:
						if ($this->mem)
						{
							$this->mem->remove($cell['id'], $cell['realm']);
						}
						$this->disk->remove($cell['id'], $cell['realm']);
						$this->db->remove($cell['id'], $cell['realm']);
						$this->static->clear($cell['realm'] . '/' . $cell['id']);
				}
				$cnt++;
			}
		}
		return $cnt;
	}

	/**
	 * Removes event/cache bindings
	 * @param string $realm Realm name (required)
	 * @param string $id Object identifier. Optional, if not specified, all bindings from the realm are removed.
	 * @return int Number of bindings removed
	 */
	public function unbind($realm, $id = '')
	{
		global $db, $db_cache_bindings;
		$c_realm = $db->quote($realm);
		$q = "DELETE FROM `$db_cache_bindings` WHERE c_realm = $c_realm";
		if (!empty($id))
		{
			$c_id = $db->quote($id);
			$q .= " AND c_id = $c_id";
		}
		$db->query($q);
		$res = $db->affectedRows;
		if ($res > 0)
		{
			$this->resync_on_exit = true;
		}
		return $res;
	}
}
