<?php
/**
 * Forums posts control service
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\forums\inc;

use Cot;
use cot\services\ItemService;
use cot\traits\GetInstanceTrait;
use PDO;
use Throwable;

defined('COT_CODE') or die('Wrong URL.');

class ForumsPostsControlService
{
    use GetInstanceTrait;

    public function delete(int $id): bool
    {
        $forumPost = ForumsPostsRepository::getInstance()->getById($id);

        if ($forumPost === null) {
            return false;
        }

        $condition = 'fp_id = :postId';
        $params = ['postId' => $id];

        $result = $this->deleteByCondition($condition, $params);

        cot_forums_resyncTopic($id, $forumPost['fp_posterid']);

        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            // @deprecated in 0.9.26
            $row = $forumPost;
            /* === Hook === */
            foreach (cot_getextplugins('forums.posts.delete.done') as $pl) {
                include $pl;
            }
            /* ===== */
        }

        cot_log("Deleted post #" . $id, 'forums', 'delete post', 'done');

        return $result > 0;
    }

    /**
     * Delete all posts in topic
     * @param int $topicId
     * @return int
     * @throws Throwable
     */
    public function deleteByTopicId(int $topicId): int
    {
        // Update posts count for all posters in this topic
        $posterIds = Cot::$db->query(
            'SELECT DISTINCT (fp_posterid) FROM ' . Cot::$db->forum_posts . ' WHERE fp_topicid = ?',
            $topicId
        )->fetchAll(PDO::FETCH_COLUMN);

        $condition = 'fp_topicid = :topiId';
        $params = ['topiId' => $topicId];

        $result = $this->deleteByCondition($condition, $params);

        if (!empty($posterIds)) {
            foreach ($posterIds as $posterId) {
                cot_forums_updateUserPostCount($posterId);
            }
        }

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
                $posts = ForumsPostsRepository::getInstance()->getByCondition(
                    $condition,
                    $params,
                    'fp_id DESC',
                    $batch
                );
                if (empty($posts)) {
                    break;
                }

                $postIds = [];
                foreach ($posts as $forumPost) {
                    $postIds[] = $forumPost['fp_id'];
                    $deletedCount++;

                    foreach (Cot::$extrafields[Cot::$db->forum_posts] as $exfld) {
                        if (isset($forumPost['fp_' . $exfld['field_name']])) {
                            cot_extrafield_unlinkfiles($forumPost['fp_' . $exfld['field_name']], $exfld);
                        }
                    }

                    /* === Hook === */
                    foreach (cot_getextplugins('forums.post.delete.done') as $pl) {
                        include $pl;
                    }
                    /* ===== */

                    ItemService::getInstance()->onDelete(ForumsDictionary::SOURCE_POST, $forumPost['fp_id']);
                }

                Cot::$db->delete(Cot::$db->forum_posts, 'fp_id IN (' . implode(',', $postIds) . ')');
            } while (true);

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        return $deletedCount;
    }
}