<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.enum.query
[END_COT_EXT]
==================== */

/**
 * Load translations in cot_page_enum function
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n_read, $i18n_locale;

if (isset($i18n_notmain) && $i18n_notmain && $i18n_read) {
    $cns_join_columns .= ',i18n.*';
    $cns_join_tables .= ' LEFT JOIN ' . Cot::$db->i18n_pages .
        " AS i18n ON i18n.ipage_id = p.page_id AND i18n.ipage_locale = " . Cot::$db->quote($i18n_locale) .
        ' AND i18n.ipage_id IS NOT NULL';
}
