<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=itemService.getItems
[END_COT_EXT]
==================== */

declare(strict_types = 1);

use cot\dto\ItemDto;
use cot\extensions\ExtensionsDictionary;
use cot\modules\page\inc\PageDictionary;
use cot\modules\page\inc\PageRepository;

defined('COT_CODE') or die('Wrong URL');

/**
 * @var string $source
 * @var list<int|numeric-string> $sourceIds
 * @var bool $withFullItemData
 * @var list<ItemDto> $result
 */

if ($source !== PageDictionary::SOURCE_PAGE || empty($sourceIds)) {
    return;
}

global $L, $R, $Ls, $i18n_read, $i18n_locale, $i18n_notmain;

require_once cot_incfile('page', ExtensionsDictionary::TYPE_MODULE);

$pageIds = [];
foreach ($sourceIds as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $pageIds[] = $id;
    }
}
$pageIds = array_unique($pageIds);

$condition = 'page_id IN (' . implode(',', $pageIds) . ')';
$pages = PageRepository::getInstance()->getByCondition($condition);

foreach ($pages as $row) {
    $url = cot_page_url($row);
    if (!cot_url_check($url)) {
        $url = COT_ABSOLUTE_URL . $url;
    }

    $dto = new ItemDto(
        PageDictionary::SOURCE_PAGE,
        $row['page_id'],
        Cot::$L['Page'],
        $row['page_title'],
        $row['page_desc'],
        $url,
        (int) $row['page_ownerid']
    );

    if ($withFullItemData) {
        $dto->data = $row;
    }
    $dto->categoryCode = $row['page_cat'];
    $dto->categoryTitle = 'Unknown';
    if (isset(Cot::$structure['page'][$row['page_cat']])) {
        $dto->categoryUrl = cot_url('page', ['c' => $row['page_cat']]);
        $dto->categoryTitle = Cot::$structure['page'][$row['page_cat']]['title'];
    }

    $result[$dto->sourceId] = $dto;
}

$i18nEnabled = !empty($i18n_read);

// @todo move to i18n plugin
if ($i18nEnabled && $i18n_notmain) {
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
}

/* === Hook === */
foreach (cot_getextplugins('page.itemService.getItems') as $pl) {
    include $pl;
}
/* ===== */
