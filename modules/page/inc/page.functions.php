<?php
/**
 * Page API
 *
 * @package page
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
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
	if ($mpos === false)
	{
		$mpos = mb_strpos($html, '<hr class="more" />');
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
 * Returns all page tags for coTemplate
 *
 * @param mixed $page_data Page Info Array or ID
 * @param string $tag_prefix Prefix for tags
 * @param int $textlength Text truncate
 * @param bool $admin_rights Page Admin Rights
 * @param bool $pagepath_home Add home link for page path
 * @param string $emptytitle Page title text if page does not exist
 * @return array
 * @global CotDB $db
 */
function cot_generate_pagetags($page_data, $tag_prefix = '', $textlength = 0, $admin_rights = null, $pagepath_home = false, $emptytitle = '')
{
	global $db, $cot_extrafields, $cfg, $L, $Ls, $R, $db_pages, $usr, $sys, $cot_yesno, $structure, $db_structure;

	static $extp_first = null, $extp_main = null;
	static $pag_auth = array();

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
		$delete_url = cot_url('page', "m=edit&a=update&delete=1&id={$page_data['page_id']}&x={$sys['xk']}");

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
			'SHORTTITLE' => htmlspecialchars($page_data['page_title'], ENT_COMPAT, 'UTF-8', false),
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
			'MORE' => ($cutted) ? cot_rc('list_more', array('page_url' => $page_data['page_pageurl'])) : '',
			'AUTHOR' => htmlspecialchars($page_data['page_author']),
			'OWNERID' => $page_data['page_ownerid'],
			'OWNERNAME' => htmlspecialchars($page_data['user_name']),
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
			'FILE_SIZE' => $page_data['page_size'] / 1024, // in KiB; deprecated but kept for compatibility
			'FILE_SIZE_BYTES' => $page_data['page_size'],
			'FILE_SIZE_READABLE' => cot_build_filesize($page_data['page_size'], 1),
			'FILE_ICON' => $page_data['page_fileicon'],
			'FILE_COUNT' => $page_data['page_filecount'],
			'FILE_COUNTTIMES' => cot_declension($page_data['page_filecount'], $Ls['Times']),
			'FILE_NAME' => basename($page_data['page_url']),
			'COUNT' => $page_data['page_count'],
			'ADMIN' => $admin_rights ? cot_rc('list_row_admin', array('unvalidate_url' => $unvalidate_url, 'edit_url' => $edit_url)) : '',
			'NOTAVAILABLE' => ($page_data['page_begin'] > $sys['now']) ? $L['page_notavailable'].cot_build_timegap($sys['now'], $pag['page_begin']) : ''
		);

		// Admin tags
		if ($admin_rights)
		{
			$validate_confirm_url = cot_confirm_url($validate_url, 'page', 'page_confirm_validate');
			$unvalidate_confirm_url = cot_confirm_url($unvalidate_url, 'page', 'page_confirm_unvalidate');
			$delete_confirm_url = cot_confirm_url($delete_url, 'page', 'page_confirm_delete');
			$temp_array['ADMIN_EDIT'] = cot_rc_link($edit_url, $L['Edit']);
			$temp_array['ADMIN_EDIT_URL'] = $edit_url;
			$temp_array['ADMIN_UNVALIDATE'] = $page_data['page_state'] == 1 ?
				cot_rc_link($validate_confirm_url, $L['Validate'], 'class="confirmLink"') :
				cot_rc_link($unvalidate_confirm_url, $L['Putinvalidationqueue'], 'class="confirmLink"');
			$temp_array['ADMIN_UNVALIDATE_URL'] = $page_data['page_state'] == 1 ?
				$validate_confirm_url : $unvalidate_confirm_url;
			$temp_array['ADMIN_DELETE'] = cot_rc_link($delete_confirm_url, $L['Delete'], 'class="confirmLink"');
			$temp_array['ADMIN_DELETE_URL'] = $delete_confirm_url;
		}
		else if ($usr['id'] == $page_data['page_ownerid'])
		{
			$temp_array['ADMIN_EDIT'] = cot_rc_link($edit_url, $L['Edit']);
			$temp_array['ADMIN_EDIT_URL'] = $edit_url;
		}

		// Extrafields
		if (isset($cot_extrafields[$db_pages]))
		{
			foreach ($cot_extrafields[$db_pages] as $exfld)
			{
				$tag = mb_strtoupper($exfld['field_name']);
				$temp_array[$tag.'_TITLE'] = isset($L['page_'.$exfld['field_name'].'_title']) ?  $L['page_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
				$temp_array[$tag] = cot_build_extrafields_data('page', $exfld, $page_data['page_'.$exfld['field_name']], $page_data['page_parser']);
			}
		}

		// Extra fields for structure
		if (isset($cot_extrafields[$db_structure]))
		{
			foreach ($cot_extrafields[$db_structure] as $exfld)
			{
				$tag = mb_strtoupper($exfld['field_name']);
				$temp_array['CAT_'.$tag.'_TITLE'] = isset($L['structure_'.$exfld['field_name'].'_title']) ?  $L['structure_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
				$temp_array['CAT_'.$tag] = cot_build_extrafields_data('structure', $exfld, $structure['page'][$page_data['page_cat']][$exfld['field_name']]);
			}
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

	$return_array = array();
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

	foreach($cot_extrafields[$db_pages] as $exfld)
	{
		$options_sort[$exfld['field_name']] = isset($L['page_'.$exfld['field_name'].'_title']) ? $L['page_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
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
 * @global CotDB $db
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
 * @global CotDB $db
 */
function cot_page_updatecat($oldcat, $newcat)
{
	global $db, $db_structure, $db_pages;
	return (bool) $db->update($db_pages, array("page_cat" => $newcat), "page_cat='".$db->prep($oldcat)."'");
}

/**
 * Returns permissions for a page category.
 * @param  string $cat Category code
 * @return array       Permissions array with keys: 'auth_read', 'auth_write', 'isadmin', 'auth_download'
 */
function cot_page_auth($cat = null)
{
	if (empty($cat))
	{
		$cat = 'any';
	}
	$auth = array();
	list($auth['auth_read'], $auth['auth_write'], $auth['isadmin'], $auth['auth_download']) = cot_auth('page', $cat, 'RWA1');
	return $auth;
}

/**
 * Imports page data from request parameters.
 * @param  string $source Source request method for parameters
 * @param  array  $rpage  Existing page data from database
 * @param  array  $auth   Permissions array
 * @return array          Page data
 */
function cot_page_import($source = 'POST', $rpage = array(), $auth = array())
{
	global $cfg, $db_pages, $cot_extrafields, $usr, $sys;

	if (count($auth) == 0)
	{
		$auth = cot_page_auth($rpage['page_cat']);
	}

	if ($source == 'D' || $source == 'DIRECT')
	{
		// A trick so we don't have to affect every line below
		global $_PATCH;
		$_PATCH = $rpage;
		$source = 'PATCH';
	}

	$rpage['page_cat']      = cot_import('rpagecat', $source, 'TXT');
	$rpage['page_keywords'] = cot_import('rpagekeywords', $source, 'TXT');
	$rpage['page_alias']    = cot_import('rpagealias', $source, 'TXT');
	$rpage['page_title']    = cot_import('rpagetitle', $source, 'TXT');
	$rpage['page_desc']     = cot_import('rpagedesc', $source, 'TXT');
	$rpage['page_text']     = cot_import('rpagetext', $source, 'HTM');
	$rpage['page_parser']   = cot_import('rpageparser', $source, 'ALP');
	$rpage['page_author']   = cot_import('rpageauthor', $source, 'TXT');
	$rpage['page_file']     = intval(cot_import('rpagefile', $source, 'INT'));
	$rpage['page_url']      = cot_import('rpageurl', $source, 'TXT');
	$rpage['page_size']     = cot_import('rpagesize', $source, 'TXT');
	$rpage['page_file']     = ($rpage['page_file'] == 0 && !empty($rpage['page_url'])) ? 1 : $rpage['page_file'];

	$rpagedatenow           = cot_import('rpagedatenow', $source, 'BOL');
	$rpage['page_date']     = cot_import_date('rpagedate', true, false, $source);
	$rpage['page_date']     = ($rpagedatenow || is_null($rpage['page_date'])) ? $sys['now'] : (int)$rpage['page_date'];
	$rpage['page_begin']    = (int)cot_import_date('rpagebegin');
	$rpage['page_expire']   = (int)cot_import_date('rpageexpire');
	$rpage['page_expire']   = ($rpage['page_expire'] <= $rpage['page_begin']) ? 0 : $rpage['page_expire'];
	$rpage['page_updated']  = $sys['now'];

	$rpage['page_keywords'] = cot_import('rpagekeywords', $source, 'TXT');
	$rpage['page_metatitle'] = cot_import('rpagemetatitle', $source, 'TXT');
	$rpage['page_metadesc'] = cot_import('rpagemetadesc', $source, 'TXT');

	$rpublish               = cot_import('rpublish', $source, 'ALP'); // For backwards compatibility
	$rpage['page_state']    = ($rpublish == 'OK') ? 0 : cot_import('rpagestate', $source, 'INT');

	if ($auth['isadmin'] && isset($rpage['page_ownerid']))
	{
		$rpage['page_count']     = cot_import('rpagecount', $source, 'INT');
		$rpage['page_ownerid']   = cot_import('rpageownerid', $source, 'INT');
		$rpage['page_filecount'] = cot_import('rpagefilecount', $source, 'INT');
	}
	else
	{
		$rpage['page_ownerid'] = $usr['id'];
	}

	$parser_list = cot_get_parsers();

	if (empty($rpage['page_parser']) || !in_array($rpage['page_parser'], $parser_list) || $rpage['page_parser'] != 'none' && !cot_auth('plug', $rpage['page_parser'], 'W'))
	{
		$rpage['page_parser'] = isset($sys['parser']) ? $sys['parser'] : $cfg['page']['parser'];
	}

	// Extra fields
	foreach ($cot_extrafields[$db_pages] as $exfld)
	{
		$rpage['page_'.$exfld['field_name']] = cot_import_extrafields('rpage'.$exfld['field_name'], $exfld, $source, $rpage['page_'.$exfld['field_name']]);
	}

	return $rpage;
}

/**
 * Validates page data.
 * @param  array   $rpage Imported page data
 * @return boolean        TRUE if validation is passed or FALSE if errors were found
 */
function cot_page_validate($rpage)
{
	global $cfg, $structure;
	cot_check(empty($rpage['page_cat']), 'page_catmissing', 'rpagecat');
	if ($structure['page'][$rpage['page_cat']]['locked'])
	{
		global $L;
		require_once cot_langfile('message', 'core');
		cot_error('msg602_body', 'rpagecat');
	}
	cot_check(mb_strlen($rpage['page_title']) < 2, 'page_titletooshort', 'rpagetitle');

	cot_check(!empty($rpage['page_alias']) && preg_match('`[+/?%#&]`', $rpage['page_alias']), 'page_aliascharacters', 'rpagealias');

	$allowemptytext = isset($cfg['page']['cat_' . $rpage['page_cat']]['allowemptytext']) ?
							$cfg['page']['cat_' . $rpage['page_cat']]['allowemptytext'] : $cfg['page']['cat___default']['allowemptytext'];
	cot_check(!$allowemptytext && empty($rpage['page_text']), 'page_textmissing', 'rpagetext');

	return !cot_error_found();
}

/**
 * Adds a new page to the CMS.
 * @param  array   $rpage Page data
 * @param  array   $auth  Permissions array
 * @return integer        New page ID or FALSE on error
 */
function cot_page_add(&$rpage, $auth = array())
{
	global $cache, $cfg, $db, $db_pages, $db_structure, $structure, $L;
	if (cot_error_found())
	{
		return false;
	}

	if (count($auth) == 0)
	{
		$auth = cot_page_auth($rpage['page_cat']);
	}

	if (!empty($rpage['page_alias']))
	{
		$page_count = $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_alias = ?", $rpage['page_alias'])->fetchColumn();
		if ($page_count > 0)
		{
			$rpage['page_alias'] = $rpage['page_alias'].rand(1000, 9999);
		}
	}

	if ($rpage['page_state'] == 0)
	{
		if ($auth['isadmin'] && $cfg['page']['autovalidate'])
		{
			$db->query("UPDATE $db_structure SET structure_count=structure_count+1 WHERE structure_area='page' AND structure_code = ?", $rpage['page_cat']);
			$cache && $cache->db->remove('structure', 'system');
		}
		else
		{
			$rpage['page_state'] = 1;
		}
	}

	/* === Hook === */
	foreach (cot_getextplugins('page.add.add.query') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($db->insert($db_pages, $rpage))
	{
		$id = $db->lastInsertId();

		cot_extrafield_movefiles();
	}
	else
	{
		$id = false;
	}

	/* === Hook === */
	foreach (cot_getextplugins('page.add.add.done') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($rpage['page_state'] == 0 && $cache)
	{
		if ($cfg['cache_page'])
		{
			$cache->page->clear('page/' . str_replace('.', '/', $structure['page'][$rpage['page_cat']]['path']));
		}
		if ($cfg['cache_index'])
		{
			$cache->page->clear('index');
		}
	}
	cot_shield_update(30, "r page");
	cot_log("Add page #".$id, 'adm');

	return $id;
}

/**
 * Removes a page from the CMS.
 * @param  int     $id    Page ID
 * @param  array   $rpage Page data
 * @return boolean        TRUE on success, FALSE on error
 */
function cot_page_delete($id, $rpage = array())
{
	global $db, $db_pages, $db_structure, $cache, $cfg, $cot_extrafields, $structure, $L;
	if (!is_numeric($id) || $id <= 0)
	{
		return false;
	}
	$id = (int)$id;
	if (count($rpage) == 0)
	{
		$rpage = $db->query("SELECT * FROM $db_pages WHERE page_id = ?", $id)->fetch();
		if (!$rpage)
		{
			return false;
		}
	}

	if ($rpage['page_state'] == 0)
	{
		$db->query("UPDATE $db_structure SET structure_count=structure_count-1 WHERE  structure_area='page' AND structure_code = ?", $rpage['page_cat']);
	}

	foreach ($cot_extrafields[$db_pages] as $exfld)
	{
		cot_extrafield_unlinkfiles($rpage['page_' . $exfld['field_name']], $exfld);
	}

	$db->delete($db_pages, "page_id = ?", $id);
	cot_log("Deleted page #" . $id, 'adm');

	/* === Hook === */
	foreach (cot_getextplugins('page.edit.delete.done') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($cache)
	{
		if ($cfg['cache_page'])
		{
			$cache->page->clear('page/' . str_replace('.', '/', $structure['page'][$rpage['page_cat']]['path']));
		}
		if ($cfg['cache_index'])
		{
			$cache->page->clear('index');
		}
	}

	return true;
}

/**
 * Updates a page in the CMS.
 * @param  integer $id    Page ID
 * @param  array   $rpage Page data
 * @param  array   $auth  Permissions array
 * @return boolean        TRUE on success, FALSE on error
 */
function cot_page_update($id, &$rpage, $auth = array())
{
	global $cache, $cfg, $db, $db_pages, $db_structure, $structure, $L;
	if (cot_error_found())
	{
		return false;
	}

	if (count($auth) == 0)
	{
		$auth = cot_page_auth($rpage['page_cat']);
	}

	if (!empty($rpage['page_alias']))
	{
		$page_count = $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_alias = ? AND page_id != ?", array($rpage['page_alias'], $id))->fetchColumn();
		if ($page_count > 0)
		{
			$rpage['page_alias'] = $rpage['page_alias'].rand(1000, 9999);
		}
	}

	$row_page = $db->query("SELECT * FROM $db_pages WHERE page_id = ?", $id)->fetch();

	if ($row_page['page_cat'] != $rpage['page_cat'] && $row_page['page_state'] == 0)
	{
		$db->query("UPDATE $db_structure SET structure_count=structure_count-1 WHERE structure_code = ? AND structure_area = 'page'", $row_page['page_cat']);
	}

	//$usr['isadmin'] = cot_auth('page', $rpage['page_cat'], 'A');
	if ($rpage['page_state'] == 0)
	{
		if ($auth['isadmin'] && $cfg['page']['autovalidate'])
		{
			if ($row_page['page_state'] != 0 || $row_page['page_cat'] != $rpage['page_cat'])
			{
				$db->query("UPDATE $db_structure SET structure_count=structure_count+1 WHERE structure_code = ? AND structure_area = 'page'", $rpage['page_cat']);
			}
		}
		else
		{
			$rpage['page_state'] = 1;
		}
	}

	if ($rpage['page_state'] != 0 && $row_page['page_state'] == 0)
	{
		$db->query("UPDATE $db_structure SET structure_count=structure_count-1 WHERE structure_code = ?", $rpage['page_cat']);
	}
	$cache && $cache->db->remove('structure', 'system');

	if (!$db->update($db_pages, $rpage, 'page_id = ?', $id))
	{
		return false;
	}

	cot_extrafield_movefiles();

	/* === Hook === */
	foreach (cot_getextplugins('page.edit.update.done') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (($rpage['page_state'] == 0  || $rpage['page_cat'] != $row_page['page_cat']) && $cache)
	{
		if ($cfg['cache_page'])
		{
			$cache->page->clear('page/' . str_replace('.', '/', $structure['page'][$rpage['page_cat']]['path']));
			if ($rpage['page_cat'] != $row_page['page_cat'])
			{
				$cache->page->clear('page/' . str_replace('.', '/', $structure['page'][$row_page['page_cat']]['path']));
			}
		}
		if ($cfg['cache_index'])
		{
			$cache->page->clear('index');
		}
	}

	return true;
}

?>
