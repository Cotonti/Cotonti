<?php
/**
 * Comments system for Cotonti
 * Dto Repository
 * @see ItemDto
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\inc;

use Cot;
use cot\dto\ItemDto;
use cot\modules\page\inc\PageDictionary;
use cot\modules\polls\inc\PollsDictionary;
use cot\repositories\BaseRepository;
use cot\services\ItemService;
use cot\structure\StructureDictionary;

class CommentsDtoRepository extends BaseRepository
{
    /**
     * @var mixed
     */
    private static $cacheById;

    public static function getTableName(): string
    {
        if (empty(Cot::$db->com)) {
            Cot::$db->registerTable('com');
        }
        return Cot::$db->com;
    }

    /**
     * Fetches comment from DB
     * @param int $id Comment ID
     * @param bool $useCache Use one time session cache
     * @return ?array
     */
    public function getById(int $id, bool $withFullItemData = false, bool $useCache = true): ?ItemDto
    {
        if ($id < 1) {
            return null;
        }

        if ($useCache && isset(self::$cacheById[$id])) {
            return self::$cacheById[$id] !== false ? self::$cacheById[$id] : null;
        }

        $condition = 'com_id = :commentId';
        $params = ['commentId' => $id];

        $results = $this->getDtoByCondition($condition, $params, null, null, null, $withFullItemData);
        $result = !empty($results) ? reset($results) : null;

        self::$cacheById[$id] = !empty($result) ? $result : false;

        return $result;
    }

    /**
     * @return list<ItemDto>
     * @todo hooks
     */
    public function getDtoByCondition(
        $condition,
        array $params = [],
        $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        bool $withFullItemData = false
    ): array {
        // for include files
        global $L, $R, $Ls, $db_com;

        $comments = CommentsRepository::getInstance()->getByCondition(
            $condition,
            $params,
            $orderBy,
            $limit,
            $offset
        );

        if (empty($comments)) {
            return [];
        }

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

        $commentTypes[StructureDictionary::SOURCE_CATEGORY] = Cot::$L['comments_commentOnCategory'];
        if (class_exists(PageDictionary::class)) {
            $commentTypes[PageDictionary::SOURCE_PAGE] = Cot::$L['comments_commentOnPage'];
        }
        if (class_exists(PollsDictionary::class)) {
            $commentTypes[PollsDictionary::SOURCE_POLL] = Cot::$L['comments_commentOnPoll'];
        }

        /* === Hook === */
        foreach (cot_getextplugins('comments.getItemDtoList.main') as $pl) {
            include $pl;
        }
        /* ===== */

        $result = [];

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
                '',
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
        foreach (cot_getextplugins('comments.getItemDtoList.done') as $pl) {
            include $pl;
        }
        /* ===== */

        return $result;
    }

    /**
     * @return list<ItemDto>
     */
    public function getByCondition(
        $condition,
        array $params = [],
        $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return $this->getDtoByCondition($condition, $params, $orderBy, $limit, $offset);
    }
}