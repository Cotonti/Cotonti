<?php
/**
 * Tags service
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\tags\inc;

use Cot;
use cot\repositories\BaseRepository;

class TagReferencesRepository extends BaseRepository
{
    private static $cacheCount = [];

    public static function getTableName(): string
    {
        if (empty(Cot::$db->tag_references)) {
            Cot::$db->registerTable('tag_references');
        }
        return Cot::$db->tag_references;
    }

    public function getCountBySource(string $source, bool $useCache = true): int
    {
        if ($source === '') {
            return 0;
        }

        if ($useCache && isset(self::$cacheCount[$source])) {
            return self::$cacheCount[$source];
        }

        $query = 'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(self::getTableName()) . ' WHERE tag_area = :source';
        $params = ['source' => $source];

        $result = Cot::$db->query($query, $params)->fetchColumn();

        self::$cacheCount[$source] = $result;

        return $result;
    }
}