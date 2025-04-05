<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=item.getItems
[END_COT_EXT]
==================== */

declare(strict_types = 1);

use cot\dto\ItemDto;
use cot\extensions\ExtensionsDictionary;
use cot\modules\page\inc\PageDictionary;
use cot\modules\page\inc\PageRepository;

defined('COT_CODE') or die('Wrong URL');

/**
 * Page module

 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $source
 * @var list<int|numeric-string> $sourceIds
 * @var bool $withFullItemData
 * @var list<ItemDto> $result
 */

if ($source !== PageDictionary::SOURCE_PAGE || empty($sourceIds)) {
    return;
}

// for include file
global $L, $R, $Ls;

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
        'page',
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

    $result[$dto->id] = $dto;
}

/* === Hook === */
foreach (cot_getextplugins('page.item.getItems.done') as $pl) {
    include $pl;
}
/* ===== */
