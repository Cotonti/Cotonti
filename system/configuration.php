<?php

/**
 * Configuration Management API
 *
 * @package API - Configuration
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
 * Generates a form input for Integer
 *
 * @param array $cfg_var Config Variable data
 * @param string $min Minimum allowed value as get from callback parameters
 * @param string $max Maximum allowed value as get from callback parameters
 * @return string Code of input field
 */
function cot_config_type_int($cfg_var, $min='', $max='')
{
	$name = $cfg_var['config_name'];
	$value = $cfg_var['config_value'];
	if (!empty($min) && !empty($max)){
		$placeholder = "$min - $max";
	} elseif(!empty($min)) {
		$placeholder = cot_rc('adm_int_min', array('value'=>$min));
	} elseif(!empty($min)) {
		$placeholder = cot_rc('adm_int_max', array('value'=>$max));
	}
	return cot_inputbox('text', $name, $value, array('placeholder' => $placeholder ));
}

/**
 * Filters value as Integer in range from Min and Max.
 * Used as custom config type callback filter function
 * @see also COT_CONFIG_TYPE_CUSTOM type
 *
 * @param string $new_value User input value
 * @param array $cfg_var Config Variable data
 * @param string $min Minimum allowed value as get from callback parameters
 * @param string $max Maximum allowed value as get from callback parameters
 * @param bool $skip_warnings Does not display warnings if set
 * @return int|NULL Filtered integer value or NULL in case of value can not be filtered / not acceptable
 */
function cot_config_type_int_filter($new_value, $cfg_var, $min='', $max='', $skip_warnings=false)
{
	$not_filtered = $new_value;
	$var_name = $cfg_var['config_name'];
	list($title, $hint) = cot_config_titles($var_name, $cfg_var['config_text']);
	if (!is_numeric($new_value))
	{
		$not_num = true;
	}
	else
	{
		$new_value = floor($new_value);
		if (!empty($min) && $new_value < $min)
		{
			$new_value = $min;
			$fix_msg = '. '.cot_rc('adm_set').cot_rc('adm_int_min', array('value'=>$min));
		}
		if (!empty($max) && $new_value > $max)
		{
			$new_value = $max;
			$fix_msg = '. '.cot_rc('adm_set').cot_rc('adm_int_max', array('value'=>$max));
		}
	}
	// user friendly notification
	$invalid_value_msg = cot_rc('adm_invalid_input', array('value' => $not_filtered, 'field_name' => $title ));
	if (!$skip_warnings && ($fix_msg || $not_num)) cot_message($invalid_value_msg . $fix_msg, $not_num ? 'error' : 'warning', $var_name);

	return $not_num ? NULL : (int)$new_value;
}

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
 * cot_config_add('test', $config_options, 'module');
 * </code>
 *
 * @param string $name Extension name (code)
 * @param array $options An associative array of configuration entries.
 * Each entry of the array has the following keys:
 * 'name' => Option name, alphanumeric and _. Must be unique for a module/plugin
 * 'type' => Option type, see COT_CONFIG_TYPE_* constants
 * 'default' => Default and initial value, by default is an empty string
 * 'variants' => A comma separated (without spaces) list of possible values,
 * 		only for SELECT options.
 * 'order' => A string that determines position of the option in the list,
 * 		e.g. '04'. Or will be assigned automatically if omitted
 * 'text' => Textual description. It is usually omitted and stored in lang files
 * @param mixed $is_module Flag indicating if it is module or plugin config
 * @param string $category Structure category code. Only for per-category config options
 * @param string $donor Extension name for extension-to-extension config implantations
 * @return bool Operation status
 * @global CotDB $db
 */
function cot_config_add($name, $options, $is_module = false, $category = '', $donor = '')
{
	global $db, $cfg, $db_config;
	$cnt = count($options);
	if (is_bool($is_module))
	{
		$type = $is_module ? 'module' : 'plug';
	}
	else
	{
		$type = !in_array($is_module, array('plug', 'core')) ? 'module' : $is_module;
	}
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
		if (!$into_struct && !isset($cfg[$module_name][$opt['name']]) || $into_struct && !isset($cfg[$module_name]['cat___default'][$opt['name']]))
		{
			$add_options[] = $opt;
		}
	}

	return cot_config_add($module_name, $add_options, 'module', $category, $donor);
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
 * @param mixed $is_module TRUE if module, FALSE if plugin
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
	if (is_bool($is_module))
	{
		$type = $is_module ? 'module' : 'plug';
	}
	else
	{
		$type = !in_array($is_module, array('plug', 'core')) ? 'module' : $is_module;
	}

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
 * @param mixed $is_module TRUE if module, FALSE if plugin
 * @param string $category Structure category code. Only for per-category config options
 * @param string $donor Extension name for extension-to-extension config implantations
 * @return int Number of entries updated
 * @global CotDB $db
 */
function cot_config_modify($name, $options, $is_module = false, $category = '', $donor = '')
{
	global $db, $db_config;

	if (is_bool($is_module))
	{
		$type = $is_module ? 'module' : 'plug';
	}
	else
	{
		$type = !in_array($is_module, array('plug', 'core')) ? 'module' : $is_module;
	}
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
			$line[1] = trim($line[1]);
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
					'order' => trim($line[0]),
					'type' => $line['Type'],
					'variants' => $line[2],
					'default' => $line[3],
					'text' => trim($line[4])
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
 * @param mixed $is_module Flag indicating if it is module or plugin config
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

	if (is_bool($is_module))
	{
		$type = $is_module ? 'module' : 'plug';
	}
	else
	{
		$type = !in_array($is_module, array('plug', 'core')) ? 'module' : $is_module;
	}
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
 * @param mixed $is_module Flag indicating if it is module or plugin config
 * @param string $category Structure category code. Only for per-category config options
 * @return int Number of entries updated
 * @global CotDB $db
 */
function cot_config_set($name, $options, $is_module = false, $category = '')
{
	global $db, $db_config;

	if (is_bool($is_module))
	{
		$type = $is_module ? 'module' : 'plug';
	}
	else
	{
		$type = !in_array($is_module, array('plug', 'core')) ? 'module' : $is_module;
	}
	$upd_cnt = 0;

	$where = 'config_owner = ? AND config_cat = ? AND config_name = ?';
	if (!empty($category))
	{
		$where .= ' AND config_subcat = ?';
		if ($category != '__default')
		{
			$default_options = cot_config_load($name, $is_module, '__default');
		}
		$structure_val = array();
	}
	else
	{
		$structure_val = cot_config_list($type, $name, '__default');
	}

	$category_tmp = $category;
	foreach ($options as $key => $val)
	{
		if (is_array($structure_val[$key]))
		{
			// roundabout way to update only structure defaults
			$where_sub = ' AND config_subcat = ?';
			$category = '__default';
		}
		else
		{
			$where_sub = '';
			$category = $category_tmp;
		}
		if (empty($category) || $category == '__default' || $val != $default_options[$key])
		{
			$params = empty($category) ? array($type, $name, $key) : array($type, $name, $key, $category);
			$upd_cnt += $db->update($db_config, array('config_value' => $val), $where . $where_sub, $params);
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

/**
 * Reset configuration value
 *
 * @param string $name Extension name config belongs to
 * @param string $option Option name
 * @param mixed $is_module Flag indicating if it is module or plugin config
 * @param string $category Structure category code. Only for per-category config options
 * @return int Number of entries updated
 * @global CotDB $db
 */
function cot_config_reset($name, $option, $is_module = false, $category = '')
{
	global $db, $db_config;
	if (is_bool($is_module))
	{
		$type = $is_module ? 'module' : 'plug';
	}
	else
	{
		$type = !in_array($is_module, array('plug', 'core')) ? 'module' : $is_module;
	}
	if (!empty($category))
	{
		$db->delete($db_config, "config_name = ? AND config_owner = ? AND config_cat = ?
					AND config_subcat = ?", array($option, $type, $name, $category));
	}
	else
	{
		$db->query("UPDATE $db_config SET config_value = config_default
			WHERE config_name = ? AND config_owner = ? AND config_cat = ? AND (config_subcat = '' OR config_subcat IS NULL OR config_subcat = '__default')", array($option, $type, $name));
	}
}

/**
 * Get configs from database
 * @param string Owner ('core', 'plug', 'module')
 * @param string Extension code (page, forums, etc.) or core subtype (menus, main, performance, etc.)
 * @param string category for modules if exists
 * @return array
 */
function cot_config_list($owner, $cat, $subcat = "")
{
	global $db, $db_config;

	$where = array(
		'type' => "config_type != '" . COT_CONFIG_TYPE_HIDDEN . "'",
		'owner' => "config_owner = '" . $db->prep($owner) . "'",
		'cat' => "config_cat = '" . $db->prep($cat) . "'",
		'subcat' => empty($subcat) ? "(config_subcat = '' OR config_subcat IS NULL OR config_subcat = '__default')" : "(config_subcat = '" . $db->prep($subcat) . "' OR config_subcat = '__default')"
	);

	$where_query = implode(" AND ", $where);

	// Attempt to fetch the entire rowset indexed by config_name
	$sql = $db->query("SELECT * FROM $db_config WHERE $where_query ORDER BY config_subcat ASC, config_order ASC, config_name ASC");
	$rs = $sql->fetchAll(PDO::FETCH_ASSOC);
	$rowset = array();
	$rowset_default = array();
	foreach ($rs as $row)
	{
		$keyx = $row['config_name'];

		if ($row['config_subcat'] == "__default")
		{
			$rowset_default[$keyx] = $row;
		}
		else
		{
			$rowset[$keyx] = $row;
		}
	}
	// merging arrays for proper display
	if (!empty($subcat))
	{
		foreach ($rowset_default as $key => $row)
		{
			if (!empty($rowset[$key]))
			{
				$rowset[$key]['config_subdefault'] = $row['config_value'];
			}
		}
		$rowset = array_merge($rowset_default, $rowset);
	}
	else
	{
		$rowset = $rowset + $rowset_default;
	}
	unset($rs);
	return $rowset;
}

/**
 * Imports data for config values from outer world
 *
 * @param string|array $name Name of value or array of names for list of values
 * @param string $source Source type
 * @param string $filter Filter type
 * @param string $defvalue Default value for filtered data
 * @see cot_import()
 * @return mixed Filtered value of array of values
 */
function cot_config_import($name, $source='POST', $filter='NOC', $defvalue=null)
{
	global $cot_import_filters;
	if (!$name) return null;
	if (!is_array($name))
	{
		$name = array($name);
		$single_value = true;
	}
	$res = array();
	foreach ($name as $idx => $var_name) {
		$filter_type = (is_array($filter)) ? ($filter[$var_name] ? $filter[$var_name] : ($filter[$idx] ? $filter[$idx] : 'NOC')) : $filter;
		$not_filtered = cot_import($var_name, $source, 'NOC');
		$value = cot_import($var_name, $source, $filter_type);
		// addition filtering by varname
		if (is_array($cot_import_filters[$var_name]) && sizeof($cot_import_filters[$var_name]))
		{
			$value = cot_import($value, 'DIRECT', $var_name);
		}

		// if invalid value is used
		if (is_null($value))
		{
			$value_to_show = (in_array($filter_type, array('INT', 'NUM', 'TXT', 'ALP')))
				? htmlspecialchars(cot_cutstring(strip_tags($not_filtered), 15))
				: '';
			list($field_title) = cot_config_titles($var_name);
			$error_msg = cot_rc('adm_invalid_input', array('value' => $value_to_show, 'field_name' => $field_title));
			if (!is_null($defvalue))
			{
				$value = !is_array($defvalue) ? $defvalue : (isset($defvalue[$var_name]) ? $defvalue[$var_name] : (isset($defvalue[$idx]) ? $defvalue[$idx] : null));
				$error_msg .= $value_to_show ? '. '.cot_rc('adm_set_default', htmlspecialchars(strip_tags($value))) : '';
			}
			cot_message($error_msg, 'error', $var_name);
		}
		$res[$var_name] = $value;
	}
	return $single_value ? $value : $res;
}

/**
 * Saves updated values of config list in DB
 *
 * @param string $name Extension or Section name config belongs to
 * @param array $optionslist Option list as return by cot_config_list()
 * @param mixed $is_module Flag indicating if it is module or plugin config
 * @param string $update_new_only Update changes values only
 * @param string $source Source of imported data
 * @return boolean|number Number of updated values
 */
function cot_config_update_options($name, &$optionslist, $is_module=false, $update_new_only = true, $source = 'POST')
{
	global $cot_import_filters;
	if (!is_array($optionslist)) return false;
	$new_options = array();
	//$cfg_var = $val;
	foreach ($optionslist as $cfg_name => $cfg_var)
	{
		// Visual separator/fieldset have no value
		if ($cfg_var['config_type'] == COT_CONFIG_TYPE_SEPARATOR) continue;

		$filtered = FALSE;
		$builtin_filter = FALSE;
		$data = $raw_input = cot_import($cfg_name, $source, 'NOC');
		$custom_type = ($cfg_var['config_type'] == COT_CONFIG_TYPE_CUSTOM)
			&& $cfg_var['config_variants']
			&& preg_match('#^(\w+)\((.*?)\)$#', $cfg_var['config_variants'], $mt);

		if ($custom_type)
		{
			$custom_func = $mt[1];
			$custom_filter_func = $custom_func . '_filter';

			// use addition custom function for filtration if exists
			if (function_exists($custom_filter_func))
			{
				$callback_params = preg_split('#\s*,\s*#', $mt[2]);
				if (count($callback_params) > 0 && !empty($callback_params[0]))
				{
					for ($i = 0; $i < count($callback_params); $i++)
					{
						$callback_params[$i] = str_replace(array("'", '"'), array('', ''), $callback_params[$i]);
					}
				}
				/**
				* Filters Value with custom function
				* @param string $data User input value
				* @param array $cfg_var Config Variable data
				*  ...   other callback params defined for function
				* @return NULL|mixed Filtered Value or NULL in case Value can not be filtered.
				* @see cot_config_type_int_filter() as example
				*/
				$filtered = call_user_func_array(
					$custom_filter_func,
					array_merge(array(&$raw_input, $cfg_var), $callback_params)
				);
			}
			else // try built-in filters
			{
				// last part of custom function name may treats as built-in filter type
				list($base_filter) = array_reverse(explode('_', strtoupper($custom_func)));
				if (in_array(strtoupper($base_filter), array('INT', 'BOL', 'PSW', 'ALP', 'TXT', 'NUM'))
					|| sizeof($cot_import_filters[$base_filter])
					)
				{
					$filtered = cot_config_import($cfg_name, $source, $base_filter);
					$builtin_filter = true;
				}
			}
		}
		if (is_null($filtered)) // filtration false
		{
			$optionslist[$cfg_name]['config_value'] = $builtin_filter ? '' : $raw_input;
		}
		else
		{
			if (false !== $filtered) $data = $filtered;
			if (is_array($data)) $data = serialize($data);
			if ($data != $cfg_var['config_value'] || !$update_new_only) $new_options[$cfg_name] = $data;
			$optionslist[$cfg_name]['config_value'] = $data;
		}
	}
	return (sizeof($new_options)) ? cot_config_set($name, $new_options, $is_module) : 0;
}

/**
 * Returns config input
 * @param array $cfg_var Array with config Variable parameters
 * @return string
 */
function cot_config_input($cfg_var)
{
	$name = $cfg_var['config_name'];
	$type = $cfg_var['config_type'];
	$value = $cfg_var['config_value'];
	$options = $cfg_var['config_variants'];
	$config_input = '';
	$split_re = '#\s*,\s*#';
	switch ($type)
	{
		case COT_CONFIG_TYPE_STRING:
			$config_input = cot_inputbox('text', $name, $value);
			break;

		case COT_CONFIG_TYPE_SELECT:
			if (!empty($options))
			{
				$params = preg_split($split_re, $options);
				$params_titles = cot_config_selecttitles($name, $params);
			}
			$config_input = (is_array($params)) ? cot_selectbox($value, $name, $params, $params_titles, false) : cot_inputbox('text', $name, $value);

			break;

		case COT_CONFIG_TYPE_RADIO:
			global $L;
			if (!empty($options))
			{
				// extending radio to use custom values list
				$params = preg_split($split_re, $options);
				$params_titles = cot_config_selecttitles($name, $params);
				if (empty($value)) $value = $cfg_var['config_default'];
			}
			else
			{
				// old style definition
				$params = array(1, 0);
				$params_titles = array($L['Yes'], $L['No']);
			}
			$config_input = cot_radiobox($value, $name, $params, $params_titles, '', ' ');
			break;

		case COT_CONFIG_TYPE_RANGE:
			$range = preg_split($split_re, $options);
			$params = range($range[0], $range[1], empty($range[2]) ? 1 : $range[2]);
			$config_input = cot_selectbox($value, $name, $params, $params, false);
			break;

		case COT_CONFIG_TYPE_CUSTOM:
			if ((preg_match('#^(\w+)\((.*?)\)$#', $options, $mt) && function_exists($mt[1])))
			{
				$callback_params = preg_split($split_re, $mt[2]);
				if (count($callback_params) > 0 && !empty($callback_params[0]))
				{
					for ($i = 0; $i < count($callback_params); $i++)
					{
						$callback_params[$i] = str_replace(array("'", '"'), array('', ''), $callback_params[$i]);
					}
				}
				$config_input = call_user_func_array($mt[1], array_merge(array($cfg_var), $callback_params));
			}
			else
			{
				$config_input = cot_inputbox('text', $name, $value);
			}
			break;

		case COT_CONFIG_TYPE_CALLBACK:
			if ((preg_match('#^(\w+)\((.*?)\)$#', $options, $mt) && function_exists($mt[1])))
			{
				$callback_params = preg_split($split_re, $mt[2]);
				if (count($callback_params) > 0 && !empty($callback_params[0]))
				{
					for ($i = 0; $i < count($callback_params); $i++)
					{
						$callback_params[$i] = str_replace("'", '', $callback_params[$i]);
						$callback_params[$i] = str_replace('"', '', $callback_params[$i]);
					}
					$params = call_user_func_array($mt[1], $callback_params);
				}
				else
				{
					$params = call_user_func($mt[1]);
				}

				// assume associative array as value=>title
				$assoc = (range( 0, count($params) -1 ) != array_keys( $params ));
				if ($assoc)
				{
					$assoc_titles = array_values($params);
					$params = array_keys($params);
				}
				$params_titles = cot_config_selecttitles($name, $params);
				if ($assoc && $params_titles == $params) $params_titles = $assoc_titles;

				$config_input = cot_selectbox($value, $name, $params, $params_titles, false);
			}
			break;

		case COT_CONFIG_TYPE_HIDDEN:
		case COT_CONFIG_TYPE_SEPARATOR:
			break;

		default :
			$config_input = cot_textarea($name, $value, 8, 56);
			break;
	}
	return $config_input;
}

/**
 * Returns option title and hint
 * @param string $name Config name
 * @param string $text Config text
 * @return array
 */
function cot_config_titles($name, $text = '')
{
	global $L;

	if (is_array($L['cfg_' . $name]))
	{
		$L['cfg_' . $name . '_hint'] = (isset($L['cfg_' . $name][1]) && !isset($L['cfg_' . $name . '_hint'])) ? $L['cfg_' . $name][1] : $L['cfg_' . $name . '_hint'];
		$L['cfg_' . $name] = $L['cfg_' . $name][0];
	}
	$text = !empty($text) ? htmlspecialchars($text) : $name;
	$title = !empty($L['cfg_' . $name]) ? $L['cfg_' . $name] : $text;

	return array($title, $L['cfg_' . $name . '_hint']);
}

/**
 * Helper function that generates selection titles.
 * @param  string $name Current config name
 * @param  array  $params  Array of config params
 * @return array Selection titles
 */
function cot_config_selecttitles($name, $params)
{
	global $L;
	if (isset($L['cfg_' . $name . '_params']))
	{
		if (!is_array($L['cfg_' . $name . '_params']))
		{
			$L['cfg_' . $name . '_params'] = preg_split('#\s*,\s*#', $L['cfg_' . $name . '_params']);
			if (preg_match('#^[\w-]+\s*:#', $L['cfg_' . $name . '_params'][0]))
			{
				// Support for assoc arrays
				$temp = array();
				foreach ($L['cfg_' . $name . '_params'] as $item)
				{
					if (preg_match('#^([\w-]+)\s*:\s*(.*)$#', $item, $mt))
					{
						$temp[$mt[1]] = $mt[2];
					}
				}
				if (count($temp) > 0)
					$L['cfg_' . $name . '_params'] = $temp;
			}
		}
		$lang_params_keys = array_keys($L['cfg_' . $name . '_params']);
		if (is_numeric($lang_params_keys[0]))
		{
			// Numeric array, simply use it
			$cfg_params_titles = $L['cfg_' . $name . '_params'];
		}
		else
		{
			// Associative, match entries
			$cfg_params_titles = array();
			foreach ($params as $val)
			{
				if (isset($L['cfg_' . $name . '_params'][$val]))
				{
					$cfg_params_titles[] = $L['cfg_' . $name . '_params'][$val];
				}
				else
				{
					$cfg_params_titles[] = $val;
				}
			}
		}
	}
	else
	{
		$cfg_params_titles = $params;
	}
	return $cfg_params_titles;
}
