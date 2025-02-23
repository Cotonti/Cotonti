<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.item.getItems
[END_COT_EXT]
==================== */

declare(strict_types=1);

use cot\dto\ItemDto;

/**
 * I18n for pages
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var bool $withFullItemData
 * @var list<int> $pageIds
 * @var list<ItemDto> $result
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n_read, $i18n_locale, $i18n_notmain;

$i18nEnabled = !empty($i18n_read);

if (!$i18nEnabled || !$i18n_notmain) {
    return;
}

$i18nTable = Cot::$db->quoteTableName(Cot::$db->i18n_pages);

$sqlSelect = $withFullItemData
    ? "{$i18nTable}.*"
    : "{$i18nTable}.ipage_title, {$i18nTable}.ipage_desc";

$query = "SELECT $sqlSelect FROM $i18nTable "
    . "WHERE {$i18nTable}.ipage_id IN (" . implode(',', $pageIds) . ") "
    . "AND {$i18nTable}.ipage_locale = '$i18n_locale'";

$data = Cot::$db->query($query)->fetchAll();
$i18nData = [];
foreach ($data as $row) {
    $i18nData[$row['ipage_id']] = $row;
}
unset($data);

foreach ($result as $row) {
    if (empty($i18nData[$row->sourceId])) {
        continue;
    }
    if (!empty($i18nData[$row->sourceId]['ipage_title'])) {
        $row->title = $i18nData[$row->sourceId]['ipage_title'];
    }
    if (!empty($i18nData[$row->sourceId]['ipage_desc'])) {
        $row->description = $i18nData[$row->sourceId]['ipage_desc'];
    }

    if ($withFullItemData) {
        $row->data = array_merge($row->data, $i18nData[$row->sourceId]);
    }

    $catI18n = cot_i18n_get_cat($row->categoryCode, $i18n_locale);
    if ($catI18n) {
        $row->categoryTitle = $catI18n['title'];
    }
}
