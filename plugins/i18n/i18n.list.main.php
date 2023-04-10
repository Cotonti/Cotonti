<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.main
[END_COT_EXT]
==================== */

/**
 * Category preload and title setup
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($i18n_enabled && $i18n_notmain) {
	$cat_i18n = cot_i18n_get_cat($c, $i18n_locale);

	if ($cat_i18n) {
		Cot::$out['desc'] = htmlspecialchars(strip_tags($cat_i18n['desc']));
        Cot::$out['subtitle'] = $cat_i18n['title'];
	}
}
