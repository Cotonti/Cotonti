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
require_once cot_langfile('page', 'module');
require_once cot_incfile('page', 'module', 'resources');
require_once cot_incfile('forms');
require_once cot_incfile('extrafields');

// Global variables
$db_pages = (isset($db_pages)) ? $db_pages : $db_x . 'pages';

$cot_extrafields['pages'] = (!empty($cot_extrafields[$db_pages]))
	? $cot_extrafields[$db_pages] : array();
	
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
	global $db, $db_structure, $usr, $cot_cat, $L, $R;

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
	global $db, $cot_extrafields, $cfg, $L, $Ls, $R, $pag_cache, $db_pages, $usr, $sys, $cot_yesno, $cot_cat;
	
	static $extp_first = null, $extp_main = null;

	if (is_null($extp_first))
	{
		$extp_first = cot_getextplugins('pagetags.first');
		$extp_main = cot_getextplugins('pagetags.main');
	}

	/* === Hook === */
	foreach ($extp_first as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (is_array($page_data) && is_array($pag_cache['page_' . $page_data['page_id']]))
	{
		$temp_array = $pag_cache['page_' . $page_data['page_id']];
	}
	elseif (is_array($pag_cache['page_' . $page_data]))
	{
		$temp_array = $pag_cache['page_' . $page_data];
	}
	else
	{
		if (!is_array($page_data))
		{
			$sql = $db->query("SELECT * FROM $db_pages WHERE page_id = '" . (int) $page_data . "' LIMIT 1");
			$page_data = $sql->fetch();
		}

		if ($page_data['page_id'] > 0 && !empty($page_data['page_title']))
		{
			$catpath = cot_structure_buildpath('page', $page_data['page_cat']);
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

			$date_format = (!empty($date_format)) ? $date_format : $cfg['dateformat'];

			$text = cot_parse($page_data['page_text'], $cfg['page']['markup']);
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
				'FILE_COUNTTIMES' => cot_declension($page_data['page_filecount'], $Ls['Times']),
				'FILE_NAME' => basename($page_data['page_url']),
				'COUNT' => $page_data['page_count'],
				'ADMIN' => $admin_rights ? cot_rc('list_row_admin', array('unvalidate_url' => cot_url('admin', "m=page&a=unvalidate&id=".$page_data['page_id']."&".cot_xg()),'edit_url' => cot_url('page', "m=edit&id=".$page_data['page_id']))) : '',
				'NOTAVAILIBLE' => ($page_data['page_date'] > $sys['now_offset']) ? $L['page_notavailable'].cot_build_timegap($sys['now_offset'], $pag['page_date']) : '',

			);

			// Admin tags
			if ($usr['isadmin'] || $usr['id'] == $page_data['page_ownerid'])
			{
				$temp_array['ADMIN_EDIT'] = cot_rc_link(cot_url('page', 'm=edit&id='.$page_data['page_id']), $L['Edit']);
			}
			else
			{
				$temp_array['ADMIN_EDIT'] = '';
			}

			if ($usr['isadmin'])
			{
				if ($page_data['page_state'] == 1)
				{
					$temp_array['ADMIN_UNVALIDATE'] = cot_rc_link(cot_url('admin', 'm=page&a=validate&id='.$page_data['page_id'].'&x='.$sys['xk']), $L['Validate']);
				}
				else
				{
					$temp_array['ADMIN_UNVALIDATE'] = cot_rc_link(cot_url('admin', 'm=page&a=unvalidate&id='.$page_data['page_id'].'&x='.$sys['xk']), $L['Putinvalidationqueue']);
				}
			}
			else
			{
				$temp_array['ADMIN_UNVALIDATE'] = '';
			}

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

			/* === Hook === */
			foreach ($extp_main as $pl)
			{
				include $pl;
			}
			/* ===== */
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

/**
 * Returns possible values for category sorting order
 */
function cot_page_config_order()
{
	global $cot_extrafields, $L;

	$options_sort = array(
		'id' => $L['Id'],
		'type' => $L['Type'],
		'key' => $L['Key'],
		'title' => $L['Title'],
		'desc' => $L['Description'],
		'text' => $L['Body'],
		'author' => $L['Author'],
		'ownerid' => $L['Owner'],
		'date' => $L['Date'],
		'begin' => $L['Begin'],
		'expire' => $L['Expire'],
		'file' => $L['adm_fileyesno'],
		'url' => $L['adm_fileurl'],
		'size' => $L['adm_filesize'],
		'filecount' => $L['adm_filecount']
	);

	foreach($cot_extrafields['pages'] as $row)
	{
		$options_sort[$row['field_name']] = isset($L['page_'.$row['field_name'].'_title']) ? $L['page_'.$row['field_name'].'_title'] : $row['field_description'];
	}

	$L['cfg_order_params'] = array_values($options_sort);
	return array_keys($options_sort);
}

/**
 * Recalculates page category counters
 *
 * @param string $cat Cat code
 * @return int
 */
function cot_page_sync($cat)
{
	global $db, $db_structure, $db_pages;
	$sql = $db->query("SELECT COUNT(*) FROM $db_pages
		WHERE page_cat='".$db->prep($cat)."' AND (page_state = 0 OR page_state=2)");
	return (int) $sql->fetchColumn();
}

/**
 * Update page category code
 *
 * @param string $oldcat Old Cat code
 * @param string $newcat New Cat code
 * @return bool
 */
function cot_page_updatecat($oldcat, $newcat)
{
	global $db, $db_structure, $db_pages;
	return (bool) $db->update($db_pages, array("page_cat" => $newcat), "page_cat='".$db->prep($oldcat)."'");
}

?>
