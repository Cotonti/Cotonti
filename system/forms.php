<?php
/**
 * Form generation API
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

/**
 * Generates a checkbox output
 * @param bool $chosen Checkbox state
 * @param string $name Input name
 * @param string $title Option caption
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $value Input value (passed), defaults to 'on' or '1'
 */
function sed_checkbox($chosen, $name, $title = '', $attrs = '', $value = '1')
{
	$input_attrs = sed_rc_attr_string($attrs);
	$checked = $chosen ? ' checked="checked"' : '';
	$result .= sed_rc('input_checkbox', array(
		'value' => $value,
		'name' => $name,
		'checked' => $checked,
		'title' => $title,
		'attrs' => $input_attrs
	));
}

/**
 * Generates a form input from a resource string
 *
 * @param string $type Input type: text, checkbox, button, file, hidden, image, password, radio, reset, submit
 * @param string $name Input name
 * @param string $value Entered value
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $custom_rc Custom resource string name
 * @return string
 */
function sed_inputbox($type, $name, $value = '', $attrs = '', $custom_rc = '')
{
	global $R;
	$input_attrs = sed_rc_attr_string($attrs);
	$rc = empty($custom_rc) ? "input_$type" : $custom_rc;
	if (!isset($R[$rc]))
	{
		$rc = 'input_default';
	}
	return sed_rc($rc, array(
		'type' => $type,
		'name' => $name,
		'value' => htmlspecialchars($value),
		'attrs' => $input_attrs
	));
}

/**
 * Generates a radio input group
 *
 * @param string $chosen Seleced value
 * @param string $name Input name
 * @param array $values Options available
 * @param array $titles Titles for options
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $separator Option separator, by default is taken from $R['input_radio_separator']
 * @return string
 */
function sed_radiobox($chosen, $name, $values, $titles = array(), $attrs = '', $separator = '')
{
	global $R;
	if (!is_array($values))
	{
		$values = explode(',', $values);
	}
	if (!is_array($titles))
	{
		$titles = explode(',', $titles);
	}
	$use_titles = count($values) == count($titles);
	$input_attrs = sed_rc_attr_string($attrs);
	if (empty($separator))
	{
		$separator = $R['input_radio_separator'];
	}
	$i = 0;
	$result = '';
	foreach ($values as $k => $x)
	{
		$checked = ($x == $chosen) ? ' checked="checked"' : '';
		$title = $use_titles ? htmlspecialchars($titles[$k]) : htmlspecialchars($x);
		if ($i > 0)
		{
			$result .= $separator;
		}
		$result .= sed_rc('input_radio', array(
			'value' => htmlspecialchars($x),
			'name' => $name,
			'checked' => $checked,
			'title' => $title,
			'attrs' => $input_attrs
		));
		$i++;
	}
	return $result;
}

/**
 * Renders a dropdown
 *
 * @param mixed $chosen Seleced value (or values array for mutli-select)
 * @param string $name Dropdown name
 * @param array $values Options available
 * @param array $titles Titles for options
 * @param bool $add_empty Allow empty choice
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @return string
 */
function sed_selectbox($chosen, $name, $values, $titles = array(), $add_empty = true, $attrs = '')
{
	global $R;
	if (!is_array($values))
	{
		$values = explode(',', $values);
	}
	if (!is_array($titles))
	{
		$titles = explode(',', $titles);
	}
	$use_titles = count($values) == count($titles);
	$input_attrs = sed_rc_attr_string($attrs);
	$multi = is_array($chosen) && isset($input_attrs['multiple']);
	$result = sed_rc('input_select_begin', array('name' => $name, 'attrs' => $input_attrs));
	$selected = (is_null($chosen) || $chosen === '' || $chosen=='00') ? ' selected="selected"' : '';
	if ($add_empty)
	{
		$result .= sed_rc('input_option', array(
			'value' => '',
			'selected' => $selected,
			'title' => $R['code_option_empty']
		));
	}
	foreach ($values as $k => $x)
	{
		$x = trim($x);
		$selected = ($multi && in_array($x, $chosen)) || (!$multi && $x == $chosen) ? ' selected="selected"' : '';
		$title = $use_titles ? htmlspecialchars($titles[$k]) : htmlspecialchars($x);
		$result .= sed_rc('input_option', array(
			'value' => htmlspecialchars($x),
			'selected' => $selected,
			'title' => $title
		));
	}
	$result .= $R['input_select_end'];
	return $result;
}

/**
 * Renders country dropdown
 *
 * @param string $chosen Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_countries($chosen, $name)
{
	global $sed_countries;

	if (!$sed_countries) include_once sed_langfile('countries', 'core');

	return sed_selectbox($chosen, $name, array_keys($sed_countries), array_values($sed_countries));
}

/**
 * Generates date part dropdown
 *
 * @param int $utime Selected timestamp
 * @param string $mode Display mode: 'short' or complete
 * @param string $ext Variable name suffix
 * @param int $max_year Max. year possible
 * @param int $min_year Min. year possible
 * @return string
 */
function sed_selectbox_date($utime, $mode, $ext = '', $max_year = 2030, $min_year = 1902)
{
	global $L, $R;
	if ($utime == 0)
	{
		list($s_year, $s_month, $s_day, $s_hour, $s_minute) = array(null, null, null, null, null);
	}
	else
	{
		list($s_year, $s_month, $s_day, $s_hour, $s_minute) = explode('-', @date('Y-m-d-H-i', $utime));
	}
	$months = array();
	$months[1] = $L['January'];
	$months[2] = $L['February'];
	$months[3] = $L['March'];
	$months[4] = $L['April'];
	$months[5] = $L['May'];
	$months[6] = $L['June'];
	$months[7] = $L['July'];
	$months[8] = $L['August'];
	$months[9] = $L['September'];
	$months[10] = $L['October'];
	$months[11] = $L['November'];
	$months[12] = $L['December'];

	$result = sed_selectbox($s_year, "ryear$ext", range($min_year, $max_year));
	$result .= sed_selectbox($s_month, "rmonth$ext", array_keys($months), array_values($months));
	$result .= sed_selectbox($s_day, "rday$ext", range(1, 31));

	if ($mode == 'short')
	{
		return $result;
	}

	$range = array();
	for ($i = 0; $i < 24; $i++)
	{
		$range[] = sprintf('%02d', $i);
	}
	$result .= sed_selectbox($s_hour, "rhour$ext", $range);
	$result .= $R['code_time_separator'];
	$range = array();
	for ($i = 0; $i < 60; $i++)
	{
		$range[] = sprintf('%02d', $i);
	}
	$result .= sed_selectbox($s_minute, "rminute$ext", $range);

	return $result;
}

/**
 * Returns language selection dropdown
 *
 * @param string $chosen Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_lang($chosen, $name)
{
	global $sed_languages, $sed_countries, $cfg;

	$handle = opendir($cfg['lang_dir'].'/');
	while ($f = readdir($handle))
	{
		if ($f[0] != '.')
		{
			$langlist[] = $f;
		}
	}
	closedir($handle);
	sort($langlist);

	if (!$sed_countries) include_once sed_langfile('countries', 'core');

	$vals = array();
	$titles = array();
	foreach ($langlist as $lang)
	{
		$vals[] = $lang;
		$titles[] = (empty($sed_languages[$lang]) ? $sed_countries[$lang] : $sed_languages[$lang]) . " ($lang)";
	}
	return sed_selectbox($chosen, $name, $vals, $titles);
}

/**
 * Generates a textarea
 *
 * @param string $name Input name
 * @param string $value Entered value
 * @param int $rows Number of rows
 * @param int $cols Number of columns
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $custom_rc Custom resource string name
 * @return string
 */
function sed_textarea($name, $value, $rows, $cols, $attrs = '', $custom_rc = '')
{
	$input_attrs = sed_rc_attr_string($attrs);
	$rc = empty($custom_rc) ? 'input_textarea' : $custom_rc;
	return sed_rc($rc, array(
		'name' => $name,
		'value' => htmlspecialchars($value),
		'rows' => $rows,
		'cols' => $cols,
		'attrs' => $input_attrs
	));
}

?>
