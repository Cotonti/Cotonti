<?php

declare(strict_types = 1);

namespace cot\users;

use cot\extensions\ExtensionsService;
use cot\traits\GetInstanceTrait;

defined('COT_CODE') or die('Wrong URL');

/**
 * Users repository
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class UsersHelper
{
    use GetInstanceTrait;

    /**
     * Returns User's full name
     *
     * Format of full name is language specific and defined by $R['users_full_name']
     * resource string.
     *
     * @param array|int $user User Data or User ID
     * @return string
     */
    public function getFullName($user): string
    {
        if (empty($user)) {
            return '';
        }

        if (function_exists('cot_user_full_name_custom')) {
            return cot_user_full_name_custom($user);
        }

        if (is_numeric($user)) {
            $userId = (int) $user;
            $user = UsersRepository::getInstance()->getById($userId);
        }

        if (empty($user) || !is_array($user) || empty($user['user_name'])) {
            return '';
        }

        $firstname = '';
        if (!empty($user['user_firstname'])) {
            $firstname = $user['user_firstname'];
        } elseif (!empty($user['user_first_name'])) {
            $firstname = $user['user_first_name'];
        }

        $middlename = '';
        if (!empty($user['user_middlename'])) {
            $middlename = $user['user_middlename'];
        } elseif (!empty($user['user_middle_name'])) {
            $middlename = $user['user_middle_name'];
        }

        $lastname = '';
        if (!empty($user['user_lastname'])) {
            $lastname = $user['user_lastname'];
        } elseif (!empty($user['user_last_name'])) {
            $lastname = $user['user_last_name'];
        }

        if ($firstname !== '' || $middlename !== '' || $lastname !== '') {
            return trim(
                cot_rc(
                    'users_full_name',
                    [
                        'firstname' => $firstname,
                        'middlename' => $middlename,
                        'lastname' => $lastname,
                        'name' => $user['user_name']
                    ],
                )
            );
        }

        return $user['user_name'];
    }

    public function getUrl(
        array $user,
        string $tail = '',
        bool $htmlspecialcharsBypass = false,
        bool $absolute = false
    ): string {
        if (!ExtensionsService::getInstance()->isModuleActive('users')) {
            return '';
        }

        $params = ['m' => 'details', 'id' => $user['user_id'], 'u' => $user['user_name']];

        if ($absolute) {
            return cot_absoluteUrl('users', $params, $tail, $htmlspecialcharsBypass);
        }

        return cot_url('users', $params, $tail, $htmlspecialcharsBypass);
    }
}