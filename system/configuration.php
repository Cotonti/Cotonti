<?php

/**
 * Configuration Management API
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Generic text configuration. Is displayed as textarea. Contains text.
 * Is used by default.
 */
define('COT_CONFIG_TYPE_TEXT', 0);
/**
 * A string, max length is 255 chars. Is displayed as a single line of input.
 * The list of variants is ignored for this type.
 */
define('COT_CONFIG_TYPE_STRING', 1);
/**
 * Selection from the list of possible variants. Is displayed as a dropdown.
 */
define('COT_CONFIG_TYPE_SELECT', 2);
/**
 * Radio yes/no selection.
 */
define('COT_CONFIG_TYPE_RADIO', 3);
/**
 * Callback function type
 */
define('COT_CONFIG_TYPE_CALLBACK', 4);
/**
 * Hidden config. It is actually a text string, but it is not displayed anywhere
 */
define('COT_CONFIG_TYPE_HIDDEN', 5);
/**
 * Visual separator/fieldset
 */
define('COT_CONFIG_TYPE_SEPARATOR', 6);
/**
 * Integer range
 */
define('COT_CONFIG_TYPE_RANGE', 7);
/**
 * Custom type.
 */
define('COT_CONFIG_TYPE_CUSTOM', 8);

/**
 * Registers a set of configuration entries at once.
 *
 * Example:
 * <code>
 * $config_options = array(
 *     array(
 *         'name' => 'disable_test',
 *         'type' => COT_CONFIG_TYPE_RADIO,
 *         'default' => '0'
 *     ),
 *     array(
 *         'name' => 'test_selection',
 *         'type' => COT_CONFIG_TYPE_SELECT,
 *         'default' => '20',
 *         'variants' => '5,10,15,20,25,30,35,40,50'
 *     ),
 *     array(
 *         'name' => 'test_value',
 *         'type' => COT_CONFIG_TYPE_STRING,
 *         'default' => 'something'
 *     ),
 *     array(
 *         'name' => 'not_visible',
 *         'type' => COT_CONFIG_TYPE_HIDDEN,
 *         'default' => 'test23'
 *     )
 * );
 *
 * cot_config_add('test', $config_options, true);
 * </code>
 *
 * @param string $name Extension name (code)
 * @param array $options An associative array of configuration entries.
 * Each entry of the arrray has the following keys:
 * 'name' => Option name, alphanumeric and _. Must be unique for a module/plugin
 * 'type' => Option type, see COT_CONFIG_TYPE_* constants
 * 'default' => Default and initial value, by default is an empty string
 * 'variants' => A comma separated (without spaces) list of possible values,
 * 		only for SELECT options.
 * 'order' => A string that determines position of the option in the list,
 * 		e.g. '04'. Or will be assigned automatically if omitted
 * 'text' => Textual description. It is usually omitted and stored in langfiles
 * @param bool $is_module Flag indicating if it is module or plugin config
 * @param string $category Structure category code. Only for per-category config options
 * @param string $donor Extension name for extension-to-extension config implantations
 * @return bool Operation status
 * @global CotDB $db
 */
function cot_config_add($name, $options, $is_module = false, $category = '', $donor = '')
{
	global $db, $cfg, $db_config;
	$cnt = count($options);
	$type = $is_module ? 'module' : 'plug';
	// Check the arguments
	if (!$cnt)
	{
		return false;
	}
	// Build the SQL query
	$option_set = array();
	for ($i = 0; $i < $cnt; $i++)
	{
		$opt = $options[$i];
		$option_set[] = array(
			'config_owner' => $type,
			'config_cat' => $name,
			'config_subcat' => $category,
			'config_order' => isset($opt['order']) ? $opt['order'] : str_pad($i, 2, 0, STR_PAD_LEFT),
			'config_name' => $opt['name'],
			'config_type' => (int) $opt['type'],
			'config_value' => $opt['default'],
			'config_default' => $opt['default'],
			'config_variants' => $opt['variants'],
			'config_text' => $opt['text'],
			'config_donor' => $donor
		);
	}

	$ins_cnt = $db->insert($db_config, $option_set);
	return $ins_cnt == $cnt;
}

/**
 * Implants given options into module configuration if they are not already there.
 * Used by plugins which extend module behavior and need per-module or per-category
 * options.
 *
 * @global array $cfg Configuration
 * @param string $module_name Target module code
 * @param array $options Array of implantable options, described in cot_config_add()
 * @param bool $into_struct A flag indicating that config options should be implanted into
 * module categories configuration rather than the module root configuration
 * @param string $donor Extension name for extension-to-extension config implantations
 * @return int Number of options actually implanted
 * @see cot_config_add()
 */
function cot_config_implant($module_name, $options, $into_struct, $donor)
{
	global $cfg;

	$category = $into_struct ? '__default' : '';
	$add_options = array();
	foreach ($options as $opt)
	{
		if (!$into_struct && !isset($cfg[$module_name][$opt['name']])
				|| $into_struct && !isset($cfg[$module_name]['cat___default'][$opt['name']]))
		{
			$add_options[] = $opt;
		}
	}

	return cot_config_add($module_name, $add_options, true, $category, $donor);
}

/**
 * Checks if there are already implanted config records
 *
 * @param string $acceptor Acceptor module name
 * @param string $donor Donor extension name
 * @return bool TRUE if implanted records found, FALSE if not
 * @global CotDB $db
 */
function cot_config_implanted($acceptor, $donor)
{
	global $db, $db_config;
	return $db->query("SELECT COUNT(*) FROM $db_config WHERE config_owner = 'module' AND config_cat = ? AND config_donor = ?", array($acceptor, $donor))->fetchColumn() > 0;
}

/**
 * Loads config structure from database into an array
 *
 * @param string $name Extension code
 * @param bool $is_module TRUE if module, FALSE if plugin
 * @param string $category Structure category code. Only for per-category config options
 * @param string $donor Extension name for extension-to-extension config implantations
 * @return array Config options structure
 * @see cot_config_add()
 * @global CotDB $db
 */
function cot_config_load($name, $is_module = false, $category = '', $donor = '')
{
	global $db, $db_config;
	$options = array();
	$type = $is_module ? 'module' : 'plug';

	$query = "SELECT config_name, config_type, config_value,
			config_default, config_variants, config_order
		FROM $db_config WHERE config_owner = ? AND config_cat = ? AND config_subcat = ? AND config_donor = ?";
	$params = array($type, $name, $category, $donor);

	$res = $db->query($query, $params);
	while ($row = $res->fetch())
	{
		$options[] = array(
			'name' => $row['config_name'],
			'type' => $row['config_type'],
			'order' => $row['config_order'],
			'value' => $row['config_value'],
			'default' => $row['config_default'],
			'variants' => $row['config_variants']
		);
	}
	$res->closeCursor();

	return $options;
}

/**
 * Updates config map properties in the database for given options
 *
 * @param string $name Extension code
 * @param array $options Configuration entries
 * @param bool $is_module TRUE if module, FALSE if plugin
 * @param string $category Structure category code. Only for per-category config options
 * @param string $donor Extension name for extension-to-extension config implantations
 * @return int Number of entries updated
 * @global CotDB $db
 */
function cot_config_modify($name, $options, $is_module = false, $category = '', $donor = '')
{
	global $db, $db_config;
	$type = $is_module ? 'module' : 'plug';
	$affected = 0;

	$where = "config_owner = ? AND config_cat = ? AND config_name = ? AND config_donor = ?";

	if (!empty($category))
	{
		$where .= " AND config_subcat = ?";
	}

	foreach ($options as $opt)
	{
		$config_name = $opt['name'];
		unset($opt['name']);
		$opt_row = array();
		foreach ($opt as $key => $val)
		{
			$opt_row['config_' . $key] = $val;
		}
		$params = empty($category) ? array($type, $name, $config_name, $donor) : array($type, $name, $config_name, $donor, $category);
		$affected += $db->update($db_config, $opt_row, $where, $params);
	}

	return $affected;
}

/**
 * Parses array of setup file configuration entries into array representation
 *
 * @param array $info_cfg Setup file config entries
 * @return array Config options
 */
function cot_config_parse($info_cfg)
{
	$options = array();
	if (is_array($info_cfg))
	{
		foreach ($info_cfg as $i => $x)
		{
			$line = explode(':', $x);
			if (is_array($line) && !empty($line[1]) && !empty($i))
			{
				switch ($line[1])
				{
					case 'string':
						$line['Type'] = COT_CONFIG_TYPE_STRING;
						break;
					case 'select':
						$line['Type'] = COT_CONFIG_TYPE_SELECT;
						break;
					case 'radio':
						$line['Type'] = COT_CONFIG_TYPE_RADIO;
						break;
					case 'callback':
						$line['Type'] = COT_CONFIG_TYPE_CALLBACK;
						break;
					case 'hidden':
						$line['Type'] = COT_CONFIG_TYPE_HIDDEN;
						break;
					case 'separator':
						$line['Type'] = COT_CONFIG_TYPE_SEPARATOR;
						break;
					case 'range':
						$line['Type'] = COT_CONFIG_TYPE_RANGE;
						break;
					case 'custom':
						$line['Type'] = COT_CONFIG_TYPE_CUSTOM;
						break;
					default:
						$line['Type'] = COT_CONFIG_TYPE_TEXT;
						break;
				}
				$options[] = array(
					'name' => $i,
					'order' => $line[0],
					'type' => $line['Type'],
					'variants' => $line[2],
					'default' => $line[3],
					'text' => $line[4]
				);
			}
		}
	}
	return $options;
}

/**
 * Unregisters configuration option(s).
 *
 * @param string $name Extension name (code)
 * @param bool $is_module Flag indicating if it is module or plugin config
 * @param mixed $option String name of a single configuration option.
 * Or pass an array of option names to remove them at once. If empty or omitted,
 * all options from selected module/plugin will be removed
 * @param string $category Structure category code. Only for per-category config options
 * @param string $donor Extension name for extension-to-extension config implantations
 * @return int Number of options actually removed
 * @global CotDB $db
 */
function cot_config_remove($name, $is_module = false, $option = '', $category = '', $donor = null)
{
	global $db, $db_config;

	$type = $is_module ? 'module' : 'plug';
	$where = "config_owner = '$type' AND config_cat = " . $db->quote($name);
	if (!empty($category))
	{
		$where .= " AND config_subcat = " . $db->quote($category);
	}
	if (!is_null($donor))
	{
		$where .= " AND config_donor = " . $db->quote($donor);
	}

	if (is_array($option))
	{
		$cnt = count($option);
		if ($cnt == 1)
		{
			$option = $option[0];
		}
		else
		{
			$where .= " AND config_name IN (";
			for ($i = 0; $i < $cnt; $i++)
			{
				if ($i > 0)
					$where .= ',';
				$where .= $db->quote($option[$i]);
			}
			$where .= ')';
			unset($option);
		}
	}
	if (!empty($option))
	{
		$where .= " AND config_name = " . $db->quote($option);
	}
	return $db->delete($db_config, $where);
}

/**
 * Updates configuration values
 *
 * Example:
 * <code>
 * $config_values = array(
 *     'disable_test' => '0',
 *     'hidden_test' => 'test45',
 * );
 *
 * cot_config_set('test', $config_values, true);
 * </code>
 *
 * @param string $name Extension name config belongs to
 * @param array $options Array of options as 'option name' => 'option value'
 * @param bool $is_module Flag indicating if it is module or plugin config
 * @param string $category Structure category code. Only for per-category config options
 * @return int Number of entries updated
 * @global CotDB $db
 */
function cot_config_set($name, $options, $is_module = false, $category = '')
{
	global $db, $db_config;
	$type = $is_module ? 'module' : 'plug';
	$upd_cnt = 0;

	$where = 'config_owner = ? AND config_cat = ? AND config_name = ?';
	if (!empty($category))
	{
		$where .= ' AND config_subcat = ?';
		if ($category != '__default')
		{
			$default_options = cot_config_load($name, $is_module, '__default');
		}
	}

	foreach ($options as $key => $val)
	{
		if (empty($category) || $category == '__default' || $val != $default_options[$key])
		{
			$params = empty($category) ? array($type, $name, $key) : array($type, $name, $key, $category);
			$upd_cnt += $db->update($db_config, array('config_value' => $val), $where, $params);
		}
	}

	return $upd_cnt;
}

/**
 * Updates existing configuration map removing obsolete options, adding new
 * options and tweaking options which need to be updated.
 *
 * @param string $name Extension code
 * @param array $options Configuration options
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @param string $category Structure category code. Only for per-category config options
 * @param string $donor Extension name for extension-to-extension config implantations
 * @return int Number of entries affected
 */
function cot_config_update($name, $options, $is_module = false, $category = '', $donor = '')
{
	$affected = 0;
	$old_options = cot_config_load($name, $is_module, $category, $donor);

	// Find and remove options which no longer exist
	$remove_opts = array();
	foreach ($old_options as $old_opt)
	{
		$keep = false;
		foreach ($options as $opt)
		{
			if ($opt['name'] == $old_opt['name'])
			{
				$keep = true;
				break;
			}
		}
		if (!$keep && $old_opt['type'] != COT_CONFIG_TYPE_HIDDEN)
		{
			$remove_opts[] = $old_opt['name'];
		}
	}
	if (count($remove_opts) > 0)
	{
		$affected += cot_config_remove($name, $is_module, $remove_opts, $category, $donor);
	}

	// Find new options and options which have been modified
	$new_options = array();
	$upd_options = array();
	foreach ($options as $opt)
	{
		$existed = false;
		foreach ($old_options as $old_opt)
		{
			if ($opt['name'] == $old_opt['name'])
			{
				$changed = array_diff($opt, $old_opt);
				if (count($changed) > 0)
				{
					// Values for modified options are set to default
					// only if both type and default value have changed
					if ($opt['type'] != $old_opt['type'] && $opt['default'] != $old_opt['default'])
					{
						$opt['value'] = $opt['default'];
					}
					$upd_options[] = $opt;
				}
				$existed = true;
				break;
			}
		}
		if (!$existed)
		{
			$new_options[] = $opt;
		}
	}
	if (count($new_options) > 0)
	{
		$affected += cot_config_add($name, $new_options, $is_module, $category);
	}
	if (count($upd_options) > 0)
	{
		$affected += cot_config_modify($name, $upd_options, $is_module, $category);
	}

	return $affected;
}

?>