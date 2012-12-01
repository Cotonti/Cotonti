<?php
/**
 * Russian Language File for the Page Module (page.ru.lang.php)
 *
 * @package page
 * @version 0.9.6
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Module Config
 */

$L['cfg_autovalidate'] = array('Автоматическое утверждение страниц', 'Автоматически утверждать публикацию страниц, созданных пользователем с правом администрирования раздела');
$L['cfg_count_admin'] = array('Считать посещения администраторов', 'Включить посещения администраторов в статистику посещаемости сайта');
$L['cfg_maxlistsperpage'] = array('Макс. количество категорий на странице', ' ');
$L['cfg_order'] = array('Поле сортировки');
$L['cfg_title_page'] = array('Формат заголовка страницы', 'Опции: {TITLE}, {CATEGORY}');
$L['cfg_way'] = array('Направление сортировки');
$L['cfg_truncatetext'] = array('Ограничить размер текста в списках страниц', '0 для отключения');
$L['cfg_allowemptytext'] = array('Разрешить пустой текст страницы');
$L['cfg_keywords'] = array('Ключевые слова');

$L['info_desc'] = 'Управление контентом: страницы и категории страниц';

/**
 * Structure Confing
 */

$L['cfg_order_params'] = array(); // Redefined in cot_page_config_order()
$L['cfg_way_params'] = array($L['Ascending'], $L['Descending']);

/**
 * Admin Page Section
 */

$L['adm_queue_deleted'] = 'Страница удалена в корзину';
$L['adm_valqueue'] = 'В очереди на утверждение';
$L['adm_validated'] = 'Утвержденные';
$L['adm_expired'] = 'C истекшим сроком';
$L['adm_structure'] = 'Структура страниц (категории)';
$L['adm_sort'] = 'Сортировать';
$L['adm_sortingorder'] = 'Порядок сортировки по умолчанию в категории';
$L['adm_showall'] = 'Показать все';
$L['adm_help_page'] = 'Страницы категории &laquo;system&raquo; не отображаются в списках страниц и являются отдельными, самостоятельными страницами';
$L['adm_fileyesno'] = 'Файл (да/нет)';
$L['adm_fileurl'] = 'URL файла';
$L['adm_filecount'] = 'Количество загрузок';
$L['adm_filesize'] = 'Размер файла';

/**
 * Page add and edit
 */

$L['page_addtitle'] = 'Создать страницу';
$L['page_addsubtitle'] = 'Заполните необходимые поля и нажмите "Отправить" для продолжения';
$L['page_edittitle'] = 'Свойства страницы';
$L['page_editsubtitle'] = 'Измените необходимые поля и нажмите "Отправить" для продолжения';

$L['page_aliascharacters'] = 'Недопустимо использование символов \'+\', \'/\', \'?\', \'%\', \'#\', \'&\' в алиасах';
$L['page_catmissing'] = 'Код категории отсутствует';
$L['page_confirm_delete'] = 'Вы действительно хотите удалить эту страницу?';
$L['page_confirm_validate'] = 'Хотите утвердить эту страницу?';
$L['page_confirm_unvalidate'] = 'Вы действительно хотите отправить эту страницу в очередь на утверждение?';
$L['page_notavailable'] = 'Страница будет опубликована через';
$L['page_textmissing'] = 'Текст страницы не должен быть пустым';
$L['page_titletooshort'] = 'Заголовок слишком короткий либо отсутствует';
$L['page_validation'] = 'Ожидают утверждения';
$L['page_validation_desc'] = 'Ваши страницы, которые еще не утверждены администратором';

$L['page_file'] = 'Прикрепить файл';
$L['page_filehint'] = '(при включении модуля загрузок заполните два поля ниже)';
$L['page_urlhint'] = '(если прикреплен файл)';
$L['page_filesize'] = 'Размер файла, Кб';
$L['page_filesizehint'] = '(если прикреплен файл)';
$L['page_filehitcount'] = 'Загрузок';
$L['page_filehitcounthint'] = '(если прикреплен файл)';
$L['page_metakeywords'] = 'Ключевые слова';
$L['page_metatitle'] = 'Meta-заголовок';
$L['page_metadesc'] = 'Meta-описание';

$L['page_formhint'] = 'После заполнения формы страница будет помещена в очередь на утверждение и будет скрыта до тех пор, пока модератор или администратор не утвердят ее публикацию в соответствующем разделе. Внимательно проверьте правильность заполнения полей формы. Если вам понадобится изменить содержание страницы, то вы сможете сделать это позже, но страница вновь будет отправлена на утверждение.';

$L['page_pageid'] = 'ID страницы';
$L['page_deletepage'] = 'Удалить страницу';

$L['page_savedasdraft'] = 'Страница сохранена в черновиках';

/**
 * Page statuses
 */

$L['page_status_draft'] = 'Черновик';
$L['page_status_pending'] = 'На рассмотрении';
$L['page_status_approved'] = 'Утверждена';
$L['page_status_published'] = 'Опубликована';
$L['page_status_expired'] = 'Устарела';

/**
 * Moved from theme.lang
 */

$L['page_linesperpage'] = 'Записей на страницу';
$L['page_linesinthissection'] = 'Записей в разделе';

$Ls['pages'] = array('страница', 'страницы', 'страниц');
$Ls['unvalidated_pages'] = array('неутвержденная страница', 'неутвержденные страницы', 'неутвержденных страниц');

?>