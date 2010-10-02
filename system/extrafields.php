<?php

/**
 * Extrafields API
 *
 * @package Cotonti
 * @version 0.9.0
 * @author esclkm
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

/**
 * Returns Extra fields edit fields
 *
 * @param string $name Variable name
 * @param array $extrafields Extra fields data
 * @param string $data Existing data for fields
 * @return string
 */
function cot_build_extrafields($name, $extrafield, $data)
{
	global $L, $R;
	$rc_name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;
	$data = ($data == null) ? $extrafield['field_default'] : $data;

	switch($extrafield['field_type'])
	{
		case "input":
			$R["input_text_{$rc_name}"] = (!empty($R["input_text_{$rc_name}"])) ? $R["input_text_{$rc_name}"] : $extrafield['field_html'];
			$result = cot_inputbox('text', $name, htmlspecialchars($data));
			break;

		case "textarea":
			$R["input_textarea_{$rc_name}"] =(!empty($R["input_textarea_{$rc_name}"])) ? $R["input_textarea_{$rc_name}"] : $extrafield['field_html'];
			$result = cot_textarea($name, htmlspecialchars($data), 4, 56);
			break;

		case "select":
			$R["input_select_{$rc_name}"] = (!empty($R["input_select_{$rc_name}"])) ? $R["input_select_{$rc_name}"] : $extrafield['field_html'];
			$extrafield['field_variants'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_variants']);
			$opt_array = explode(",", $extrafield['field_variants']);
			$ii = 0;
			foreach ($opt_array as $var)
			{
				$ii++;
				$options_titles[$ii] = (!empty($L[$extrafield['field_name'].'_'.$var])) ? $L[$extrafield['field_name'].'_'.$var] : $var;
				$options_values[$ii] .= trim($var);
			}
			$result = cot_selectbox(trim($data), $name, $options_values, $options_titles, false);
			break;

		case "radio":
			$R["input_radio_{$rc_name}"] = (!empty($R["input_radio_{$rc_name}"])) ? $R["input_radio_{$rc_name}"] :  $extrafield['field_html'];
			$extrafield['field_variants'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_variants']);
			$opt_array = explode(",", $extrafield['field_variants']);
			if (count($opt_array) > 0)
			{
				$ii = 0;
				foreach ($opt_array as $var)
				{
					$ii++;
					$options_titles[$ii] = (!empty($L[$extrafield['field_name'].'_'.$var])) ? $L[$extrafield['field_name'].'_'.$var] : $var;
					$options_values[$ii] .= trim($var);
				}
			}
			$result = cot_radiobox(trim($data), $name, $options_values, $options_titles);
			break;

		case "checkbox":
			$R["input_checkbox_{$rc_name}"] = (!empty($R["input_checkbox_{$rc_name}"])) ? $R["input_checkbox_{$rc_name}"] : $extrafield['field_html'];
			$result = cot_checkbox($data, $name, $extrafield['field_description']);
			break;

		case "datetime":
			$R["input_date_{$rc_name}"] = (!empty($R["input_date_{$rc_name}"])) ? $R["input_date_{$rc_name}"] : $extrafield['field_html'];
			$result = cot_selectbox_date($data, 'long', $name);
			break;
	}
	return $result;
}

/**
 * Imports Extra fields data
 *
 * @param string $name Variable name
 * @param array $extrafields Extra fields data
 * @param string $source Source type: G (GET), P (POST), C (COOKIE) or D (variable filtering)
 * @return string
 */
function cot_import_extrafields($name, $extrafield, $source='P')
{
	global $L;
	switch($extrafield['field_type'])
	{
		case "input":
			$import = cot_import($inputname, $source, 'HTM');
			if (!empty($extrafield['field_variants']) && !is_null($import) && !preg_match($extrafield['field_variants'], $import))
			{
				$L['field_pregmatch_'.$extrafield['field_name']] = (isset($L['field_pregmatch_'.$extrafield['field_name']])) ? $L['field_pregmatch_'.$extrafield['field_name']] : $L['field_pregmatch'];
				cot_error('field_pregmatch_'.$extrafield['field_name'], $name);
			}
			break;

		case "textarea":
			$import = cot_import($inputname, $source, 'HTM');
			break;

		case "select":
		case "radio":
			$extrafield['field_variants'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_variants']);
			$opt_array = explode(",", trim($extrafield['field_variants']));
			$import = cot_import($inputname, $source, 'HTM');
			if(!is_null($import) && !in_array(trim($import), $opt_array))
			{
				$L['field_notinarray_'.$extrafield['field_name']] = (isset($L['field_notinarray_'.$extrafield['field_name']])) ? $L['field_notinarray_'.$extrafield['field_name']] : $L['field_notinarray'];
				cot_error('field_notinarray_'.$extrafield['field_name'], $name);
			}
			break;

		case "checkbox":
			$import = cot_import($inputname, $source, 'BOL') ? 1 : 0;
			break;

		case "datetime":
			$import = cot_import_date($inputname, true, false, $source);
			break;
	}
	if (is_null($import) && $extrafield['field_required'])
	{
		$L['field_required_'.$extrafield['field_name']] = (isset($L['field_required_'.$extrafield['field_name']])) ? $L['field_required_'.$extrafield['field_name']] : $L['field_required'];
		cot_error('field_required_'.$extrafield['field_name'], $name);
	}
	return  $import;
}

/**
 * Returns Extra fields data
 *
 * @param string $name Lang row
 * @param array $extrafields Extra fields data
 * @param string $value Existing user value
 * @return string
 */
function cot_build_extrafields_data($name, $extrafield, $value)
{
	global $L;
	$value = htmlspecialchars($value);
	$parse_type = array('HTML', 'BBCode', 'Text');
	switch($extrafield['field_type'])
	{
		case "input":
		case "textarea":
			if($extrafield['field_parse'] == 'BBCode')
			{
				$value = cot_parse($value);
			}
			if($extrafield['field_parse'] == 'Text')
			{
				$value = htmlspecialchars($value);
			}
			return $value;
			break;

		case "select":
		case "radio":
			$value = htmlspecialchars($value);
			return (!empty($L[$name.'_'.$extrafield['field_name'].'_'.$value])) ? $L[$name.'_'.$extrafield['field_name'].'_'.$value] : $value;
			break;

		case "checkbox":
			return $value;
			break;

		case "datetime":
			return @date($cfg['dateformat'], $value + $usr['timezone'] * 3600);
			break;
	}
}

/**
 * Loads extrafields data into global
 * @global array $cot_extrafields
 */
function cot_load_extrafields()
{
	global $cot_dbc, $cot_extrafields, $db_extra_fields, $cot_cache;
	if (!$cot_extrafields && $cot_dbc)
	{
		$cot_extrafields = array();
		$fieldsres = cot_db_query("SELECT * FROM $db_extra_fields WHERE 1");
		while ($row = cot_db_fetchassoc($fieldsres))
		{
			$cot_extrafields[$row['field_location']][$row['field_name']] = $row;
		}
		$cot_extrafields['structure'] = (!empty($cot_extrafields[$GLOBALS['db_structure']])) ? $cot_extrafields[$GLOBALS['db_structure']] : array();
		cot_db_freeresult($fieldsres);
		$cot_cache && $cot_cache->db->store('cot_extrafields', $cot_extrafields, 'system');
	}
}

/* ======== Extrafields Pre-load ======== */

cot_load_extrafields();

?>