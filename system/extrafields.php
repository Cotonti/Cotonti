<?php

/**
 * Extrafields API
 *
 * @package API - Extrafields
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forms');

/**
 * Returns Extra fields edit fields
 *
 * @param string $name Variable name
 * @param array $extrafield Extra fields data
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
			$options_titles = $options_values = array();
			foreach ($opt_array as $var)
			{
				$ii++;
				$var = trim($var);
				$options_titles[$ii] = (!empty($L[$extrafield['field_name'] . '_' . $var])) ? $L[$extrafield['field_name'] . '_' . $var] : $var;
				$options_values[$ii] = $var;
			}
			$result = cot_selectbox(trim($data), $name, $options_values, $options_titles, false, '', $extrafield['field_html']);
			break;

		case 'radio':
			$extrafield['field_variants'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_variants']);
			$opt_array = explode(",", $extrafield['field_variants']);
			$options_titles = $options_values = array();
			if (count($opt_array) > 0)
			{
				$ii = 0;
				foreach ($opt_array as $var)
				{
					$ii++;
					$var = trim($var);
					$options_titles[$ii] = (!empty($L[$extrafield['field_name'] . '_' . $var])) ? $L[$extrafield['field_name'] . '_' . $var] : $var;
					$options_values[$ii] = $var;
				}
			}
			$result = cot_radiobox(trim($data), $name, $options_values, $options_titles, '', '', $extrafield['field_html']);
			break;

		case 'checkbox':
			$title = cot_extrafield_title($extrafield);
			$result = cot_checkbox($data, $name, $title, '', '1', $extrafield['field_html']);
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
			$options_titles = $options_values = array();
			if (count($opt_array) > 0)
			{
				$ii = 0;
				foreach ($opt_array as $var)
				{
					$ii++;
					$var = trim($var);
					$options_titles[$ii] = (!empty($L[$extrafield['field_name'] . '_' . $var])) ? $L[$extrafield['field_name'] . '_' . $var] : $var;
					$options_values[$ii] = $var;
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
			$extrafield['field_params'] = (!empty($extrafield['field_params'])) ? $extrafield['field_params'] : $cfg['extrafield_files_dir'];
			$extrafield['field_params'] .= (mb_substr($extrafield['field_params'], -1) == '/') ? '' : '/';
			$data_filepath = $extrafield['field_params'] . htmlspecialchars($data);
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
 * @param $extrafield
 * @param string $source Source type: G (GET), P (POST), C (COOKIE) or D (variable filtering)
 * @param string $oldvalue Old value of extrafield
 * @param string $titlePrefix
 * @return string
 */
function cot_import_extrafields($inputname, $extrafield, $source = 'P', $oldvalue = '', $titlePrefix = '')
{
	global $L;

    $exfld_title = cot_extrafield_title($extrafield, $titlePrefix);

    $import = null;
	switch ($extrafield['field_type'])
	{
		case 'input':
			$import = ($extrafield['field_parse'] == 'Text') ? cot_import($inputname, $source, 'TXT') : cot_import($inputname, $source, 'HTM');
			if (!empty($extrafield['field_params']) && !is_null($import) && !preg_match($extrafield['field_params'], $import))
			{
                $errMsg = (isset($L['field_pregmatch_' . $extrafield['field_name']])) ?
                    $L['field_pregmatch_' . $extrafield['field_name']] : $exfld_title.': '.$L['field_pregmatch'];
				cot_error($errMsg, $inputname);
			}
			break;

		case 'inputint':
		case 'range':
			$extrafield['field_params'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_params']);
			$import = cot_import($inputname, $source, 'INT');
                        
                        list($min, $max) = explode(",", $extrafield['field_params'], 2);
                        $min = (int) $min;
                        $max = (int) $max;
                        
			if (!is_null($import) && !empty($extrafield['field_params']))
			{
				if ($import < $min || $import > $max)
				{
                    $errMsg = (isset($L['field_range_' . $extrafield['field_name']])) ?
                        $L['field_range_' . $extrafield['field_name']] : $exfld_title.': '.$L['field_range'];
					cot_error($errMsg, $inputname);
				}
			}
                        
                        if(is_null($import))
                        {
                            $import = ($extrafield['field_type'] == 'inputint' || !empty($extrafield['field_default'])) ? (int)$extrafield['field_default'] : '';
                         
                            if(is_numeric($import) && !empty($extrafield['field_params'])){
                                    if($import < $min)$import = $min;
                                    if($import > $max)$import = $max;
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
                        
                        list($min, $max) = explode(",",$extrafield['field_params'], 2);
                        $min = (int) $min;
                        $max = (int) $max;
                        
			if (!is_null($import) && !empty($extrafield['field_params']))
			{

				if ($import < $min || $import > $max)
				{
                    $errMsg = (isset($L['field_range_' . $extrafield['field_name']])) ?
                        $L['field_range_' . $extrafield['field_name']] : $exfld_title.': '.$L['field_range'];
                    cot_error($errMsg, $inputname);
				}
			}
                        
                        if(is_null($import))
                        {
                            $import =  !empty($extrafield['field_default']) ? floatval($extrafield['field_default']) : floatval(0);
                
                            if(is_numeric($import) && !empty($extrafield['field_params'])){
                                    if($import < $min)$import = $min;
                                    if($import > $max)$import = $max;
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
				$errMsg = (isset($L['field_notinarray_' . $extrafield['field_name']])) ? 
					$L['field_notinarray_' . $extrafield['field_name']] : $exfld_title.': '.$L['field_notinarray'];
				cot_error($errMsg, $inputname);
			}
			break;

		case 'checkbox':
			$import = (int)cot_import($inputname, $source, 'BOL');
			break;

		case 'datetime':
			$extrafield['field_params'] = str_replace(array(' , ', ', ', ' ,'), ',', $extrafield['field_params']);
			list($min, $max) = explode(",",$extrafield['field_params'], 2);

			$import = cot_import_date($inputname, true, false, $source);
			if (!is_null($import) && ((int)$min > 0 || (int)$max > 0))
			{
				list($s_year, $s_month, $s_day, $s_hour, $s_minute) = explode('-', @date('Y-m-d-H-i', $import));
				if ($min > $s_year)
				{
					$import=mktime($s_hour, $s_minute, 0, $s_month, $s_day, $min);
				}
				if ($max < $s_year)
				{
					$import=mktime($s_hour, $s_minute, 0, $s_month, $s_day, $max);
				}
			}
                        if (is_null($import))
                        {
                            $import = 0;
                        }  
			break;

		case 'country':
			$import = cot_import($inputname, $source,'ALP');
                        if($extrafield['field_required'] && $import == '00')
                        {
                            $import = null;
                        }

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
                        $errMsg = (isset($L['field_notinarray_' . $extrafield['field_name']])) ?
                            $L['field_notinarray_' . $extrafield['field_name']] : $exfld_title.': '.$L['field_notinarray'];
                        cot_error($errMsg, $inputname);
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
			if ($source == 'P' || $source == 'POST')
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
					if ($lang != 'en' && file_exists(cot_langfile('translit', 'core')))
					{
						require_once cot_langfile('translit', 'core');
						$fname = (is_array($cot_translit)) ? strtr($fname, $cot_translit) : '';
					}
					$fname = str_replace(array(' ', '  ', '__'), '_', $fname);
					$fname = preg_replace('#[^a-zA-Z0-9\-_\.\ \+]#', '', $fname);
					$fname = str_replace('..', '.', $fname);
					$fname = str_replace('__', '_', $fname);
					$fname = (empty($fname)) ? cot_unique() : $fname;

					// Generate unique file name. Old file - must be removed any way
					$extrafield['field_params'] = (!empty($extrafield['field_params'])) ? $extrafield['field_params'] : $cfg['extrafield_files_dir'];
					$extrafield['field_params'] .= (mb_substr($extrafield['field_params'], -1) == '/') ? '' : '/';

					if(file_exists("{$extrafield['field_params']}$fname.$ext")){
						$fname = $inputname.'_'.date("YmjGis").'_'.$fname;
					}

					$fname .= '.' . $ext;
					
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
                    $errMsg = (isset($L['field_extension_' . $extrafield['field_name']])) ?
                        $L['field_extension_' . $extrafield['field_name']] : $exfld_title.': '.$L['field_extension'];
                    cot_error($errMsg, $inputname);

					$exfldsize[$extrafield['field_name']] = null;
					$import = null;
				}
			}
			elseif (is_array($import) && $import['delete'])
			{
				$exfldsize[$extrafield['field_name']] = 0;
				$import = '';
				$extrafield['field_params'] = (!empty($extrafield['field_params'])) ? $extrafield['field_params'] : $cfg['extrafield_files_dir'];
				$file['old'] = (!empty($oldvalue)) ? "{$extrafield['field_params']}/$oldvalue" : '';
				$file['field'] = $extrafield['field_name'];
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

	if ((is_null($import) || $import === '' || ($extrafield['field_type'] == 'datetime' && $import == 0)) && $extrafield['field_required'])
	{
		$msg = (isset(cot::$L['field_required_' . $extrafield['field_name']])) ? cot::$L['field_required_' . $extrafield['field_name']] :
            cot::$L['field_required'].': '.$exfld_title;
		cot_error($msg, $inputname);
	}
	return $import;
}

/**
 * Extrafield title
 *
 * @param array  $extrafield
 * @param string $titlePrefix
 * @return string
 */
function cot_extrafield_title($extrafield, $titlePrefix = '') {
    $title = $extrafield['field_description'];

    $fieldLocation = '';
    if(isset($extrafield['field_location'])) $fieldLocation = $extrafield['field_location'];

    if($titlePrefix != '' && isset(cot::$L[$titlePrefix.$extrafield['field_name'].'_title'])) {
        $title = cot::$L[$titlePrefix.$extrafield['field_name'].'_title'];

    } elseif($fieldLocation != '' && isset(cot::$L[$fieldLocation.'_'.$extrafield['field_name'].'_title'])) {
        $title = cot::$L[$fieldLocation.'_'.$extrafield['field_name'].'_title'];

    } elseif(isset(cot::$L[$extrafield['field_name'].'_title'])) {
        $title = cot::$L[$extrafield['field_name'].'_title'];
    }
    
    if($title == '') $title = $extrafield['field_name'];

    return $title;
}

/**
 * Returns Extra fields data
 *
 * @param string $name Lang row
 * @param array $extrafield Extra fields data
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

	include $cfg['system_dir'].'/resources.rc.php';
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
			$html = $R['input_filebox'] . '|' . $R['input_filebox_empty'];
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
function cot_extrafield_add($location, $name, $type, $html='', $variants='', $default='', $required=false, $parse='HTML',
                            $description='', $params = '', $enabled = 1, $noalter = false, $customtype = '')
{
	global $db, $db_extra_fields;

	$checkname = cot_import($name, 'D', 'ALP');
	$checkname = str_replace(array('-', '.'), array('', ''), $checkname);
	if($checkname != $name)
	{
		return false;
	}

	if ( $db->query("SELECT field_name FROM $db_extra_fields WHERE field_name = '$name' AND field_location='$location'")->rowCount() > 0 ||
		($db->query("SHOW COLUMNS FROM $location WHERE SUBSTR(Field, INSTR(Field, '_') + 1) = '$name'")->rowCount() > 0 && !$noalter))
	{
		// No adding - fields already exist // Check table cot_$sql_table - if field with same name exists - exit.
		return false;
	}

	$fieldsres = $db->query("SHOW COLUMNS FROM $location");
    $prefixFound = false;
	while ($fieldrow = $fieldsres->fetch())
	{
		$column = $fieldrow['Field'];
		// get column prefix in this table
		if(!$prefixFound) {
            $column_prefix = substr($column, 0, strpos($column, "_"));
            $fieldName = $column;
            if(!empty($column_prefix)) $fieldName = str_replace($column_prefix.'_', '', $column);
            if($fieldName == 'id' || mb_strtolower($fieldrow['Key']) == 'pri') $prefixFound = true;
        }

		preg_match("#.*?_$name$#", $column, $match);
		if ($match[1] != "" && !$noalter)
		{
			return false; // No adding - fields already exist
		}
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
		case 'select':
		case 'radio':
		case 'range':
		case 'file':
		case 'input': $sqltype = "VARCHAR(255) DEFAULT ''";
			break;
		case 'inputint': $sqltype = "int(11) DEFAULT '0'";
			break;
		case 'currency': $sqltype = "DOUBLE(13,2) DEFAULT '0'";
			break;
		case 'double': $sqltype = "DOUBLE DEFAULT '0'";
			break;
		case 'checklistbox':
		case 'textarea': $sqltype = "TEXT";
			break;
		case 'checkbox': $sqltype = 'TINYINT(1) UNSIGNED'; //'BOOL';
			break;
		case 'datetime': $sqltype = "int(11) DEFAULT '0'";
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
    $fieldName = $name;
    if($column_prefix != '') $fieldName = $column_prefix.'_'.$name;
	$step2 = $db->query("ALTER TABLE $location ADD $fieldName $sqltype ");

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
	if ($fieldsres->rowCount() <= 0 || $name != $oldname && $db->query("SHOW COLUMNS FROM $location WHERE SUBSTR(Field, INSTR(Field, '_') + 1) = '$name'")->rowCount() > 0)
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
		case 'select':
		case 'radio':
		case 'range':
		case 'file':
		case 'input': $sqltype = "VARCHAR(255) DEFAULT ''";
			break;
		case 'inputint': $sqltype = "int(11) DEFAULT '0'";
			break;
		case 'currency': $sqltype = "DOUBLE(13,2) DEFAULT '0'";
			break;
		case 'double': $sqltype = "DOUBLE DEFAULT '0'";
			break;
		case 'checklistbox':
		case 'textarea': $sqltype = "TEXT";
			break;
		case 'checkbox': $sqltype = 'TINYINT(1) UNSIGNED'; //'BOOL';
			break;
		case 'datetime': $sqltype = "int(11) DEFAULT '0'";
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

    if($column_prefix != '') {
        $oldname = $column_prefix.'_'.$oldname;
        $name = $column_prefix.'_'.$name;
    }
	$sql = "ALTER TABLE $location CHANGE $oldname $name $sqltype ";
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
    $tmp = $name;
    if($column_prefix != '') $tmp = '_'.$name;
    if ($db->query("SHOW COLUMNS FROM $location LIKE '%{$tmp}'")->rowCount() > 0)
	{
        if($column_prefix != '') $name = $column_prefix . "_" . $name;
		$step2 = $db->query("ALTER TABLE $location DROP " . $name);
	}

	return $step1 && $step2;
}

/**
 * Registers a table in extrafields registry
 * @param  string $table_name Unprefixed table name
 */
function cot_extrafields_register_table($table_name)
{
	if (!isset(cot::$extrafields[cot::$db->{$table_name}]))
	{
		cot::$extrafields[cot::$db->{$table_name}] = array();
	}
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
    if(empty($file_post)) return null;

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
			if (!empty($uploadfile['tmp']) && !empty($uploadfile['new']))
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
	if (empty($cot_extrafields) || $forcibly)
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
