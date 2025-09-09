<?php
/**
 * Users helper
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types = 1);

namespace cot\modules\users\inc;

use cot\traits\GetInstanceTrait;
use Resources;

class UsersHelper
{
    use GetInstanceTrait;


    private static $userSelectInited = false;

    /**
     * User select widget
     * @param string $inputName
     * @param string|string[] $chosen Selected value (or values array for mutli-select)
     * @param array $data Options available
     * @param string|array<string, string> $attributes Additional attributes as an associative array or a string
     * @param string $customRc Custom resource string name
     * @return string
     * @todo \cot\users\UsersHelper ???
     */
    public function usersSelect(
        string $inputName,
        $chosen = '',
        array $data = [],
        bool $multiple = false,
        $attributes = [],
        string $customRc = ''
    ): string {
        if (!self::$userSelectInited) {
            Resources::linkFileFooter(Resources::SELECT2);

            $selectUrl = cot_url('users', ['n' => 'common', 'a' => 'get-users', '_ajax' => 1], false, true);
            Resources::embedFooter(
                <<<JS
                function initUsersSelector () {
                    const elements = document.querySelectorAll('.user-input');
                    if ((typeof window.jQuery === 'undefined') || elements.length === 0) {
                        return;
                    }
                    
                    elements.forEach((element) => {
                        if (element.dataset.inited !== undefined) {
                           return;
                        }
                        $(element).select2({
                            ajax: {
                                url: '{$selectUrl}',
                                dataType: 'json',
                                delay: 500 //,
                                // cache: false
                            },
                            minimumInputLength: 1
                        });
                        element.dataset.inited = 'true';
                    });
                }
                initUsersSelector();
                JS
            );
            self::$userSelectInited = true;
        }

        // @todo Attributes can come as a string
        $attributes['class'] = 'user-input';
        if ($multiple) {
            $attributes['multiple'] = 'multiple';
        }

        if (!empty($chosen) && empty($data)) {
            $usersIds = [];
            foreach ($chosen as $userId) {
                $userId = (int) $userId;
                if ($userId > 0 && !in_array($userId, $usersIds)) {
                    $usersIds[] = $userId;
                }
            }
            $users = UsersRepository::getInstance()->getByIds($usersIds);
            $helper = \cot\users\UsersHelper::getInstance();
            if (!empty($users)) {
                foreach ($users as $user) {
                    $text = $helper->getFullName($user);
                    if ($text !== $user['user_name']) {
                        $text .= ' (' . $user['user_name'] . ')';
                    }
                    $data[$user['user_id']] = $text;
                }
            }
        }

        $inputAttributes = cot_rc_attr_string($attributes);
        $chosen = cot_import_buffered($inputName, $chosen);

        return cot_selectbox(
            $chosen,
            $inputName,
            array_keys($data),
            array_values($data),
            false,
            $inputAttributes,
            $customRc
        );
    }
}