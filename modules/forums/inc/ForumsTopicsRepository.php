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

class ForumsTopicsRepository extends BaseRepository
{
    public static function getTableName(): string
    {
        if (empty(Cot::$db->forum_topics)) {
            Cot::$db->registerTable('forum_topics');
        }
        return Cot::$db->forum_topics;
    }

    /**
     * Fetches page entry from DB
     * @param int $id Page ID
     * @param bool $useCache Use one time session cache
     * @return ?array
     */
    public function getById(int $id, bool $useCache = true): ?array
    {
        if ($id < 1) {
            return null;
        }

        $condition = 'ft_id = :topicId';
        $params = ['topicId' => $id];

        $results = $this->getByCondition($condition, $params);
        return !empty($results) ? $results[0] : null;
    }

    protected function afterFetch(array $item): array
    {
        $item['ft_id'] = (int) $item['ft_id'];
        $item['ft_mode'] = (int) $item['ft_mode'];
        $item['ft_state'] = (int) $item['ft_state'];
        $item['ft_sticky'] = (bool) $item['ft_sticky'];
        $item['ft_creationdate'] = (int) $item['ft_creationdate'];
        $item['ft_updated'] = (int) $item['ft_updated'];
        $item['ft_postcount'] = (int) $item['ft_postcount'];
        $item['ft_viewcount'] = (int) $item['ft_viewcount'];
        $item['ft_lastposterid'] = (int) $item['ft_lastposterid'];
        $item['ft_firstposterid'] = (int) $item['ft_firstposterid'];
        $item['ft_poll'] = (int) $item['ft_poll'];
        $item['ft_movedto'] = (int) $item['ft_movedto'];

        return $item;
    }
}