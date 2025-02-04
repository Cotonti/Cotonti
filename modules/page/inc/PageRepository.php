<?php
/**
 * Page repository
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\page\inc;

use Cot;
use cot\repositories\BaseRepository;

defined('COT_CODE') or die('Wrong URL');

class PageRepository extends BaseRepository
{
    private static $cacheById = [];

    public static function getTableName(): string
    {
        if (empty(Cot::$db->pages)) {
            Cot::$db->registerTable('pages');
        }
        return Cot::$db->pages;
    }

    /**
     * Fetches user entry from DB
     * @param int $id Page ID
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

        $condition = 'page_id = :pageId';
        $params = ['pageId' => $id];

        $results = $this->getByCondition($condition, $params);
        $result = !empty($results) ? $results[0] : null;

        self::$cacheById[$id] = !empty($result) ? $result : false;

        return $result;
    }

    protected function afterFetch(array $item): array
    {
        $item['page_id'] = (int) $item['page_id'];
        $item['page_state'] = (int) $item['page_state'];
        $item['page_ownerid'] = (int) $item['page_ownerid'];
        $item['page_date'] = (int) $item['page_date'];
        $item['page_begin'] = (int) $item['page_begin'];
        $item['page_expire'] = (int) $item['page_expire'];
        $item['page_updated'] = (int) $item['page_updated'];
        $item['page_size'] = (int) $item['page_size'];
        $item['page_count'] = (int) $item['page_count'];

        return $item;
    }
}