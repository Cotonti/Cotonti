<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=search.page.query
[END_COT_EXT]
==================== */

/**
 * Modifies search query so that it searches in translations too
 *
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if (is_array($i18n_structure) && count($i18n_structure) > 0
	&& ($rsearch['pag']['sub'][0] == 'all' || count($i18n_search_cats) > 0))
{
	// Add ipage_id IS NULL and rebuild the 1st part
	$where_and['i18n'] = 'ipage_id IS NULL';
	$where = implode(' AND ', $where_and);

	// Build the 2nd part for ipage_id IS NOT NULL
	$where_and['i18n'] = 'ipage_id IS NOT NULL';

	if ($rsearch['pag']['sub'][0] == 'all' || count($i18n_search_cats) == 0)
	{
		$where_and['cat'] = "page_cat IN ('".implode("','", $pag_catauth)."')";
	}
	else
	{
		$where_subcats = array();
		foreach ($i18n_search_cats as $lc => $cats)
		{
			$cats_auth = array_intersect($cats, $pag_catauth);
			if (count($cats_auth) > 0)
			{
				$where_subcats[] = "page_cat IN ('".implode("','", $cats_auth)."') AND ipage_locale = '$lc'";
			}
		}
		$where_and['cat'] = implode(' OR ', $where_subcats);
	}

	if ($rsearch['set']['limit'] > 0)
	{
		$where_and['date2'] = "ipage_date >= ".$rsearch['set']['from']." AND ipage_date <= ".$rsearch['set']['to'];
	}

	unset($where_or['title'], $where_or['desc'], $where_or['text']);

	$where_or['title'] = $rsearch['pag']['title'] ? "ipage_title LIKE '".$db->prep($sqlsearch)."'" : '';
	$where_or['desc'] = $rsearch['pag']['desc'] ? "ipage_desc LIKE '".$db->prep($sqlsearch)."'" : '';
	$where_or['text'] = $rsearch['pag']['text'] ? "ipage_text LIKE '".$db->prep($sqlsearch)."'" : '';

	$where_or = array_diff($where_or, array(''));
	count($where_or) || $where_or['title'] = "ipage_title LIKE '".$db->prep($sqlsearch)."'";
	$where_and['or'] = '('.implode(' OR ', $where_or).')';
	$where_and = array_diff($where_and, array(''));
	$where2 = implode(' AND ', $where_and);

	// Append 2nd to the 1st with OR
	$where .= ' OR ' . $where2;

	// Join the translation table
	$search_join_columns .= ', i18n.*';
	$search_join_condition .= "LEFT JOIN $db_i18n_pages AS i18n ON p.page_id = i18n.ipage_id";
}

?>
