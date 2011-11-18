<?php
/**
 * Page API
 *
 * @package page
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// Requirements
require_once cot_langfile('page', 'module');
require_once cot_incfile('page', 'module', 'resources');
require_once cot_incfile('forms');
require_once cot_incfile('extrafields');

// Global variables
global $cot_extrafields, $db_pages, $db_x;
$db_pages = (isset($db_pages)) ? $db_pages : $db_x . 'pages';

$cot_extrafields[$db_pages] = (!empty($cot_extrafields[$db_pages]))	? $cot_extrafields[$db_pages] : array();

$structure['page'] = (is_array($structure['page'])) ? $structure['page'] : array();	

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
	global $db, $db_structure, $usr, $structure, $L, $R;

	$structure['page'] = (is_array($structure['page'])) ? $structure['page'] : array();
	
	$result_array = array();
	foreach ($structure['page'] as $i => $x)
	{
		$display = ($hideprivate) ? cot_auth('page', $i, 'W') : true;
		if ($display && !empty($subcat) && isset($structure['page'][$subcat]) && !(empty($check)))
		{
			$mtch = $structure['page'][$subcat]['path'].".";
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
 * @param bool $pagepath_home Add home link for page path
 * @param string $emptytitle Page title text if page does not exist
 * @param bool $cacheitem Cache tags
 * @return array
 */
function cot_generate_pagetags($page_data, $tag_prefix = '', $textlength = 0, $admin_rights = null, $pagepath_home = false, $emptytitle = '', $cacheitem = true)
{
	global $db, $cot_extrafields, $cfg, $L, $Ls, $R, $db_pages, $usr, $sys, $cot_yesno, $structure, $db_structure;
	
	static $extp_first = null, $extp_main = null;
	static $pag_auth = array(), $pag_cache = array();

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

	if (is_array($page_data) && is_array($pag_cache[$page_data['page_id']]))
	{
		$temp_array = $pag_cache[$page_data['page_id']];
	}
	elseif (is_int($page_data) && is_array($pag_cache[$page_data]))
	{
		$temp_array = $pag_cache[$page_data];
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
			if (is_null($admin_rights))
			{
				if (!isset($pag_auth[$page_data['page_cat']]))
				{
					$pag_auth[$page_data['page_cat']] = cot_auth('page', $page_data['page_cat'], 'RWA1');
				}
				$admin_rights = (bool) $pag_auth[$page_data['page_cat']][2];
			}
			$pagepath = cot_structure_buildpath('page', $page_data['page_cat']);
			$catpath = cot_breadcrumbs($pagepath, $pagepath_home);
			$page_data['page_pageurl'] = (empty($page_data['page_alias'])) ? cot_url('page', 'c='.$page_data['page_cat'].'&id='.$page_data['page_id']) : cot_url('page', 'c='.$page_data['page_cat'].'&al='.$page_data['page_alias']);
			$page_link[] = array($page_data['page_pageurl'], $page_data['page_title']);
			$page_data['page_fulltitle'] = cot_breadcrumbs(array_merge($pagepath, $page_link), $pagepath_home);
			if (!empty($page_data['page_url']) && $page_data['page_file'])
			{
				$dotpos = mb_strrpos($page_data['page_url'], ".") + 1;
				$type = mb_strtolower(mb_substr($page_data['page_url'], $dotpos, 5));
				$page_data['page_fileicon'] = cot_rc('page_icon_file_path', array('type' => $type));
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

			$date_format = 'datetime_medium';

			$text = cot_parse($page_data['page_text'], $cfg['page']['markup'], $page_data['page_parser']);
			$text_cut = ((int)$textlength > 0) ? cot_string_truncate($text, $textlength) : cot_cut_more($text);
			$cutted = (mb_strlen($text) > mb_strlen($text_cut)) ? true : false;

			$cat_url = cot_url('page', 'c=' . $page_data['page_cat']);
			$validate_url = cot_url('admin', "m=page&a=validate&id={$page_data['page_id']}&x={$sys['xk']}");
			$unvalidate_url = cot_url('admin', "m=page&a=unvalidate&id={$page_data['page_id']}&x={$sys['xk']}");
			$edit_url = cot_url('page', "m=edit&id={$page_data['page_id']}");
			
			$page_data['page_status'] = cot_page_status(
				$page_data['page_state'], 
				$page_data['page_begin'], 
				$page_data['page_expire']
			);

			$temp_array = array(
				'URL' => $page_data['page_pageurl'],
				'ID' => $page_data['page_id'],
				'TITLE' => $page_data['page_fulltitle'],
				'ALIAS' => $page_data['page_alias'],
				'STATE' => $page_data['page_state'],
				'STATUS' => $page_data['page_status'],
				'LOCALSTATUS' => $L['page_status_'.$page_data['page_status']],
				'SHORTTITLE' => htmlspecialchars($page_data['page_title']),
				'CAT' => $page_data['page_cat'],
				'CATURL' => $cat_url,
				'CATTITLE' => htmlspecialchars($structure['page'][$page_data['page_cat']]['title']),
				'CATPATH' => $catpath,
				'CATPATH_SHORT' => cot_rc_link($cat_url, htmlspecialchars($structure['page'][$page_data['page_cat']]['title'])),
				'CATDESC' => htmlspecialchars($structure['page'][$page_data['page_cat']]['desc']),
				'CATICON' => $structure['page'][$page_data['page_cat']]['icon'],
				'KEYWORDS' => htmlspecialchars($page_data['page_keywords']),
				'DESC' => htmlspecialchars($page_data['page_desc']),
				'TEXT' => $text,
				'TEXT_CUT' => $text_cut,
				'TEXT_IS_CUT' => $cutted,
				'DESC_OR_TEXT' => (!empty($page_data['page_desc'])) ? htmlspecialchars($page_data['page_desc']) : $text,
				'MORE' => ($cutted) ? cot_rc_link($page_data['page_pageurl'], $L['ReadMore']) : "",
				'AUTHOR' => htmlspecialchars($page_data['page_author']),
				'DATE' => cot_date($date_format, $page_data['page_date']),
				'BEGIN' => cot_date($date_format, $page_data['page_begin']),
				'EXPIRE' => cot_date($date_format, $page_data['page_expire']),
				'UPDATED' => cot_date($date_format, $page_data['page_updated']),
				'DATE_STAMP' => $page_data['page_date'],
				'BEGIN_STAMP' => $page_data['page_begin'],
				'EXPIRE_STAMP' => $page_data['page_expire'],
				'UPDATED_STAMP' => $page_data['page_updated'],
				'FILE' => $cot_yesno[$page_data['page_file']],
				'FILE_URL' => empty($page_data['page_url']) ? '' : cot_url('page', 'c='.$page_data['page_cat'].'&id='.$page_data['page_id'].'&a=dl'),
				'FILE_SIZE' => $page_data['page_size'],
				'FILE_SIZE_READABLE' => cot_build_filesize($page_data['page_size']),
				'FILE_ICON' => $page_data['page_fileicon'],
				'FILE_COUNT' => $page_data['page_filecount'],
				'FILE_COUNTTIMES' => cot_declension($page_data['page_filecount'], $Ls['Times']),
				'FILE_NAME' => basename($page_data['page_url']),
				'COUNT' => $page_data['page_count'],
				'ADMIN' => $admin_rights ? cot_rc('list_row_admin', array('unvalidate_url' => $unvalidate_url, 'edit_url' => $edit_url)) : '',
				'NOTAVAILABLE' => ($page_data['page_begin'] > $sys['now_offset']) ? $L['page_notavailable'].cot_build_timegap($sys['now'], $pag['page_begin']) : ''
			);

			// Admin tags
			if ($admin_rights)
			{
				$validate_confirm_url = cot_confirm_url($validate_url, 'page', 'page_confirm_validate');
				$unvalidate_confirm_url = cot_confirm_url($unvalidate_url, 'page', 'page_confirm_unvalidate');
				$temp_array['ADMIN_EDIT'] = cot_rc_link($edit_url, $L['Edit']);
				$temp_array['ADMIN_EDIT_URL'] = $edit_url;
				$temp_array['ADMIN_UNVALIDATE'] = $page_data['page_state'] == 1 ?
					cot_rc_link($validate_confirm_url, $L['Validate'], 'class="confirmLink"') :
					cot_rc_link($unvalidate_confirm_url, $L['Putinvalidationqueue'], 'class="confirmLink"');
				$temp_array['ADMIN_UNVALIDATE_URL'] = $page_data['page_state'] == 1 ?
					$validate_confirm_url : $unvalidate_confirm_url;
			}
			else if ($usr['id'] == $page_data['page_ownerid'])
			{
				$temp_array['ADMIN_EDIT'] = cot_rc_link($edit_url, $L['Edit']);
				$temp_array['ADMIN_EDIT_URL'] = $edit_url;
			}

			// Extrafields
			foreach ($cot_extrafields[$db_pages] as $row)
			{
				$tag = mb_strtoupper($row['field_name']);
				$temp_array[$tag.'_TITLE'] = isset($L['page_'.$row['field_name'].'_title']) ?  $L['page_'.$row['field_name'].'_title'] : $row['field_description'];
				$temp_array[$tag] = cot_build_extrafields_data('page', $row, $page_data["page_{$row['field_name']}"], $page_data['page_parser']);
			}

			// Extra fields for structure
			foreach ($cot_extrafields[$db_structure] as $row)
			{
				$tag = mb_strtoupper($row['field_name']);
				$temp_array['CAT_'.$tag.'_TITLE'] = isset($L['structure_'.$row['field_name'].'_title']) ?  $L['structure_'.$row['field_name'].'_title'] : $row['field_description'];
				$temp_array['CAT_'.$tag] = cot_build_extrafields_data('structure', $row, $structure['page'][$row['page_cat']][$row['field_name']]);
			}

			/* === Hook === */
			foreach ($extp_main as $pl)
			{
				include $pl;
			}
			/* ===== */
			$cacheitem && $pag_cache[$page_data['page_id']] = $temp_array;
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
	global $cot_extrafields, $L, $db_pages;

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

	foreach($cot_extrafields[$db_pages] as $row)
	{
		$options_sort[$row['field_name']] = isset($L['page_'.$row['field_name'].'_title']) ? $L['page_'.$row['field_name'].'_title'] : $row['field_description'];
	}

	$L['cfg_order_params'] = array_values($options_sort);
	return array_keys($options_sort);
}

/**
 * Determines page status
 *
 * @param int $page_state
 * @param int $page_begin
 * @param int $page_expire
 * @return string 'draft', 'pending', 'approved', 'published' or 'expired'
 */
function cot_page_status($page_state, $page_begin, $page_expire)
{
	global $sys;
	
	if ($page_state == 0)
	{
		if ($page_expire > 0 && $page_expire <= $sys['now'])
		{
			return 'expired';
		}
		elseif ($page_begin > $sys['now'])
		{
			return 'approved';
		}
		return 'published';
	}
	elseif ($page_state == 2)
	{
		return 'draft';
	}
	return 'pending';
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
