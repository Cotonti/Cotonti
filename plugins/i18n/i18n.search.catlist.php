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

if ($i18n_read && $i18n_notmain && !empty($i18n_structure) && is_array($i18n_structure) && count($i18n_structure) > 0) {
    // @see cot_load_structure();
    cot_load_structure();
    $separator = (Cot::$cfg['separator'] == strip_tags(Cot::$cfg['separator'])) ?
        ' ' . Cot::$cfg['separator'] . ' ' : ' / ';

    // Translate categories in the multiselect
	foreach ($i18n_structure as $cat => $row) {
		if (isset($pages_cat_list[$cat])) {
            $i18nCatPath = cot_i18n_build_catpath('page', $cat, $i18n_locale);
            $i18nTitlePath = [];
            if (!empty($i18nCatPath)) {
                foreach ($i18nCatPath as $row) {
                    $i18nTitlePath[] = $row['1'];
                }
            }

            if (!empty($i18nTitlePath)) {
                $pages_cat_list[$cat] = implode($separator, $i18nTitlePath);
            }
		}
	}
}
