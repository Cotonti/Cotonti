<?php
/**
 * Page API
 *
 * @package page
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

// Requirements
sed_require_lang('page', 'module');
sed_require_rc('page');

// Global variables
$GLOBALS['db_pages'] = $GLOBALS['db_x'] . 'pages';

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
 * Gets an array of category children
 *
 * @param string $cat Cat code
 * @param bool $allsublev All sublevels array
 * @param bool $firstcat Add main cat
 * @param bool $userrights Check userrights
 * @param bool $sqlprep use sed_sql_prep function
 * @return array
 */
function sed_structure_children($cat, $allsublev = true,  $firstcat = true, $userrights = true, $sqlprep = true)
{
	global $sed_cat, $sys, $cfg;

	$mtch = $sed_cat[$cat]['path'].'.';
	$mtchlen = mb_strlen($mtch);
	$mtchlvl = mb_substr_count($mtch,".");

	$catsub = array();
	if ($firstcat && (($userrights && sed_auth('page', $cat, 'R') || !$userrights)))
	{
		$catsub[] = $cat;
	}

	foreach($sed_cat as $i => $x)
	{
		if(mb_substr($x['path'], 0, $mtchlen) == $mtch && (($userrights && sed_auth('page', $i, 'R') || !$userrights)))
		{
			$subcat = mb_substr($x['path'], $mtchlen + 1);
			if($allsublev || (!$allsublev && mb_substr_count($x['path'],".") == $mtchlvl))
			{
				$i = ($sqlprep) ? sed_sql_prep($i) : $i;
				$catsub[] = $i;
			}
		}
	}
	return($catsub);
}

/**
 * Gets an array of category parents
 *
 * @param string $cat Cat code
 * @param string $type Type 'full', 'first', 'last'
 * @return mixed
 */
function sed_structure_parents($cat, $type = 'full')
{
	global $sed_cat, $cfg;
	$pathcodes = explode('.', $sed_cat[$cat]['path']);

	if ($type == 'first')
	{
		reset($pathcodes);
		$pathcodes = current($pathcodes);
	}
	elseif ($type == 'last')
	{
		$pathcodes = end($pathcodes);
	}

	return $pathcodes;
}


/**
 * Renders category dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param string $subcat Show only subcats of selected category
 * @param bool $hideprivate Hide private categories
 * @return string
 */
function sed_selectbox_categories($check, $name, $subcat = '', $hideprivate = true)
{
	global $db_structure, $usr, $sed_cat, $L;

	$result = '<select name="'.$name.'" size="1">';

	foreach ($sed_cat as $i => $x)
	{
		$display = ($hideprivate) ? sed_auth('page', $i, 'W') : true;
		if ($display && !empty($subcat) && isset($sed_cat[$subcat]) && !(empty($check)))
		{
			$mtch = $sed_cat[$subcat]['path'].".";
			$mtchlen = mb_strlen($mtch);
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