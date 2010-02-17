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
 * Parses PHPDoc file header into an array
 *
 * @param string $filename Path to a PHP file
 * @return array Associative array containing PHPDoc contents. The array is empty if no PHPDoc was found
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
			$res['description'] = trim(preg_replace('#\r?\n\s\*\s?#', '', $phpdoc[0]));
			for ($i = 1; $i < $cnt; $i++)
			{
				$delim = mb_strpos($phpdoc[$i], ' ');
				$key = mb_substr($phpdoc[$i], 0, $delim);
				$contents = trim(preg_replace('#\r?\n\s\*\s?#', '', substr($phpdoc[$i], $delim + 1)));
				$res[$key] = $contents;
			}
		}
	}
	return $res;
}

/**
 * Registers a module in the core
 *
 * @param string $name Module name (code)
 * @param string $title Title name
 * @param string $version Version number as A.B.C
 * @param int $revision Revision number, integer
 * @return bool TRUE on success, FALSE on error
 */
function sed_module_add($name, $title, $version = '1.0.0', $revision = 1)
{
	global $db_core, $db_updates;

	$res = sed_sql_insert($db_core, array('code' => $name, 'title' => $title, 'version' => $version), 'ct_');

	if ($res == 1)
	{
		sed_sql_insert($db_updates, array('param' => $name, 'value' => "$revision"), 'upd_');
		return true;
	}

	return  false;
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

	return sed_sql_update($db_core, "ct_code = '$name'", array('state' => 0), 'ct_') == 1;
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

	sed_sql_delete($db_updates, "upd_param = '$name'");

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

	return sed_sql_update($db_core, "ct_code = '$name'", array('state' => 1), 'ct_') == 1;
}

/**
 * Updates module version and revision number in the registry
 *
 * @param string $name Module name
 * @param string $version Version string
 * @param int $revision Revision number
 * @return bool
 */
function sed_module_update($name, $version, $revision)
{
	global $db_core, $db_updates;

	$res = sed_sql_update($db_core, "ct_code = '$name'", array('version' => $version), 'ct_');
	$res &= sed_sql_update($db_updates, "upd_param = '$name'", array('value' => $revision), 'upd_');

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
 * @param bool $is_module TRUE if it is a module, otherwise a plugin is considered
 * @return int Number of records added
 */
function sed_plugin_add($hook_bindings, $name, $title, $is_module = false)
{
	global $db_plugins, $cfg;

	if (empty($title)) $title = $name;
	$path = $is_module ? $cfg['modules_dir'] . "/$name." : $cfg['plugins_dir'] . "/$name.";
	
	$insert_rows = array();
	foreach ($hook_bindings as $binding)
	{
		$insert_rows[] = array(
			'hook' => $binding['hook'],
			'code' => $name,
			'part' => $binding['part'],
			'title' => $title,
			'file' => $path . $binding['part'] . '.php',
			'order' => isset($binding['order']) ? (int) $binding['order'] : COT_PLUGIN_DEFAULT_ORDER,
			'active' => 1
		);
	}
	return sed_sql_insert($db_plugins, $insert_rows, 'pl_');
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
