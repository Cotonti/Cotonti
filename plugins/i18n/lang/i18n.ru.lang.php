<?php
/**
 * Russian Language File for Content Internationalization Plugin
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

// Plugin configuration

$L['cfg_cats'] = 'Корневые категории для применения i18n';
$L['cfg_cats_hint'] = 'Коды категорий через запятую';
$L['cfg_locales'] = 'Список локалей сайта';
$L['cfg_locales_hint'] = 'Каждая локаль с новой строки, формат: locale_code|Заголовок локали';
$L['cfg_omitmain'] = 'Опускать параметр языка в URL, если он указывает на основной язык';
$L['cfg_rewrite'] = 'Включить ЧПУ для параметра языка в ссылках';
$L['cfg_rewrite_hint'] = 'Требует ручного обновления .htaccess';
$L['cfg_cookie'] = 'Запоминать выбранный язык в cookie';

$L['info_desc'] = 'Поддержка многоязычного контента в ядре и расширениях';

// Plugin strings

$L['i18n_adding'] = 'Добавление нового перевода';
$L['i18n_editing'] = 'Редактирование перевода';
$L['i18n_incorrect_locale'] = 'Неверная локаль';
$L['i18n_items_added'] = '{$cnt} элементов добавлено';
$L['i18n_items_removed'] = '{$cnt} элементов удалено';
$L['i18n_items_updated'] = '{$cnt} элементов обновлено';
$L['i18n_locale_selection'] = 'Выбор локали';
$L['i18n_localized'] = 'Локализованное';
$L['i18n_original'] = 'Оригинал';
$L['i18n_structure'] = 'Интернационализация структуры';
$L['i18n_translate'] = 'Перевести';
$L['i18n_translation'] = 'Перевод';
