<?php
/**
 * Admin function library.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL.');

// Requirements
cot_require_api('extrafields');
cot_require_api('forms');
cot_require_api('extensions');
cot_require_lang('admin', 'core');
cot_require_rc('admin');

/* ======== Defaulting the admin variables ========= */

unset($adminmain, $adminhelp, $admin_icon, $plugin_body, $plugin_title, $plugin_help);
$adminpath = array();

/**
 * Builds administration breadcrumbs (path)
 *
 * @param array $adminpath Path links
 * @return string
 */
function cot_build_adminsection($adminpath)
{
	global $cfg, $L;

	$result = array();
	$result[] = '<a href="'.cot_url('admin').'">'.$L['Adminpanel'].'</a>';
	foreach ($adminpath as $i => $k)
	{
		$result[] = '<a href="'.$k[0].'">'.$k[1].'</a>';
	}
	$result = implode(' '.$cfg['separator'].' ', $result);

	return $result;
}

/**
 * Returns $url as an HTML link if $cond is TRUE or just plain $text otherwise
 * @param string $url Link URL
 * @param string $text Link text
 * @param bool $cond Condition
 * @return string
 */
function cot_linkif($url, $text, $cond)
{
	if ($cond)
	{
		$res = '<a href="'.$url.'">'.$text.'</a>';
	}
	else
	{
		$res = $text;
	}

	return $res;
}

/**
 * Returns a list of possible charsets
 *
 * @return array
 */
function cot_loadcharsets()
{
	// FIXME this function is obviously obsolete
	$result = array();
	$result[] = array('ISO-10646-UTF-1', 'ISO-10646-UTF-1 / Universal Transfer Format');
	$result[] = array('UTF-8', 'UTF-8 / Standard Unicode');
	$result[] = array('ISO-8859-1', 'ISO-8859-1 / Western Europe');
	$result[] = array('ISO-8859-2', 'ISO-8859-2 / Middle Europe');
	$result[] = array('ISO-8859-3', 'ISO-8859-3 / Maltese');
	$result[] = array('ISO-8859-4', 'ISO-8859-4 / Baltic');
	$result[] = array('ISO-8859-5', 'ISO-8859-5 / Cyrillic');
	$result[] = array('ISO-8859-6', 'ISO-8859-6 / Arabic');
	$result[] = array('ISO-8859-7', 'ISO-8859-7 / Greek');
	$result[] = array('ISO-8859-8', 'ISO-8859-8 / Hebrew');
	$result[] = array('ISO-8859-9', 'ISO-8859-9 / Turkish');
	$result[] = array('ISO-2022-KR', 'ISO-2022-KR / Korean');
	$result[] = array('ISO-2022-JP', 'ISO-2022-JP / Japanese');
	$result[] = array('windows-1250', 'windows-1250 / Central European');
	$result[] = array('windows-1251', 'windows-1251 / Russian');
	$result[] = array('windows-1252', 'windows-1252 / Western Europe');
	$result[] = array('windows-1254', 'windows-1254 / Turkish');
	$result[] = array('EUC-JP', 'EUC-JP / Japanese');
	$result[] = array('GB2312', 'GB2312 / Chinese simplified');
	$result[] = array('BIG5', 'BIG5 / Chinese traditional');
	$result[] = array('tis-620', 'Tis-620 / Thai');
	return $result;
}

/**
 * Returns a list of possible DOCTYPEs
 *
 * @return array
 */
function cot_loaddoctypes()
{
	$result = array();
	$result[] = array(0, 'HTML 4.01');
	$result[] = array(1, 'HTML 4.01 Transitional');
	$result[] = array(2, 'HTML 4.01 Frameset');
	$result[] = array(3, 'XHTML 1.0 Strict');
	$result[] = array(4, 'XHTML 1.0 Transitional');
	$result[] = array(5, 'XHTML 1.0 Frameset');
	$result[] = array(6, 'XHTML 1.1');
	$result[] = array(7, 'XHTML 2');
	return $result;
}

/**
 * Removes a category
 *
 * @param int $id Category ID
 * @param string $c Category code
 */
function cot_structure_delcat($id, $c)
{
	global $cot_db, $db_structure, $db_auth, $cfg, $cot_cache;

	$sql = $cot_db->query("DELETE FROM $db_structure WHERE structure_id='$id'");
	cot_auth_remove_item('page', $c);
	$cot_cache && $cot_cache->db->remove('cot_cat', 'system');
}

/**
 * Recalculates category counters
 *
 * @param int $id Category ID
 * @return bool
 */
function cot_structure_resync($id)
{
	global $cot_db, $db_structure, $db_pages;

	$sql = $cot_db->query("SELECT structure_code FROM $db_structure WHERE structure_id='".$id."' ");
	$row = $sql->fetch();
	$sql = $cot_db->query("SELECT COUNT(*) FROM $db_pages
		WHERE page_cat='".$row['structure_code']."' AND (page_state = 0 OR page_state=2)");
	$num = (int) $sql->fetchColumn();
	return (bool) $cot_db->query("UPDATE $db_structure SET structure_pagecount=$num WHERE structure_id='$id'");
}

/**
 * Recalculates counters
 *
 * @param int $id Category ID
 * @return bool
 */
function cot_structure_resyncall()
{
	global $cot_db, $db_structure;

	$res = TRUE;
	$sql = $cot_db->query("SELECT structure_id FROM $db_structure");
	while ($row = $sql->fetch())
	{
		$res &= cot_structure_resync($row['structure_id']);
	}
	$sql->closeCursor();
	return $res;
}

// =========== Extra fields ================================
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

	$extf['field_location'] = $location;
	$extf['field_name'] = $name;
	$extf['field_type'] = $type;
	$extf['field_html'] = $html;
	$extf['field_variants'] = is_null($variants) ? '' : $variants;
	$extf['field_default'] = is_null($default) ? '' : $default;
	$extf['field_required'] = ($required > 0) ? 1 : 0;
	$extf['field_parse'] = is_null($parse) ? 'HTML' : $parse;
	$extf['field_description'] = is_null($description) ? '' : $description;

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
	}
	$sql = "ALTER TABLE $location ADD ".$column_prefix."_$name $sqltype ";
	$step2 = $cot_db->query($sql);
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
	global $cot_db, $db_extra_fields;
	$fieldsres = $cot_db->query("SELECT COUNT(*) FROM $db_extra_fields
			WHERE field_name = '$oldname' AND field_location='$location'");
	if ($fieldsres->rowCount() <= 0
		|| $name != $oldname
		&& $cot_db->query("SHOW COLUMNS FROM $location LIKE '%\_$name'")->rowCount() > 0)
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

	$step1 = $cot_db->update($db_extra_fields, $extf, "field_name = ? AND field_location=?", array($oldname, $location)) == 1;

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
 * Returns forum category dropdown code
 *
 * @param int $check Selected category
 * @param string $name Dropdown name
 * @return string
 */
function cot_selectbox_forumcat($check, $name)
{
	global $usr, $cot_forums_str, $L;

	$result =  "<select name=\"$name\" size=\"1\">";
	if (is_array($cot_forums_str))
		foreach($cot_forums_str as $i => $x)
		{
			$selected = ($i==$check) ? "selected=\"selected\"" : '';
			$result .= "<option value=\"".$i."\" $selected> ".$x['tpath']."</option>";
		}
	$result .= "</select>";
	return $result;
}

/**
 * Returns group selection dropdown code
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param array $skip Hidden groups
 * @return string
 */
function cot_selectbox_groups($check, $name, $skip=array(0))
{
	global $cot_groups;

	$res = "<select name=\"$name\" size=\"1\">";

	foreach($cot_groups as $k => $i)
	{
		$selected = ($k == $check) ? "selected=\"selected\"" : '';
		$res .= (in_array($k, $skip)) ? '' : "<option value=\"$k\" $selected>".$cot_groups[$k]['title']."</option>";
	}
	$res .= "</select>";

	return $res;
}

/**
 * Returns substring position in file
 *
 * @param string $file File path
 * @param string $str Needle
 * @param int $maxsize Search limit
 * @return int
 */
function cot_stringinfile($file, $str, $maxsize=32768)
{
	if ($fp = @fopen($file, 'r'))
	{
		$data = fread($fp, $maxsize);
		$pos = mb_strpos($data, $str);
		$result = !($pos === FALSE);
	}
	else
	{
		$result = FALSE;
	}
	@fclose($fp);
	return $result;
}
?>