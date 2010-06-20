<?php

/**
 * Reads raw data from file
 *
 * @param string $file File path
 * @return string
 */
function sed_readraw($file)
{
	return (mb_strpos($file, '..') === false && file_exists($file)) ? file_get_contents($file) : 'File not found : '.$file; // TODO need translate
}

/**
 * Renders category dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param bool $subcatonly Show only subcats of selected category
 * @param bool $hideprivate Hide private categories
 * @return string
 */
function sed_selectbox_categories($check, $name, $subcatonly = false, $hideprivate = true)
{
	global $db_structure, $usr, $sed_cat, $L;

	$mtch = $sed_cat[$check]['path'].".";
	$mtchlen = mb_strlen($mtch);

	$result = '<select name="'.$name.'" size="1">';

	foreach ($sed_cat as $i => $x)
	{
		$display = ($hideprivate) ? sed_auth('page', $i, 'W') : true;
		if ($display && $subcatonly && !(empty($check)))
		{
			$display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i == $check) ? true : false;
		}

		if (sed_auth('page', $i, 'R') && $i!='all' && $display)
		{
			$selected = ($i == $check) ? 'selected="selected"' : '';
			$result .= '<option value="'.$i.'" '.$selected.'> '.$x['tpath'].'</option>';
		}
	}
	$result .= '</select>';
	return($result);
}

?>