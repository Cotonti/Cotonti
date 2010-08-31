<?php

/**
 * Returns Extra fields edit fields
 *
 * @param string $rowname Post/SQL/Lang row
 * @param array $extrafields Extra fields data
 * @param string $data Existing data for fields
 * @param bool $importnew Import type new
 * @return string
 */
function sed_build_extrafields($rowname, $extrafield, $data, $importnew = FALSE)
{
	global $L, $R;
	$inputname = ($importnew) ? 'new' : 'r';
	$inputname .= $rowname.$extrafield['field_name'];

	switch($extrafield['field_type'])
	{
		case "input":
			$R["input_text_{$inputname}"] = (!empty($R["input_text_{$inputname}"])) ? $R["input_text_{$inputname}"] : $extrafield['field_html'];
			$result = sed_inputbox('text', $inputname, htmlspecialchars($data));
			break;

		case "textarea":
			$R["input_textarea_{$inputname}"] =(!empty($R["input_textarea_{$inputname}"])) ? $R["input_textarea_{$inputname}"] : $extrafield['field_html'];
			$result = sed_textarea($inputname, htmlspecialchars($data), 4, 56);
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
			$result = sed_selectbox(trim($data), $inputname, $options_values, $options_titles, false);
			break;

		case "checkbox":
			$R["input_checkbox_{$inputname}"] = (!empty($R["input_checkbox_{$inputname}"])) ? $R["input_checkbox_{$inputname}"] : $extrafield['field_html'];
			$result = sed_checkbox($data, $inputname, $extrafield['field_description']);
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
			$result = sed_radiobox(trim($data), $inputname, $options_values, $options_titles);
			break;
	}
	return $result;
}

/**
 * Imports Extra fields data
 *
 * @param string $rowname Post/SQL/Lang row
 * @param array $extrafields Extra fields data
 * @param string $data Existing data for fields
 * @param bool $importnew Import type new
 * @return string
 */
function sed_import_extrafields($rowname, $extrafield, $data, $importnew = FALSE)
{
	$inputname = ($importnew) ? 'new' : 'r';
	$inputname .= $rowname.$extrafield['field_name'];
	return '';
}

/**
 * Returns Extra fields data
 *
 * @param string $rowname Lang row
 * @param string $type Extra field type
 * @param string $field_name Extra field name
 * @param string $value Existing user value
 * @return string
 */
function sed_build_extrafields_data($rowname, $type, $field_name, $value)
{
	global $L;
	$value = htmlspecialchars($value);
	switch($type)
	{
		case "input":
			return $value;
			break;

		case "textarea":
			return $value;
			break;

		case "select":
			return (!empty($L[$rowname.'_'.$field_name.'_'.$value])) ? $L[$rowname.'_'.$field_name.'_'.$value] : $value;
			break;

		case "checkbox":
			return $value;
			break;

		case "radio":
			return (!empty($L[$rowname.'_'.$field_name.'_'.$value])) ? $L[$rowname.'_'.$field_name.'_'.$value] : $value;
			break;
	}
}

/**
 * Loads extrafields data into global
 * @global array $sed_extrafields
 */
function sed_load_extrafields()
{
	global $sed_dbc, $sed_extrafields, $db_extra_fields, $cot_cache;
	if (!$sed_extrafields && $sed_dbc)
	{
		$sed_extrafields = array();
		$sed_extrafields['structure'] = array();
		$sed_extrafields['pages'] = array();
		$sed_extrafields['users'] = array();
		$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE 1");
		while ($row = sed_sql_fetchassoc($fieldsres))
		{
			$sed_extrafields[$row['field_location']][$row['field_name']] = $row;
		}
		sed_sql_freeresult($fieldsres);
		$cot_cache && $cot_cache->db->store('sed_extrafields', $sed_extrafields, 'system');
	}
}

/* ======== Extrafields Pre-load ======== */

sed_load_extrafields();

?>