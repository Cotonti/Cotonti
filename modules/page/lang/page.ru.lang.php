<?php
/**
 * Russian Language File for the Page Module (page.ru.lang.php)
 *
 * @package page
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
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

$L['info_desc'] = 'Расширяемая функциональность по управлению содержимым: страницы и категории страниц.';

/**
 * Structure Confing
 */

$L['cfg_order_params'] = array(); // Redefined in cot_page_config_order()
$L['cfg_way_params'] = array($L['Ascending'], $L['Descending']);

/**
 * Extrafields Section
 */

$L['adm_help_pages_extrafield'] = '<p><em>HTML-код</em> поля установится в значение по умолчанию автоматически, если его очистить и обновить</p>
<p class="margintop10"><b>Новые тэги в tpl-файлах:</b></p>
<ul class="follow">
<li>page.tpl: {PAGE_XXXXX}, {PAGE_XXXXX_TITLE}</li>
<li>page.add.tpl: {PAGEADD_FORM_XXXXX}, {PAGEADD_FORM_XXXXX_TITLE}</li>
<li>page.edit.tpl: {PAGEEDIT_FORM_XXXXX}, {PAGEEDIT_FORM_XXXXX_TITLE}</li>
<li>page.list.tpl: {LIST_ROW_XXXXX}, {LIST_TOP_XXXXX}</li>
</ul>';

/**
 * Admin Page Section
 */

$L['adm_queue_deleted'] = 'Страница удалена в корзину';
$L['adm_valqueue'] = 'В очереди на утверждение';
$L['adm_validated'] = 'Утвержденные';
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

$L['page_formhint'] = 'После заполнения формы страница будет помещена в очередь на утверждение и будет скрыта до тех пор, пока модератор или администратор не утвердят ее публикацию в соответствующем разделе. Внимательно проверьте правильность заполнения полей формы. Если вам понадобится изменить содержание страницы, то вы сможете сделать это позже, но страница вновь будет отправлена на утверждение.';

$L['page_pageid'] = 'ID страницы';
$L['page_deletepage'] = 'Удалить страницу';

/**
 * Page statuses
 */

$L['page_status_draft'] = 'Draft';
$L['page_status_pending'] = 'Pending';
$L['page_status_approved'] = 'Approved';
$L['page_status_published'] = 'Published';
$L['page_status_expired'] = 'Expired';

/**
 * Moved from theme.lang
 */

$L['page_linesperpage'] = 'Записей на страницу';
$L['page_linesinthissection'] = 'Записей в разделе';

?>