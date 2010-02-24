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
	global $L;
	$inputname = ($importnew) ? 'new' : 'r';
	$inputname .= $rowname.$extrafield['field_name'];

	$t2 = $extrafield['field_html'];
	switch($extrafield['field_type'])
	{
		case "input":
			$t2 = str_replace('<input ', '<input name="'.$inputname.'" ', $t2);
			$t2 = str_replace('<input ', '<input value="'.htmlspecialchars($data).'" ', $t2);
			break;

		case "textarea":
			$t2 = str_replace('<textarea ', '<textarea name="'.$inputname.'" ', $t2);
			$t2 = str_replace('</textarea>', htmlspecialchars($data).'</textarea>', $t2);
			break;

		case "select":
			$t2 = str_replace('<select', '<select name="'.$inputname.'"', $t2);
			$options = "";
			$opt_array = explode(",", $extrafield['field_variants']);
			if (count($opt_array) > 0)
			{
				foreach ($opt_array as $var)
				{
					$var_text = (!empty($L[$rowname.'_'.$extrafield['field_name'].'_'.$var])) ? $L[$rowname.'_'.$extrafield['field_name'].'_'.$var] : $var;
					$sel = (trim($var) == trim($data)) ? ' selected="selected"' : '';
					$options .= '<option value="'.$var.'" '.$sel.'>'.$var_text.'</option>';
				}
			}
			$t2 = str_replace('</select>', $options.'</select>', $t2);
			break;

		case "checkbox":
			$t2 = str_replace('<input', '<input name="'.$inputname.'"', $t2);
			$sel = ($data == 1) ? ' checked="checked"' : '';
			$t2 = str_replace('<input ', '<input value="on" '.$sel.' ', $t2);
			break;

		case "radio":

			$t2 = str_replace('<input', '<input name="'.$inputname.'"', $t2);
			$options = "";
			$opt_array = explode(",", $extrafield['field_variants']);
			if (count($opt_array) > 0)
			{
				foreach ($opt_array as $var)
				{
					$var_text = (!empty($L[$rowname.'_'.$extrafield['field_name'].'_'.$var])) ? $L[$rowname.'_'.$extrafield['field_name'].'_'.$var] : $var;
					$sel = (trim($var) == trim($data)) ? ' checked="checked"' : '';
					$options .= str_replace('/>', 'value="'.$var.'"'.$sel.' />'.$var_text.'&nbsp;&nbsp;', $t2);
				}
			}
			$t2 = $options;
			break;
	}
	return $t2;
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

/* ======== Extrafields Pre-load ======== */

if (!$sed_extrafields)
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
	$cot_cache && $cot_cache->db->store('sed_extrafields', $sed_extrafields, 'system');
}

?>