<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.main
[END_COT_EXT]
==================== */

/**
 * Category preload and title setup
 *
 * @package i18n
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010-2011
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if ($i18n_enabled && $i18n_notmain)
{
	$cat_i18n = cot_i18n_get_cat($c, $i18n_locale);
	
	if ($cat_i18n)
	{
		$title_params = array(
			'TITLE' => $cat_i18n['title']
		);
		$out['desc'] = htmlspecialchars(strip_tags($cat_i18n['desc']));
		$out['subtitle'] = cot_title('title_list', $title_params);
		
		// Enable indexing
		$sys['noindex'] = falase;
	}
}

?>
