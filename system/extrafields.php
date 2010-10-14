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
 * Extra fields - Return default base html-construction for various types of fields (without value= and name=)
 *
 * @access private
 * @param string $type Type of field (input, textarea etc)
 * @return string
 *
 */
function cot_default_html_construction($type)
{
	global $R;
	$html = '';
	switch($type)
	{
		case 'input':
			$html = $R['input_text'];
			break;

		case 'textarea':
			$html = $R['input_textarea'];
			break;

		case 'select':
			$html = $R['input_select'];
			break;

		case 'checkbox':
			$html = $R['input_checkbox'];
			break;

		case 'radio':
			$html = $R['input_radio'];
			break;

		case 'datetime':
			$html = $R['input_date'];
			break;
	}
	return $html;
}

/**
 * Add extra field for pages
 *
 * @param string $location Table for adding extrafield
 * @param string $name Field name (unique)
 * @param string $type Field type (input, textarea etc)
 * @param string $html HTML Resource string
 * @param string $variants Variants of values (for radiobuttons, selectors etc)
 * @param string $default Default value
 * @param bool $required Required field
 * @param string $parse Parsing Type (HTML, BBCodes)
 * @param string $description Description of field (optional, for admin)
 * @param bool $noalter Do not ALTER the table, just register the extra field
 * @return bool
 *
 */
function cot_extrafield_add($location, $name, $type, $html, $variants="", $default="", $required=0, $parse='HTML', $description="", $noalter = false)
{
	global $cot_db, $db_extra_fields;
	$fieldsres = $cot_db->query("SELECT field_name FROM $db_extra_fields WHERE field_location='$location'");
	while($row = $fieldsres->fetch())
	{
		$extrafieldsnames[] = $row['field_name'];
	}
	if(count($extrafieldsnames)>0) if (in_array($name,$extrafieldsnames)) return 0; // No adding - fields already exist

	// Check table cot_$sql_table - if field with same name exists - exit.
	if ($cot_db->query("SHOW COLUMNS FROM $location LIKE '%\_$name'")->rowCount() > 0 && !$noalter)
	{
		return false;
	}
	$fieldsres = $cot_db->query("SHOW COLUMNS FROM $location");
	while ($fieldrow = $fieldsres->fetch())
	{
		$column = $fieldrow['Field'];
		// get column prefix in this table
		$column_prefix = substr($column, 0, strpos($column, "_"));

		preg_match("#.*?_$name$#",$column,$match);
		if($match[1]!="" && !$noalter) return false; // No adding - fields already exist
		$i++;
	}

	$extf['location'] = $location;
	$extf['name'] = $name;
	$extf['type'] = $type;
	$extf['html'] = $html;
	$extf['variants'] = is_null($variants) ? '' : $variants;
	$extf['default'] = is_null($default) ? '' : $default;
	$extf['required'] = ($required > 0) ? 1 : 0;
	$extf['parse'] = is_null($parse) ? 'HTML' : $parse;
	$extf['description'] = is_null($description) ? '' : $description;

	$step1 = $cot_db->insert($db_extra_fields, $extf, 'field_') == 1;
	if ($noalter)
	{
		return $step1;
	}
	switch($type)
	{
		case 'input': $sqltype = "VARCHAR(255)";
			break;
		case 'textarea': $sqltype = "TEXT";
			break;
		case 'select': $sqltype = "VARCHAR(255)";
			break;
		case 'checkbox': $sqltype = "BOOL";
			break;
		case 'radio': $sqltype = "VARCHAR(255)";
			break;
		case 'datetime': $sqltype = "int(11) NOT NULL default '0'";
			break;
		case 'file': $sqltype = "VARCHAR(255)";
			break;
	}
	$sql = "ALTER TABLE $location ADD ".$column_prefix."_$name $sqltype ";
	$step2 = $cot_db->query($sql);
	$step3 = true;
	if ($type = 'file')
	{

	}
	return $step1 && $step2 && $step3;
}

/**
 * Update extra field for pages
 *
 * @param string $location Table contains extrafield
 * @param string $oldname Exist name of field
 * @param string $name Field name (unique)
 * @param string $html HTML Resource string
 * @param string $variants Variants of values (for radiobuttons, selectors etc)
 * @param string $default Default value
 * @param bool $required Required field
 * @param string $parse Parsing Type (HTML, BBCodes)
 * @param string $html HTML Resource string
 * @param string $variants Variants of values (for radiobuttons, selectors etc)
 * @param string $description Description of field (optional, for admin)
 * @return bool
 *
 */
function cot_extrafield_update($location, $oldname, $name, $type, $html, $variants="", $default="", $required=0, $parse='HTML', $description="")
{
	global $cot_db, $db_extra_fields;
	$fieldsres = $cot_db->query("SELECT COUNT(*) FROM $db_extra_fields
			WHERE field_name = '$oldname' AND field_location='$location'");
	if (cot_db_numrows($fieldsres) <= 0  || $name != $oldname && cot_db_numrows(cot_db_query("SHOW COLUMNS FROM $location LIKE '%\_$name'")) > 0)
	{
		// Attempt to edit non-extra field or override an existing field
		return false;
	}
	$field = $fieldsres->fetch();
	$fieldsres = $cot_db->query("SHOW COLUMNS FROM $location");
	$fieldrow = $fieldsres->fetch();
	$column = $fieldrow['Field'];
	$column_prefix = substr($column, 0, strpos($column, "_"));
	$alter = false;
	if ($name != $field['field_name'])
	{
		$extf['name'] = $name;
		$alter = true;
	}
	if ($type != $field['field_type'])
	{
		$extf['type'] = $type;
		$alter = true;
	}

	$extf['html'] = $html;
	$extf['parse'] = is_null($parse) ? 'HTML' : $parse;
	$extf['variants'] = is_null($variants) ? '' : $variants;
	$extf['default'] = is_null($default) ? '' : $default;
	$extf['required'] = ($required > 0) ? 1 : 0;
	$extf['description'] = is_null($description) ? '' : $description;

	$step1 = $cot_db->update($db_extra_fields, $extf, "field_name = '$oldname' AND field_location='$location'", 'field_') == 1;

	if (!$alter) return $step1;

	switch ($type)
	{
		case 'input': $sqltype = "VARCHAR(255)";
			break;
		case 'textarea': $sqltype = "TEXT";
			break;
		case 'select': $sqltype = "VARCHAR(255)";
			break;
		case 'checkbox': $sqltype = "BOOL";
			break;
		case 'radio': $sqltype = "VARCHAR(255)";
			break;
		case 'datetime': $sqltype = "int(11) NOT NULL default '0'";
			break;
		case 'file': $sqltype = "VARCHAR(255)";
			break;
	}
	$sql = "ALTER TABLE $location CHANGE ".$column_prefix."_$oldname ".$column_prefix."_$name $sqltype ";
	$step2 = $cot_db->query($sql);

	return $step1 && $step2;
}

/**
 * Delete extra field
 *
 * @param string $location Table contains extrafield
 * @param string $name Name of extra field
 * @return bool
 *
 */
function cot_extrafield_remove($location, $name)
{
	global $cot_db, $db_extra_fields;
	if ((int) $cot_db->query("SELECT COUNT(*) FROM $db_extra_fields
		WHERE field_name = '$name' AND field_location='$location'")->fetchColumn() <= 0)
	{
		// Attempt to remove non-extra field
		return false;
	}
	$fieldsres = $cot_db->query("SHOW COLUMNS FROM $location");
	$fieldrow = $fieldsres->fetch();
	$column = $fieldrow['Field'];
	$column_prefix = substr($column, 0, strpos($column, "_"));
	$step1 = $cot_db->delete($db_extra_fields, "field_name = '$name' AND field_location='$location'") == 1;
	$sql = "ALTER TABLE $location DROP ".$column_prefix."_".$name;
	$step2 = $cot_db->query($sql);
	return $step1 && $step2;
}

/**
 * Loads extrafields data into global
 * @global array $cot_extrafields
 */
function cot_load_extrafields()
{
	global $cot_db, $cot_dbc, $cot_extrafields, $db_extra_fields, $cot_cache;
	if (!$cot_extrafields && $cot_dbc)
	{
		$cot_extrafields = array();
		$fieldsres = $cot_db->query("SELECT * FROM $db_extra_fields WHERE 1");
		while ($row = $fieldsres->fetch())
		{
			$cot_extrafields[$row['field_location']][$row['field_name']] = $row;
		}

		$fieldsres->closeCursor();
		$cot_cache && $cot_cache->db->store('cot_extrafields', $cot_extrafields, 'system');
	}
}

/* ======== Extrafields Pre-load ======== */

cot_load_extrafields();
$GLOBALS['cot_extrafields']['structure'] = (!empty($GLOBALS['cot_extrafields'][$GLOBALS['db_structure']])) ? $GLOBALS['cot_extrafields'][$GLOBALS['db_structure']] : array();

?>