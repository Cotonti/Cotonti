<?php

/**
 * Index News functions
 *
 * @package Index News
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Returns the list of page categories
 *
 * @global array $structure
 * @return array
 */
function cot_pagecat_list()
{
	global $structure, $L;
	$extension = 'page';
	$structure[$extension] = (is_array($structure[$extension])) ? $structure[$extension] : array();

	$result_array = array();
	foreach ($structure[$extension] as $i => $x)
	{
		if ($i!='all')
		{
			$result_array[$i] = $x['tpath'];
		}
	}
	$L['cfg_category_params'] = array_values($result_array);

	return(array_keys($result_array));
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
			
			$total_categogies = array();
			foreach ($categories as $cat)
			{
				$cats = cot_structure_children('page', $cat, $use_subcat);
				$total_categogies = array_merge($total_categogies, $cats);
			}
			$categories = array_unique($total_categogies);
		}
		$categories = (count($blacklist) > 0 ) ? array_diff($categories, $blacklist) : $categories;
		$where['cat'] = "page_cat IN ('" . implode("','", $cats) . "')";
		
		
	}
	elseif (count($blacklist)) 
	{
		$where['cat_black'] = "page_cat NOT IN ('" . implode("','", $blacklist) . "')";
	}

	$where['condition'] = $condition;

	if ($exclude_current && defined('COT_PAGES') && !defined('COT_LIST'))
	{
		global $id;
		$where['page_id'] = "page_id != $id";
	}
	if ($active_only)
	{
		$where['state'] = "page_state=0";
		$where['date'] = "page_begin <= {$sys['now']} AND (page_expire = 0 OR page_expire > {$sys['now']})";
	}

	// Get pagination number if necessary
	if(!empty($pagination))
	{
		list($pg, $d, $durl) = cot_import_pagenav($pagination, $count);
	}
	else
	{
		$d = 0;
	}

	// Display the items
	$mskin = file_exists($template) ? $template : cot_tplfile(array('page', 'enum', $template), 'module');

	/* === Hook === */
	foreach (cot_getextplugins('page.enum.query') as $pl)
	{
		include $pl;
	}
	/* ===== */
	
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
		$t->assign(cot_generate_pagetags($pag, 'PAGE_ROW_'));

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
	$url_area = 'index';
	$module_name = cot_import('e', 'G', 'ALP');
	if(cot_module_active($module_name))
	{
		$url_area = $url_params['e'];
		unset($url_params['e']);
	}
	if(cot_plugin_active($module_name))
	{
		$url_area = 'plug';
	}	
	unset($url_params[$pagination]);
	if(!empty($pagination))
	{
		$pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $count, $pagination);
	}

	$t->assign(array(
		'PAGE_TOP_PAGINATION' => $pagenav['main'],
		'PAGE_TOP_PAGEPREV' => $pagenav['prev'],
		'PAGE_TOP_PAGENEXT' => $pagenav['next'],
		'PAGE_TOP_FIRST' => $pagenav['first'],
		'PAGE_TOP_LAST' => $pagenav['last'],
		'PAGE_TOP_CURRENTPAGE' => $pagenav['current'],
		'PAGE_TOP_TOTALLINES' => $totalitems,
		'PAGE_TOP_MAXPERPAGE' => $count,
		'PAGE_TOP_TOTALPAGES' => $pagenav['total']
	));

	/* === Hook === */
	foreach (cot_getextplugins('pagelist.tags') as $pl)
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
