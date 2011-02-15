<?php

/**
 * Plugin and Module Management API
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
require_once cot_incfile('auth');
require_once cot_incfile('configuration');
require_once cot_langfile('admin', 'core');

/**
 * A value returned by cot_extension_install() when updating and
 * there is nothing to update
 */
define('COT_EXT_NOTHING_TO_UPDATE', 2);

/**
 * Default plugin part execution priority
 */
define('COT_PLUGIN_DEFAULT_ORDER', 10);

/**
 * These parts ($name.$part.php) are reserved handlers with no hooks
 */
$cot_ext_ignore_parts = array('configure', 'install', 'setup', 'uninstall');

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
function cot_apply_patches($directory, $from_ver,
	$sql_pattern = '(sql)_([\w\.\-\_]+)\.sql',
	$php_pattern = '(php)_([\w\.\-\_]+)\.inc')
{
	global $L, $db;

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
			$error = $db->runScript(file_get_contents($val['sql']));
			if (empty($error))
			{
				cot_message(cot_rc('ext_patch_applied',
					array('f' => $val['sql'], 'msg' => 'OK')));
			}
			else
			{
				cot_error(cot_rc('ext_patch_error',
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
				cot_message(cot_rc('ext_patch_applied',
					array('f' => $val['php'], 'msg' => $msg)));
			}
			else
			{
				cot_error(cot_rc('ext_patch_error',
					array('f' => $val['php'], 'msg' => $L['Error'])));
				return false;
			}
		}
		$max_ver = $key;
	}

	return $max_ver;
}

/**
 * Checks if all dependencies for selected extension are satisfied. It means
 * that either all required modules and plugins are already installed or
 * selected for installation.
 *
 * Unsatisfied requirements messages are emitted with error & messaging API.
 *
 * @param string $name Extension code
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @param array $selected_modules A list of modules currently in selection
 * @param array $selected_plugins A list of plugins currently in selection
 * @return bool TRUE if all dependencies are satisfied, or FALSE otherwise
 */
function cot_extension_dependencies_statisfied($name, $is_module = false,
	$selected_modules = array(), $selected_plugins = array())
{
	global $cfg, $L;
	$path = $is_module ? $cfg['modules_dir'] . "/$name" : $cfg['plugins_dir'] . "/$name";
	$ret = true;

	// Get the dependency list
	$info = cot_infoget("$path/$name.setup.php", 'COT_EXT');
	$required_modules = empty($info['Requires_modules']) ? array()
		: explode(',', $info['Requires_modules']);
	$required_modules = array_map('trim', $required_modules);
	$required_plugins = empty($info['Requires_plugins']) ? array()
		: explode(',', $info['Requires_plugins']);
	$required_plugins = array_map('trim', $required_plugins);

	// Check each dependency
	foreach ($required_modules as $req_ext)
	{
		if (!empty($req_ext) && !in_array($req_ext, $selected_modules)
			&& !cot_module_installed($req_ext))
		{
			cot_error(cot_rc('ext_dependency_error', array(
				'name' => $name,
				'type' => $is_module ? $L['Module'] : $L['Plugin'],
				'dep_type' => $L['Module'],
				'dep_name' => $req_ext
			)));
			$ret = false;
		}
	}

	foreach ($required_plugins as $req_ext)
	{
		if (!empty($req_ext) && !in_array($req_ext, $selected_plugins)
			&& !cot_plugin_installed($req_ext))
		{
			cot_error(cot_rc('ext_dependency_error', array(
				'name' => $name,
				'type' => $is_module ? $L['Module'] : $L['Plugin'],
				'dep_type' => $L['Plugin'],
				'dep_name' => $req_ext
			)));
			$ret = false;
		}
	}

	return $ret;
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
function cot_extension_install($name, $is_module = false, $update = false)
{
    global $cfg, $L, $cache, $usr, $db_auth, $db_config, $db_users,
		$db_updates, $db_core, $cot_groups, $cot_ext_ignore_parts, $db, $db_x;

    $path = $is_module ? $cfg['modules_dir'] . "/$name" : $cfg['plugins_dir'] . "/$name";

    // Check setup file and tags
    $setup_file = $path . "/$name.setup.php";
    if (!file_exists($setup_file))
    {
        cot_error('ext_setup_not_found');
        return false;
    }

	$old_ext_format = false;

    $info = cot_infoget($setup_file, 'COT_EXT');
	if (!$info && cot_plugin_active('genoa'))
	{
		// Try load old format info
		$info = cot_infoget($setup_file, 'SED_EXTPLUGIN');
		if ($info)
		{
			$old_ext_format = true;
		}
	}
    if ($info === false)
    {
        cot_error('ext_invalid_format');
        return false;
    }

	// Check versions
	if ($is_module)
	{
		$res = $db->query("SELECT ct_version FROM $db_core
			WHERE ct_code = '$name'");
	}
	else
	{
		$res = $db->query("SELECT upd_value FROM $db_updates
			WHERE upd_param = '$name.ver'");
	}
	if ($res->rowCount() == 1)
	{
		$current_ver = $res->fetchColumn();
		$res->closeCursor();
		if ($update)
		{
			if (version_compare($current_ver, $info['Version']) == 0)
			{
				// Nothing to update
				cot_message(cot_rc('ext_up2date', array(
					'type' => $is_module ? $L['Module'] : $L['Plugin'],
					'name' => $name
				)));
				return COT_EXT_NOTHING_TO_UPDATE;
			}
		}
		else
		{
			cot_error('ext_already_installed');
			return false;
		}
	}

	if ($update)
	{
		// Safely drop existing bindings
		$bindings_cnt = cot_plugin_remove($name);
		cot_message(cot_rc('ext_bindings_uninstalled', array('cnt' => $bindings_cnt)));
	}
    // Install hook parts and bindings
    $hook_bindings = array();
    $dp = opendir($path);
    while ($f = readdir($dp))
    {
        if (preg_match("#^$name(\.([\w\.]+))?.php$#", $f, $mt)
			&& !in_array($mt[2], $cot_ext_ignore_parts))
        {
            $part_info = cot_infoget($path . "/$f", 'COT_EXT');
			if (!$part_info && cot_plugin_active('genoa'))
			{
				// Try to load old format info
				$part_info = cot_infoget($path . "/$f", 'SED_EXTPLUGIN');
			}
            if ($part_info)
            {
                if (empty($part_info['Hooks']))
                {
                    $hooks = $is_module ? array('module') : array('standalone');
                }
                else
                {
                    $hooks = explode(',', $part_info['Hooks']);
					$hooks = is_array($hooks) ? array_map('trim', $hooks) : array();
                }
				if (empty($part_info['Order']))
				{
					$order = COT_PLUGIN_DEFAULT_ORDER;
				}
				else
				{
					$order = array_map('trim', explode(',', $part_info['Order']));
					if (count($order) == 1)
					{
						$order = (int) $order[0];
					}
				}
				$i = 0;
                foreach ($hooks as $hook)
                {
                    $hook_bindings[] = array(
                        'part' => empty($mt[2]) ? 'main' : $mt[2],
						'file' => $f,
                        'hook' => $hook,
                        'order' => isset($order[$i]) ? (int) $order[$i] : $order
                    );
					++$i;
                }
            }
        }
    }
    closedir($dp);
    $bindings_cnt = cot_plugin_add($hook_bindings, $name, $info['Name'], $is_module);
    cot_message(cot_rc('ext_bindings_installed', array('cnt' => $bindings_cnt)));

    // Install config
    $info_cfg = cot_infoget($setup_file, 'COT_EXT_CONFIG');
	if (!$info_cfg && cot_plugin_active('genoa'))
	{
		// Try to load old format config
		$info_cfg = cot_infoget($setup_file, 'SED_EXTPLUGIN_CONFIG');
	}
    $options = cot_config_parse($info_cfg, $is_module);

	if ($update)
	{
		// Get differential config
		if (cot_config_update($name, $options, $is_module) > 0)
		{
			cot_message('ext_config_updated');
		}
	}
	elseif (count($options) > 0)
	{
		if (cot_config_add($name, $options, $is_module))
		{
			cot_message('ext_config_installed');
		}
		else
		{
			cot_error('ext_config_error');
			return false;
		}
	}

	// Install structure config if present
	$info_cfg = cot_infoget($setup_file, 'COT_EXT_CONFIG_STRUCTURE');
	if ($info_cfg)
	{
		$options = cot_config_parse($info_cfg, $is_module);
		if ($update)
		{
			if (cot_config_update($name, $options, $is_module, '__default') > 0)
			{
				// Update all nested categories
				$type = $is_module ? 'module' : 'plug';
				$res = $db->query("SELECT DISTINCT config_subcat FROM $db_config
					WHERE config_owner = '$type' AND config_cat = '$name'
						AND config_subcat != '' AND config_subcat != '__default'");
				$cat_list = $res->fetchAll(PDO::FETCH_COLUMN, 0);
				foreach ($cat_list as $cat)
				{
					cot_config_update($name, $options, $is_module, $cat);
				}
				cot_message('ext_config_struct_updated');
			}
		}
		elseif (count($options) > 0)
		{
			if (cot_config_add($name, $options, $is_module, '__default'))
			{
				cot_message('ext_config_struct_installed');
			}
			else
			{
				cot_error('ext_config_struct_error');
				return false;
			}
		}
	}

	if ($update)
	{
		// Only update auth locks
		$lock_guests = cot_auth_getvalue($info['Lock_guests']);
		$db->update($db_auth, array('auth_rights_lock' => $lock_guests), 'auth_groupid = ' . COT_GROUP_GUESTS
				. ' OR auth_groupid = ' . COT_GROUP_INACTIVE);

		$lock_members = cot_auth_getvalue($info['Lock_members']);
		$ingore_groups = implode(',', array(
			COT_GROUP_GUESTS,
			COT_GROUP_INACTIVE,
			COT_GROUP_BANNED,
			COT_GROUP_SUPERADMINS
		));
		$db->update($db_auth, array('auth_rights_lock' => $lock_members),
			"auth_groupid NOT IN ($ingore_groups)");

		cot_message('ext_auth_locks_updated');
	}
	else
	{
		// Install auth
		$insert_rows = array();
		foreach ($cot_groups as $k => $v)
		{
			if ($v['id'] == COT_GROUP_GUESTS || $v['id'] == COT_GROUP_INACTIVE)
			{
				$ins_auth = cot_auth_getvalue($info['Auth_guests']);
				$ins_lock = cot_auth_getvalue($info['Lock_guests']);

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
				$ins_auth = cot_auth_getvalue($info['Auth_members']);
				$ins_lock = cot_auth_getvalue($info['Lock_members']);
			}

			if ($is_module)
			{
				$insert_rows[] = array(
					'auth_groupid' => $v['id'],
					'auth_code' => $name,
					'auth_option' => 'a',
					'auth_rights' => $ins_auth,
					'auth_rights_lock' => $ins_lock,
					'auth_setbyuserid' => $usr['id']
				);
			}
			else
			{
				$insert_rows[] = array(
					'auth_groupid' => $v['id'],
					'auth_code' => 'plug',
					'auth_option' => $name,
					'auth_rights' => $ins_auth,
					'auth_rights_lock' => $ins_lock,
					'auth_setbyuserid' => $usr['id']
				);
			}
		}
		if ($db->insert($db_auth, $insert_rows))
		{
			$db->update($db_users, array('user_auth' => ''));
			cot_message('ext_auth_installed');
		}
	}


	if ($update)
	{
		// Find and apply patches
		if (file_exists("$path/setup"))
		{
			$new_ver = cot_apply_patches("$path/setup", $current_ver);
		}
		if (version_compare($info['Version'], $new_ver) > 0 || $new_ver === true)
		{
			$new_ver = $info['Version'];
		}
	}
	else
	{
		if (file_exists($path . "/setup/$name.install.sql"))
		{
			// Run SQL install script
			$sql_err = $db->runScript(
				file_get_contents("$path/setup/$name.install.sql"));
			if (empty($sql_err))
			{
				cot_message(cot_rc('ext_executed_sql', array('ret' => 'OK')));
			}
			else
			{
				cot_error(cot_rc('ext_executed_sql', array('ret' => $sql_err)));
				return false;
			}
		}

		$install_handler = $old_ext_format ? $setup_file : $path . "/setup/$name.install.php";

		if ($old_ext_format)
		{
			$action = 'install';
		}

		if (file_exists($install_handler))
		{
			// Run PHP install handler
			$ret = include $install_handler;
			if ($ret !== false)
			{
				$msg = $ret == 1 ? 'OK' : $ret;
				cot_message(cot_rc('ext_executed_php', array('ret' => $msg)));
			}
			else
			{
				cot_error(cot_rc('ext_executed_php',
					array('ret' => $L['Error'])));
				return false;
			}
		}
	}

	// Register version information
	if ($is_module)
	{
		if ($update)
		{
			cot_module_update($name, $new_ver);
			cot_message(cot_rc('ext_updated', array(
				'type' => $L['Module'],
				'name' => $name,
				'ver' => $new_ver
			)));
		}
		else
		{
			cot_module_add($name, $info['Name'], $info['Version']);
		}
	}
    else
    {
        if ($update)
		{
			$db->query("INSERT INTO $db_updates (upd_param, upd_value) VALUES('$name.ver', ?)
				ON DUPLICATE KEY UPDATE upd_value = ?", array($new_ver, $new_ver));
			cot_message(cot_rc('ext_updated', array(
				'type' => $L['Plugin'],
				'name' => $name,
				'ver' => $new_ver
			)));
		}
		else
		{
			$db->insert($db_updates, array('upd_param' => "$name.ver",
				'upd_value' => $info['Version']));
		}
    }

    // Cleanup
    cot_auth_reorder();
    $cache && $cache->clear();

    return true;
}

/**
 * Uninstalls an extension and removes all its data
 * @param string $name Extension code
 * @param bool $is_module TRUE for modules, FALSE for plugins
 */
function cot_extension_uninstall($name, $is_module = false)
{
    global $cfg, $db_auth, $db_config, $db_users, $db_updates, $cache, $db, $db_x;

    $path = $is_module ? $cfg['modules_dir'] . "/$name" : $cfg['plugins_dir']
		. "/$name";

    // Remove bindings
    cot_plugin_remove($name);

    // Drop auth and config
    if ($is_module)
    {
        $db->delete($db_config, "config_owner = 'module'
			AND config_cat = '$name'");
        $db->delete($db_auth, "auth_code = '$name'");
    }
    else
    {
        $db->delete($db_config, "config_owner = 'plug'
			AND config_cat = '$name'");
        $db->delete($db_auth, "auth_code = 'plug'
			AND auth_option = '$name'");
    }
    cot_message('ext_auth_uninstalled');
    cot_message('ext_config_uninstalled');

    // Run SQL script if present
    if (file_exists($path . "/setup/$name.uninstall.sql"))
    {
        $sql_err = $db->runScript(
			file_get_contents("$path/setup/$name.uninstall.sql"));
        if (empty($sql_err))
        {
            cot_message(cot_rc('ext_executed_sql', array('ret' => 'OK')));
        }
        else
        {
            cot_error(cot_rc('ext_executed_sql', array('ret' => $sql_err)));
        }
    }

    // Run handler part
	if (cot_plugin_active('genoa') && cot_infoget($path . "/$name.setup.php", 'SED_EXTPLUGIN'))
	{
		$action = 'uninstall';
		$uninstall_handler = $path . "/$name.setup.php";
	}
	else
	{
		$uninstall_handler = $path . "/setup/$name.uninstall.php";
	}

    if (file_exists($uninstall_handler))
    {
        $ret = include $uninstall_handler;
        if ($ret !== false)
        {
            cot_message(cot_rc('ext_executed_php', array('ret' => $ret)));
        }
        else
        {
            cot_error(cot_rc('ext_executed_php', array('ret' => $L['Error'])));
        }
    }

    if ($is_module)
	{
		// Unregister from modules table
		cot_module_remove($name);
	}
	else
    {
        // Unregister from updates table
        $db->delete($db_updates, "upd_param = '$name.ver'");
    }

	// Clear cache
	$db->update($db_users, array('user_auth' => ''));
	$cache && $cache->clear();
}

/**
 * Parses PHPDoc file header into an array
 *
 * @param string $filename Path to a PHP file
 * @return array Associative array containing PHPDoc contents. The array is
 *  empty if no PHPDoc was found
 */
function cot_file_phpdoc($filename)
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
					mb_substr($phpdoc[$i], $delim + 1)));
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
function cot_infoget($file, $limiter = 'COT_EXT', $maxsize = 32768)
{
    global $L;
    $result = array();

	$fp = @fopen($file, 'r');
    if ($fp)
    {
        $limiter_begin = '[BEGIN_' . $limiter . ']';
        $limiter_end = '[END_' . $limiter . ']';
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
                $linex = explode('=', $line);
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
function cot_module_add($name, $title, $version = '1.0.0')
{
    global $db, $db_core;

    $res = $db->insert($db_core, array('ct_code' => $name, 'ct_title' => $title,
		'ct_version' => $version));

    return false;
}

/**
 * Checks if module is already installed
 *
 * @param string $name Module code
 * @return bool
 */
function cot_module_installed($name)
{
    global $db, $db_core, $cfg;

    $cnt = $db->query("SELECT COUNT(*) FROM $db_core
		WHERE ct_code = '$name'")->fetchColumn();
    return $cnt > 0 && file_exists($cfg['modules_dir'] . '/' . $name);
}

/**
 * Suspends (temporarily disables) a module
 *
 * @param string $name Module name
 * @return bool
 */
function cot_module_pause($name)
{
    global $db, $db_core;

    return $db->update($db_core, array('ct_state' => 0), "ct_code = '$name'") == 1;
}

/**
 * Unregisters a module from the core
 *
 * @param string $name Module name
 * @return bool
 */
function cot_module_remove($name)
{
    global $db, $db_core, $db_updates;

    return $db->delete($db_core, "ct_code = '$name'");
}

/**
 * Resumes a paused module
 *
 * @param string $name Module name
 * @return bool
 */
function cot_module_resume($name)
{
    global $db, $db_core;

    return $db->update($db_core, array('ct_state' => 1), "ct_code = '$name'") == 1;
}

/**
 * Updates module version number in the registry
 *
 * @param string $name Module name
 * @param string $version New version string
 * @return bool
 */
function cot_module_update($name, $version)
{
    global $db, $db_core;

    return $db->update($db_core, array('ct_version' => $version), "ct_code = '$name'");
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
 * cot_plugin_add($hook_bindings, 'test', 'Test plugin', false);
 * </code>
 *
 * @param array $hook_bindings Hook binding map
 * @param string $name Module or plugin name (code)
 * @param string $title Module or plugin title
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @return int Number of records added
 */
function cot_plugin_add($hook_bindings, $name, $title, $is_module = false)
{
    global $db, $db_plugins, $cfg;

    if (empty($title))
    {
		$title = $name;
	}
    $path = $is_module ? $cfg['modules_dir'] . "/$name" : $cfg['plugins_dir'] . "/$name";

    $insert_rows = array();
    foreach ($hook_bindings as $binding)
    {
        $insert_rows[] = array(
            'pl_hook' => $binding['hook'],
            'pl_code' => $name,
            'pl_part' => $binding['part'],
            'pl_title' => $title,
            'pl_file' => empty($binding['file']) ? "$path/$name.{$binding['part']}.php" : $path . '/' . $binding['file'],
            'pl_order' => $binding['order'],
            'pl_active' => 1,
            'pl_module' => (int) $is_module
        );
    }
    return $db->insert($db_plugins, $insert_rows);
}

/**
 * Checks if plugin is already installed
 *
 * @param string $name Plugin code
 * @return bool
 */
function cot_plugin_installed($name)
{
    global $db, $db_plugins, $cfg;

    $cnt = $db->query("SELECT COUNT(*) FROM $db_plugins
		WHERE pl_code = '$name'")->fetchColumn();
    return $cnt > 0 && file_exists($cfg['plugins_dir'] . '/' . $name);
}

/**
 * Suspends a plugin or one of its parts
 *
 * @param string $name Module or plugin name
 * @param int $binding_id ID of the binding to supsend or 0 to suspend all
 * @return int Number of bindings suspended
 */
function cot_plugin_pause($name, $binding_id = 0)
{
    global $db, $db_plugins;

    $condition = "pl_code = '$name'";
    if ($binding_id > 0)
    {
        $condition .= " AND pl_id = $binding_id";
    }

    return $db->update($db_plugins, array('pl_active' => 0), $condition);
}

/**
 * Removes a plugin or one of its parts from hook registry
 *
 * @param string $name Module or plugin name
 * @param int $binding_id ID of the binding to remove or 0 to remove all
 * @return int Number of bindings removed
 */
function cot_plugin_remove($name, $binding_id = 0)
{
    global $db, $db_plugins;

    $condition = "pl_code = '$name'";
    if ($binding_id > 0)
    {
        $condition .= " AND pl_id = $binding_id";
    }

    return $db->delete($db_plugins, $condition);
}

/**
 * Resumes a suspended plugin or one of its parts
 *
 * @param string $name Module or plugin name
 * @param int $binding_id ID of the binding to resume or 0 to resume all
 * @return int Number of bindings resumed
 */
function cot_plugin_resume($name, $binding_id = 0)
{
    global $db, $db_plugins;

    $condition = "pl_code = '$name'";
    if ($binding_id > 0)
    {
        $condition .= " AND pl_id = $binding_id";
    }

    return $db->update($db_plugins, array('pl_active' => 1), $condition);
}

?>
