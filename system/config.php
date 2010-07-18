<?php
/**
 * Configuration Management API
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD
 */

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
 * Hidden config. It is actually a text string, but it is not displayed anywhere
 */
define('COT_CONFIG_TYPE_HIDDEN', 5);

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
 * sed_config_add($config_options, 'test', 'core');
 * </code>
 *
 * @param array $options An associative array of configuration entries.
 * Each entry of the arrray has the following keys:
 * 'name' => Option name, alphanumeric and _. Must be unique for a module/plugin
 * 'type' => Option type, see COT_CONFIG_TYPE_* constants
 * 'default' => Default and initial value, by default is an empty string
 * 'variants' => A comma separated (without spaces) list of possible values,
 *		only for SELECT options.
 * 'order' => A string that determines position of the option in the list,
 *		e.g. '04'. Or will be assigned automatically if omitted
 * 'text' => Textual description. It is usually omitted and stored in langfiles
 * @param string $mod_name Module or plugin name (code)
 * @param bool $is_module Flag indicating if it is module or plugin config
 * @return bool Operation status
 */
function sed_config_add($options, $mod_name, $is_module = false)
{
	global $cfg, $db_config;
	$cnt = count($options);
	$type = $is_module ? 'core' : 'plug';
	// Check the arguments
	if (!$cnt
		|| $is_module && count($cfg['module'][$mod_name]) > 0
		|| !$is_module && count($cfg['plugin'][$mod_name]) > 0)
	{
		return false;
	}
	// Build the SQL query
	$query = "INSERT INTO `$db_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES ";
	for ($i = 0; $i < $cnt; $i++)
	{
		if ($i > 0) $query .= ',';
		$order = isset($options[$i]['order']) ? sed_sql_prep($options[$i]['order'])
			: str_pad($i, 2, 0, STR_PAD_LEFT);
		$query .= "('$type', '$mod_name', '$order', '" . sed_sql_prep($options[$i]['name']) . "', "
			. (int) $options[$i]['type'] . ", '" . sed_sql_prep($options[$i]['default']) . "', '"
			. sed_sql_prep($options[$i]['default']) . "', '" . sed_sql_prep($options[$i]['variants']) . "', '"
			. sed_sql_prep($options[$i]['text']) . "')";
	}
	sed_sql_query($query);
	return sed_sql_affectedrows() == $cnt;
}

/**
 * Parses array of setup file configuration entries into array representation
 *
 * @param array $info_cfg Setup file config entries
 * @return array Config options
 */
function sed_config_parse($info_cfg)
{
	$options = array();
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
				case 'hidden':
					$line['Type'] = COT_CONFIG_TYPE_HIDDEN;
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
	return $options;
}

/**
 * Unregisters configuration option(s).
 *
 * @param string $mod_name Module or plugin name (code)
 * @param bool $is_module Flag indicating if it is module or plugin config
 * @param mixed $option String name of a single configuration option.
 * Or pass an array of option names to remove them at once. If empty or omitted,
 * all options from selected module/plugin will be removed
 * @return int Number of options actually removed
 */
function sed_config_remove($mod_name, $is_module = false, $option = '')
{
	global $db_config;
	$type = $is_module ? 'core' : 'plug';
	$where = "config_owner = '$type' AND config_cat = '$mod_name'";
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
			for ($i = 0; $i < 0; $i++)
			{
				if ($i > 0) $where .= ',';
				$where .= "'" . sed_sql_prep($option[$i]) . "'";
			}
			unset($option);
		}
	}
	if (!empty($option))
	{
		$where .= " AND config_name = '" . sed_sql_prep($option) . "'";
	}
	return sed_sql_delete($db_config, $where);
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
 * sed_config_set($config_values, 'test', 'core');
 * </code>
 *
 * @param array $options Array of options as 'option name' => 'option value'
 * @param string $mod_name Module a plugin name config belongs to, will apply to all if omitted
 * @param bool $is_module Flag indicating if it is module or plugin config
 * @return int Number of entries updated
 */
function sed_config_set($options, $mod_name = '', $is_module = false)
{
	global $db_config;
	$type = $is_module ? 'core' : 'plug';
	$upd_cnt = 0;
	foreach ($options as $key => $val)
	{
		$where = "config_owner = '$type' AND config_name = '" . sed_sql_prep($key) . "'";
		if (!empty($mod_name)) $where .= " AND config_cat = '$mod_name'";
		$upd_cnt += sed_sql_update($db_config, $where, array('value' => $val), 'config_');
	}
	return $upd_cnt;
}

?>