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
use PDO;
use Throwable;

defined('COT_CODE') or die('Wrong URL.');

class CommentsControlService
{
    use GetInstanceTrait;

    public function delete(int $id): bool
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

        return $this->deleteByCondition($condition, $params);
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