<?php

/**
 * Plugin and Module Management API
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD
 */
/**
 * Default plugin part execution priority
 */
define('COT_PLUGIN_DEFAULT_ORDER', 10);

/**
 * Applies custom SQL and PHP patches in a directory. Error and success
 * messages are emitted via standard messaging API during execution.
 * 
 * @param string $directory Directory path
 * @param string $from_ver Current version, to patch starting from
 * @param string $sql_pattern SQL patch file name pattern (PCRE)
 * @param string $php_pattern PHP patch file name pattern (PCRE)
 * @return mixed The function returns TRUE if there are not patches to apply,
 * FALSE if an error occured while patching or a string containing version
 * number of the latest applied patch if patching was successful.
 */
function sed_apply_patches($directory, $from_ver,
	$sql_pattern = '(sql)_([\w\.\-\_]+)\.sql',
	$php_pattern = '(php)_([\w\.\-\_]+)\.inc')
{
	global $L;

	// Find new patches
	$dp = opendir($directory);
	$delta = array();
	while ($f = readdir($dp))
	{
		if (preg_match('#^' . $sql_pattern . '$#', $f, $mt)
			|| preg_match('#^' . $php_pattern . '$#', $f, $mt))
		{
			$type = $mt[1];
			$ver = $mt[2];
			if (version_compare($ver, $from_ver) > 0)
			{
				$delta[$ver][$type] = $directory . '/' . $f;
			}
		}
	}
	closedir($dp);
	if (count($delta) == 0)
	{
		return true;
	}

	// Apply patches in verion order
	uksort($delta, 'version_compare');
	$max_ver = $from_ver;
	foreach ($delta as $key => $val)
	{
		if (isset($val['sql']))
		{
			$error = sed_sql_runscript(file_get_contents($val['sql']));
			if (empty($error))
			{
				sed_message(sed_rc('ext_patch_applied',
					array('f' => $val['sql'], 'msg' => 'OK')));
			}
			else
			{
				sed_error(sed_rc('ext_patch_error',
					array('f' => $val['sql'], 'msg' => $error)));
				return false;
			}
		}
		if (isset($val['php']))
		{
			$ret = include $val['php'];
			if ($ret !== false)
			{
				$msg = $ret == 1 ? 'OK' : $ret;
				sed_message(sed_rc('ext_patch_applied',
					array('f' => $val['php'], 'msg' => $msg)));
			}
			else
			{
				sed_error(sed_rc('ext_patch_error',
					array('f' => $val['php'], 'msg' => $L['Error'])));
				return false;
			}
		}
		$max_ver = $key;
	}

	return $max_ver;
}

/**
 * Installs or updates a Cotonti extension: module or plugin.
 * Messages emitted during installation can be received through standard
 * Cotonti messages interface.
 * @param string $name Plugin code
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @param bool $update Perform update rather than new install
 * @return bool Operation status
 */
function sed_extension_install($name, $is_module = false, $update = false)
{
    global $cfg, $L, $cot_error, $cot_cache, $usr, $db_auth, $db_users,
		$db_updates, $db_core;

    $path = $is_module ? $cfg['modules_dir'] . "/$name" : $cfg['plugins_dir']
		. "/$name";

    // Check setup file and tags
    $setup_file = $path . "/$name.setup.php";
    if (!file_exists($setup_file))
    {
        sed_error('ext_setup_not_found');
        return false;
    }
    $info = sed_infoget($setup_file, 'COT_EXT');
    if ($info === false)
    {
        sed_error('ext_invalid_format');
        return false;
    }

	// Check versions
	if ($is_module)
	{
		$res = sed_sql_query("SELECT ct_version FROM $db_core
			WHERE ct_code = '$name'");
	}
	else
	{
		$res = sed_sql_query("SELECT upd_value FROM $db_updates
			WHERE upd_param = '$name.ver'");
	}
	if (sed_sql_numrows($res) == 1)
	{
		$current_ver = sed_sql_result($res);
		sed_sql_freeresult($res);
		if (!$update)
		{
			sed_error('ext_already_installed');
			return false;
		}
	}

    // Check dependencies
    if (!empty($info['Requires_modules']))
    {
        $req_mods = explode(',', $info['Requires_modules']);
        array_walk($req_mods, 'trim');
        foreach ($req_mods as $req_mod)
        {
            if (!sed_module_installed($req_mod))
            {
                sed_error(sed_rc('ext_req_module_missing',
					array('name' => $req_mod)));
            }
        }
    }
    if (!empty($info['Requires_plugins']))
    {
        $req_plugs = explode(',', $info['Requires_plugins']);
        array_walk($req_plugs, 'trim');
        foreach ($req_mods as $req_plug)
        {
            if (!sed_plugin_installed($req_plug))
            {
                sed_error(sed_rc('ext_req_plugin_missing',
					array('name' => $req_plug)));
            }
        }
    }
    if ($cot_error)
    {
        return false;
    }

	if ($update)
	{
		// Safely drop existing bindings
		$bindings_cnt = sed_plugin_remove($name);
		sed_message(sed_rc('ext_bindings_uninstalled',
			array('cnt' => $bindings_cnt)));
	}
    // Install hook parts and bindings
    $hook_bindings = array();
    $dp = opendir($path);
    while ($f = readdir($dp))
    {
        if (preg_match("#^$name.([\w\.]+).php$#", $f, $mt))
        {
            $part_info = sed_infoget($path . "/$f", 'COT_EXT');
            if ($part_info)
            {
                if (empty($info['Hooks']))
                {
                    $hooks = $is_module ? 'module' : 'standalone';
                }
                else
                {
                    $hooks = explode(',', $part_info['Hooks']);
                    array_walk($hooks, 'trim');
                }
                foreach ($hooks as $hook)
                {
                    $hook_bindings[] = array(
                        'part' => $mt[1],
                        'hook' => $hook,
                        'order' => isset($part_info['Order'])
							? (int) $part_info['Order']
							: COT_PLUGIN_DEFAULT_ORDER
                    );
                }
            }
        }
    }
    closedir($dp);
    $bindings_cnt = sed_plugin_add($hook_bindings, $name, $info['Name'],
		$is_module);
    sed_message(sed_rc('ext_bindings_installed',
		array('cnt' => $bindings_cnt)));

    // Install config
    $info_cfg = sed_infoget($setup_file, 'COT_EXT_CONFIG');
    $options = sed_config_parse($info_cfg, $is_module);

	if ($update)
	{
		// Get differential config
		if (sed_config_update($name, $options, $is_module) > 0)
		{
			sed_message('ext_config_updated');
		}
	}
	else
	{
		if (sed_config_add($name, $options, $is_module))
		{
			sed_message('ext_config_installed');
		}
		else
		{
			sed_error('ext_config_error');
		}
	}


	if ($update)
	{
		// Only update auth locks
		$lock_guests = sed_auth_getvalue($info['Lock_guests']);
		sed_sql_update($db_auth, 'auth_groupid = ' . COT_GROUP_GUESTS
			. ' OR auth_groupid = ' . COT_GROUP_INACTIVE,
			array('rights_lock' => $lock_guests), 'auth_');

		$lock_members = sed_auth_getvalue($info['Lock_members']);
		$ingore_groups = implode(',', array(COT_GROUP_GUESTS,
			COT_GROUP_INACTIVE, COT_GROUP_BANNED, COT_GROUP_BANNED,
			COT_GROUP_SUPERADMINS));
		sed_sql_update($db_auth, "auth_groupid NOT IN ($ingore_groups)",
			array('rights_lock' => $lock_members), 'auth_');

		sed_message('ext_auth_locks_updated');
	}
	else
	{
		// Install auth
		$insert_rows = array();
		foreach ($sed_groups as $k => $v)
		{
			if ($v['id'] == COT_GROUP_GUESTS || $v['id'] == COT_GROUP_INACTIVE)
			{
				$ins_auth = sed_auth_getvalue($info['Auth_guests']);
				$ins_lock = sed_auth_getvalue($info['Lock_guests']);

				if ($ins_auth > 128 || $ins_lock < 128)
				{
					$ins_auth = ($ins_auth > 127) ? $ins_auth - 128 : $ins_auth;
					$ins_lock = 128;
				}
			}
			elseif ($v['id'] == COT_GROUP_BANNED)
			{
				$ins_auth = 0;
				$ins_lock = 255;
			}
			elseif ($v['id'] == COT_GROUP_SUPERADMINS)
			{
				$ins_auth = 255;
				$ins_lock = 255;
			}
			else
			{
				$ins_auth = sed_auth_getvalue($info['Auth_members']);
				$ins_lock = sed_auth_getvalue($info['Lock_members']);
			}

			if ($is_module)
			{
				$insert_rows[] = array(
					'groupid' => $v['id'],
					'code' => $name,
					'option' => 'a',
					'rights' => $ins_auth,
					'rights_lock' => $ins_lock,
					'setbyuserid' => $usr['id']
				);
			}
			else
			{
				$insert_rows[] = array(
					'groupid' => $v['id'],
					'code' => 'plug',
					'option' => $name,
					'rights' => $ins_auth,
					'rights_lock' => $ins_lock,
					'setbyuserid' => $usr['id']
				);
			}
		}
		if (sed_sql_insert($db_auth, $insert_rows, 'auth_'))
		{
			sed_sql_update($db_users, '1', array('auth' => ''), 'user_');
			sed_message('ext_auth_installed');
		}
	}


	if ($update)
	{
		// Find and apply patches
		$new_ver = sed_apply_patches("$path/setup", $current_ver);
		if (version_compare($info['Version'], $new_ver) > 0)
		{
			$new_ver = $info['Version'];
		}
	}
	else
	{
		if (file_exists($path . "/setup/$name.install.sql"))
		{
			// Run SQL install script
			$sql_err = sed_sql_runscript(
				file_get_contents("$path/setup/$name.install.sql"));
			if (empty($sql_err))
			{
				sed_message(sed_rc('ext_executed_sql', array('ret' => 'OK')));
			}
			else
			{
				sed_error(sed_rc('ext_executed_sql', array('ret' => $sql_err)));
			}
		}

		if (file_exists(sed_incfile('install', $name, !$is_module)))
		{
			// Run PHP install handler
			$ret = include sed_incfile('install', $name, !$is_module);
			if ($ret !== false)
			{
				$msg = $ret == 1 ? 'OK' : $ret;
				sed_message(sed_rc('ext_executed_php', array('ret' => $msg)));
			}
			else
			{
				sed_error(sed_rc('ext_executed_php',
					array('ret' => $L['Error'])));
			}
		}
	}

	// Register version information
	if ($is_module)
	{
		if ($update)
		{
			sed_module_update($name, $new_ver);
		}
		else
		{
			sed_module_add($name, $info['Name'], $info['Version']);
		}
	}
    else
    {
        if ($update)
		{
			sed_sql_update($db_updates, "upd_param = '$name.ver'",
				array('value' => $new_ver), 'upd_');
		}
		else
		{
			sed_sql_insert($db_updates, array('param' => "$name.ver",
				'value' => $info['Version']), 'upd_');
		}
    }

    // Cleanup
    sed_auth_reorder();
    $cot_cache && $cot_cache->db->remove('sed_plugins', 'system');

    return !$cot_error;
}

/**
 * Uninstalls an extension and removes all its data
 * @param string $name Extension code
 * @param bool $is_module TRUE for modules, FALSE for plugins
 */
function sed_extension_uninstall($name, $is_module = false)
{
    global $cfg, $db_auth, $db_config, $db_users, $db_updates, $cot_cache,
		$cot_error;

    $path = $is_module ? $cfg['modules_dir'] . "/$name" : $cfg['plugins_dir']
		. "/$name";

    // Remove bindings
    sed_plugin_remove($name);

    // Drop auth and config
    if ($is_module)
    {
        sed_sql_delete($db_config, "config_owner = 'module'
			AND config_cat = '$name'");
        sed_sql_delete($db_auth, "auth_code = '$name'");
    }
    else
    {
        sed_sql_delete($db_config, "config_owner = 'plug'
			AND config_cat = '$name'");
        sed_sql_delete($db_auth, "auth_code = 'plug'
			AND auth_option = '$name'");
    }
    sed_message('ext_auth_uninstalled');
    sed_message('ext_config_uninstalled');

    // Clear cache
    $cot_cache && $cot_cache->db->remove('sed_plugins', 'system');
    sed_sql_update($db_users, '1', array('auth' => ''), 'user_');

    // Run SQL script if present
    if (file_exists($path . "/setup/$name.uninstall.sql"))
    {
        $sql_err = sed_sql_runscript(
			file_get_contents("$path/setup/$name.uninstall.sql"));
        if (empty($sql_err))
        {
            sed_message(sed_rc('ext_executed_sql', array('ret' => '')));
        }
        else
        {
            sed_error(sed_rc('ext_executed_sql', array('ret' => $sql_err)));
        }
    }

    // Run handler part
    if (file_exists(sed_incfile('uninstall', $name, !$is_module)))
    {
        $ret = include sed_incfile('uninstall', $name, !$is_module);
        if ($ret !== false)
        {
            sed_message(sed_rc('ext_executed_php', array('ret' => $ret)));
        }
        else
        {
            sed_error(sed_rc('ext_executed_php', array('ret' => $L['Error'])));
        }
    }

    if ($is_module)
	{
		// Unregister from modules table
		sed_module_remove($name);
	}
	else
    {
        // Unregister from updates table
        sed_sql_delete($db_updates, "upd_param = '$name.ver'");
    }
}

/**
 * Parses PHPDoc file header into an array
 *
 * @param string $filename Path to a PHP file
 * @return array Associative array containing PHPDoc contents. The array is
 *  empty if no PHPDoc was found
 */
function sed_file_phpdoc($filename)
{
    $res = array();
    $data = file_get_contents($filename);
    if (preg_match('#^/\*\*(.*?)^\s\*/#ms', $data, $mt))
    {
        $phpdoc = preg_split('#\r?\n\s\*\s@#', $mt[1]);
        $cnt = count($phpdoc);
        if ($cnt > 0)
        {
            $res['description'] = trim(preg_replace('#\r?\n\s\*\s?#', '',
				$phpdoc[0]));
            for ($i = 1; $i < $cnt; $i++)
            {
                $delim = mb_strpos($phpdoc[$i], ' ');
                $key = mb_substr($phpdoc[$i], 0, $delim);
                $contents = trim(preg_replace('#\r?\n\s\*\s?#', '',
					substr($phpdoc[$i], $delim + 1)));
                $res[$key] = $contents;
            }
        }
    }
    return $res;
}

/**
 * Extract info from SED file headers
 *
 * @param string $file File path
 * @param string $limiter Tag name
 * @param int $maxsize Max header size
 * @return array Array containing block data or FALSE on error
 */
function sed_infoget($file, $limiter = 'SED', $maxsize = 32768)
{
    global $L;
    $result = array();

	$fp = @fopen($file, 'r');
    if ($fp)
    {
        $limiter_begin = "[BEGIN_" . $limiter . "]";
        $limiter_end = "[END_" . $limiter . "]";
        $data = fread($fp, $maxsize);
        $begin = mb_strpos($data, $limiter_begin);
        $end = mb_strpos($data, $limiter_end);

        if ($end > $begin && $begin > 0)
        {
            $lines = mb_substr($data, $begin + 8 + mb_strlen($limiter),
				$end - $begin - mb_strlen($limiter) - 8);
            $lines = explode("\n", $lines);

            foreach ($lines as $k => $line)
            {
                $linex = explode("=", $line);
                $ii = 1;
                while (!empty($linex[$ii]))
                {
                    $result[$linex[0]] .= trim($linex[$ii]);
                    $ii++;
                }
            }
        }
        else
        {
            $result = false;
        }
    }
    else
    {
        $result = false;
    }
    @fclose($fp);
    return $result;
}

/**
 * Registers a module in the core
 *
 * @param string $name Module name (code)
 * @param string $title Title name
 * @param string $version Version number as A.B.C
 * @return bool TRUE on success, FALSE on error
 */
function sed_module_add($name, $title, $version = '1.0.0')
{
    global $db_core;

    $res = sed_sql_insert($db_core, array('code' => $name, 'title' => $title,
		'version' => $version), 'ct_');

    return false;
}

/**
 * Checks if module is already installed
 *
 * @param string $name Module code
 * @return bool
 */
function sed_module_installed($name)
{
    global $db_core, $cfg;

    $cnt = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_core
		WHERE ct_code = '$name'"));
    return $cnt > 0 && file_exists($cfg['modules_dir'] . '/' . $name);
}

/**
 * Suspends (temporarily disables) a module
 *
 * @param string $name Module name
 * @return bool
 */
function sed_module_pause($name)
{
    global $db_core;

    return sed_sql_update($db_core, "ct_code = '$name'", array('state' => 0),
		'ct_') == 1;
}

/**
 * Unregisters a module from the core
 *
 * @param string $name Module name
 * @return bool
 */
function sed_module_remove($name)
{
    global $db_core, $db_updates;

    return sed_sql_delete($db_core, "ct_code = '$name'");
}

/**
 * Resumes a paused module
 *
 * @param string $name Module name
 * @return bool
 */
function sed_module_resume($name)
{
    global $db_core;

    return sed_sql_update($db_core, "ct_code = '$name'", array('state' => 1),
		'ct_') == 1;
}

/**
 * Updates module version number in the registry
 *
 * @param string $name Module name
 * @param string $version New version string
 * @return bool
 */
function sed_module_update($name, $version)
{
    global $db_core;

    // TODO write correct module update code
    $res = sed_sql_update($db_core, "ct_code = '$name'",
		array('version' => $version), 'ct_');

    return $res;
}

/**
 * Registers a plugin or module in hook registry
 *
 * Example:
 * <code>
 * $hook_bindings = array(
 *     array(
 *         'part' => 'rss',
 *         'hook' => 'rss.main',
 *         'order' => 20
 *     ),
 *     array(
 *         'part' => 'header',
 *         'hook' => 'header.tags',
 *     )
 * );
 *
 * sed_plugin_add($hook_bindings, 'test', 'Test plugin', false);
 * </code>
 *
 * @param array $hook_bindings Hook binding map
 * @param string $name Module or plugin name (code)
 * @param string $title Module or plugin title
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @return int Number of records added
 */
function sed_plugin_add($hook_bindings, $name, $title, $is_module = false)
{
    global $db_plugins, $cfg;

    if (empty($title))
        $title = $name;
    $path = $is_module ? $cfg['modules_dir'] . "/$name/$name."
		: $cfg['plugins_dir'] . "/$name/$name.";

    $insert_rows = array();
    foreach ($hook_bindings as $binding)
    {
        $insert_rows[] = array(
            'hook' => $binding['hook'],
            'code' => $name,
            'part' => $binding['part'],
            'title' => $title,
            'file' => $path . $binding['part'] . '.php',
            'order' => $binding['order'],
            'active' => 1,
            'module' => (int) $is_module
        );
    }
    return sed_sql_insert($db_plugins, $insert_rows, 'pl_');
}

/**
 * Checks if plugin is already installed
 *
 * @param string $name Plugin code
 * @return bool
 */
function sed_plugin_installed($name)
{
    global $db_plugins, $cfg;

    $cnt = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_plugins
		WHERE pl_code = '$name'"));
    return $cnt > 0 && file_exists($cfg['plugins_dir'] . '/' . $name);
}

/**
 * Suspends a plugin or one of its parts
 *
 * @param string $name Module or plugin name
 * @param int $binding_id ID of the binding to supsend or 0 to suspend all
 * @return int Number of bindings suspended
 */
function sed_plugin_pause($name, $binding_id = 0)
{
    global $db_plugins;

    $condition = "pl_code = '$name'";
    if ($binding_id > 0)
    {
        $condition .= " AND pl_id = $binding_id";
    }

    return sed_sql_update($db_plugins, $condition, array('active' => 0), 'pl_');
}

/**
 * Removes a plugin or one of its parts from hook registry
 *
 * @param string $name Module or plugin name
 * @param int $binding_id ID of the binding to remove or 0 to remove all
 * @return int Number of bindings removed
 */
function sed_plugin_remove($name, $binding_id = 0)
{
    global $db_plugins;

    $condition = "pl_code = '$name'";
    if ($binding_id > 0)
    {
        $condition .= " AND pl_id = $binding_id";
    }

    return sed_sql_delete($db_plugins, $condition);
}

/**
 * Resumes a suspended plugin or one of its parts
 *
 * @param string $name Module or plugin name
 * @param int $binding_id ID of the binding to resume or 0 to resume all
 * @return int Number of bindings resumed
 */
function sed_plugin_resume($name, $binding_id = 0)
{
    global $db_plugins;

    $condition = "pl_code = '$name'";
    if ($binding_id > 0)
    {
        $condition .= " AND pl_id = $binding_id";
    }

    return sed_sql_update($db_plugins, $condition, array('active' => 1), 'pl_');
}

?>
