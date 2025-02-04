<?php

declare(strict_types=1);

namespace cot\extensions;

use Cot;
use cot\repositories\BaseRepository;

class ExtensionsRepository extends BaseRepository
{
    public static function getTableName(): string
    {
        return Cot::$db->core;
    }

    /**
     * @return ?array{
     *    ct_id: int,
     *    ct_code: string,
     *    ct_title: string,
     *    ct_version: string,
     *    ct_state: int,
     *    ct_lock: bool,
     *    ct_plug: bool
     *   }
     */
    public function getByCode(string $extensionCode, ?string $extensionType = null, ?bool $active = null): ?array
    {
        $condition = ['ct_code = :code'];
        $params = ['code' => $extensionCode];

        if ($extensionType !== null) {
            $condition[] = 'ct_plug = ' . ($extensionType === ExtensionsDictionary::TYPE_MODULE ? 0 : 1);
        }

        if ($active !== null) {
            $condition[] = 'ct_state = ' . ($active ? 1 : 0);
        }

        $result = $this->getByCondition($condition, $params);
        if (empty($result)) {
            return null;
        }

        return array_shift($result);
    }

    protected function afterFetch(array $item): array
    {
        $item['ct_id'] = (int) $item['ct_id'];
        $item['ct_state'] = (int) $item['ct_state'];
        $item['ct_lock'] = (bool) $item['ct_lock'];
        $item['ct_plug'] = (bool) $item['ct_plug'];

        return $item;
    }
}