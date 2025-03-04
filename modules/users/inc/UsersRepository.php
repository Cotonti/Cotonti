<?php
/**
 * User repository
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

namespace cot\modules\users\inc;

use cot\users\UsersRepository as BaseUsersRepository;

class UsersRepository extends BaseUsersRepository
{
    /**
     * @param list<int|numeric-string> $usersIds
     * @return list<array>
     */
    public function getByIds(array $usersIds, ?int $limit = 1000): array
    {
        $ids = $this->prepareIds($usersIds);
        if (empty($ids)) {
            return [];
        }

        $condition = 'user_id IN (' . implode(',', $ids) . ')';

        $users = $this->getByCondition($condition, [], null, $limit);
        $result = [];

        foreach ($users as $user) {
            $result[$user['user_id']] = $user;
        }

        return $result;
    }

    /**
     * @param list<int> $ids
     * @return list<int>
     */
    private function prepareIds(array $ids): array
    {
        $result = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $result[] = $id;
            }
        }
        return array_unique($result);
    }
}