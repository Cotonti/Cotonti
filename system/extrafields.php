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
 * @param string $rowname Post/SQL/Lang row
 * @param array $extrafields Extra fields data
 * @param string $data Existing data for fields
 * @param string $ext Variable name suffix
 * @return string
 */
function cot_build_extrafields($rowname, $extrafield, $data, $ext='')
{
	global $L, $R;
	$inputname .= 'r'.$rowname.$extrafield['field_name'];
	$inputnamefull = $inputname.$ext;
	$data = ($data == null) ? $extrafield['field_default'] : $data;
	
	switch($extrafield['field_type'])
	{
		case "input":
			$R["input_text_{$inputname}"] = (!empty($R["input_text_{$inputname}"])) ? $R["input_text_{$inputname}"] : $extrafield['field_html'];
			$result = cot_inputbox('text', $inputnamefull, htmlspecialchars($data));
			break;

		case "textarea":
			$R["input_textarea_{$inputname}"] =(!empty($R["input_textarea_{$inputname}"])) ? $R["input_textarea_{$inputname}"] : $extrafield['field_html'];
			$result = cot_textarea($inputnamefull, htmlspecialchars($data), 4, 56);
			break;

		case "select":
			$R["input_select_{$inputname}"] = (!empty($R["input_select_{$inputname}"])) ? $R["input_select_{$inputname}"] : $extrafield['field_html'];
			$opt_array = explode(",", $extrafield['field_variants']);
			$ii = 0;
			foreach ($opt_array as $var)
			{
				$ii++;
				$options_titles[$ii] = (!empty($L[$rowname.'_'.$extrafield['field_name'].'_'.$var])) ? $L[$rowname.'_'.$extrafield['field_name'].'_'.$var] : $var;
				$options_values[$ii] .= trim($var);
			}
			$result = cot_selectbox(trim($data), $inputnamefull, $options_values, $options_titles, false);
			break;

		case "radio":
			$R["input_radio_{$inputname}"] = (!empty($R["input_radio_{$inputname}"])) ? $R["input_radio_{$inputname}"] :  $extrafield['field_html'];
			$opt_array = explode(",", $extrafield['field_variants']);
			if (count($opt_array) > 0)
			{
				$ii = 0;
				foreach ($opt_array as $var)
				{
					$ii++;
					$options_titles[$ii] = (!empty($L[$rowname.'_'.$extrafield['field_name'].'_'.$var])) ? $L[$rowname.'_'.$extrafield['field_name'].'_'.$var] : $var;
					$options_values[$ii] .= trim($var);
				}
			}
			$result = cot_radiobox(trim($data), $inputnamefull, $options_values, $options_titles);
			break;

		case "checkbox":
			$R["input_checkbox_{$inputname}"] = (!empty($R["input_checkbox_{$inputname}"])) ? $R["input_checkbox_{$inputname}"] : $extrafield['field_html'];
			$result = cot_checkbox($data, $inputnamefull, $extrafield['field_description']);
			break;
	}
	return $result;
}

/**
 * Imports Extra fields data
 *
 * @param string $rowname Post/SQL/Lang row
 * @param array $extrafields Extra fields data
 * @param string $source Source type: G (GET), P (POST), C (COOKIE) or D (variable filtering)
 * @return string
 */
function cot_import_extrafields($rowname, $extrafield, $source='P')
{
	$inputname = ($source == 'D') ? $rowname[$extrafield['field_name']] : 'r'.$rowname.$extrafield['field_name'];

	$import = cot_import($inputname, $source, 'HTM');
	if ($extrafield['field_type'] == 'checkbox' && !is_null($import))
	{
		$import = $import != '';
	}
	return  $import;
}

/**
 * Returns Extra fields data
 *
 * @param string $rowname Lang row
 * @param array $extrafields Extra fields data
 * @param string $value Existing user value
 * @return string
 */
function cot_build_extrafields_data($rowname, $extrafield, $value)
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
			return (!empty($L[$rowname.'_'.$extrafield['field_name'].'_'.$value])) ? $L[$rowname.'_'.$extrafield['field_name'].'_'.$value] : $value;
			break;

		case "checkbox":
			return $value;
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