<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=search.page.query
[END_COT_EXT]
==================== */

/**
 * Modifies search query so that it searches in translations too
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($i18n_notmain && $i18n_read) {
    if ($rs['pagtitle']) {
        $where_or['title_i18n'] = 'i18n.ipage_title LIKE ' . Cot::$db->quote($sqlsearch);
    }

    if ($rs['pagdesc']) {
        $where_or['desc_i18n'] = 'i18n.ipage_desc LIKE ' . Cot::$db->quote($sqlsearch);
    }

    if ($rs['pagtext']) {
        $where_or['text_i18n'] = 'i18n.ipage_text LIKE ' . Cot::$db->quote($sqlsearch);
    }

    // Join the translation table
    $search_join_columns .= ', i18n.*';
    $search_join_condition .= "\nLEFT JOIN " . Cot::$db->i18n_pages .
        " AS i18n ON p.page_id = i18n.ipage_id AND i18n.ipage_locale = " .
        Cot::$db->quote($i18n_locale) . ' AND i18n.ipage_id IS NOT NULL';
}
