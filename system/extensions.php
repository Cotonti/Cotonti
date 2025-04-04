<?php

/**
 * Plugin and Module Management API
 *
 * @package API - Extensions
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\extensions\ExtensionsControlService;
use cot\extensions\ExtensionsDictionary;
use cot\extensions\ExtensionsService;

defined('COT_CODE') or die('Wrong URL');

// Requirements
require_once cot_incfile('auth');
require_once cot_incfile('configuration');
require_once cot_langfile('admin', 'core');

/**
 * A value returned by cot_extension_install() when updating and
 * there is nothing to update
 */
const COT_EXT_NOTHING_TO_UPDATE = 2;

/**
 * Default plugin part execution priority
 */
const COT_PLUGIN_DEFAULT_ORDER = 10;

/**
 * These parts ($name.$part.php) are reserved handlers with no hooks
 */
$cot_ext_ignore_parts = ['configure', 'install', 'setup', 'uninstall'];

/**
 * Applies custom SQL and PHP patches in a directory. Error and success
 * messages are emitted via standard messaging API during execution.
 *
 * @param string $directory Directory path
 * @param string $from_ver Current version, to patch starting from
 * @param string $sql_pattern SQL patch file name pattern (PCRE)
 * @param string $php_pattern PHP patch file name pattern (PCRE)
 * @return string|bool The function returns TRUE if there are not patches to apply,
 * FALSE if an error occurred while patching or a string containing version
 * number of the latest applied patch if patching was successful.
 */
function cot_apply_patches(
    $directory,
    $from_ver,
	$sql_pattern = 'patch_([\w\.\-\_]+)\.(sql)',
	$php_pattern = 'patch_([\w\.\-\_]+)\.(inc)'
)
{
    global $L, $Ls, $cfg, $R, $db;

	// Find new patches
	$dp = opendir($directory);
	$delta = [];
	while ($f = readdir($dp)) {
		if (
            preg_match('#^' . $sql_pattern . '$#', $f, $mt)
			|| preg_match('#^' . $php_pattern . '$#', $f, $mt)
        ) {
			$type = $mt[2] == 'sql' ? 'sql' : 'php';
			$ver = $mt[1];
			if (version_compare($ver, $from_ver) > 0 && !isset($delta[$ver][$type])) {
				$delta[$ver][$type] = $directory . '/' . $f;
			}
		}
	}
	closedir($dp);
	if (count($delta) == 0) {
		return true;
	}

	// Apply patches in version order
	uksort($delta, 'version_compare');

	$max_ver = $from_ver;
    static $executed = [];
	foreach ($delta as $key => $val) {
		if (isset($val['sql']) && !in_array($val['sql'], $executed)) {
			$error = Cot::$db->runScript(file_get_contents($val['sql']));
            $executed[] = $val['sql'];
			if (empty($error)) {
				cot_message(cot_rc('ext_patch_applied',
					['f' => $val['sql'], 'msg' => 'OK']));
			} else {
				cot_error(cot_rc('ext_patch_error', ['f' => $val['sql'], 'msg' => $error]));
				return false;
			}
		}
		if (isset($val['php']) && !in_array($val['php'], $executed)) {
			$ret = include $val['php'];
            $executed[] = $val['php'];
			if ($ret !== false) {
				$msg = $ret == 1 ? 'OK' : $ret;
				cot_message(cot_rc('ext_patch_applied', ['f' => $val['php'], 'msg' => $msg]));
			} else {
				cot_error(cot_rc('ext_patch_error', ['f' => $val['php'], 'msg' => $L['Error']]));
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
	$selected_modules = [], $selected_plugins = [])
{
	global $cfg, $L;
	$path = $is_module ? $cfg['modules_dir'] . "/$name" : $cfg['plugins_dir'] . "/$name";
	$ret = true;

	// Get the dependency list
	$info = cot_infoget("$path/$name.setup.php", 'COT_EXT');
	$required_modules = empty($info['Requires_modules']) ? []
		: explode(',', $info['Requires_modules']);
	$required_modules = array_map('trim', $required_modules);
	$required_plugins = empty($info['Requires_plugins']) ? []
		: explode(',', $info['Requires_plugins']);
	$required_plugins = array_map('trim', $required_plugins);

	// Check each dependency
	foreach ($required_modules as $req_ext) {
		if (!empty($req_ext) && !in_array($req_ext, $selected_modules)
			&& !cot_extension_installed($req_ext))
		{
			cot_error(cot_rc('ext_dependency_error', [
				'name' => $name,
				'type' => $is_module ? $L['Module'] : $L['Plugin'],
				'dep_type' => $L['Module'],
				'dep_name' => $req_ext
			]));
			$ret = false;
		}
	}

	foreach ($required_plugins as $req_ext) {
		if (!empty($req_ext) && !in_array($req_ext, $selected_plugins)
			&& !cot_extension_installed($req_ext))
		{
			cot_error(cot_rc('ext_dependency_error', [
				'name' => $name,
				'type' => $is_module ? $L['Module'] : $L['Plugin'],
				'dep_type' => $L['Plugin'],
				'dep_name' => $req_ext
			]));
			$ret = false;
		}
	}

	return $ret;
}

/**
 * Installs or updates a Cotonti extension: module or plugin.
 * Messages emitted during installation can be received through standard
 * Cotonti messages interface.
 * @param string $extensionCode Module (or plugin) code
 * @param bool $isModule TRUE for modules, FALSE for plugins
 * @param bool $update Perform update rather than new install
 * @param bool $force_update Forces extension update even if version has not changed
 * @return bool Operation status
 * @global Cache $cache
 *
 * @todo move ExtensionControlService or to separate ExtensionSetupService
 */
function cot_extension_install($extensionCode, $isModule = false, $update = false, $force_update = false)
{
	global $cfg, $L, $R, $cache, $usr, $db_auth, $db_config, $db_users,
		$db_core, $cot_groups, $cot_ext_ignore_parts, $db, $db_x, $env;

    /** @deprecated in 0.9.24 for backward compatibility */
    $is_module = $isModule;
    /** @deprecated in 0.9.24 for backward compatibility */
    $name = $extensionCode;

	$path = $isModule ? Cot::$cfg['modules_dir'] . "/$extensionCode" : Cot::$cfg['plugins_dir'] . "/$extensionCode";

	// Emit initial message
	if ($update) {
		cot_message(cot_rc('ext_updating', [
			'type' => $isModule ? Cot::$L['Module'] : Cot::$L['Plugin'],
			'name' => $extensionCode,
		]));
	} else {
		cot_message(cot_rc('ext_installing', [
			'type' => $isModule ? $L['Module'] : $L['Plugin'],
			'name' => $extensionCode,
		]));
	}

	// Check setup file and tags
	$setup_file = $path . "/$extensionCode.setup.php";
	if (!file_exists($setup_file)) {
		cot_error(cot_rc('ext_setup_not_found', ['path' => $setup_file]));
		return false;
	}

	$old_ext_format = false;

	$info = cot_infoget($setup_file, 'COT_EXT');
	if (!$info && cot_plugin_active('genoa')) {
		// Try load old format info
		$info = cot_infoget($setup_file, 'SED_EXTPLUGIN');
		if ($info) {
			$old_ext_format = true;
		}
	}

	if ($info === false) {
		cot_error('ext_invalid_format');
		return false;
	}

	// Check versions
	$res = Cot::$db->query('SELECT ct_version FROM ' . Cot::$db->core . ' WHERE ct_code = ?', $extensionCode);
	if ($res->rowCount() == 1) {
		$current_ver = $res->fetchColumn();
		$res->closeCursor();

		if ($update) {
			if (version_compare($current_ver, $info['Version']) == 0 && !$force_update) {
				// Nothing to update
				cot_message(cot_rc('ext_up2date', [
					'type' => $isModule ? Cot::$L['Module'] :Cot::$L['Plugin'],
					'name' => $extensionCode
				]));

				return COT_EXT_NOTHING_TO_UPDATE;
			}
		} else {
			cot_clear_messages();
			cot_error(cot_rc('ext_already_installed', ['name' => $extensionCode]));
			return false;
		}
	}

    $registeredParts = [];
	if ($update) {
        $query = $query = Cot::$db->query(
            'SELECT * FROM ' . Cot::$db->plugins . ' WHERE pl_code = :code',
            ['code' => $extensionCode]
        );
        while ($row = $query->fetch()) {
            if (!isset($registeredParts[$row['pl_part']]['hooks'])) {
                $registeredParts[$row['pl_part']]['hooks'] = [];
            }
            $registeredParts[$row['pl_part']]['hooks'][$row['pl_hook']] = [
                'hook' => $row['pl_hook'],
                'order' => (int) $row['pl_order'],
            ];
            unset($row['pl_hook'], $row['pl_order']);
            $registeredParts[$row['pl_part']] = array_merge($registeredParts[$row['pl_part']], $row);
        }
        $query->closeCursor();

		// Safely drop existing bindings
		$bindings_cnt = cot_plugin_remove($extensionCode);
		cot_message(cot_rc('ext_bindings_uninstalled', ['cnt' => $bindings_cnt]));
	}

	// Install hook parts and bindings
	$hookBindings = [];
	$dp = opendir($path);
	while ($f = readdir($dp)) {
		if (
            preg_match("#^$extensionCode(\.([\w\.]+))?.php$#", $f, $mt)
			&& (!isset($mt[2]) || !in_array($mt[2], $cot_ext_ignore_parts))
        ) {
			$part_info = cot_infoget($path . "/$f", 'COT_EXT');
			if (!$part_info && cot_plugin_active('genoa')) {
				// Try to load old format info
				$part_info = cot_infoget($path . "/$f", 'SED_EXTPLUGIN');
			}
			if ($part_info) {
				if (empty($part_info['Hooks'])) {
					$hooks = $isModule ? ['module'] : ['standalone'];
				} else {
					$hooks = explode(',', $part_info['Hooks']);
					$hooks = is_array($hooks) ? array_map('trim', $hooks) : [];
				}
				if (empty($part_info['Order'])) {
					$order = COT_PLUGIN_DEFAULT_ORDER;
				} else {
					$order = array_map('trim', explode(',', $part_info['Order']));
					if (count($order) == 1 || count($order) < count($hooks)) {
						$order = (int) $order[0];
					}
				}

				$i = 0;
				foreach ($hooks as $hook) {
                    $hookBinding = [
                        'part' => !isset($mt[2]) ? 'main' : $mt[2],
                        'file' => $f,
                        'hook' => $hook,
                        'order' => isset($order[$i]) ? (int) $order[$i] : $order,
                    ];
                    if (isset($registeredParts[$hookBinding['part']])) {
                        $hookBinding['active'] = $registeredParts[$hookBinding['part']]['pl_active'];
                    }
					$hookBindings[] = $hookBinding;
					++$i;
				}
			}
		}
	}

	closedir($dp);
	$bindings_cnt = cot_plugin_add($hookBindings, $extensionCode, $info['Name'], $isModule);
	cot_message(cot_rc('ext_bindings_installed', ['cnt' => $bindings_cnt]));

	// Install config
	$info_cfg = cot_infoget($setup_file, 'COT_EXT_CONFIG');
	if (!$info_cfg && cot_plugin_active('genoa')) {
		// Try to load old format config
		$info_cfg = cot_infoget($setup_file, 'SED_EXTPLUGIN_CONFIG');
	}
	$options = cot_config_parse($info_cfg, $isModule);

	if ($update) {
		// Get differential config
		if (cot_config_update($extensionCode, $options, $isModule) > 0) {
			cot_message('ext_config_updated');
		}
	} elseif (count($options) > 0) {
		if (cot_config_add($extensionCode, $options, $isModule)) {
			cot_message('ext_config_installed');
		} else {
			cot_error('ext_config_error');
			return false;
		}
	}

	// Install structure config if present
	$info_cfg = cot_infoget($setup_file, 'COT_EXT_CONFIG_STRUCTURE');
	if ($info_cfg) {
		$options = cot_config_parse($info_cfg, $isModule);
		if ($update) {
			if (cot_config_update($extensionCode, $options, $isModule, '__default') > 0) {
				// Update all nested categories
				$type = $isModule ? 'module' : 'plug';
				$res = $db->query("SELECT DISTINCT config_subcat FROM $db_config
					WHERE config_owner = '$type' AND config_cat = '$extensionCode'
						AND config_subcat != '' AND config_subcat != '__default'");
				$cat_list = $res->fetchAll(PDO::FETCH_COLUMN, 0);
				foreach ($cat_list as $cat) {
					cot_config_update($extensionCode, $options, $isModule, $cat);
				}
				cot_message('ext_config_struct_updated');
			}
		} elseif (count($options) > 0) {
			if (cot_config_add($extensionCode, $options, $isModule, '__default')) {
				cot_message('ext_config_struct_installed');
			} else {
				cot_error('ext_config_struct_error');
				return false;
			}
		}
	}

    // Install / Update Auth
    if (!isset($info['Auth_guests'])) {
        $info['Auth_guests'] = '';
    }
    if (!isset($info['Lock_guests'])) {
        $info['Lock_guests'] = '';
    }
    if (!isset($info['Auth_members'])) {
        $info['Auth_members'] = '';
    }
    if (!isset($info['Lock_members'])) {
        $info['Lock_members'] = '';
    }
	if ($update) {
		// Only update auth locks
		if ($isModule) {
			$auth_code = $extensionCode;
			$auth_option = 'a';
		} else {
			$auth_code = 'plug';
			$auth_option = $extensionCode;
		}

		$lock_guests = cot_auth_getvalue($info['Lock_guests']);
		$db->update($db_auth, ['auth_rights_lock' => $lock_guests], "
			auth_code = '$auth_code' AND auth_option = '$auth_option'
			AND (auth_groupid = " . COT_GROUP_GUESTS
				. ' OR auth_groupid = ' . COT_GROUP_INACTIVE . ')');

		$lock_members = cot_auth_getvalue($info['Lock_members']);
		$ingore_groups = implode(',', [
			COT_GROUP_GUESTS,
			COT_GROUP_INACTIVE,
			COT_GROUP_BANNED,
			COT_GROUP_SUPERADMINS
		]);
		$db->update($db_auth, ['auth_rights_lock' => $lock_members],
			"auth_code = '$auth_code' AND auth_option = '$auth_option' AND auth_groupid NOT IN ($ingore_groups)");

		cot_message('ext_auth_locks_updated');

    } else {
		// Install auth
		$insert_rows = [];
		foreach ($cot_groups as $v) {
            $v['skiprights'] = isset($v['skiprights']) ? $v['skiprights'] : false;
			if (!$v['skiprights']) {
				if ($v['id'] == COT_GROUP_GUESTS || $v['id'] == COT_GROUP_INACTIVE) {
					$ins_auth = cot_auth_getvalue($info['Auth_guests']);
					$ins_lock = cot_auth_getvalue($info['Lock_guests']);

					if ($ins_auth > 128 || $ins_lock < 128) {
						$ins_auth = ($ins_auth > 127) ? $ins_auth - 128 : $ins_auth;
						$ins_lock = 128;
					}
				} elseif ($v['id'] == COT_GROUP_BANNED) {
					$ins_auth = 0;
					$ins_lock = 255;

				} elseif ($v['id'] == COT_GROUP_SUPERADMINS) {
					$ins_auth = 255;
					$ins_lock = 255;

				} else {
					$ins_auth = cot_auth_getvalue($info['Auth_members']);
					$ins_lock = cot_auth_getvalue($info['Lock_members']);
				}

				if ($isModule) {
					$insert_rows[] = [
						'auth_groupid' => $v['id'],
						'auth_code' => $extensionCode,
						'auth_option' => 'a',
						'auth_rights' => $ins_auth,
						'auth_rights_lock' => $ins_lock,
						'auth_setbyuserid' => $usr['id']
					];
				} else {
					$insert_rows[] = [
						'auth_groupid' => $v['id'],
						'auth_code' => 'plug',
						'auth_option' => $extensionCode,
						'auth_rights' => $ins_auth,
						'auth_rights_lock' => $ins_lock,
						'auth_setbyuserid' => $usr['id']
					];
				}
			}
		}

		if ($db->insert($db_auth, $insert_rows)) {
			$db->update($db_users, ['user_auth' => ''], "user_auth != ''");
			cot_message('ext_auth_installed');
		}
	}


    $new_ver = '';
	if ($update) {
		// Find and apply patches
		if (file_exists("$path/setup")) {
			$new_ver = cot_apply_patches("$path/setup", $current_ver);
		}
		if (version_compare($info['Version'], $new_ver) > 0 || $new_ver === true) {
			$new_ver = $info['Version'];
		}
	} else {
		if (file_exists($path . "/setup/$extensionCode.install.sql")) {
			// Run SQL install script
			$sql_err = $db->runScript(
				file_get_contents("$path/setup/$extensionCode.install.sql"));
			if (empty($sql_err)) {
				cot_message(cot_rc('ext_executed_sql', ['ret' => 'OK']));
			} else {
				cot_error(cot_rc('ext_executed_sql', ['ret' => $sql_err]));
				return false;
			}
		}

		$install_handler = $old_ext_format ? $setup_file : $path . "/setup/$extensionCode.install.php";

		if ($old_ext_format) {
			global $action;
			$action = 'install';
		}

		if (file_exists($install_handler)) {
			// Run PHP install handler
			$envtmp = $env;
			$env = [
				'ext' => $extensionCode,
				'location' => $extensionCode,
				'type' => ($isModule) ? 'module' : 'plug',
			];
			$ret = include $install_handler;
			$env = $envtmp;

			if ($ret !== false) {
				$msg = $ret == 1 ? 'OK' : $ret;
				cot_message(cot_rc('ext_executed_php', ['ret' => $msg]));
			} else {
				cot_error(cot_rc('ext_executed_php',
					['ret' => $msg ? $msg : $L['Error']]));
				return false;
			}
		}
	}

	// Register version information
	if ($update) {
		cot_extension_update($extensionCode, $new_ver, !$isModule);

        ExtensionsControlService::getInstance()->checkIsActive($extensionCode);

		cot_message(cot_rc('ext_updated', [
			'type' => $isModule ? $L['Module'] : $L['Plugin'],
			'name' => $extensionCode,
			'ver' => $new_ver
		]));

        /* === Hook  === */
        foreach (cot_getextplugins('extension.update.done') as $pl) {
            include $pl;
        }
        /* ===== */

	} else {
		cot_extension_add($extensionCode, $info['Name'], $info['Version'], !$isModule);

        /* === Hook  === */
        foreach (cot_getextplugins('extension.install.done') as $pl) {
            include $pl;
        }
        /* ===== */
	}

	// Cleanup
	cot_auth_reorder();
	$cache && $cache->clear();

	return true;
}

/**
 * Uninstalls an extension and removes all its data
 * @param string $code Extension code
 * @param bool $isModule TRUE for modules, FALSE for plugins
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_extension_uninstall($code, $isModule = false)
{
	global $cfg, $db_auth, $db_config, $db_users, $db_updates, $cache, $db, $db_x, $db_plugins, $cot_plugins,
           $cot_plugins_enabled, $cot_modules, $env, $structure, $db_structure, $L, $R;

	$path = $isModule ? $cfg['modules_dir'] . "/$code" : $cfg['plugins_dir'] . "/$code";

	// Emit initial message
	cot_message(cot_rc('ext_uninstalling', [
		'type' => $isModule ? Cot::$L['Module'] : Cot::$L['Plugin'],
		'name' => $code
	]));

	// Remove bindings
	cot_plugin_remove($code);

	// Drop auth and config
	if ($isModule) {
        Cot::$db->delete($db_config, "config_owner = 'module' AND config_cat = '$code'");
        Cot::$db->delete($db_auth, "auth_code = '$code'");
	} else {
        Cot::$db->delete($db_config, "config_owner = 'plug' AND config_cat = '$code'");
        Cot::$db->delete($db_auth, "auth_code = 'plug' AND auth_option = '$code'");
	}
	cot_message('ext_auth_uninstalled');
	cot_message('ext_config_uninstalled');

	// Remove extension structure
	if ($isModule && isset($structure[$code])) {
		$db->delete($db_structure, "structure_area = ?", $code);
		unset($structure[$code]);
	}

	// Run SQL script if present
	if (file_exists($path . "/setup/$code.uninstall.sql")) {
		$sql_err = $db->runScript(
			file_get_contents("$path/setup/$code.uninstall.sql"));
		if (empty($sql_err)) {
			cot_message(cot_rc('ext_executed_sql', ['ret' => 'OK']));
		} else {
			cot_error(cot_rc('ext_executed_sql', ['ret' => $sql_err]));
		}
	}

	// Run handler part
	if (cot_plugin_active('genoa') && cot_infoget($path . "/$code.setup.php", 'SED_EXTPLUGIN')) {
		global $action;
		$action = 'uninstall';
		$uninstall_handler = $path . "/$code.setup.php";
	} else {
		$uninstall_handler = $path . "/setup/$code.uninstall.php";
	}

	if (file_exists($uninstall_handler)) {
		$envtmp = $env;
		$env = [
			'ext' => $code,
			'location' => $code,
			'type' => ($isModule) ? ExtensionsDictionary::TYPE_MODULE : ExtensionsDictionary::TYPE_PLUGIN,
		];
		$ret = include $uninstall_handler;
		$env = $envtmp;

		if ($ret !== false) {
			cot_message(cot_rc('ext_executed_php', ['ret' => $ret]));
		} else {
			cot_error(cot_rc('ext_executed_php', ['ret' => Cot::$L['Error']]));
		}
	}

	// Unregister from core table
	cot_extension_remove($code, !$isModule);

	$sql = $db->query("SELECT pl_code, pl_file, pl_hook, pl_module FROM $db_plugins
		WHERE pl_active = 1 ORDER BY pl_hook ASC, pl_order ASC");
	$cot_plugins = [];
	if ($sql->rowCount() > 0) {
		while ($row = $sql->fetch()) {
			$cot_plugins[$row['pl_hook']][] = $row;
		}
		$sql->closeCursor();
	}

	if (!$isModule) {
		unset($cot_plugins_enabled[$code]);
	} else {
		unset($cot_modules[$code]);
	}

    /* === Hook  === */
    foreach (cot_getextplugins('extension.uninstall.done') as $pl) {
        include $pl;
    }
    /* ===== */

	// Clear cache
	$db->update($db_users, ['user_auth' => ''], "user_auth != ''");
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
	$res = [];
	$data = file_get_contents($filename);
	if (preg_match('#^/\*\*(.*?)^\s\*/#ms', $data, $mt)) {
		$phpdoc = preg_split('#\r?\n\s\*\s@#', $mt[1]);
		$cnt = count($phpdoc);
		if ($cnt > 0) {
			$res['description'] = trim(preg_replace('#\r?\n\s\*\s?#', '',
				$phpdoc[0]));
			for ($i = 1; $i < $cnt; $i++) {
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
 * Extract info from COT file headers
 *
 * @param string $file File path
 * @param string $limiter Tag name
 * @param int $maxsize Max header size
 * @return array Array containing block data or FALSE on error
 */
function cot_infoget($file, $limiter = 'COT_EXT', $maxsize = 32768)
{
    $result = [];

    // Default data structure
    if ($limiter == 'COT_EXT' || $limiter == 'SED_EXTPLUGIN') {
        $result = [
            'Code' => '',
            'Name' => '',
            'Description' => '',
            'Category' => '',
            'Version' => '',
            'Date' => '',
            'Author' => '',
            'Copyright' => '',
            'Notes' => '',
            'Auth_members' => '',
            'Lock_members' => '',
            'Auth_guests' => '',
            'Lock_guests' => '',
        ];

        if ($limiter == 'COT_EXT') {
            $result['Requires_modules'] = '';
            $result['Requires_plugins'] = '';
            $result['Recommends_modules'] = '';
            $result['Recommends_plugins'] = '';
        }
    }

	$fp = @fopen($file, 'r');
	if ($fp) {
		$limiter_begin = '[BEGIN_' . $limiter . ']';
		$limiter_end = '[END_' . $limiter . ']';
		$data = fread($fp, $maxsize);
		$begin = mb_strpos($data, $limiter_begin);
		$end = mb_strpos($data, $limiter_end);

		if ($end > $begin && $begin > 0) {
			$lines = mb_substr($data, $begin + 8 + mb_strlen($limiter),
				$end - $begin - mb_strlen($limiter) - 8);
			$lines = explode("\n", $lines);

			foreach ($lines as $line) {
				$line = ltrim($line, " */");
				$linex = preg_split('/\s*\=\s*/', trim($line), 2);
				if ($linex[0]) {
					$result[$linex[0]] = isset($linex[1]) ? $linex[1] : '';
				}
			}

		} else {
			$result = false;
		}
	} else {
		$result = false;
	}
	@fclose($fp);

	return $result;
}

/**
 * Registers an extension in the core
 *
 * @param string $name Extension name (code)
 * @param string $title Title name
 * @param string $version Version number as A.B.C
 * @param bool $is_plug Is a plugin
 * @return bool TRUE on success, FALSE on error
 * @global CotDB $db
 */
function cot_extension_add($name, $title, $version = '1.0.0', $is_plug = false)
{
	global $db, $db_core;

	$res = $db->insert($db_core, ['ct_code' => $name, 'ct_title' => $title,
		'ct_version' => $version, 'ct_plug' => (int) $is_plug]);

	return $res > 0;
}

/**
 * Compares 2 extension info entries by category code.
 * post-install extensions are always last.
 *
 * @param array $ext1 Ext info 1
 * @param array $ext2 Ext info 2
 * @return int
 */
function cot_extension_catcmp($ext1, $ext2)
{
	global $L;
	if (isset($L['ext_cat_' . $ext1['Category']])) {
		$ext1['Category'] = $L['ext_cat_' . $ext1['Category']];
	}
	if (isset($L['ext_cat_' . $ext2['Category']])) {
		$ext2['Category'] = $L['ext_cat_' . $ext2['Category']];
	}
	if ($ext1['Category'] == $ext2['Category']) {
		// Compare by name
		if ($ext1['Name'] == $ext2['Name']) {
			return 0;
		} else {
			return ($ext1['Name'] > $ext2['Name']) ? 1 : -1;
		}
	} else {
		return ($ext1['Category'] > $ext2['Category'] || $ext1['Category'] == 'post-install') ? 1 : -1;
	}
}

/**
 * Checks if module is already installed
 *
 * @param string $extensionCode Extension code
 * @return bool
 * @deprecated
 * @see ExtensionsService::isInstalled()
 */
function cot_extension_installed($extensionCode)
{
    if (empty($extensionCode)) {
        return false;
    }
    return ExtensionsService::getInstance()->isInstalled($extensionCode);
}

/**
 * Returns installed extension type: 'module' if extension is a module,
 * 'plug' if extension is a plugin or FALSE if extension is not installed.
 *
 * @param string $name Module code
 * @return mixed
 * @global CotDB $db
 * @deprecated
 * @see ExtensionsService::getType()
 */
function cot_extension_type($name)
{
	global $db, $db_core;

	$res = $db->query("SELECT ct_plug FROM $db_core WHERE ct_code = ?", $name);
	if ($res->rowCount() == 0) {
		return false;
	}
	$is_plug = (int) $res->fetchColumn();
	return $is_plug ? 'plug' : 'module';
}

/**
 * Returns an array containing meta information for all extensions in a directory
 *
 * @param string $dir Directory to search for extensions in
 * @return array Extension code => info array
 */
function cot_extension_list_info($dir)
{
	$ext_list = [];
	clearstatcache();
	$dp = opendir($dir);
	while ($f = readdir($dp))
	{
		$path = $dir . '/' . $f;
		if ($f[0] != '.' && is_dir($path) && file_exists("$path/$f.setup.php"))
		{
			$info = cot_infoget("$path/$f.setup.php", 'COT_EXT');
			if (!$info && cot_plugin_active('genoa')) {
				// Try to load old format info
				$info = cot_infoget("$path/$f.setup.php", 'SED_EXTPLUGIN');
			}

			if ($info == false) {
                // Failed to load info block
                // Lets use default data
                $info = [
                    'Code' => $f,
                    'Name' => $f,
                    'Description' => '',
                    'Category' => '',
                    'Version' => '',
                    'Date' => '',
                    'Author' => '',
                    'Copyright' => '',
                    'Notes' => '',
                    'Auth_members' => '',
                    'Lock_members' => '',
                    'Auth_guests' => '',
                    'Lock_guests' => '',
                    'Requires_modules' => '',
                    'Requires_plugins' => '',
                    'Recommends_modules' => '',
                    'Recommends_plugins' => ''
                ];
            }

			if (empty($info['Category'])) {
				$info['Category'] = 'misc-ext';
			}

			$ext_list[$f] = $info;
		}
	}
	closedir($dp);

	return $ext_list;
}

/**
 * Unregisters a module from the core
 *
 * @param string $name Module name
 * @return bool
 * @global CotDB $db
 */
function cot_extension_remove($name)
{
	global $db, $db_core;

	return $db->delete($db_core, "ct_code = '$name'");
}

/**
 * Updates module version number in the registry
 *
 * @param string $name Module name
 * @param string $version New version string
 * @return bool
 * @global CotDB $db
 */
function cot_extension_update($name, $version)
{
	global $db, $db_core;

	return $db->update($db_core, ['ct_version' => $version], "ct_code = '$name'");
}

/**
 * Registers a plugin or module in hook registry
 *
 * Example:
 * <code>
 * $hook_bindings = [
 *     [
 *         'part' => 'rss',
 *         'hook' => 'rss.main',
 *         'order' => 20
 *     ],
 *     [
 *         'part' => 'header',
 *         'hook' => 'header.tags',
 *     ]
 * ];
 *
 * cot_plugin_add($hook_bindings, 'test', 'Test plugin', false);
 * </code>
 *
 * @param array $hook_bindings Hook binding map
 * @param string $code Module or plugin name (code)
 * @param string $title Module or plugin title
 * @param bool $isModule TRUE for modules, FALSE for plugins
 * @return int Number of records added
 * @global CotDB $db
 */
function cot_plugin_add($hook_bindings, $code, $title, $isModule = false)
{
	if (empty($title)) {
		$title = $code;
	}

	$insertRows = [];
	foreach ($hook_bindings as $binding) {
        $active = 1;
        if (isset($binding['active']) && !$binding['active']) {
            $active = 0;
        }
		$insertRows[] = [
			'pl_hook' => $binding['hook'],
			'pl_code' => $code,
			'pl_part' => $binding['part'],
			'pl_title' => $title,
			'pl_file' => empty($binding['file']) ? "$code/$code.{$binding['part']}.php" : $code . '/' . $binding['file'],
			'pl_order' => $binding['order'],
			'pl_active' => $active,
			'pl_module' => (int) $isModule
		];
	}

    Cot::$db->beginTransaction();
    try {
        $result = Cot::$db->insert(Cot::$db->plugins, $insertRows);
        Cot::$db->commit();
    } catch (Exception $e) {
        Cot::$db->rollBack();
        return 0;
    }

	return $result;
}

/**
 * Removes a plugin or one of its parts from hook registry
 *
 * @param string $name Module or plugin name
 * @param int $binding_id ID of the binding to remove or 0 to remove all
 * @return int Number of bindings removed
 * @global CotDB $db
 */
function cot_plugin_remove($name, $binding_id = 0)
{
	global $db, $db_plugins;

	$condition = "pl_code = '$name'";
	if ($binding_id > 0) {
		$condition .= " AND pl_id = $binding_id";
	}

	return $db->delete($db_plugins, $condition);
}
