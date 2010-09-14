<?php
/**
 * Russian Language File for the Install Module
 *
 * @package install
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$L['Complete'] = 'Выполнено';
$L['Finish'] = 'Завершить';
$L['Install'] = 'Установить';
$L['Next'] = 'Далее';

$L['install_adminacc'] = 'Данные администратора';
$L['install_body_title'] = 'Установщик Cotonti';
$L['install_body_message1'] = 'Этот скрипт поможет вам осуществить первичную установку и настройку Cotonti. Вы должны создать пустую базу данных на вашем сервере, так как скрипт не сможет этого сделать самостоятельно.';
$L['install_body_message2'] = 'Рекомендуем создать в папке datas/ файл config.php и установить на него права CHMOD 666.';
$L['install_chmod_value'] = 'CHMOD {$chmod}';
$L['install_complete'] = 'Установка Cotonti успешно завершена!';
$L['install_complete_note'] = 'Удалите install.php и установите на datas/config.php права CHMOD 644. Это необходимо для повышения безопасности вашего сайта.';
$L['install_db'] = 'Настройки базы данных MySQL';
$L['install_db_host'] = 'Сервер СУБД';
$L['install_db_user'] = 'Пользователь';
$L['install_db_pass'] = 'Пароль';
$L['install_db_name'] = 'Имя базы данных';
$L['install_db_x'] = 'Префикс таблиц';
$L['install_dir_not_found'] = 'Каталог установки не найден';
$L['install_error_config'] = 'Не удаётся создать или отредактировать файл конфигурации. Скопируйте содержимое файла config-sample.php в config.php. Установите на файл config.php права CHMOD 777.';
$L['install_error_sql'] = 'Не удалось подключиться к базе MySQL. Проверьте настройки подключения.';
$L['install_error_sql_db'] = 'Не удалось выбрать базу MySQL. Проверьте настройки подключения.';
$L['install_error_sql_ext'] = 'Для запуска Cotonti необходимо PHP-расширение mysql';
$L['install_error_sql_script'] = 'Выполнение SQL-скрипта завершилось неудачно: {$msg}';
$L['install_error_sql_ver'] = 'Cotonti требуется версия MySQL 4.1.0 и выше. Ваша версия {$ver}';
$L['install_error_mainurl'] = 'Укажите основной URL вашего сайта';
$L['install_error_mbstring'] = 'Для запуска Cotonti необходимо расширение PHP mbstring';
$L['install_error_missing_file'] = 'Отсутствует файл {$file}. Загрузите его для продолжения установки.';
$L['install_error_php_ver'] = 'Для запуска Cotonti необходим PHP 5.2.0 и выше. Ваша версия {$ver}';
$L['install_misc'] = 'Дополнительные настройки';
$L['install_misc_lng'] = 'Основной язык';
$L['install_misc_skin'] = 'Основная тема оформления';
$L['install_misc_url'] = 'Основной URL сайта (без слеша в конце)';
$L['install_permissions'] = 'Права на файлы и каталоги';
$L['install_recommends'] = 'Рекомендуется';
$L['install_requires'] = 'Требуется';
$L['install_retype_password'] = 'Повторите пароль';
$L['install_step'] = 'Шаг {$step} из {$total}';
$L['install_title'] = 'Установка Cotonti';
$L['install_update'] = 'Обновление Cotonti';
$L['install_update_config_error'] = 'Ошибка обновления файла datas/config.php';
$L['install_update_config_success'] = 'Файл datas/config.php обновлен';
$L['install_update_error'] = 'Обновление не выполнено';
$L['install_update_nothing'] = 'Обновление не требуется';
$L['install_update_patch_applied'] = 'Установить патч {$f}: {$msg}';
$L['install_update_patch_error'] = 'Ошибка установки патча {$f}: {$msg}';
$L['install_update_patches'] = 'Установленные патчи:';
$L['install_update_success'] = 'Успешное обновление до версии {$rev}';
$L['install_update_template_not_found'] = 'Не найден файл шаблона обновления';
$L['install_upgrade_error'] = 'Ошибка обновления Cotonti до версии {$ver}';
$L['install_upgrade_success'] = 'Успешное обновление Cotonti до версии {$ver}';
$L['install_ver'] = 'Информация о сервере';
$L['install_ver_invalid'] = '{$ver} &mdash; неудачно!';
$L['install_ver_valid'] = '{$ver} &mdash; успешно!';
$L['install_view_site'] = 'Открыть сайт';
$L['install_writable'] = 'Доступно';

?>