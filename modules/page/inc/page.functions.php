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
 * Cuts the page after 'more' tag or after the first page (if multipage)
 *
 * @param string $html Page body
 * @return string
 */
function cot_cut_more($html)
{
	$mpos = mb_strpos($html, '<!--more-->');
	if ($mpos === false)
	{
		$mpos = mb_strpos($html, '[more]');
	}
	if ($mpos !== false)
	{
		$html = mb_substr($html, 0, $mpos);
	}
	$mpos = mb_strpos($html, '[newpage]');
	if ($mpos !== false)
	{
		$html = mb_substr($html, 0, $mpos);
	}
	if (mb_strpos($html, '[title]'))
	{
		$html = preg_replace('#\[title\](.*?)\[/title\][\s\r\n]*(<br />)?#i', '', $html);
	}
	return $html;
}

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
 * @param bool $sqlprep use $cot_db->prep function
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
				$i = ($sqlprep) ? $cot_db->prep($i) : $i;
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
	global $cot_db, $db_structure, $usr, $cot_cat, $L, $R;

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

/**
 * Returns all page tags for coTemplate
 *
 * @param mixed $page_data Page Info Array or ID
 * @param string $tag_prefix Prefix for tags
 * @param int $textlength Text truncate
 * @param bool $admin_rights Page Admin Rights
 * @param bool $date_format DateTime Format
 * @param string $emptytitle Page title text if page is not exist
 *
 * @return array
 */
function cot_generate_pagetags($page_data, $tag_prefix = '', $textlength = 0, $admin_rights = 0, $dateformat='', $emptytitle='')
{
	global $cot_db, $cot_extrafields, $cfg, $L, $R, $cache, $db_pages, $usr, $sys, $cot_yesno, $cot_cat;
	if (is_array($page_data) && is_array($cache['page_' . $page_data['page_id']]))
	{
		$temp_array = $cache['page_' . $page_data['page_id']];
	}
	elseif (is_array($cache['page_' . $page_data]))
	{
		$temp_array = $cache['page_' . $page_data];
	}
	else
	{
		if (!is_array($page_data))
		{
			$sql = $cot_db->query("SELECT * FROM $db_pages WHERE page_id = '" . (int) $page_data . "' LIMIT 1");
			$page_data = $sql->fetch();
		}

		if ($page_data['user_id'] > 0 && !empty($page_data['user_name']))
		{
			$catpath = cot_build_catpath($page_data['page_cat']);
			$page_data['page_pageurl'] = (empty($page_data['page_alias'])) ? cot_url('page', 'id='.$page_data['page_id']) : cot_url('page', 'al='.$page_data['page_alias']);
			$page_data['page_fulltitle'] = $catpath." ".$cfg['separator'].' '.cot_rc_link($page_data['page_pageurl'], htmlspecialchars($page_data['page_title']));

			if (!empty($page_data['page_url']) && $page_data['page_file'])
			{
				$dotpos = mb_strrpos($page_data['page_url'], ".") + 1;
				$type = mb_strtolower(mb_substr($page_data['page_url'], $dotpos, 5));
				$page_data['page_fileicon'] = cot_rc('page_icon_file_path');
				if (!file_exists($page_data['page_fileicon']))
				{
					$page_data['page_fileicon'] = cot_rc('page_icon_file_default');
				}
				$page_data['page_fileicon'] = cot_rc('page_icon_file', array('icon' => $page_data['page_fileicon']));
			}
			else
			{
				$page_data['page_fileicon'] = '';
			}

			$item_code = 'p'.$page_data['page_id'];
			list($page_data['page_ratings'], $page_data['page_ratings_display']) = cot_build_ratings($item_code, $page_data['page_pageurl'], $ratings);

			$date_format = (!empty($date_format)) ? $date_format : $cfg['dateformat'];

			$text = cot_parse($page_data['page_text'], $cfg['module']['page']['markup']);
			$text = ((int)$textlength > 0) ? cot_string_truncate($text, $textlength) : cot_cut_more($text);
			$cutted = (mb_strlen($page_data['page_text']) > mb_strlen($text)) ? true : false;

			$temp_array = array(
				'URL' => $page_data['page_pageurl'],
				'ID' => $page_data['page_id'],
				'TITLE' => $page_data['page_fulltitle'],
				'ALIAS' => $page_data['page_alias'],
				'STATE' => $page_data['page_state'],
				'SHORTTITLE' => htmlspecialchars($page_data['page_title']),
				'CAT' => $page_data['page_cat'],
				'CATURL' => cot_url('page', 'c=' . $page_data['page_cat']),
				'CATTITLE' => htmlspecialchars($cot_cat[$page_data['page_cat']]['title']),
				'CATPATH' => $catpath,
				'CATPATH_SHORT' => cot_rc_link(cot_url('page', 'c='.$page_data['page_cat']), htmlspecialchars($cot_cat[$page_data['page_cat']]['title'])),
				'CATDESC' => htmlspecialchars($cot_cat[$page_data['page_cat']]['desc']),
				'CATICON' => $cot_cat[$page_data['page_cat']]['icon'],
				'KEY' => htmlspecialchars($page_data['page_key']),
				'DESC' => htmlspecialchars($page_data['page_desc']),
				'TEXT' => $text,
				'DESC_OR_TEXT' => (!empty($page_data['page_desc'])) ? htmlspecialchars($page_data['page_desc']) : $text,
				'MORE' => ($cutted) ? cot_rc_link($page_data['page_pageurl'], $L['ReadMore']) : "",
				'AUTHOR' => htmlspecialchars($page_data['page_author']),
				'DATE' => @date($date_format, $page_data['page_date'] + $usr['timezone'] * 3600),
				'BEGIN' => @date($date_format, $page_data['page_begin'] + $usr['timezone'] * 3600),
				'EXPIRE' => @date($date_format, $page_data['page_expire'] + $usr['timezone'] * 3600),
				'FILE' => $cot_yesno[$page_data['page_file']],
				'FILE_URL' => empty($page_data['page_url']) ? '' : cot_url('page', 'id='.$page_data['page_id'].'&a=dl'),
				'FILE_SIZE' => $page_data['page_size'],
				'FILE_ICON' => $page_data['page_fileicon'],
				'FILE_COUNT' => $page_data['page_filecount'],
				'FILE_NAME' => basename($page_data['page_url']),
				'COUNT' => $page_data['page_count'],
				'RATINGS' => $page_data['page_ratings'],
				'ADMIN' => $admin_rights ? cot_rc('list_row_admin', array('unvalidate_url' => cot_url('admin', "m=page&a=unvalidate&id=".$page_data['page_id']."&".cot_xg()),'edit_url' => cot_url('page', "m=edit&id=".$page_data['page_id']))) : '',
				'NOTAVAILIBLE' => ($page_data['page_date'] > $sys['now_offset']) ? $L['page_notavailable'].cot_build_timegap($sys['now_offset'], $pag['page_date']) : '',

			);

			// Extrafields
			foreach ($cot_extrafields['pages'] as $row)
			{
				$temp_array[strtoupper($row_p['field_name']).'_TITLE'] = isset($L['page_'.$row['field_name'].'_title']) ?  $L['page_'.$row['field_name'].'_title'] : $row['field_description'];
				$temp_array[mb_strtoupper($row['field_name'])] = cot_build_extrafields_data('page', $row['field_type'], $row['field_name'], $page_data["page_{$row['field_name']}"]);
			}

			// Extra fields for structure
			foreach ($cot_extrafields['structure'] as $row_c)
			{
				$uname = strtoupper($row_c['field_name']);
				$temp_array['CAT_'.$uname.'_TITLE'] = isset($L['structure_'.$row_c['field_name'].'_title']) ?  $L['structure_'.$row_c['field_name'].'_title'] : $row_c['field_description'];
				$temp_array['CAT_'.$uname] = cot_build_extrafields_data('structure', $row_c['field_type'], $row_c['field_name'], $cot_cat[$row['page_cat']][$row_c['field_name']]);
			}
		}
		else
		{
			$temp_array = array(
				'TITLE' => (!empty($emptytitle)) ? $emptytitle : $L['Deleted'],
				'SHORTTITLE' => (!empty($emptytitle)) ? $emptytitle : $L['Deleted'],
			);
		}
	}
	foreach ($temp_array as $key => $val)
	{
		$return_array[$tag_prefix . $key] = $val;
	}

	return $return_array;
}

?>
