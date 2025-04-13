<?php
/**
 * Comments system for Cotonti
 * Control Service
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\inc;

use Cot;
use cot\services\ItemService;
use cot\traits\GetInstanceTrait;
use Exception;
use Throwable;

defined('COT_CODE') or die('Wrong URL.');

class CommentsControlService
{
    use GetInstanceTrait;

    /**
     * @param ?int $id
     * @param array $data
     * @param ?string $ciExtensionCode Commented item extension code (To clear static cache)
     * @param ?array $ciUrlParams Commented item url params (To clear static cache)
     * @param ?string $ciUrl Commented item url (To clear static cache)
     *
     * You can use $ciExtensionCode and $ciUrlParams, or instead of these parameters, $ciUrl.
     *
     * @return int|false CommentID
     */
    public function save(
        ?int $id,
        array $data,
        ?string $ciExtensionCode = null,
        ?array $ciUrlParams = null,
        ?string $ciUrl = null
    ) {
        $isNew = $id === null || $id < 1;

        unset($data['com_id']);

        /* == Hook == */
        foreach (cot_getextplugins('comment.save.first') as $pl) {
            include $pl;
        }
        /* ===== */

        if ($isNew) {
            if ($data['com_authorid'] === null || $data['com_authorid'] === '') {
                $data['com_authorid'] = Cot::$usr['id'];
            }
            if (empty($data['com_authorip'])) {
                $data['com_authorip'] = Cot::$usr['ip'];
            }
            if (empty($data['com_date'])) {
                $data['com_date'] = Cot::$sys['now'];
            }
        }

        $service = CommentsService::getInstance();

        $errors = $service->validate($data, true);
        if (!empty($errors)) {
            throw new Exception(implode(" \n", $errors));
        }

        $savedId = null;
        if ($isNew) {
            $savedId = $this->create($data);
        } else {
            $savedId = $this->update($id, $data) ? $id : null; ;
        }

        /* == Hook == */
        foreach (cot_getextplugins('comment.save.done') as $pl) {
            include $pl;
        }
        /* ===== */

        // Clear related cache
        $service->clearRelatedCache($data, $ciExtensionCode, $ciUrlParams, $ciUrl);

        return $savedId ?: false;
    }

    private function create(array $comment): ?int
    {
        unset($comment['com_id']);

        /* == Hook == */
        foreach (cot_getextplugins('comment.create.first') as $pl) {
            include $pl;
        }
        /* ===== */

        try {
            Cot::$db->beginTransaction();
            $sql = Cot::$db->insert(Cot::$db->com, $comment);
            $id = (int) Cot::$db->lastInsertId();

            cot_extrafield_movefiles();

            /* == Hook == */
            foreach (cot_getextplugins('comment.create.done') as $pl) {
                include $pl;
            }
            /* ===== */

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        return $id;
    }

    /**
     * Update comment ID
     * @param int $id Comment ID
     * @param array $comment Comment data
     * @throws Throwable
     */
    private function update(int $id, array $comment): bool
    {
        unset($comment['com_id']);

        /* == Hook == */
        foreach (cot_getextplugins('comment.update.first') as $pl) {
            include $pl;
        }
        /* ===== */

        try {
            Cot::$db->beginTransaction();

            $result = Cot::$db->update(Cot::$db->com, $comment, 'com_id = :commentId', ['commentId' => $id]);

            cot_extrafield_movefiles();

            /* == Hook == */
            foreach (cot_getextplugins('comment.update.done') as $pl) {
                include $pl;
            }
            /* ===== */

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        if ($result > 0) {
            cot_log(
                'Edited comment #' . $id . ' in "' . $comment['com_area'] . ' : '. $comment['com_code'] . '"',
                'comments',
                'edit',
                'done'
            );
        }

        return $result > 0;
    }

    /**
     * Delete comment by ID
     * @param int $id
     * @param ?string $ciExtensionCode Commented item extension code (To clear static cache)
     * @param ?array $ciUrlParams Commented item url params (To clear static cache)
     *    if $ciExtensionCode is empty, \cot\services\ItemService::get() will be used
     * @return bool
     * @throws Throwable
     */
    public function delete(int $id, ?string $ciExtensionCode = null, ?array $ciUrlParams = null): bool
    {
        $comment = CommentsRepository::getInstance()->getById($id);

        if ($comment === null) {
            return false;
        }

        $condition = 'com_id = :commentId';
        $params = ['commentId' => $id];

        $result = $this->deleteByCondition($condition, $params);

        $deleted = $result > 0;

        if ($deleted) {
            if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                // @deprecated in 0.9.26
                $row = $comment;
                /* === Hook === */
                foreach (cot_getextplugins('comments.delete') as $pl) {
                    include $pl;
                }
                /* ===== */
            }

            // Clear related cache
            CommentsService::getInstance()->clearRelatedCache($comment, $ciExtensionCode, $ciUrlParams);

            cot_log(
                'Deleted comment #' . $id . ' in "' . $comment['com_area'] . ' : '. $comment['com_code'] . '"',
                'comments',
                'delete',
                'done'
            );
        }

        return $deleted;
    }

    /**
     * Removes comments associated with an item
     *
     * @param string $source
     * @param string $sourceId
     * @return int
     */
    public function deleteBySourceId(string $source, string $sourceId): int
    {
        $condition = 'com_area = :source AND com_code = :sourceId';
        $params = ['source' => $source, 'sourceId' => $sourceId];

        $result = $this->deleteByCondition($condition, $params);

        // Clear related cache ?

        return $result;
    }

    /**
     * Deletes posts based on the specified condition. Does not update any statistics.
     * @return int Deleted posts count
     */
    private function deleteByCondition($condition, array $params = []): int
    {
        $batch = 300;

        $deletedCount = 0;

        try {
            Cot::$db->beginTransaction();
            do {
                $comments = CommentsRepository::getInstance()->getByCondition(
                    $condition,
                    $params,
                    'com_id DESC',
                    $batch
                );
                if (empty($comments)) {
                    break;
                }

                $commentIds = [];
                foreach ($comments as $comment) {
                    $commentIds[] = $comment['com_id'];
                    $deletedCount++;

                    if (!empty(Cot::$extrafields[Cot::$db->com])) {
                        foreach (Cot::$extrafields[Cot::$db->com] as $exfld) {
                            if (isset($comment['com_' . $exfld['field_name']])) {
                                cot_extrafield_unlinkfiles($comment['com_' . $exfld['field_name']], $exfld);
                            }
                        }
                    }

                    /* === Hook === */
                    foreach (cot_getextplugins('comment.delete.done') as $pl) {
                        include $pl;
                    }
                    /* ===== */

                    ItemService::getInstance()->onDelete(CommentsDictionary::SOURCE_COMMENT, $comment['com_id']);
                }

                Cot::$db->delete(Cot::$db->com, 'com_id IN (' . implode(',', $commentIds) . ')');
            } while (true);

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        return $deletedCount;
    }
}