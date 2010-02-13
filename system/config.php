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
 * Registers a set of configuration entries at once.
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
 * @param string $type Parent type: 'core' for modules, 'plug' for plugins
 * @return bool Operation status
 */
function sed_config_add($options, $mod_name, $type = 'core')
{
	global $cfg, $db_config;
	$cnt = count($options);
	// Check the arguments
	if (!$cnt
		|| $type !== 'core' && $type !== 'plug'
		|| $type === 'core' && count($cfg['mod'][$mod_name]) > 0
		|| $type === 'plug' && count($cfg['plugins'][$mod_name]) > 0)
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
 * Unregisters configuration option(s).
 *
 * @param string $mod_name Module or plugin name (code)
 * @param string $type Parent type: 'core' for modules, 'plug' for plugins
 * @param mixed $option String name of a single configuration option.
 * Or pass an array of option names to remove them at once. If empty or omitted,
 * all options from selected module/plugin will be removed
 * @return int Number of options actually removed
 */
function sed_config_remove($mod_name, $type = 'core', $option = '')
{
	global $db_config;
	if ($type !== 'core' && $type !== 'plug') return false;
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
 * @param array $options Array of options as 'option name' => 'option value'
 * @param string $mod_name Module a plugin name config belongs to, will apply to all if omitted
 * @param string $type Parent type: 'core' for modules, 'plug' for plugins
 * @return int Number of entries updated
 */
function sed_config_set($options, $mod_name = '', $type = 'core')
{
	global $db_config;
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
