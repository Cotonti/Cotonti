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

defined('COT_CODE') or die('Wrong URL.');

// Requirements
cot_require_lang('page', 'module');
cot_require_rc('page');
cot_require_api('forms');

// Global variables
$GLOBALS['db_pages'] = (isset($GLOBALS['db_pages'])) ? $GLOBALS['db_pages'] : $GLOBALS['db_x'] . 'pages';

$GLOBALS['cot_extrafields']['pages'] = (!empty($GLOBALS['cot_extrafields'][$GLOBALS['db_pages']]))
	? $GLOBALS['cot_extrafields'][$GLOBALS['db_pages']] : array();

/**
 * Reads raw data from file
 *
 * @param string $file File path
 * @return string
 */
function cot_readraw($file)
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
 * @param bool $sqlprep use cot_db_prep function
 * @return array
 */
function cot_structure_children($cat, $allsublev = true,  $firstcat = true, $userrights = true, $sqlprep = true)
{
	global $cot_cat, $sys, $cfg;

	$mtch = $cot_cat[$cat]['path'].'.';
	$mtchlen = mb_strlen($mtch);
	$mtchlvl = mb_substr_count($mtch,".");

	$catsub = array();
	if ($firstcat && (($userrights && cot_auth('page', $cat, 'R') || !$userrights)))
	{
		$catsub[] = $cat;
	}

	foreach($cot_cat as $i => $x)
	{
		if(mb_substr($x['path'], 0, $mtchlen) == $mtch && (($userrights && cot_auth('page', $i, 'R') || !$userrights)))
		{
			$subcat = mb_substr($x['path'], $mtchlen + 1);
			if($allsublev || (!$allsublev && mb_substr_count($x['path'],".") == $mtchlvl))
			{
				$i = ($sqlprep) ? cot_db_prep($i) : $i;
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
function cot_structure_parents($cat, $type = 'full')
{
	global $cot_cat, $cfg;
	$pathcodes = explode('.', $cot_cat[$cat]['path']);

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
function cot_selectbox_categories($check, $name, $subcat = '', $hideprivate = true)
{
	global $db_structure, $usr, $cot_cat, $L, $R;

	foreach ($cot_cat as $i => $x)
	{
		$display = ($hideprivate) ? cot_auth('page', $i, 'W') : true;
		if ($display && !empty($subcat) && isset($cot_cat[$subcat]) && !(empty($check)))
		{
			$mtch = $cot_cat[$subcat]['path'].".";
			$mtchlen = mb_strlen($mtch);
			$display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i == $check) ? true : false;
		}

		if (cot_auth('page', $i, 'R') && $i!='all' && $display)
		{
			$result_array[$i] = $x['tpath'];
		}
	}
	$result = cot_selectbox($check, $name, array_keys($result_array), array_values($result_array), false);

	return($result);
}

?>