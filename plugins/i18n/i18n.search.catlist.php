<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=search.page.catlist
[END_COT_EXT]
==================== */

/**
 * Inserts translated categories into search category list
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
	if (is_array($rs['pagsub']))
	{
		$subcnt = count($rs['pagsub']);
		$tmp = array();
		for ($i = 0; $i < $subcnt; $i++)
		{
			if (mb_strpos($rs['pagsub'][$i], ':') !== false)
			{
				list ($cat, $lc) = explode(':', $rs['pagsub'][$i]);
				$i18n_search_cats[$lc][] = $cat;
			}
			else
			{
				$tmp[] = $rs['pagsub'][$i];
			}
		}
		$rs['pagsub'] = $tmp;
	}
}
