<?php
/**
 * Russian Language File for the Users Module
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$L['info_desc'] = 'Регистрация и управление пользователями, профили и страницы пользователей';

/**
 * Module Config
 */
$L['cfg_disablereg'] = 'Отключить регистрацию';
$L['cfg_disablereg_hint'] = 'Запретить регистрацию новых пользователей';
$L['cfg_maxusersperpage'] = 'Максимальное количество записей на страницу в списке пользователей';
$L['cfg_maxusersperpage_hint'] = ' ';
$L['cfg_filterFields'] = 'Поля для фильтра';
$L['cfg_filterFields_hint'] = 'По умолчанию, в списке пользователей, фильтр по имени фильтрует по полю '
 . '<strong>user_name</strong> и экстраполям <strong>first_name</strong>, <strong>firstname</strong>, '
 . '<strong>last_name</strong>, <strong>lastname</strong>, <strong>middle_name</strong>, <strong>middlename</strong> '
 . 'если они есть.<br>'
 . 'Вы можете указать дополнительные экстраполя для фильтра. Через запятую. Без префикса <strong>user_</strong>.';
$L['cfg_regnoactivation'] = 'Отменить проверку e-mail при регистрации';
$L['cfg_regnoactivation_hint'] = 'По причине безопасности рекомендуется &laquo;Нет&raquo;!';
$L['cfg_register_auto_login'] = 'Автовход после регистрации';
$L['cfg_register_auto_login_hint'] = 'Пользователи, не активировавшие учетную запись будут авторизованы, если включена соответсвующая опция.';
$L['cfg_inactive_login'] = 'Авторизация для пользователей, не подтвердивших регистрацию';
$L['cfg_inactive_login_hint'] = 'Разрешить авторизацию пользователям, неподтвердившим регистрацию.';
$L['cfg_regrequireadmin'] = 'Утверждение новых учетных записей администратором';
$L['cfg_regrequireadmin_hint'] = ' ';
$L['cfg_user_email_noprotection'] = 'Выключить защиту смены e-mail с паролем';
$L['cfg_user_email_noprotection_hint'] = 'По причине безопасности рекомендуется &laquo;Нет&raquo;!';
$L['cfg_useremailchange'] = 'Разрешить пользователям изменять свой e-mail';
$L['cfg_useremailchange_hint'] = 'По причине безопасности рекомендуется &laquo;Нет&raquo;!';
$L['cfg_usertextmax'] = 'Максимальная длина подписи, символов';
$L['cfg_usertextmax_hint'] = '';
$L['cfg_usertextimg'] = 'Разрешить изображения и HTML-код в подписях пользователей';
$L['cfg_usertextimg_hint'] = 'По причине безопасности рекомендуется &laquo;Нет&raquo;!';

/**
 * User profile & edit
 */
$L['Password_updated'] = 'Пароль изменен';
$L['Profile_updated'] = 'Настройки профиля сохранены';
$L['User_data_updated'] = 'Данные пользователя сохранены';
$L['Filter_search'] = 'Поиск по параметрам';
$L['Username_search'] = 'Поиск по имени';
$L['Not_indicated'] = 'Не указано';

$L['users_meta_title'] = 'Список пользователей сайта';
$L['users_meta_desc'] = 'Список зарегистрированных пользователей сайта';
