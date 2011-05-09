<?php

/**
 * Extrafields API
 *
 * @package Cotonti
 * @version 0.9.0
 * @author esclkm
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

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

	switch ($extrafield['field_type'])
	{
		case 'input':
		case 'inputint':
		case 'currency':
		case 'double':	
			$R["input_text_{$rc_name}"] = (!empty($R["input_text_{$rc_name}"])) ? $R["input_text_{$rc_name}"] : $extrafield['field_html'];
			$result = cot_inputbox('text', $name, $data);
			break;

		case 'textarea':
			$R["input_textarea_{$rc_name}"] = (!empty($R["input_textarea_{$rc_name}"])) ? $R["input_textarea_{$rc_name}"] : $extrafield['field_html'];
			$result = cot_textarea($name, $data, 4, 56);
			break;

		case 'select':
			$R["input_select_{$rc_name}"] = (!empty($R["input_select_{$rc_name}"])) ? $R["input_select_{$rc_name}"] : $extrafield['field_html'];
			$extrafield['field_variants'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_variants']);
			$opt_array = explode(",", $extrafield['field_variants']);
			$ii = 0;
			foreach ($opt_array as $var)
			{
				$ii++;
				$options_titles[$ii] = (!empty($L[$extrafield['field_name'] . '_' . $var])) ? $L[$extrafield['field_name'] . '_' . $var] : $var;
				$options_values[$ii] .= trim($var);
			}
			$result = cot_selectbox(trim($data), $name, $options_values, $options_titles, false);
			break;

		case 'radio':
			$R["input_radio_{$rc_name}"] = (!empty($R["input_radio_{$rc_name}"])) ? $R["input_radio_{$rc_name}"] : $extrafield['field_html'];
			$extrafield['field_variants'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_variants']);
			$opt_array = explode(",", $extrafield['field_variants']);
			if (count($opt_array) > 0)
			{
				$ii = 0;
				foreach ($opt_array as $var)
				{
					$ii++;
					$options_titles[$ii] = (!empty($L[$extrafield['field_name'] . '_' . $var])) ? $L[$extrafield['field_name'] . '_' . $var] : $var;
					$options_values[$ii] .= trim($var);
				}
			}
			$result = cot_radiobox(trim($data), $name, $options_values, $options_titles);
			break;

		case 'checkbox':
			$R["input_checkbox_{$rc_name}"] = (!empty($R["input_checkbox_{$rc_name}"])) ? $R["input_checkbox_{$rc_name}"] : $extrafield['field_html'];
			$result = cot_checkbox($data, $name, $extrafield['field_description']);
			break;

		case 'datetime':
			$R["input_date_{$rc_name}"] = (!empty($R["input_date_{$rc_name}"])) ? $R["input_date_{$rc_name}"] : $extrafield['field_html'];
			$result = cot_selectbox_date($data, 'long', $name);
			break;

		case 'file':
			$R["input_text_{$rc_name}"] = (!empty($R["input_text_{$rc_name}"])) ? $R["input_text_{$rc_name}"] : $extrafield['field_html'];

			$result['FILE'] = cot_inputbox('file', $name, '');
			$result['DELETE'] = cot_checkbox(false, 'rdel_' . $name, $L['Delete']);
			$result['LINK'] = htmlspecialchars($data);
			break;

		default:
			$result = '';
			break;
	}
	return $result;
}

/**
 * Imports Extra fields data
 *
 * @param string $inputname Variable name (or value for source=D)
 * @param array $extrafields Extra fields data
 * @param string $source Source type: G (GET), P (POST), C (COOKIE) or D (variable filtering)
 * @param string $oldvalue Old value of extrafield
 * @return string
 */
function cot_import_extrafields($inputname, $extrafield, $source='P', $oldvalue='')
{
	global $L;
	switch ($extrafield['field_type'])
	{
		case 'input':
			$import = ($extrafield['field_parse'] == 'Text') ? cot_import($inputname, $source, 'TXT') : cot_import($inputname, $source, 'HTM');
			if (!empty($extrafield['field_variants']) && !is_null($import) && !preg_match($extrafield['field_variants'], $import))
			{
				$L['field_pregmatch_' . $extrafield['field_name']] = (isset($L['field_pregmatch_' . $extrafield['field_name']])) ? $L['field_pregmatch_' . $extrafield['field_name']] : $L['field_pregmatch'];
				cot_error('field_pregmatch_' . $extrafield['field_name'], $name);
			}
			break;

		case 'inputint':
			$import = cot_import($inputname, $source, 'INT');
			$import = ((int)$import > 0) ? (int)$import : 0;
			break;
		case 'currency':
		case 'double':	
			$import = cot_import($inputname, $source, 'NUM');
			$import = (doubleval($import) != 0) ? doubleval($import) : 0;
			break;

		case 'textarea':
			$import = cot_import($inputname, $source, 'HTM');
			break;

		case 'select':
		case 'radio':
			$extrafield['field_variants'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_variants']);
			$opt_array = explode(",", trim($extrafield['field_variants']));
			$import = cot_import($inputname, $source, 'HTM');
			if (!is_null($import) && !in_array(trim($import), $opt_array))
			{
				$L['field_notinarray_' . $extrafield['field_name']] = (isset($L['field_notinarray_' . $extrafield['field_name']])) ? $L['field_notinarray_' . $extrafield['field_name']] : $L['field_notinarray'];
				cot_error('field_notinarray_' . $extrafield['field_name'], $name);
			}
			break;

		case 'checkbox':
			$import = cot_import($inputname, $source, 'BOL') ? 1 : 0;
			break;

		case 'datetime':
			$import = cot_import_date($inputname, true, false, $source);
			break;

		case 'file':
			global $lang, $cot_translit, $exfldfiles, $exfldsize, $cfg, $uploadfiles;
			if ($source == 'P')
			{
				$import = $_FILES[$inputname];
				$import['delete'] = cot_import('rdel_' . $inputname, 'P', 'BOL') ? 1 : 0;
			}
			elseif ($source == 'D')
			{
				$import = $inputname;
			}
			if (is_array($import) && !$import['error'] && !empty($import['name']))
			{
				$fname = mb_substr($import['name'], 0, mb_strrpos($import['name'], '.'));
				$ext = mb_strtolower(mb_substr($import['name'], mb_strrpos($import['name'], '.') + 1));

				$fname = ($lang != 'en' && is_array($cot_translit)) ? strtr($fname, $cot_translit) : '';
				$fname = str_replace(' ', '_', $fname);
				$fname = preg_replace('#[^a-zA-Z0-9\-_\.\ \+]#', '', $fname);
				$fname = str_replace('..', '.', $fname);
				$fname = (empty($fname)) ? cot_unique() : $fname;

				$fname .= (file_exists("{$cfg['extrafield_files_dir']}/$fname.$ext") && $oldvalue != $fname . '.' . $ext) ? date("YmjGis") : '';

				$fname .= '.' . $ext;

				$file['old'] = (!empty($oldvalue) && ($import['delete'] || $import['tmp_name'])) ? "{$cfg['extrafield_files_dir']}/$oldvalue" : '';
				$file['field'] = $extrafield['field_name'];
				$file['tmp'] = (!$import['delete']) ? $import['tmp_name'] : '';
				$file['new'] = (!$import['delete']) ? "{$cfg['extrafield_files_dir']}/$fname" : '';
				$exfldsize[$extrafield['field_name']] = $import['size'];
				$uploadfiles[] = $file;
				$import = $fname;
			}
			elseif (is_array($import) && $import['delete'])
			{
				$exfldsize[$extrafield['field_name']] = 0;
				$import = '';
				$file['old'] = (!empty($oldvalue)) ? "{$cfg['extrafield_files_dir']}/$oldvalue" : '';
				$uploadfiles[] = $file;
			}
			else
			{
				$exfldsize[$extrafield['field_name']] = null;
				$import = null;
			}
			break;
		case 'filesize':
			global $exfldsize;
			$import = $exfldsize[$extrafield['field_variants']];
			break;
	}
	if (empty($import) && $extrafield['field_required'])
	{
		$L['field_required_' . $extrafield['field_name']] = (isset($L['field_required_' . $extrafield['field_name']])) ? $L['field_required_' . $extrafield['field_name']] : $L['field_required'];
		cot_error('field_required_' . $extrafield['field_name'], $name);
	}
	return $import;
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
	$parse_type = array('HTML', 'Text');
	switch ($extrafield['field_type'])
	{
		case 'select':
		case 'radio':
			$value = htmlspecialchars($value);
			return (!empty($L[$name . '_' . $extrafield['field_name'] . '_' . $value])) ? $L[$name . '_' . $extrafield['field_name'] . '_' . $value] : $value;
			break;

		case 'checkbox':
			$value = ($value) ? 1 : 0;
			return $value;
			break;

		case 'datetime':
			return cot_date('datetime_medium', $value + $usr['timezone'] * 3600);
			break;
		
		case 'file':
			$value = (is_null($value)) ? '' : $value;
			return $value;
			break;	
		
		case 'filesize':
			$value = (is_null($value)) ? '' : $value;
			return $value;
			break;

		case 'input':
		case 'inputint':
		case 'currency':
		case 'double':	
		case 'textarea':
		default:
			$value = (is_null($value)) ? '' : $value;
			$value = cot_parse($value, ($extrafield['field_parse'] == 'Text') ? false : true);
			return $value;
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
	switch ($type)
	{
		case 'input':
		case 'inputint':
		case 'currency':
		case 'double':	
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

		case 'file':
			$html = $R['input_text'] . '|' . $R['input_checkbox'];
			break;

		case 'filesize':
			$html = '';
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
	global $db, $db_extra_fields;
	if ($db->query("SELECT field_name FROM $db_extra_fields WHERE field_name = '$name' AND field_location='$location'")->rowCount() > 0 ||
		($db->query("SHOW COLUMNS FROM $location LIKE '%\_$name'")->rowCount() > 0 && !$noalter))
	{
		return false; // No adding - fields already exist // Check table cot_$sql_table - if field with same name exists - exit.
	}
	$fieldsres = $db->query("SHOW COLUMNS FROM $location");
	while ($fieldrow = $fieldsres->fetch())
	{
		$column = $fieldrow['Field'];
		// get column prefix in this table
		$column_prefix = substr($column, 0, strpos($column, "_"));

		preg_match("#.*?_$name$#", $column, $match);
		if ($match[1] != "" && !$noalter)
			return false; // No adding - fields already exist
		$i++;
	}
	$fieldsres->closeCursor();

	$extf['field_location'] = $location;
	$extf['field_name'] = $name;
	$extf['field_type'] = $type;
	$extf['field_html'] = $html;
	$extf['field_variants'] = is_null($variants) ? '' : $variants;
	$extf['field_default'] = is_null($default) ? '' : $default;
	$extf['field_required'] = ($required > 0) ? 1 : 0;
	$extf['field_parse'] = is_null($parse) ? 'HTML' : $parse;
	$extf['field_description'] = is_null($description) ? '' : $description;

	$step1 = $db->insert($db_extra_fields, $extf) == 1;
	if ($noalter)
	{
		return $step1;
	}
	switch ($type)
	{
		case 'input': $sqltype = "VARCHAR(255)";
			break;
		case 'inputint': $sqltype = "int(11) NOT NULL default '0'";
			break;
		case 'currency': $sqltype = "DOUBLE(13,2) NOT NULL default '0'";
			break;
		case 'double': $sqltype = "DOUBLE NOT NULL default '0'";
			break;		
		case 'textarea': $sqltype = 'TEXT';
			break;
		case 'select': $sqltype = "VARCHAR(255)";
			break;
		case 'checkbox': $sqltype = 'BOOL';
			break;
		case 'radio': $sqltype = "VARCHAR(255)";
			break;
		case 'datetime': $sqltype = "int(11) NOT NULL default '0'";
			break;
		case 'file': $sqltype = "VARCHAR(255)";
			break;
		case 'filesize': $sqltype = "int(11) NOT NULL";
			break;
	}
	$step2 = $db->query("ALTER TABLE $location ADD " . $column_prefix . "_$name $sqltype ");

	return $step1 && $step2;
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
	global $db, $db_extra_fields;
	$fieldsres = $db->query("SELECT * FROM $db_extra_fields WHERE field_name = '$oldname' AND field_location='$location'");
	if ($fieldsres->rowCount() <= 0 || $name != $oldname && $db->query("SHOW COLUMNS FROM $location LIKE '%\_$name'")->rowCount() > 0)
	{
		// Attempt to edit non-extra field or override an existing field
		return false;
	}
	$field = $fieldsres->fetch();
	$fieldsres->closeCursor();
	$fieldsres = $db->query("SHOW COLUMNS FROM $location");
	$fieldrow = $fieldsres->fetch();
	$fieldsres->closeCursor();
	$column = $fieldrow['Field'];
	$column_prefix = substr($column, 0, strpos($column, "_"));
	$alter = false;
	if ($name != $field['field_name'])
	{
		$extf['field_name'] = $name;
		$alter = true;
	}
	if ($type != $field['field_type'])
	{
		$extf['field_type'] = $type;
		$alter = true;
	}

	$extf['field_html'] = $html;
	$extf['field_parse'] = is_null($parse) ? 'HTML' : $parse;
	$extf['field_variants'] = is_null($variants) ? '' : $variants;
	$extf['field_default'] = is_null($default) ? '' : $default;
	$extf['field_required'] = ($required > 0) ? 1 : 0;
	$extf['field_description'] = is_null($description) ? '' : $description;

	$step1 = $db->update($db_extra_fields, $extf, "field_name = '$oldname' AND field_location='$location'") == 1;

	if (!$alter)
	{
		$step1 = ($step1) ? $step1 : 2;
		return $step1;
	}

	switch ($type)
	{
		case 'input': $sqltype = "VARCHAR(255)";
			break;
		case 'inputint': $sqltype = "int(11) NOT NULL default '0'";
			break;
		case 'currency': $sqltype = "DOUBLE(13,2) NOT NULL default '0'";
			break;
		case 'double': $sqltype = "DOUBLE NOT NULL default '0'";
			break;		
		case 'textarea': $sqltype = 'TEXT';
			break;
		case 'select': $sqltype = "VARCHAR(255)";
			break;
		case 'checkbox': $sqltype = 'BOOL';
			break;
		case 'radio': $sqltype = "VARCHAR(255)";
			break;
		case 'datetime': $sqltype = "int(11) NOT NULL default '0'";
			break;
		case 'file': $sqltype = "VARCHAR(255)";
			break;
		case 'filesize': $sqltype = "int(11) NOT NULL";
			break;
	}
	$sql = "ALTER TABLE $location CHANGE " . $column_prefix . "_$oldname " . $column_prefix . "_$name $sqltype ";
	$step2 = $db->query($sql);

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
	global $db, $db_extra_fields;
	if ((int)$db->query("SELECT COUNT(*) FROM $db_extra_fields WHERE field_name = '$name' AND field_location='$location'")->fetchColumn() <= 0)
	{
		// Attempt to remove non-extra field
		return false;
	}
	$fieldsres = $db->query("SHOW COLUMNS FROM $location");
	$fieldrow = $fieldsres->fetch();
	$fieldsres->closeCursor();
	$column = $fieldrow['Field'];
	$column_prefix = substr($column, 0, strpos($column, "_"));
	$step1 = $db->delete($db_extra_fields, "field_name = '$name' AND field_location='$location'") == 1;
	
	$step2 = true;
	if ($db->query("SHOW COLUMNS FROM $location LIKE '%\_$name'")->rowCount() > 0)
	{	
		$step2 = $db->query("ALTER TABLE $location DROP " . $column_prefix . "_" . $name);
	}
	
	return $step1 && $step2;
}

/**
 * Fixes the indexing of multiple file uploads from the format:
 *
 * $_FILES['field']['key']['index']
 * To the more standard and appropriate:
 * $array['index']['key']
 * 
 * @param array $file_post $_FILE array
 * @return array
 *
 */
function cot_import_filesarray($file_post)
{
	$file_post = $_FILES[$file_post];
	$file_arr = array();
	$file_keys = array_keys($file_post);

	foreach ($file_post['name'] as $name => $value)
	{
		foreach ($file_keys as $key)
		{
			$file_arr[$name][$key] = $file_post[$key][$name];
		}
	}

	return $file_arr;
}

/**
 * Moves and unset files in the $uploadfiles array
 */
function cot_extrafield_movefiles()
{
	global $uploadfiles;
	if (is_array($uploadfiles))
	{
		foreach ($uploadfiles as $uploadfile)
		{
			if (!empty($uploadfile['old']) && file_exists($uploadfile['old']))
			{
				@unlink($uploadfile['old']);
			}
			if (!empty($uploadfile['tmp']) && !empty($uploadfile['tmp']))
			{
				@move_uploaded_file($uploadfile['tmp'], $uploadfile['new']);
			}
		}
	}
}

/**
 * Loads extrafields data into global
 * @global array $cot_extrafields
 */
function cot_load_extrafields()
{
	global $db, $cot_extrafields, $db_extra_fields, $cache;
	if (!isset($cot_extrafields))
	{
		$cot_extrafields = array();
		$fieldsres = $db->query("SELECT * FROM $db_extra_fields WHERE 1 ORDER BY field_type ASC");
		while ($row = $fieldsres->fetch())
		{
			$cot_extrafields[$row['field_location']][$row['field_name']] = $row;
		}

		$fieldsres->closeCursor();
		$cache && $cache->db->store('cot_extrafields', $cot_extrafields, 'system');
	}
}

/* ======== Extrafields Pre-load ======== */

cot_load_extrafields();
$cot_extrafields['structure'] = (!empty($cot_extrafields[$db_structure])) ? $cot_extrafields[$db_structure] : array();
?>