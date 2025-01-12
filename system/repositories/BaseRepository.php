<?php

declare(strict_types=1);

namespace cot\repositories;

use Cot;
use cot\traits\GetInstanceTrait;

class BaseRepository
{
    use GetInstanceTrait;

    protected $tableName = null;

    /**
     * @param array|string $condition
     * @param array|string $orderBy
     * @return array<int, array<int|string>> Requested items data
     * @todo joins, index by
     */
    public function getByCondition($condition, array $params = [], $orderBy = null): array
    {
        $table = Cot::$db->quoteTableName($this->tableName);

        $sqlWhere = is_array($condition) ? $this->prepareCondition($condition) : $condition;
        if (!empty($sqlWhere)) {
            $sqlWhere = ' WHERE ' . $sqlWhere;
        }

        $sqlOrderBy = '';
        if (!empty($orderBy)) {
            $sqlOrderBy = ' ORDER BY ' . (is_array($orderBy) ? implode(', ', $orderBy) : $orderBy);
        }

        $sql = "SELECT {$table}.* "
            . " FROM {$table} "
            . $sqlWhere . $sqlOrderBy;

        $items = Cot::$db->query($sql, $params)->fetchAll();
        if (empty($items)) {
            return [];
        }

        $result = [];
        foreach ($items as $item) {
            $item = $this->castAttributes($item);
            $result[] = $item;
        }

        return $result;
    }

    protected function prepareCondition(array $condition): string
    {
        return Cot::$db->prepareCondition($condition);
    }

    /**
     * In PHP below version 8, all fields are fetching as strings
     * So it can be needed to cast some attributes
     */
    protected function castAttributes(array $item): array
    {
        return $item;
    }
}