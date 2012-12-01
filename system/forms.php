<?php

/**
 * Form generation API
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Registers textarea instances to inform RichText editors that they need to be loaded
 */
$cot_textarea_count = 0;

/**
 * Generates a checkbox output
 * @param bool $chosen Checkbox state
 * @param string $name Input name
 * @param string $title Option caption
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $value Input value (passed), defaults to 'on' or '1'
 * @param string $custom_rc Custom resource string name
 * @return string
 */
function cot_checkbox($chosen, $name, $title = '', $attrs = '', $value = '1', $custom_rc = '')
{
	global $R;
	$input_attrs = cot_rc_attr_string($attrs);
	$value_off = (is_array($value)) ? $value[0] : 0;
	$value = (is_array($value)) ? $value[1] : $value;
	$chosen = cot_import_buffered($name, $chosen);
	$checked = $chosen ? ' checked="checked"' : '';
	$rc_name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;
	$rc = empty($custom_rc)
		? empty($R["input_checkbox_{$rc_name}"]) ? 'input_checkbox' : "input_checkbox_{$rc_name}"
		: $custom_rc;
	return cot_rc($rc, array(
		'value' => htmlspecialchars(cot_import_buffered($name, $value)),
		'value_off' => $value_off,
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
function cot_inputbox($type, $name, $value = '', $attrs = '', $custom_rc = '')
{
	global $R, $cfg;
	$input_attrs = cot_rc_attr_string($attrs);
	$rc_name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;
	$rc = empty($custom_rc)
		? (empty($R["input_{$type}_{$rc_name}"]) ? "input_$type" : "input_{$type}_{$rc_name}")
		: $custom_rc;
	if (!isset($R[$rc]))
	{
		$rc = 'input_default';
	}
	$error = $cfg['msg_separate'] ? cot_implode_messages($name, 'error') : '';
	return cot_rc($rc, array(
		'type' => $type,
		'name' => $name,
		'value' => htmlspecialchars((string)cot_import_buffered($name, $value)),
		'attrs' => $input_attrs,
		'error' => $error
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
 * @param string $custom_rc Custom resource string name
 * @return string
 */
function cot_radiobox($chosen, $name, $values, $titles = array(), $attrs = '', $separator = '', $custom_rc = '')
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
	$input_attrs = cot_rc_attr_string($attrs);
	$chosen = cot_import_buffered($name, $chosen);
	if (empty($separator))
	{
		$separator = $R['input_radio_separator'];
	}
	$i = 0;
	$result = '';
	$rc_name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;
	$rc = empty($custom_rc)
		? empty($R["input_radio_{$rc_name}"]) ? 'input_radio' : "input_radio_{$rc_name}"
		: $custom_rc;
	foreach ($values as $k => $x)
	{
		$checked = ($x == $chosen) ? ' checked="checked"' : '';
		$title = $use_titles ? htmlspecialchars($titles[$k]) : htmlspecialchars($x);
		if ($i > 0)
		{
			$result .= $separator;
		}
		$result .= cot_rc($rc, array(
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
 * @param string $custom_rc Custom resource string name
 * @param bool $htmlspecialchars_bypass Bypass htmlspecialchars() in values
 * @return string
 */
function cot_selectbox($chosen, $name, $values, $titles = array(), $add_empty = true, $attrs = '', $custom_rc = '', $htmlspecialchars_bypass = false)
{
	global $R, $cfg;

	if (!is_array($values))
	{
		$values = explode(',', $values);
	}
	if (!is_array($titles))
	{
		$titles = explode(',', $titles);
	}
	$use_titles = count($values) == count($titles);
	$input_attrs = cot_rc_attr_string($attrs);
	$chosen = cot_import_buffered($name, $chosen);
	$multi = is_array($chosen) && isset($input_attrs['multiple']);
	$error = $cfg['msg_separate'] ? cot_implode_messages($name, 'error') : '';
	$rc_name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;

	$selected = (is_null($chosen) || $chosen === '' || $chosen == '00') ? ' selected="selected"' : '';
	$rc = empty($R["input_option_{$rc_name}"]) ? 'input_option' : "input_option_{$rc_name}";
	if ($add_empty)
	{
		$options .= cot_rc($rc, array(
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
		$options .= cot_rc($rc, array(
			'value' => $htmlspecialchars_bypass ? $x : htmlspecialchars($x),
			'selected' => $selected,
			'title' => $title
		));
	}
	$rc = empty($custom_rc)
		? empty($R["input_select_{$rc_name}"]) ? 'input_select' : "input_select_{$rc_name}"
		: $custom_rc;
	$result .= cot_rc($rc, array(
		'name' => $name,
		'attrs' => $input_attrs,
		'error' => $error,
		'options' => $options
	));
	return $result;
}

/**
 * Renders country dropdown
 *
 * @param string $chosen Seleced value
 * @param string $name Dropdown name
 * @param bool $add_empty Add empty language option
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $custom_rc Custom resource string name
 * @param string $custom_rc Custom resource string name
 * @return string
 */
function cot_selectbox_countries($chosen, $name, $add_empty = true, $attrs = '', $custom_rc = '')
{
	global $cot_countries;

	if (!$cot_countries)
		include_once cot_langfile('countries', 'core');

	$codes = array_keys($cot_countries);
	$names = array_values($cot_countries);

	if ($add_empty)
	{
		array_unshift($codes, '00');
		array_unshift($names, '---');
	}

	return cot_selectbox($chosen, $name, $codes, $names, false, $attrs, $custom_rc);
}

/**
 * Generates date part dropdown
 *
 * @param int $utime Selected timestamp
 * @param string $mode Display mode: 'short' or complete
 * @param string $name Variable name preffix
 * @param int $max_year Max. year possible
 * @param int $min_year Min. year possible
 * @param bool $usertimezone Use user timezone
 * @param string $custom_rc Custom resource string name
 * @return string
 */
function cot_selectbox_date($utime, $mode = 'long', $name = '', $max_year = 2030, $min_year = 2000, $usertimezone = true, $custom_rc = '')
{
	global $L, $R, $usr;
	$rc_name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;

	$utime = ($usertimezone && $utime > 0) ? ($utime + $usr['timezone'] * 3600) : $utime;

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

	$year = cot_selectbox($s_year, $name.'[year]', range($min_year, $max_year));
	$month = cot_selectbox($s_month, $name.'[month]', array_keys($months), array_values($months));
	$day = cot_selectbox($s_day, $name.'[day]', range(1, 31));

	$range = array();
	for ($i = 0; $i < 24; $i++)
	{
		$range[] = sprintf('%02d', $i);
	}
	$hour = cot_selectbox($s_hour, $name.'[hour]', $range);

	$range = array();
	for ($i = 0; $i < 60; $i++)
	{
		$range[] = sprintf('%02d', $i);
	}

	$minute = cot_selectbox($s_minute, $name.'[minute]', $range);

	$rc = empty($R["input_date_{$mode}"]) ? 'input_date' : "input_date_{$mode}";
	$rc = empty($R["input_date_{$rc_name}"]) ? $rc : "input_date_{$rc_name}";
	$rc = empty($custom_rc) ? $rc : $custom_rc;

	$result = cot_rc($rc, array(
		'day' => $day,
		'month' => $month,
		'year' => $year,
		'hour' => $hour,
		'minute' => $minute
	));

	return $result;
}

/**
 * Returns language selection dropdown
 *
 * @param string $chosen Seleced value
 * @param string $name Dropdown name
 * @param bool $add_empty Add empty language option
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $custom_rc Custom resource string name
 * @return string
 */
function cot_selectbox_lang($chosen, $name, $add_empty = false, $attrs = '', $custom_rc = '')
{
	global $cot_languages, $cot_countries, $cfg;

	$handle = opendir($cfg['lang_dir'] . '/');
	while ($f = readdir($handle))
	{
		if ($f[0] != '.' && is_dir($cfg['lang_dir'] . '/' . $f))
		{
			$langlist[] = $f;
		}
	}
	closedir($handle);
	sort($langlist);

	if (!$cot_countries)
		include_once cot_langfile('countries', 'core');

	$vals = array();
	$titles = array();
	foreach ($langlist as $lang)
	{
		$vals[] = $lang;
		$titles[] = (empty($cot_languages[$lang]) ? $cot_countries[$lang] : $cot_languages[$lang]) . " ($lang)";
	}
	return cot_selectbox($chosen, $name, $vals, $titles, $add_empty, $attrs, $custom_rc);
}

/**
 * Returns timezone selection dropdown
 *
 * @param string $chosen Seleced value, must be one of PHP supported timezone identifiers.
 * @param string $name Form input name
 * @param bool $add_gmt Add GMT option at the top
 * @param bool $dst Show offsets including DST, if DST is currently in effect at the timezone.
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $custom_rc Custom resource string name
 * @return string
 */
function cot_selectbox_timezone($chosen, $name, $add_gmt = true, $dst = false, $attrs = '', $custom_rc = '')
{
	$timezonelist = cot_timezone_list($add_gmt, $dst);
	foreach($timezonelist as $timezone)
	{
		$names[] = $timezone['identifier'];
		$titles[] = $timezone['description'];
	}
	return cot_selectbox($chosen, $name, $names, $titles, false, $attrs, $custom_rc);
}

/**
 * Renders stucture dropdown
 *
 * @param string $extension Extension code
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param string $subcat Show only subcats of selected category
 * @param bool $hideprivate Hide private categories
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @return string
 * @global CotDB $db
 */
function cot_selectbox_structure($extension, $check, $name, $subcat = '', $hideprivate = true, $is_module = true)
{
	global $db, $db_structure, $usr, $structure, $L, $R;

	$structure[$extension] = (is_array($structure[$extension])) ? $structure[$extension] : array();

	$result_array = array();
	foreach ($structure[$extension] as $i => $x)
	{
		$display = ($hideprivate && $is_module) ? cot_auth($extension, $i, 'W') : true;
		if ($display && !empty($subcat) && isset($structure[$extension][$subcat]) && !(empty($check)))
		{
			$mtch = $structure[$extension][$subcat]['path'].".";
			$mtchlen = mb_strlen($mtch);
			$display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i == $check) ? true : false;
		}

		if ((!$is_module || cot_auth($extension, $i, 'R')) && $i!='all' && $display)
		{
			$result_array[$i] = $x['tpath'];
		}
	}
	$result = cot_selectbox($check, $name, array_keys($result_array), array_values($result_array), false);

	return($result);
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
function cot_textarea($name, $value, $rows, $cols, $attrs = '', $custom_rc = '')
{
	global $cot_textarea_count, $R;
	$cot_textarea_count++;
	$input_attrs = cot_rc_attr_string($attrs);
	$rc_name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;
	$rc = empty($custom_rc)
		? (empty($R["input_textarea_{$rc_name}"]) ? 'input_textarea' : "input_textarea_{$rc_name}")
		: $custom_rc;
	$error = $cfg['msg_separate'] ? cot_implode_messages($name, 'error') : '';
	return cot_rc($rc, array(
		'name' => $name,
		'value' => htmlspecialchars(cot_import_buffered($name, $value)),
		'rows' => $rows,
		'cols' => $cols,
		'attrs' => $input_attrs,
		'error' => $error
	));
}

/**
 * Generates a checklistbox output
 * @param mixed $chosen Checkbox state
 * @param string $name Input name
 * @param array $values Options available
 * @param array $titles Titles for options
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $separator Option separator, by default is taken from $R['input_radio_separator']
 * @param bool $addnull add nullvalue field for easycheck if chechlisybox is isset on the form
 * @param string $custom_rc Custom resource string name
 * @return string
 */
function cot_checklistbox($chosen, $name, $values, $titles = array(), $attrs = '', $separator = '', $addnull = true, $custom_rc = '')
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
	$input_attrs = cot_rc_attr_string($attrs);

	$chosen = cot_import_buffered($name, $chosen);

	if (empty($separator))
	{
		$separator = $R['input_radio_separator'];
	}

	$i = 0;
	$result = '';
	if ($addnull)
	{
		$result .= cot_inputbox('hidden', $name.'[nullval]', 'nullval');
	}
	$rc_name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;

	$rc = empty($custom_rc)
		? empty($R["input_check_{$rc_name}"]) ? 'input_check' : "input_check_{$rc_name}"
		: $custom_rc;
	foreach ($values as $k => $x)
	{
		$i++;
		$x = trim($x);
		$checked = (is_array($chosen) && in_array($x, $chosen)) || (!is_array($chosen) && $x == $chosen) ? ' checked="checked"' : '';
		$title = $use_titles ? htmlspecialchars($titles[$k]) : htmlspecialchars($x);
		if ($i > 1)
		{
			$result .= $separator;
		}
		$result .= cot_rc($rc, array(
			'value' => htmlspecialchars($x),
			'name' => $name.'['.$i.']',
			'checked' => $checked,
			'title' => $title,
			'attrs' => $input_attrs
		));

	}
	return $result;

}

/**
 * Generates a form input file from a resource string
 *
 * @param string $name Input name
 * @param string $value Entered value
 * @param string $filepath Entered filepath if defferent from value
 * @param string $delname  Delete file chechbox name
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $custom_rc Custom resource string name
 * @return string
 */
function cot_filebox($name, $value = '', $filepath = '', $delname ='', $attrs = '', $custom_rc = '')
{
	global $R, $cfg, $L;
	$input_attrs = cot_rc_attr_string($attrs);
	$rc_name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;

	$custom_rc = explode('|', $custom_rc, 2);
	if(empty($value))
	{
		$rc = empty($custom_rc[1])
			? (empty($R["input_filebox_{$rc_name}_empty"]) ? "input_filebox_empty" : "input_filebox_{$rc_name}_empty")
			: $custom_rc[1];
	}
	else
	{
		$rc = empty($custom_rc[0])
			? (empty($R["input_filebox_{$rc_name}"]) ? "input_filebox" : "input_filebox_{$rc_name}")
			: $custom_rc[0];
	}

	$filepath = empty($filepath) ? $value : $filepath;
	$delname = empty($delname) ? 'del'.$name : $delname;
	$error = $cfg['msg_separate'] ? cot_implode_messages($name, 'error') : '';
	return cot_rc($rc, array(
		'name' => $name,
		'filepath' => $filepath,
		'delname' => $delname,
		'value' => $value,
		'attrs' => $input_attrs,
		'error' => $error
	));
}

?>