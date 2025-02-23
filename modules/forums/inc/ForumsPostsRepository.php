<?php
/**
 * Forum topics repository
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\forums\inc;

use Cot;
use cot\repositories\BaseRepository;

defined('COT_CODE') or die('Wrong URL.');

class ForumsPostsRepository extends BaseRepository
{
    private static $cacheById = [];

    public static function getTableName(): string
    {
        if (empty(Cot::$db->forum_posts)) {
            Cot::$db->registerTable('forum_posts');
        }
        return Cot::$db->forum_posts;
    }

    /**
     * Fetches forum post from DB
     * @param int $id Post ID
     * @param bool $useCache Use one time session cache
     * @return ?array
     */
    public function getById(int $id, bool $useCache = true): ?array
    {
        if ($id < 1) {
            return null;
        }

        if ($useCache && isset(self::$cacheById[$id])) {
            return self::$cacheById[$id] !== false ? self::$cacheById[$id] : null;
        }

        $condition = 'fp_id = :postId';
        $params = ['postId' => $id];

        $results = $this->getByCondition($condition, $params);
        $result = !empty($results) ? $results[0] : null;

        self::$cacheById[$id] = !empty($result) ? $result : false;

        return $result;
    }

    /**
     * Fetches posts from DB by topic ID
     * @param int $topicId Topic ID
     * @param array|string $orderBy
     * @return list<array>
     */
    public function getByTopicId(int $topicId, $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $condition = 'fp_topicid = :topiId';
        $params = ['topiId' => $topicId];

        return $this->getByCondition($condition, $params, $orderBy, $limit, $offset);
    }

    /**
     * Fetches first post in the topic
     * @param int $topicId Topic ID
     * @return ?array
     */
    public function getFirstPostInTopic(int $topicId): ?array
    {
        $result = $this->getByTopicId($topicId, 'fp_id ASC', 1);
        return !empty($result) ? $result[0] : null;
    }

    protected function afterFetch(array $item): array
    {
        $item['fp_id'] = (int) $item['fp_id'];
        $item['fp_topicid'] = (int) $item['fp_topicid'];
        $item['fp_posterid'] = (int) $item['fp_posterid'];
        $item['fp_creation'] = (int) $item['fp_creation'];
        $item['fp_updated'] = (int) $item['fp_updated'];

        return $item;
    }
}