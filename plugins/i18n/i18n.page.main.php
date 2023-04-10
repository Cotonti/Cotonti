<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.main
Order=5
[END_COT_EXT]
==================== */

/**
 * I18n for pages: redefines page body and title
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$i18n_enabled = $i18n_read && cot_i18n_enabled($pag['page_cat']);

if ($i18n_enabled && $i18n_notmain) {
	$pag_i18n = cot_i18n_get_page($id, $i18n_locale);
	$cat_i18n = cot_i18n_get_cat($pag['page_cat'], $i18n_locale);
	if (!$cat_i18n) {
		$cat_i18n = &Cot::$structure['page'][$pag['page_cat']];
	}

	if ($pag_i18n) {
		// Override <title>, subtitle and desc
		$title_params = array(
			'TITLE' => $pag_i18n['ipage_title'],
			'CATEGORY' => $cat_i18n['title']
		);
        Cot::$out['subtitle'] = cot_title(Cot::$cfg['page']['title_page'], $title_params);
        Cot::$out['desc'] = htmlspecialchars(strip_tags($pag_i18n['ipage_desc']));

		// Merge with page data
		$pag = array_merge($pag, $pag_i18n);
	}
}
