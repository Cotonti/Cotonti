<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=search.page.catlist
[END_COT_EXT]
==================== */

/**
 * Inserts translated categories into search category list
 *
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if (is_array($i18n_structure) && count($i18n_structure) > 0)
{
	// Add translated categories into the multiselect
	foreach ($i18n_structure as $cat => $row)
	{
		if (isset($pages_cat_list[$cat]))
		{
			// Permissions to main category already checked
			foreach ($row as $lc => $x)
			{
				$pages_cat_list[$cat.':'.$lc] = cot_breadcrumbs(cot_i18n_build_catpath('page', $cat, $lc), false, true, true);
			}
		}
	}

	// Extract previously selected options, they are handled separately
	$i18n_search_cats = array();
	if (is_array($rsearch['pag']['sub']))
	{
		$subcnt = count($rsearch['pag']['sub']);
		$tmp = array();
		for ($i = 0; $i < $subcnt; $i++)
		{
			if (mb_strpos($rsearch['pag']['sub'][$i], ':') !== false)
			{
				list ($cat, $lc) = explode(':', $rsearch['pag']['sub'][$i]);
				$i18n_search_cats[$lc][] = $cat;
			}
			else
			{
				$tmp[] = $rsearch['pag']['sub'][$i];
			}
		}
		$rsearch['pag']['sub'] = $tmp;
	}
}

?>
