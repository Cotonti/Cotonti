<?php
/**
 * Page API
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

// Requirements
require_once cot_langfile('page', 'module');
require_once cot_incfile('page', 'module', 'resources');
require_once cot_incfile('forms');
require_once cot_incfile('extrafields');

/*
 * Page States
 * 0 - published
 * 1 - waiting for approve by admin (moderator)
 * 2 - draft
*/
const COT_PAGE_STATE_PUBLISHED = 0;
const COT_PAGE_STATE_PENDING = 1;
const COT_PAGE_STATE_DRAFT = 2;

// Tables and extras
Cot::$db->registerTable('pages');

cot_extrafields_register_table('pages');

if (empty(Cot::$structure['page'])) {
    Cot::$structure['page'] = [];
}

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
 * @param int|array $page_data Page Info Array or ID
 * @param string $tag_prefix Prefix for tags
 * @param int $textlength Text truncate
 * @param bool $admin_rights Page Admin Rights
 * @param bool $pagepath_home Add home link for page path
 * @param string $emptytitle Page title text if page does not exist
 * @param string $backUrl BackUrl for page validate actions
 *
 * @return array|null
 * @global CotDB $db
 */
function cot_generate_pagetags(
    $page_data,
    $tag_prefix = '',
    $textlength = 0,
    $admin_rights = null,
    $pagepath_home = false,
    $emptytitle = '',
    $backUrl = null
) {
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R, $cfg;

	global $db, $cot_extrafields, $db_pages, $usr, $sys, $cot_yesno, $structure, $db_structure;

	static $extp_first = null, $extp_main = null;
	static $pag_auth = array();

	if (is_null($extp_first)) {
		$extp_first = cot_getextplugins('pagetags.first');
		$extp_main = cot_getextplugins('pagetags.main');
	}

	/* === Hook === */
	foreach ($extp_first as $pl) {
		include $pl;
	}
	/* ===== */

	if (!empty($page_data) && !is_array($page_data)) {
        $pageID = (int) $page_data;
        $page_data = null;
        if ($pageID > 0) {
            $sql = Cot::$db->query('SELECT * FROM ' . Cot::$db->pages . ' WHERE page_id = ? LIMIT 1', $pageID);
            $page_data = $sql->fetch();
        }
	}

    if (empty($page_data)) {
        return null;
    }

	if ($page_data['page_id'] > 0 && !empty($page_data['page_title'])) {
		if (is_null($admin_rights)) {
			if (!isset($pag_auth[$page_data['page_cat']])) {
				$pag_auth[$page_data['page_cat']] = cot_auth('page', $page_data['page_cat'], 'RWA1');
			}
			$admin_rights = (bool) $pag_auth[$page_data['page_cat']][2];
		}
		$pagepath = cot_structure_buildpath('page', $page_data['page_cat']);
		$catpath = cot_breadcrumbs($pagepath, $pagepath_home, false);
        $page_data['page_pageurl'] = cot_page_url($page_data);
		$page_link[] = [$page_data['page_pageurl'], $page_data['page_title']];
		$page_data['page_fulltitle'] = cot_breadcrumbs(array_merge($pagepath, $page_link), $pagepath_home);
		if (!empty($page_data['page_url']) && $page_data['page_file']) {
			$dotpos = mb_strrpos($page_data['page_url'], ".") + 1;
			$type = mb_strtolower(mb_substr($page_data['page_url'], $dotpos, 5));
			$page_data['page_fileicon'] = cot_rc('page_icon_file_path', ['type' => $type]);
			if (!file_exists($page_data['page_fileicon'])) {
				$page_data['page_fileicon'] = cot_rc('page_icon_file_default');
			}
			$page_data['page_fileicon'] = cot_rc('page_icon_file', ['icon' => $page_data['page_fileicon']]);

        } else {
			$page_data['page_fileicon'] = '';
		}

		$date_format = 'datetime_medium';

		$text = cot_parse($page_data['page_text'], $cfg['page']['markup'], $page_data['page_parser']);
		$text_cut = cot_cut_more($text);
		if ($textlength > 0 && mb_strlen($text_cut) > $textlength) {
			$text_cut = cot_string_truncate($text_cut, $textlength);
		}
		$cutted = mb_strlen($text) > mb_strlen($text_cut);

		$cat_url = cot_url('page', ['c' => $page_data['page_cat']]);

        $urlParams = [
            'm' => 'page',
            'a' => 'validate',
            'id' => $page_data['page_id'],
            'x' => Cot::$sys['xk'],
        ];
        if (!empty($backUrl)) {
            $urlParams['back'] = base64_encode($backUrl);
        }
		$validate_url = cot_url('admin', $urlParams);

        $urlParams['a'] = 'unvalidate';
		$unvalidate_url = cot_url('admin', $urlParams);

		$edit_url = cot_url('page', "m=edit&id={$page_data['page_id']}");
		$delete_url = cot_url('page', "m=edit&a=update&delete=1&id={$page_data['page_id']}&x={$sys['xk']}");

		$page_data['page_status'] = cot_page_status(
			$page_data['page_state'],
			$page_data['page_begin'],
			$page_data['page_expire']
		);

        $haveFile = Cot::$L['No'];
        $page_data['page_file'] = (int) $page_data['page_file'];
        if($page_data['page_file'] == 1) {
            $haveFile = Cot::$L['Yes'];
        } elseif ($page_data['page_file'] == 2) {
            $haveFile = Cot::$L['Members_download'];
        }

        $catTitle = '';
        if (isset($structure['page'][$page_data['page_cat']]['title'])) {
            $catTitle = htmlspecialchars($structure['page'][$page_data['page_cat']]['title']);
        }

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
			'CATTITLE' => $catTitle,
			'CATPATH' => $catpath,
			'CATPATH_SHORT' => cot_rc_link($cat_url, $catTitle),
			'CATDESC' => (isset($structure['page'][$page_data['page_cat']]['desc'])
                && $structure['page'][$page_data['page_cat']]['desc'] != '') ?
                htmlspecialchars($structure['page'][$page_data['page_cat']]['desc']) : '',
			'CATICON' => isset($structure['page'][$page_data['page_cat']]['icon']) ?
                $structure['page'][$page_data['page_cat']]['icon'] : '',
			'KEYWORDS' => (isset($page_data['page_keywords']) && $page_data['page_keywords'] != '') ?
                htmlspecialchars($page_data['page_keywords']) : '',
			'DESC' => (isset($page_data['page_desc']) && $page_data['page_desc'] != '') ?
                htmlspecialchars($page_data['page_desc']) : '',
			'TEXT' => $text,
			'TEXT_CUT' => $text_cut,
			'TEXT_IS_CUT' => $cutted,
			'DESC_OR_TEXT' => (isset($page_data['page_desc']) && $page_data['page_desc'] != '') ?
                htmlspecialchars($page_data['page_desc']) : $text,
			'DESC_OR_TEXT_CUT' => (isset($page_data['page_desc']) && $page_data['page_desc'] != '') ?
                htmlspecialchars($page_data['page_desc']) : $text_cut,
			'MORE' => ($cutted) ? cot_rc('list_more', array('page_url' => $page_data['page_pageurl'])) : '',
			'AUTHOR' => (isset($page_data['page_author']) && $page_data['page_author'] != '') ?
                htmlspecialchars($page_data['page_author']) : '',
			'OWNERID' => $page_data['page_ownerid'],
			'OWNERNAME' => (isset($page_data['user_name']) && $page_data['user_name'] != '') ?
                htmlspecialchars($page_data['user_name']) : '',
			'DATE' => cot_date($date_format, $page_data['page_date']),
			'BEGIN' => cot_date($date_format, $page_data['page_begin']),
			'EXPIRE' => cot_date($date_format, $page_data['page_expire']),
			'UPDATED' => cot_date($date_format, $page_data['page_updated']),
			'DATE_STAMP' => $page_data['page_date'],
			'BEGIN_STAMP' => $page_data['page_begin'],
			'EXPIRE_STAMP' => $page_data['page_expire'],
			'UPDATED_STAMP' => $page_data['page_updated'],
			'FILE' => $haveFile,
			'FILE_URL' => !empty($page_data['page_url'])
                ? cot_url('page', ['c' => $page_data['page_cat'], 'id' => $page_data['page_id'], 'a' => 'dl'])
                : '',
			'FILE_SIZE' => $page_data['page_size'] / 1024, // in KiB; deprecated but kept for compatibility
			'FILE_SIZE_BYTES' => $page_data['page_size'],
			'FILE_SIZE_READABLE' => cot_build_filesize($page_data['page_size'], 1),
			'FILE_ICON' => $page_data['page_fileicon'],
			'FILE_COUNT' => $page_data['page_filecount'],
			'FILE_COUNTTIMES' => cot_declension($page_data['page_filecount'], $Ls['Times']),
			'FILE_NAME' => !empty($page_data['page_url']) ? basename($page_data['page_url']) : '',
			'COUNT' => $page_data['page_count'],
                'ADMIN' => $admin_rights ? cot_rc('list_row_admin', array('unvalidate_url' => $unvalidate_url, 'edit_url' => $edit_url)) : '',
			'NOTAVAILABLE' => ($page_data['page_begin'] > Cot::$sys['now']) ?
                Cot::$L['page_notavailable'] . cot_build_timegap(Cot::$sys['now'], $page_data['page_begin']) : ''
		);

		// Admin tags
		if ($admin_rights) {
			$validate_confirm_url = cot_confirm_url($validate_url, 'page', 'page_confirm_validate');
			$unvalidate_confirm_url = cot_confirm_url($unvalidate_url, 'page', 'page_confirm_unvalidate');
			$delete_confirm_url = cot_confirm_url($delete_url, 'page', 'page_confirm_delete');
			$temp_array['ADMIN_EDIT'] = cot_rc_link($edit_url, Cot::$L['Edit']);
			$temp_array['ADMIN_EDIT_URL'] = $edit_url;
			$temp_array['ADMIN_UNVALIDATE'] = $page_data['page_state'] == COT_PAGE_STATE_PENDING ?
				cot_rc_link($validate_confirm_url, Cot::$L['Validate'], 'class="confirmLink"') :
				cot_rc_link($unvalidate_confirm_url, Cot::$L['Putinvalidationqueue'], 'class="confirmLink"');
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

		if (cot_auth('page', 'any', 'W'))
		{
			$clone_url = cot_url('page', "m=add&c={$page_data['page_cat']}&clone={$page_data['page_id']}");
			$temp_array['ADMIN_CLONE'] = cot_rc_link($clone_url, $L['page_clone']);
			$temp_array['ADMIN_CLONE_URL'] = $clone_url;
		}

		// Extrafields
        if(!empty(Cot::$extrafields[Cot::$db->pages])) {
            foreach (Cot::$extrafields[Cot::$db->pages] as $exfld) {
				$tag = mb_strtoupper($exfld['field_name']);
                $exfld_title = cot_extrafield_title($exfld, 'page_');

				$temp_array[$tag.'_TITLE'] = $exfld_title;
                $temp_value = null;
                if (isset($page_data['page_'.$exfld['field_name']])) {
                    $temp_value = $page_data['page_'.$exfld['field_name']];
                }
				$temp_array[$tag] = cot_build_extrafields_data('page', $exfld, $temp_value, $page_data['page_parser']);
				$temp_array[$tag.'_VALUE'] = $temp_value;
			}
		}

		// Extra fields for structure
		if (isset(Cot::$extrafields[Cot::$db->structure])) {
			foreach (Cot::$extrafields[Cot::$db->structure] as $exfld) {
				$tag = mb_strtoupper($exfld['field_name']);
                $exfld_title = cot_extrafield_title($exfld, 'structure_');

				$temp_array['CAT_'.$tag.'_TITLE'] = $exfld_title;
                $temp_value = null;
                if (isset(Cot::$structure['page'][$page_data['page_cat']][$exfld['field_name']])) {
                    $temp_value = Cot::$structure['page'][$page_data['page_cat']][$exfld['field_name']];
                }
				$temp_array['CAT_'.$tag] = cot_build_extrafields_data('structure', $exfld, $temp_value);
				$temp_array['CAT_'.$tag.'_VALUE'] = $temp_value;
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
 * Possible values for category sorting order
 * @param bool $adminpart Call from admin part
 * @return array
 */
function cot_page_config_order($adminpart = false)
{
	global $cot_extrafields, $L, $db_pages;

	$options_sort = array(
		'id' => $L['Id'],
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
		'filecount' => $L['adm_filecount'],
		'count' => $L['Count'],
		'updated' => $L['Updated'],
		'cat' => $L['Category']
	);

	foreach($cot_extrafields[$db_pages] as $exfld)
	{
		$options_sort[$exfld['field_name']] = isset($L['page_'.$exfld['field_name'].'_title']) ? $L['page_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
	}

	if ($adminpart || version_compare('0.9.19', Cot::$cfg['version']) < 1)
	{
		return $options_sort;
	}
	else
	{
		// old style trick, will be removed in next versions
		$L['cfg_order_params'] = array_values($options_sort);
		return array_keys($options_sort);
	}
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
 * Returns page category counter
 * Used in Admin/Structure/Resync All
 *
 * @param string $category Category code
 * @return int
 */
function cot_page_sync($category)
{
    if (empty($category)) {
        return 0;
    }

    return (int) Cot::$db->query(
        'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(Cot::$db->pages) .
        ' WHERE page_cat=?',
        $category
    )->fetchColumn();
}

/**
 * Recalculate and update structure counters
 * @param string $category Category code
 * @return void
 */
function cot_page_updateStructureCounters($category)
{
    if (empty($category) || empty(Cot::$structure['page'][$category])) {
        return;
    }

    $count = cot_page_sync($category);

    Cot::$db->query('UPDATE ' . Cot::$db->quoteTableName(Cot::$db->structure) .
        ' SET structure_count = ' . $count .
        " WHERE structure_area='page' AND structure_code = :category", ['category' => $category]);

    if (Cot::$cache) {
        Cot::$cache->db->remove('structure', 'system');
    }
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
 * Url address of the page
 *
 * @param array $data Page data as array
 * @param array $params Additional URL Parameters
 * @param string $tail URL postfix, e.g. anchor
 * @param bool $htmlspecialcharsBypass If TRUE, will not convert & to &amp; and so on.
 * @param bool $ignoreAppendix If TRUE, $cot_url_appendix will be ignored for this URL
 * @return string Valid HTTP URL
 */
function cot_page_url($data, $params = [], $tail = '', $htmlspecialcharsBypass = false, $ignoreAppendix = false)
{
    $urlParams = ['c' => $data['page_cat']];
    if (!empty($data['page_alias'])) {
        $urlParams['al'] = $data['page_alias'];
    } elseif (!empty($data['page_id'])) {
        $id = (int) $data['page_id'];
        if ($id <= 0) {
            return '';
        }
        $urlParams['id'] = $id;
    } else {
        return '';
    }

    if (!empty($params)) {
        $urlParams = array_merge($urlParams, $params);
    }

    return cot_url('page', $urlParams, $tail, $htmlspecialcharsBypass, $ignoreAppendix);
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
	$rpage['page_size']     = (int)cot_import('rpagesize', $source, 'INT');
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
		$rpage['page_ownerid'] = Cot::$usr['id'];
	}

	$parser_list = cot_get_parsers();

	if (empty($rpage['page_parser']) || !in_array($rpage['page_parser'], $parser_list) || $rpage['page_parser'] != 'none' &&
        !cot_auth('plug', $rpage['page_parser'], 'W'))
	{
		$rpage['page_parser'] = isset(Cot::$sys['parser']) ? Cot::$sys['parser'] : Cot::$cfg['page']['parser'];
	}

	// Extra fields
    if (!empty(Cot::$extrafields[Cot::$db->pages])) {
        foreach (Cot::$extrafields[Cot::$db->pages] as $exfld) {
            $value = isset($rpage['page_' . $exfld['field_name']]) ? $rpage['page_' . $exfld['field_name']] : null ;
            $rpage['page_' . $exfld['field_name']] = cot_import_extrafields('rpage' . $exfld['field_name'], $exfld,
                $source, $value, 'page_');
        }
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
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

	if (cot_error_found()) {
		return false;
	}

	if (count($auth) == 0) {
		$auth = cot_page_auth($rpage['page_cat']);
	}

	if (!empty($rpage['page_alias'])) {
		$page_count = \Cot::$db->query(
            'SELECT COUNT(*) FROM ' . \Cot::$db->pages . ' WHERE page_alias = ?',
            $rpage['page_alias']
        )->fetchColumn();
		if ($page_count > 0) {
			$rpage['page_alias'] = $rpage['page_alias'] . rand(1000, 9999);
		}
	}

	if (
        $rpage['page_state'] == COT_PAGE_STATE_PUBLISHED
        && !($auth['isadmin'] && \Cot::$cfg['page']['autovalidate'])
    ) {
        $rpage['page_state'] = COT_PAGE_STATE_PENDING;
	}

	/* === Hook === */
	foreach (cot_getextplugins('page.add.add.query') as $pl) {
		include $pl;
	}
	/* ===== */

	if (\Cot::$db->insert(\Cot::$db->pages, $rpage)) {
		$id = \Cot::$db->lastInsertId();
		cot_extrafield_movefiles();
        cot_page_updateStructureCounters($rpage['page_cat']);
	} else {
		$id = false;
	}

	/* === Hook === */
	foreach (cot_getextplugins('page.add.add.done') as $pl) {
		include $pl;
	}
	/* ===== */

	if ($rpage['page_state'] == COT_PAGE_STATE_PUBLISHED && \Cot::$cache) {
		if (\Cot::$cfg['cache_page']) {
            \Cot::$cache->static->clearByUri(cot_page_url($rpage));
            \Cot::$cache->static->clearByUri(cot_url('page', ['c' => $rpage['page_cat']]));
		}
		if (\Cot::$cfg['cache_index']) {
            \Cot::$cache->static->clear('index');
		}
	}

	cot_shield_update(30, "r page");
	cot_log("Add page #".$id, 'page', 'add', 'done');

	return $id;
}

/**
 * Removes a page from the CMS.
 * @param  int     $id    Page ID
 * @param  array   $rpage Page data
 * @return boolean        TRUE on success, FALSE on error
 */
function cot_page_delete($id, $rpage = [])
{
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

	if (!is_numeric($id) || $id <= 0) {
		return false;
	}
	$id = (int) $id;
	if (empty($rpage)) {
		$rpage = Cot::$db->query('SELECT * FROM ' . Cot::$db->pages . " WHERE page_id = ?", $id)->fetch();
		if (!$rpage) {
			return false;
		}
	}

	foreach (Cot::$extrafields[Cot::$db->pages] as $exfld) {
		cot_extrafield_unlinkfiles($rpage['page_' . $exfld['field_name']], $exfld);
	}

    \Cot::$db->delete(Cot::$db->pages, 'page_id = ?', $id);
	cot_log("Deleted page #" . $id, 'page', 'delete', 'done');

    cot_page_updateStructureCounters($rpage['page_cat']);

	/* === Hook === */
	foreach (cot_getextplugins('page.edit.delete.done') as $pl) {
		include $pl;
	}
	/* ===== */

	if (\Cot::$cache) {
		if (\Cot::$cfg['cache_page']) {
            \Cot::$cache->static->clearByUri(cot_page_url($rpage));
            \Cot::$cache->static->clearByUri(cot_url('page', ['c' => $rpage['page_cat']]));
		}
		if (\Cot::$cfg['cache_index']) {
            \Cot::$cache->static->clear('index');
		}
	}

	return true;
}

/**
 * Updates a page in the CMS.
 * @param int $id Page ID
 * @param array $rpage Page data
 * @param array $auth  Permissions array
 * @return bool TRUE on success, FALSE on error
 */
function cot_page_update($id, &$rpage, $auth = [])
{
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

    if (cot_error_found()) {
		return false;
	}

	if (count($auth) == 0) {
		$auth = cot_page_auth($rpage['page_cat']);
	}

	if (!empty($rpage['page_alias'])) {
		$page_count = Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->pages .
            ' WHERE page_alias = ? AND page_id != ?', array($rpage['page_alias'], $id))->fetchColumn();
		if ($page_count > 0) {
			$rpage['page_alias'] = $rpage['page_alias'] . rand(1000, 9999);
		}
	}

	$row_page = \Cot::$db->query('SELECT * FROM ' . \Cot::$db->pages . ' WHERE page_id = ?', $id)->fetch();

    if (
        $rpage['page_state'] == COT_PAGE_STATE_PUBLISHED
        && !($auth['isadmin'] && \Cot::$cfg['page']['autovalidate'])
    ) {
        $rpage['page_state'] = COT_PAGE_STATE_PENDING;
    }

    \Cot::$cache && \Cot::$cache->db->remove('structure', 'system');

	if (!\Cot::$db->update(\Cot::$db->pages, $rpage, 'page_id = ?', $id)) {
		return false;
	}
	cot_log("Edited page #" . $id, 'page', 'edit', 'done');

	cot_extrafield_movefiles();

	/* === Hook === */
	foreach (cot_getextplugins('page.edit.update.done') as $pl) {
		include $pl;
	}
	/* ===== */

	if (
        ($rpage['page_state'] == COT_PAGE_STATE_PUBLISHED  || $rpage['page_cat'] != $row_page['page_cat'])
        && Cot::$cache
    ) {
		if (\Cot::$cfg['cache_page']) {
            \Cot::$cache->static->clearByUri(cot_page_url($rpage));
            \Cot::$cache->static->clearByUri(cot_url('page', ['c' => $rpage['page_cat']]));

			if ($rpage['page_cat'] != $row_page['page_cat']) {
                \Cot::$cache->static->clearByUri(cot_page_url($row_page));
                \Cot::$cache->static->clearByUri(cot_url('page', ['c' => $row_page['page_cat']]));
			}
		}
		if (\Cot::$cfg['cache_index']) {
            \Cot::$cache->static->clear('index');
		}
	}

	return true;
}

/**
 * Generates page list widget
 * @param  mixed   $categories       Custom parent categories code
 * @param  integer $count            Number of items to show. 0 - all items
 * @param  string  $template         Path for template file
 * @param  string  $order            Sorting order (SQL)
 * @param  string  $condition        Custom selection filter (SQL)
 * @param  mixed   $active_only	     Custom parent category code
 * @param  boolean $use_subcat       Include subcategories TRUE/FALSE
 * @param  boolean $exclude_current  Exclude the current page from the rowset for pages.
 * @param  string  $blacklist        Category black list, semicolon separated
 * @param  string  $pagination       Pagination symbol
 * @param  integer $cache_ttl        Cache lifetime in seconds, 0 disables cache
 * @return string                    Parsed HTML
 */
function cot_page_enum($categories = '', $count = 0, $template = '', $order = '', $condition = '',
	$active_only = true, $use_subcat = true, $exclude_current = false, $blacklist = '', $pagination = '', $cache_ttl=null)
{
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

	global $db, $db_pages, $db_users, $structure, $cfg, $sys, $lang, $cache;

	// Compile lists
	if(!is_array($blacklist))
	{
		$blacklist = str_replace(' ', '', $blacklist);
		$blacklist = (!empty($blacklist)) ? explode(',', $blacklist) : array();
	}

	// Get the cats
	if(!empty($categories))
	{
		if(!is_array($categories))
		{
			$categories = str_replace(' ', '', $categories);
			$categories = explode(',', $categories);
		}
		$categories = array_unique($categories);
		if ($use_subcat)
		{

			$total_categories = array();
			foreach ($categories as $cat)
			{
				$cats = cot_structure_children('page', $cat, $use_subcat);
				$total_categories = array_merge($total_categories, $cats);
			}
			$categories = array_unique($total_categories);
		}
		$categories = (count($blacklist) > 0 ) ? array_diff($categories, $blacklist) : $categories;
		$where['cat'] = "page_cat IN ('" . implode("','", $categories) . "')";


	}
	elseif (count($blacklist))
	{
		$where['cat_black'] = "page_cat NOT IN ('" . implode("','", $blacklist) . "')";
	}

	$where['condition'] = $condition;

	if ($exclude_current && defined('COT_PAGES') && !defined('COT_LIST'))
	{
		global $id;
        $tmp = 0;
        if(!empty($id)) $tmp = (int)$id;
		if(!empty($tmp)) $where['page_id'] = "page_id != $tmp";
	}
	if ($active_only)
	{
		$where['state'] = "page_state = 0";
		$where['date'] = "page_begin <= {$sys['now']} AND (page_expire = 0 OR page_expire > {$sys['now']})";
	}

	// Get pagination number if necessary
	if (!empty($pagination))
	{
		list($pg, $d, $durl) = cot_import_pagenav($pagination, $count);
	}
	else
	{
		$d = 0;
	}

	// Display the items
	$mskin = (!empty($template) && file_exists($template)) ?
        $template : cot_tplfile(array('page', 'enum', $template), 'module');

    $cns_join_tables = '';
	$cns_join_columns = '';

	/* === Hook === */
	foreach (cot_getextplugins('page.enum.query') as $pl)
	{
		include $pl;
	}
	/* ===== */

    // Todo move it to comments plugin
	if (cot_plugin_active('comments'))
	{
		global $db_com;
		require_once cot_incfile('comments', 'plug');
		$cns_join_columns .= ", (SELECT COUNT(*) FROM `$db_com` WHERE com_area = 'page' AND com_code = p.page_id) AS com_count";
	}
	$sql_order = empty($order) ? 'ORDER BY page_date DESC' : "ORDER BY $order";
	$sql_limit = ($count > 0) ? "LIMIT $d, $count" : '';
	$where = array_filter($where);
	$where = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';

	$sql_total = "SELECT COUNT(*) FROM $db_pages AS p $cns_join_tables $where";
	$sql_query = "SELECT p.*, u.* $cns_join_columns FROM $db_pages AS p LEFT JOIN $db_users AS u ON p.page_ownerid = u.user_id
			$cns_join_tables $where $sql_order $sql_limit";

	$t = new XTemplate($mskin);

	isset($md5hash) || $md5hash = 'page_enum_'.md5(str_replace($sys['now'], '_time_', $mskin.$lang.$sql_query));

	if ($cache && (int)$cache_ttl > 0)
	{
		$page_query_html = $cache->disk->get($md5hash, 'page', (int)$cache_ttl);

		if(!empty($page_query_html))
		{
			return $page_query_html;
		}
	}

	$totalitems = $db->query($sql_total)->fetchColumn();
	$sql = $db->query($sql_query);

	$sql_rowset = $sql->fetchAll();
	$jj = 0;
	foreach ($sql_rowset as $pag)
	{
		$jj++;
		$t->assign(cot_generate_pagetags($pag, 'PAGE_ROW_', $cfg['page']['cat___default']['truncatetext']));

		$t->assign(array(
			'PAGE_ROW_NUM' => $jj,
			'PAGE_ROW_ODDEVEN' => cot_build_oddeven($jj),
			'PAGE_ROW_RAW' => $pag
		));

		$t->assign(cot_generate_usertags($pag, 'PAGE_ROW_OWNER_'));

		/* === Hook === */
		foreach (cot_getextplugins('page.enum.loop') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if (cot_plugin_active('comments'))
		{
			$rowe_urlp = empty($pag['page_alias']) ? array('c' => $pag['page_cat'], 'id' => $pag['page_id']) : array('c' => $pag['page_cat'], 'al' => $pag['page_alias']);
			$t->assign(array(
				'PAGE_ROW_COMMENTS' => cot_comments_link('page', $rowe_urlp, 'page', $pag['page_id'], $pag['page_cat'], $pag),
				'PAGE_ROW_COMMENTS_COUNT' => cot_comments_count('page', $pag['page_id'], $pag)
			));
		}

		$t->parse("MAIN.PAGE_ROW");
	}

	// Render pagination
	$url_params = $_GET;
    if(isset($url_params['rwr'])) unset($url_params['rwr']);
	$url_area = 'index';
	$module_name = cot_import('e', 'G', 'ALP');
	if(cot_module_active($module_name))
	{
		$url_area = $url_params['e'];
		unset($url_params['e']);
	}
    elseif (cot_plugin_active($module_name))
	{
		$url_area = 'plug';
	}
	unset($url_params[$pagination]);

    $pagenav = array(
        'main' => null,
        'prev' => null,
        'next' => null,
        'first' => null,
        'last' => null,
        'current' => 1,
        'total' => 1,
    );

	if(!empty($pagination)) {
		$pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $count, $pagination);
	}

	$t->assign(array(
		'PAGE_TOP_PAGINATION' => $pagenav['main'],
		'PAGE_TOP_PAGEPREV' => $pagenav['prev'],
		'PAGE_TOP_PAGENEXT' => $pagenav['next'],
		'PAGE_TOP_FIRST' => isset($pagenav['first']) ? $pagenav['first'] : '',
		'PAGE_TOP_LAST' => $pagenav['last'],
		'PAGE_TOP_CURRENTPAGE' => $pagenav['current'],
		'PAGE_TOP_TOTALLINES' => $totalitems,
		'PAGE_TOP_MAXPERPAGE' => $count,
		'PAGE_TOP_TOTALPAGES' => $pagenav['total']
	));

	/* === Hook === */
	foreach (cot_getextplugins('page.enum.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse("MAIN");
	$page_query_html = $t->text("MAIN");

	if ($cache && (int) $cache_ttl > 0)
	{
		$cache->disk->store($md5hash, $page_query_html, 'page');
	}
	return $page_query_html;
}

