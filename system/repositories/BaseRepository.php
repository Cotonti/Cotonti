<?php

declare(strict_types=1);

namespace cot\repositories;

use Cot;
use cot\traits\GetInstanceTrait;

abstract class BaseRepository
{
    use GetInstanceTrait;

    abstract public static function getTableName(): string;

    /**
     * @param array|string $condition
     * @param array|string $orderBy
     * @return array<int, array<int|string>> Requested items data
     * @todo joins, index by
     */
    public function getByCondition(
        $condition,
        array $params = [],
        $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $table = Cot::$db->quoteTableName(static::getTableName());

        $sqlWhere = is_array($condition) ? $this->prepareCondition($condition) : $condition;
        if (!empty($sqlWhere)) {
            $sqlWhere = ' WHERE ' . $sqlWhere;
        }

        $sqlOrderBy = '';
        if (!empty($orderBy)) {
            $sqlOrderBy = ' ORDER BY ' . (is_array($orderBy) ? implode(', ', $orderBy) : $orderBy);
        }

        $sqlLimit = '';
        if ($limit !== null) {
            $sqlLimit = " LIMIT $limit";
        }

        $sqlOffset = '';
        if ($offset !== null) {
            $sqlOffset = " OFFSET $offset";
        }

        $sql = "SELECT {$table}.* "
            . " FROM {$table} "
            . $sqlWhere . $sqlOrderBy . $sqlLimit . $sqlOffset;

        $items = Cot::$db->query($sql, $params)->fetchAll();
        if (empty($items)) {
            return [];
        }

        $result = [];
        foreach ($items as $item) {
            $item = $this->afterFetch($item);
            $result[] = $item;
        }

        return $result;
    }

    protected function prepareCondition(array $condition): string
    {
        return Cot::$db->prepareCondition($condition);
    }

    /**
     * This method allows to perform additional actions on the fetched data.
     * For example: in PHP below version 8, all fields are fetching as strings. So it can be needed to cast some
     * attributes
     */
    protected function afterFetch(array $item): array
    {
        return $item;
    }
}