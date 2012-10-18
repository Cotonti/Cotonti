<?php

/**
 * Extrafields API
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forms');

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
	global $L, $R, $cfg, $pl;
	$data = ($data == null) ? $extrafield['field_default'] : $data;

	switch ($extrafield['field_type'])
	{
		case 'input':
		case 'inputint':
		case 'currency':
		case 'double':
			$result = cot_inputbox('text', $name, $data, '', $extrafield['field_html']);
			break;

		case 'textarea':
			$result = cot_textarea($name, $data, 4, 56, '', $extrafield['field_html']);
			break;

		case 'select':
			$extrafield['field_variants'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_variants']);
			$opt_array = explode(",", $extrafield['field_variants']);
			$ii = 0;
			foreach ($opt_array as $var)
			{
				$ii++;
				$options_titles[$ii] = (!empty($L[$extrafield['field_name'] . '_' . $var])) ? $L[$extrafield['field_name'] . '_' . $var] : $var;
				$options_values[$ii] .= trim($var);
			}
			$result = cot_selectbox(trim($data), $name, $options_values, $options_titles, false, '', $extrafield['field_html']);
			break;

		case 'radio':
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
			$result = cot_radiobox(trim($data), $name, $options_values, $options_titles, '', '', $extrafield['field_html']);
			break;

		case 'checkbox':
			$result = cot_checkbox($data, $name, $extrafield['field_description'], '', '1', $extrafield['field_html']);
			break;

		case 'datetime':
			global $sys;
			$extrafield['field_params'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_params']);
			list($min, $max, $format) = explode(",",$extrafield['field_params'], 3);
			$max = (int)$max > 0 ? $max : 2030;
			$min =  (int)$min > 0 ? $min : 2000;

			$data = (mb_substr($data, 0, 1) == "+") ? $sys['now'] + (int)(mb_substr($data, 1)) : $data;
			$data = (mb_substr($data, 0, 1) == "-") ? $sys['now'] - (int)(mb_substr($data, 1)) : $data;

			$result = cot_selectbox_date((int)$data, 'long', $name, (int)$max, (int)$min, true, $extrafield['field_html']);
			break;

		case 'country':
			global $cot_countries;
			$result = cot_selectbox_countries(trim($data), $name, true, '', $extrafield['field_html']);
			break;

		case 'range':
			$extrafield['field_params'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_params']);
			list($min, $max) = explode(',',$extrafield['field_params'], 2);
			$result = cot_selectbox(trim($data), $name, range((int)$min, (int)$max), range((int)$min, (int)$max), true, '', $extrafield['field_html']);
			break;
		case 'checklistbox':
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
			if (!is_array($data))
			{
				$data = trim(str_replace(array(' , ', ', ', ' ,'), ',', $data));
				$data = explode(',', $data);
			}
			$result = cot_checklistbox($data, $name, $options_values, $options_titles, '', '', true, $extrafield['field_html']);
			break;

		case 'file':
			$data_filepath = $cfg['extrafield_files_dir'].'/'.htmlspecialchars($data);
			/* === Hook === */
			foreach (cot_getextplugins('extrafields.build.file') as $pl)
			{
				include $pl;
			}
			/* ===== */
			$result = cot_filebox($name, htmlspecialchars($data), $data_filepath, 'rdel_' . $name, '', $extrafield['field_html']);
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
			if (!empty($extrafield['field_params']) && !is_null($import) && !preg_match($extrafield['field_params'], $import))
			{
				$L['field_pregmatch_' . $extrafield['field_name']] = (isset($L['field_pregmatch_' . $extrafield['field_name']])) ? $L['field_pregmatch_' . $extrafield['field_name']] : $L['field_pregmatch'];
				cot_error('field_pregmatch_' . $extrafield['field_name'], $name);
			}
			break;

		case 'inputint':
		case 'range':
			$extrafield['field_params'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_params']);
			$import = cot_import($inputname, $source, 'INT');
			if (!is_null($import) && !empty($extrafield['field_params']))
			{
				list($min, $max) = explode(",",$extrafield['field_params'], 2);
				$min = (int) $min;
				$max = (int) $max;
				if ($import < $min || $import > $max)
				{
					cot_error('field_range_' . $extrafield['field_name'], $name);
				}
			}
			break;
		case 'currency':
		case 'double':
			$extrafield['field_params'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_params']);
			$import = cot_import($inputname, $source, 'NUM');
			if (!is_null($import))
			{
				$import = floatval($import);
			}
			if (!is_null($import) && !empty($extrafield['field_params']))
			{
				list($min, $max) = explode(",",$extrafield['field_params'], 2);
				$min = (int) $min;
				$max = (int) $max;
				if ($import < $min || $import > $max)
				{
					cot_error('field_range_' . $extrafield['field_name'], $name);
				}
			}
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
			$import = cot_import($inputname, $source, 'BOL');
			break;

		case 'datetime':
			$extrafield['field_params'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_params']);
			list($min, $max) = explode(",",$extrafield['field_params'], 2);

			$import = cot_import_date($inputname, true, false, $source);
			if (!is_null($import) && ((int)$min > 0 || (int)$max > 0))
			{
				list($s_year, $s_month, $s_day, $s_hour, $s_minute) = explode('-', @date('Y-m-d-H-i', $utime));
				if ($min > $s_year)
				{
					$import=mktime($s_hour, $s_minute, 0, $s_month, $s_day, $min);
				}
				if ($max < $s_year)
				{
					$import=mktime($s_hour, $s_minute, 0, $s_month, $s_day, $max);
				}
			}
			break;

		case 'country':
			$import = cot_import($inputname, $source,'ALP');
			break;

		case 'checklistbox':
			$import = cot_import($inputname, $source, 'ARR');
			$extrafield['field_variants'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_variants']);
			$opt_array = explode(',', trim($extrafield['field_variants']));
			if(count($import) < 1)
			{
				$import = null;
			}
			elseif(count($import) == 1 && isset($import['nullval']))
			{
				$import = array();
			}
			else
			{
				unset($import['nullval']);
				foreach ($import as $k => $v)
				{
					$import[$k] = cot_import($v, 'D', 'HTM');
					if (!is_null($import[$k]) && !in_array($import[$k], $opt_array))
					{
						$L['field_notinarray_' . $extrafield['field_name']] = (isset($L['field_notinarray_' . $extrafield['field_name']])) ? $L['field_notinarray_' . $extrafield['field_name']] : $L['field_notinarray'];
						cot_error('field_notinarray_' . $extrafield['field_name'], $name);
					}
				}
			}

			if(is_array($import))
			{
				$import = implode(',', $import);
			}
			break;

		case 'file':
			global $lang, $cot_translit, $exfldfiles, $exfldsize, $cfg, $uploadfiles, $pl;
			if ($source == 'P')
			{
				$import = $_FILES[$inputname];
				$import['delete'] = cot_import('rdel_' . $inputname, 'P', 'BOL') ? 1 : 0;
			}
			elseif ($source == 'D')
			{
				$import = $inputname;
			}

			/* === Hook === */
			foreach (cot_getextplugins('extrafields.import.file.first') as $pl)
			{
				include $pl;
			}
			/* ===== */

			if (is_array($import) && !$import['error'] && !empty($import['name']))
			{
				$fname = mb_substr($import['name'], 0, mb_strrpos($import['name'], '.'));
				$ext = mb_strtolower(mb_substr($import['name'], mb_strrpos($import['name'], '.') + 1));

				//check extension
				$extrafield['field_variants'] = str_replace(array(' , ', ', ', ' ,'), ',', mb_strtolower($extrafield['field_variants']));
				$ext_array = explode(",", trim($extrafield['field_variants']));

				if(empty($extrafield['field_variants']) || in_array($ext, $ext_array))
				{
					if ($lang != 'en')
					{
						require_once cot_langfile('translit', 'core');
						$fname = (is_array($cot_translit)) ? strtr($fname, $cot_translit) : '';
					}					$fname = str_replace(' ', '_', $fname);
					$fname = preg_replace('#[^a-zA-Z0-9\-_\.\ \+]#', '', $fname);
					$fname = str_replace('..', '.', $fname);
					$fname = (empty($fname)) ? cot_unique() : $fname;

					$fname .= (file_exists("{$cfg['extrafield_files_dir']}/$fname.$ext") && $oldvalue != $fname . '.' . $ext) ? date("YmjGis") : '';

					$fname .= '.' . $ext;

					$extrafield['field_params'] = (!empty($extrafield['field_params'])) ? $extrafield['field_params'] : $cfg['extrafield_files_dir'];
					$extrafield['field_params'] .= (mb_substr($extrafield['field_params'], -1) == '/') ? '' : '/';

					$file['old'] = (!empty($oldvalue) && ($import['delete'] || $import['tmp_name'])) ? $extrafield['field_params'].$oldvalue : '';
					$file['field'] = $extrafield['field_name'];
					$file['tmp'] = (!$import['delete']) ? $import['tmp_name'] : '';
					$file['new'] = (!$import['delete']) ? $extrafield['field_params'].$fname : '';

					/* === Hook === */
					foreach (cot_getextplugins('extrafields.import.file.done') as $pl)
					{
						include $pl;
					}
					/* ===== */

					$exfldsize[$extrafield['field_name']] = $import['size'];
					$uploadfiles[] = $file;
					$import = $fname;
				}
				else
				{
					cot_error('field_extension_' . $extrafield['field_name'], $name);
					$exfldsize[$extrafield['field_name']] = null;
					$import = null;
				}
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
	if ((is_null($import) || $import === '') && $extrafield['field_required'])
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
 * @param string $parser Non-default parser to use
 * @return string
 */
function cot_build_extrafields_data($name, $extrafield, $value, $parser = '')
{
	global $L;
	$parse_type = array('HTML', 'Text');
	switch ($extrafield['field_type'])
	{
		case 'select':
		case 'radio':
			$value = htmlspecialchars($value);
			return (!empty($L[$extrafield['field_name'] . '_' . $value])) ? $L[$extrafield['field_name'] . '_' . $value] : $value;
			break;

		case 'checkbox':
			$value = ($value) ? 1 : 0;
			return $value;
			break;

		case 'datetime':
			$extrafield['field_params'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_params']);
			list($min, $max, $format) = explode(",",$extrafield['field_params'], 3);
			return (empty($format)) ? $value : cot_date($format, $value);
			break;

		case 'checklistbox':
			$value = htmlspecialchars($value);
			$value = trim(str_replace(array(' , ', ', ', ' ,'), ',', $value));
			$value = explode(',', $value);
			$sep = (!empty($extrafield['field_params'])) ? $extrafield['field_params'] : ', ';
			$result = '';
			$i = 0;
			if(is_array($value))
			{
				foreach($value as $k => $v)
				{
					if($i != 0)
					{
						$result .= $sep;
					}
					$i++;
					$result .= (!empty($L[$extrafield['field_name'] . '_' . $v])) ? $L[$extrafield['field_name'] . '_' . $v] : $v;
				}
			}
			return $result;
			break;

		case 'country':
		case 'file':
		case 'filesize':
			$value = (is_null($value)) ? '' : $value;
			return $value;
			break;

		case 'input':
		case 'inputint':
		case 'currency':
		case 'double':
		case 'textarea':
		case 'range':
		default:
			$value = (is_null($value)) ? '' : $value;
			$value = cot_parse($value, ($extrafield['field_parse'] == 'Text') ? false : true, $parser);
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
	global $cfg;

	include $cfg['system_dir'].'/resources.php';
	if (file_exists("{$cfg['themes_dir']}/{$cfg['defaulttheme']}/{$cfg['defaulttheme']}.php"))
	{
		include "{$cfg['themes_dir']}/{$cfg['defaulttheme']}/{$cfg['defaulttheme']}.php";
	}

	$html = '';
	switch ($type)
	{
		case 'input':
		case 'inputint':
		case 'currency':
		case 'double':
			$html = $R['input_text'];
			$html = str_replace('{$attrs}', '{$attrs} maxlength="255"', $html);
			break;

		case 'textarea':
			$html = $R['input_textarea'];
			break;

		case 'country':
		case 'select':
		case 'range':
			$html = $R['input_select'];
			break;

		case 'checkbox':
			$html = $R['input_checkbox'];
			break;

		case 'checklistbox':
			$html = $R['input_check'];
			break;

		case 'radio':
			$html = $R['input_radio'];
			break;

		case 'datetime':
			$html = $R['input_date'];
			break;

		case 'file':
			$html = $R['input_file'] . '|' . $R['input_file_empty'];
			break;

		case 'filesize':
			$html = '';
			break;
	}
	$html = str_replace('{$attrs}', '', $html);
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
 * @param string $params Params (for radiobuttons, selectors etc)
 * @param string $enabled Enable field
 * @param bool $noalter Do not ALTER the table, just register the extra field
 * @param string $customtype Modify sql type, size, default
 * @return bool
 *
 * @global CotDB $db
 */
function cot_extrafield_add($location, $name, $type, $html='', $variants='', $default='', $required=false, $parse='HTML', $description='', $params = '', $enabled = 1, $noalter = false, $customtype = '')
{
	global $db, $db_extra_fields;
	$checkname = cot_import($name, 'D', 'ALP');
	$checkname = str_replace(array('-', '.'), array('', ''), $checkname);
	if($checkname != $name)
	{
		return false;
	}
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
		{
			return false; // No adding - fields already exist
		}
		$i++;
	}
	$fieldsres->closeCursor();

	$extf['field_location'] = $location;
	$extf['field_name'] = $name;
	$extf['field_type'] = $type;
	$extf['field_html'] = (!empty($html)) ? $html : cot_default_html_construction($type);
	$extf['field_variants'] = is_null($variants) ? '' : $variants;
	$extf['field_params'] = is_null($params) ? '' : $params;
	$extf['field_default'] = is_null($default) ? '' : $default;
	$extf['field_required'] = $required ? 1 : 0;
	$extf['field_parse'] = is_null($parse) ? 'HTML' : $parse;
	$extf['field_enabled'] = ($enabled > 0) ? 1 : 0;
	$extf['field_description'] = is_null($description) ? '' : $description;

	$step1 = $db->insert($db_extra_fields, $extf) == 1;
	if ($noalter)
	{
		return $step1;
	}
	switch ($type)
	{
		case 'checklistbox':
		case 'select':
		case 'radio':
		case 'range':
		case 'file':
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
		case 'checkbox': $sqltype = 'BOOL';
			break;
		case 'datetime': $sqltype = "int(11) NOT NULL default '0'";
			break;
		case 'country': $sqltype = "CHAR(2)";
			break;
		case 'filesize': $sqltype = "int(11) NOT NULL";
			break;
	}
	if(!empty($customtype))
	{
		$sqltype = $customtype;
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
 * @param string $params Params (for radiobuttons, selectors etc)
 * @param string $enabled Enable field
 * @param string $customtype Modify sql type, size, default
 * @return bool
 *
 * @global CotDB $db
 */
function cot_extrafield_update($location, $oldname, $name, $type, $html='', $variants='', $default='', $required=0, $parse='HTML', $description='', $params = '', $enabled = 1, $customtype = '')
{
	global $db, $db_extra_fields;

	$checkname = cot_import($name, 'D', 'ALP');
	$checkname = str_replace(array('-', '.'), array('', ''), $checkname);
	if($checkname != $name)
	{
		return false;
	}
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
	if ($type != $field['field_type'] && empty($customtype))
	{
		$extf['field_type'] = $type;
		$alter = true;
	}

	$extf['field_html'] = (!empty($html) && $type == $field['field_type']) ? $html : cot_default_html_construction($type);
	$extf['field_parse'] = is_null($parse) ? 'HTML' : $parse;
	$extf['field_variants'] = is_null($variants) ? '' : $variants;
	$extf['field_params'] = is_null($params) ? '' : $params;
	$extf['field_default'] = is_null($default) ? '' : $default;
	$extf['field_required'] = ($required > 0) ? 1 : 0;
	$extf['field_description'] = is_null($description) ? '' : $description;
	$extf['field_enabled'] = ($enabled > 0) ? 1 : 0;

	$step1 = $db->update($db_extra_fields, $extf, "field_name = '$oldname' AND field_location='$location'") == 1;

	if (!$alter)
	{
		$step1 = ($step1) ? $step1 : 2;
		return $step1;
	}

	switch ($type)
	{
		case 'checklistbox':
		case 'select':
		case 'radio':
		case 'range':
		case 'file':
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
		case 'checkbox': $sqltype = 'BOOL';
			break;
		case 'datetime': $sqltype = "int(11) NOT NULL default '0'";
			break;
		case 'country': $sqltype = "CHAR(2)";
			break;
		case 'filesize': $sqltype = "int(11) NOT NULL";
			break;
	}
	if(!empty($customtype))
	{
		$sqltype = $customtype;
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
 * @global CotDB $db
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
	global $uploadfiles, $cfg, $pl;
	if (is_array($uploadfiles))
	{
		foreach ($uploadfiles as $uploadfile)
		{
			/* === Hook === */
			foreach (cot_getextplugins('extrafields.movefiles') as $pl)
			{
				include $pl;
			}
			/* ===== */
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
 * Delete files in extrafield array
 * @param string $fielddata field data
 * @param array $extrafields Extra fields data
 */
function cot_extrafield_unlinkfiles($fielddata, $extrafield)
{
	global $cfg, $pl;
	if ($extrafield['field_type'] == 'file')
	{
		$extrafield['field_params'] = (!empty($extrafield['field_params'])) ? $extrafield['field_params'] : $cfg['extrafield_files_dir'];
		$extrafield['field_params'] .= (mb_substr($extrafield['field_params'], -1) == '/') ? '' : '/';
		if($extrafield['field_params'].$fielddata)
		{
			/* === Hook === */
			foreach (cot_getextplugins('extrafields.unlinkfiles') as $pl)
			{
				include $pl;
			}
			/* ===== */
			@unlink($extrafield['field_params'].$fielddata);
		}
	}
}

/**
 * Loads extrafields data into global
 * @param bool $forcibly Forcibly reload exflds
 * @global array $cot_extrafields
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_load_extrafields($forcibly = false)
{
	global $db, $cot_extrafields, $db_extra_fields, $cache;
	if (!isset($cot_extrafields) || $forcibly)
	{
		$cot_extrafields = array();
		$where = (defined('COT_INSTALL')) ? "1" : "field_enabled=1";
		$fieldsres = $db->query("SELECT * FROM $db_extra_fields WHERE $where ORDER BY field_type ASC");
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
$cot_extrafields[$db_structure] = (!empty($cot_extrafields[$db_structure])) ? $cot_extrafields[$db_structure] : array();
?>