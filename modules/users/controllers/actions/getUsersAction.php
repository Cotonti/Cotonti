<?php
/**
 * Users get users action. User's names for select2
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\users\controllers\actions;

use Cot;
use cot\controllers\BaseAction;
use cot\modules\users\inc\UsersRepository;
use cot\users\UsersHelper;

class getUsersAction extends BaseAction
{
    /**
     * @var array{results: array{id: int, text: string}}
     */
    private $result = ['results' => []];

    public function run(): string
    {
        $q = mb_strtolower(cot_import('q', 'G', 'TXT'));
        if ($q === null || $q === '') {
            return $this->results();
        }
        $term = mb_strtolower($q);
        $term = urldecode($term);

        $minLength = 1;

        if (!$term || mb_strlen($term) < $minLength) {
            return $this->results();
        }

        $condition = ['user_name' => 'user_name LIKE :userName'];
        $params = ['userName' => "%$term%"]; // @todo or '$term%'

        if (!empty(Cot::$extrafields[Cot::$db->users])) {
            $searchFields = ['first_name', 'firstname', 'last_name', 'lastname', 'middle_name', 'middlename'];
            $searchExtraFields = trim(Cot::$cfg['users']['filterFields']);
            if ($searchExtraFields !== '') {
                $searchExtraFields = explode(',', $searchExtraFields);
                foreach ($searchExtraFields as $extraField) {
                    $extraField = trim($extraField);
                    if ($extraField !== '' && !in_array($extraField, $searchFields)) {
                        $searchFields[] = $extraField;
                    }
                }
            }

            foreach ($searchFields as $searchField) {
                if (
                    mb_strpos($searchField, 'user_') !== 0
                    && !isset(Cot::$extrafields[Cot::$db->users][$searchField])
                ) {
                    continue;
                }
                $field = mb_strpos($searchField, 'user_') === 0 ? $searchField : 'user_' . $searchField;
                $condition[$searchField] =  "{$field} LIKE :userName";
            }
        }

        $users = UsersRepository::getInstance()->getByCondition(implode(' OR ', $condition), $params, null, 300);
        if (empty($users)) {
            return $this->results();
        }

        $helper = UsersHelper::getInstance();

        foreach ($users as $user) {
            $text = $helper->getFullName($user);
            if ($text !== $user['user_name']) {
                $text .= ' (' . $user['user_name'] . ')';
            }
            $this->result['results'][] = ['id' => $user['user_id'], 'text' => $text];
        }

        return $this->results();
    }

    private function results(): string
    {
        cot_sendheaders('application/json');
        return json_encode($this->result);
    }
}