<?php
/**
 * Returns Extra fields edit fields
 *
 * @param string $rowname Post/SQL/Lang row
 * @param string $tpl_tag Template tag area
 * @param array $extrafields Extra fields data
 * @param array $data Existing data for fields
 * @param bool $importnew Import type new
 * @return array
 */
function sed_build_extrafields($rowname, $tpl_tag, $extrafields, $data = array(), $importnew = FALSE)
{
	global $L, $t, $global;
	$importrowname = ($importnew) ? 'new'.$rowname : 'r'.$rowname;
	foreach($extrafields as $i=>$row)
	{
		isset($L[$rowname.'_'.$row['field_name'].'_title']) ? $t->assign($tpl_tag.'_'.strtoupper($row['field_name']).'_TITLE', $L[$rowname.'_'.$row['field_name'].'_title']) : $t->assign($tpl_tag.'_'.strtoupper($row['field_name']).'_TITLE', $row['field_description']);
		$t1 = $tpl_tag.'_'.strtoupper($row['field_name']);
		$t2 = $row['field_html'];
		switch($row['field_type'])
		{
			case "input":
				$t2 = str_replace('<input ', '<input name="'.$importrowname.$row['field_name'].'" ', $t2);
				$t2 = str_replace('<input ', '<input value="'.htmlspecialchars($data[$rowname.'_'.$row['field_name']]).'" ', $t2);
			break;

			case "textarea":
				$t2 = str_replace('<textarea ', '<textarea name="'.$importrowname.$row['field_name'].'" ', $t2);
				$t2 = str_replace('</textarea>', htmlspecialchars($data[$rowname.'_'.$row['field_name']]).'</textarea>', $t2);
			break;

			case "select":
				$t2 = str_replace('<select', '<select name="'.$importrowname.$row['field_name'].'"', $t2);
				$options = "";
				$opt_array = explode(",", $row['field_variants']);
				if (count($opt_array) != 0)
				{
					foreach ($opt_array as $var)
					{
						$var_text = (!empty($L[$rowname.'_'.$row['field_name'].'_'.$var])) ? $L[$rowname.'_'.$row['field_name'].'_'.$var] : $var;
						$sel = ($var == $data[$rowname.'_'.$row['field_name']]) ? ' selected="selected"' : '';
						$options .= "<option value=\"$var\" $sel>$var_text</option>";
					}
				}
				$t2 = str_replace("</select>", "$options</select>", $t2);
			break;

			case "checkbox":
				$t2 = str_replace('<input', '<input name="'.$importrowname.$row['field_name'].'"', $t2);
				$sel = ($data[$rowname.'_'.$row['field_name']] == 1) ? ' checked' : '';
				$t2 = str_replace('<input ', '<input value="on" '.$sel.' ', $t2);
			break;

			case "radio":
				$t2 = str_replace('<input', '<input name="'.$importrowname.$row['field_name'].'"', $t2);
				$options = "";
				$opt_array = explode(",", $row['field_variants']);
				if (count($opt_array) != 0)
				{
					foreach ($opt_array as $var)
					{
						$var_text = (!empty($L[$rowname.'_'.$row['field_name'].'_'.$var])) ? $L[$rowname.'_'.$row['field_name'].'_'.$var] : $var;
						$sel = ($var == $data[$rowname.'_'.$row['field_name']]) ? ' checked="checked"' : '';
						$buttons .= str_replace('/>', 'value="'.$var.'"'.$sel.' />'.$var_text.'&nbsp;&nbsp;', $t2);
					}
				}
				$t2 = $buttons;
			break;
		}
		$return_arr[$t1] = $t2;
	}
	return $return_arr;
}

/**
 * Returns Extra fields date
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