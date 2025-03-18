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
use cot\modules\polls\inc\PollsDictionary;
use cot\plugins\comments\inc\CommentsDictionary;
use cot\plugins\comments\inc\CommentsRepository;
use cot\services\ItemService;

defined('COT_CODE') or die('Wrong URL');

/**
 * Comments system for Cotonti
 * Get items
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $source
 * @var list<int|numeric-string> $sourceIds
 * @var bool $withFullItemData
 * @var list<ItemDto> $result
 */

if ($source !== CommentsDictionary::SOURCE_COMMENT || empty($sourceIds)) {
    return;
}

// for include file
global $L, $R, $Ls, $db_com;

require_once cot_incfile('comments', ExtensionsDictionary::TYPE_PLUGIN);

$commentsIds = [];
foreach ($sourceIds as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $commentsIds[] = $id;
    }
}
$commentsIds = array_unique($commentsIds);

$condition = 'com_id IN (' . implode(',', $commentsIds) . ')';
$comments = CommentsRepository::getInstance()->getByCondition($condition);

$dataForQuery = [];
foreach ($comments as $comment) {
    // Impossible case, but anyway
    if ($comment['com_area'] === CommentsDictionary::SOURCE_COMMENT) {
        continue;
    }
    if (
        empty($dataForQuery[$comment['com_area']]) ||
        !in_array($comment['com_code'], $dataForQuery[$comment['com_area']], true)
    ) {
        $dataForQuery[$comment['com_area']][] = $comment['com_code'];
    }
}

$itemService = ItemService::getInstance();

$relatedItems = [];
foreach ($dataForQuery as $source => $ids) {
    $relatedItems[$source] = $itemService->getItems($source, $ids);
}

$commentTypes = [];

if (class_exists(PageDictionary::class)) {
    $commentTypes[PageDictionary::SOURCE_PAGE] = Cot::$L['comments_commentOnPage'];
}
if (class_exists(PollsDictionary::class)) {
    $commentTypes[PollsDictionary::SOURCE_POLL] = Cot::$L['comments_commentOnPoll'];
}

/* === Hook === */
foreach (cot_getextplugins('comments.item.getItems.main') as $pl) {
    include $pl;
}
/* ===== */

foreach ($comments as $comment) {
    $item = $relatedItems[$comment['com_area']][$comment['com_code']] ?? null;
    $titlePrefix = $commentTypes[$comment['com_area']] ?? Cot::$L['comments_commentOn'];
    $title = $item ? $item->title : $comment['com_area'] . ' #' . $comment['com_code'];
    $dto = new ItemDto(
        CommentsDictionary::SOURCE_COMMENT,
        $comment['com_id'],
        'comments',
        Cot::$L['comments_comment'],
        "$titlePrefix \"{$title}\"",
        $item ? $item->description : '',
        $item ? $item->url : '',
        (int) $comment['com_authorid']
    );

    if ($withFullItemData) {
        $dto->data = $comment;
    }

    if ($dto->url !== '') {
        $dto->url .= '#com' . $comment['com_id'];
        $dto->titleHtml = $titlePrefix . ': "' . cot_rc_link($dto->url, $title) . '"';
    }
    if ($item) {
        $dto->categoryCode = $item->categoryCode;
        $dto->categoryTitle = $item->categoryTitle;
        $dto->categoryUrl = $item->categoryUrl;
    }

    $result[$comment['com_id']] = $dto;
}

/* === Hook === */
foreach (cot_getextplugins('comments.item.getItems.done') as $pl) {
    include $pl;
}
/* ===== */
