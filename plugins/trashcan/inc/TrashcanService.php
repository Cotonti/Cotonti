<?php
/**
 * Trashcan service API
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\trashcan\inc;

use Cot;
use cot\traits\GetInstanceTrait;

class TrashcanService
{
    use GetInstanceTrait;

    /**
     * @var array Objects recently placed in the trash
     */
    private static $recentlyPlaced = [];

    public function addRecentlyPlaced(string $source, string $sourceId, int $trashcanId): void
    {
        static::$recentlyPlaced[$source][$sourceId] = $trashcanId;
    }

    public function getRecentlyPlacedId(string $source, string $sourceId): ?int
    {
        return static::$recentlyPlaced[$source][$sourceId] ?? null;
    }

    /**
     * Sends item to trash
     *
     * @param string $source Item type
     * @param string $title Title
     * @param string $sourceId Item ID
     * @param array|string $itemData Item contents in array or SQL string condition for deleting records
     * @param ?int $parentId trashcan parent id
     * @return ?int Trash insert id
     */
    public function put(string $source, string $title, string $sourceId, $itemData, ?int $parentId = null): ?int
    {
        global $trash_types;

        $trash = [
            'tr_date' => Cot::$sys['now'],
            'tr_type' => $source,
            'tr_title' => $title,
            'tr_itemid' => $sourceId,
            'tr_trashedby' => (int) Cot::$usr['id'],
            'tr_parentid' => $parentId ?? 0,
        ];

        /* === Hook  === */
        foreach (cot_getextplugins('trash.put.first') as $pl) {
            include $pl;
        }
        /* ===== */

        $i = 0;
        $existId = 0;
        if (is_array($itemData)) {
            $i++;
            $trash['tr_datas'] = serialize($itemData);

            $existId = (int) Cot::$db->query(
                'SELECT tr_id FROM ' . Cot::$db->quoteTableName(Cot::$db->trash) .
                ' WHERE tr_type = :type AND tr_itemid = :id ORDER BY tr_date DESC LIMIT 1',
                ['type' => $source, 'id' => $sourceId]
            )->fetchColumn();
            if ($existId > 0) {
                // If for some reason the item is already in the trash (For example because of an error)
                unset($trash['tr_type'], $trash['tr_itemid']);
                Cot::$db->update(
                    Cot::$db->trash,
                    $trash,
                    'tr_type = :type AND tr_itemid = :id',
                    ['type' => $source, 'id' => $sourceId]
                );
            } else {
                Cot::$db->insert(Cot::$db->trash, $trash);
            }
        } elseif (is_string($itemData)) {
            $tablename = $trash_types[$source] ?? $source;
            $sql_s = Cot::$db->query("SELECT * FROM $tablename WHERE $itemData");
            while ($row_s = $sql_s->fetch()) {
                $i++;
                $trash['tr_datas'] = serialize($row_s);
                Cot::$db->insert(Cot::$db->trash, $trash);
            }
            $sql_s->closeCursor();
        }

        if ($existId > 0) {
            $trashId = $existId;
        } else {
            $trashId = $i > 0 ? (int) Cot::$db->lastInsertId() : null;
        }

        $this->addRecentlyPlaced($source, $sourceId, $trashId);

        /* === Hook  === */
        foreach (cot_getextplugins('trash.put.done') as $pl) {
            include $pl;
        }
        /* ===== */

        return $trashId;
    }
}