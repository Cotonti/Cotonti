<?php
/**
 * Russian Language File for Content Internationalization Plugin
 *
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// Plugin configuration

$L['cfg_cats'] = array('Корневые категории для применения i18n', 'Коды категорий через запятую');
$L['cfg_locales'] = array('Список локалей сайта', 'Каждая локаль с новой строки, формат: locale_code|Заголовок локали');
$L['cfg_omitmain'] = array('Опускать параметр языка в URL, если он указывает на основной язык');
$L['cfg_rewrite'] = array('Включить ЧПУ для параметра языка в ссылках', 'Требует ручного обновления .htaccess');

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

?>
