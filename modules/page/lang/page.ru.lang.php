<?php
/**
 * Russian Language File for the Page Module (page.ru.lang.php)
 *
 * @package page
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Admin Page Section
 */

$L['addnewentry'] = 'Добавить новую запись';
$L['adm_queue_deleted'] = 'Страница удалена в корзину';
$L['adm_valqueue'] = 'В очереди на утверждение';
$L['adm_validated'] = 'Утвержденные';
$L['adm_structure'] = 'Структура страниц (категории)';
$L['adm_extrafields_desc'] = 'Создание / правка дополнительных полей';
$L['adm_sort'] = 'Сортировать';
$L['adm_sortingorder'] = 'Порядок сортировки по умолчанию в категории';
$L['adm_showall'] = 'Показать все';
$L['adm_help_page'] = 'Страницы категории &laquo;system&raquo; не отображаются в списках страниц и являются отдельными, самостоятельными страницами';
$L['adm_fileyesno'] = 'Файл (да/нет)';
$L['adm_fileurl'] = 'URL файла';
$L['adm_filecount'] = 'Количество загрузок';
$L['adm_filesize'] = 'Размер файла';

/**
 * Page Section
 * Extrafields Subsection
 */

$L['adm_help_pages_extrafield'] = 'HTML-код поля установится в значение по умолчанию автоматически, если его очистить и обновить<br /><br />
<b>Новые тэги в tpl-файлах:</b><br /><br />
page.tpl: {PAGE_XXXXX}, {PAGE_XXXXX_TITLE}<br /><br />
page.add.tpl: {PAGEADD_FORM_XXXXX}, {PAGEADD_FORM_XXXXX_TITLE}<br /><br />
page.edit.tpl: {PAGEEDIT_FORM_XXXXX}, {PAGEEDIT_FORM_XXXXX_TITLE}<br /><br />
list.tpl: {LIST_ROW_XXXXX}, {LIST_TOP_XXXXX}<br />';

/**
 * Config Section
 * Page Subsection
 */

$L['cfg_allowphp_pages'] = array('Разрешить страницы на PHP', 'Внимание: исполнение PHP кода в страницах может стать причиной некорректной работы или взлома сайта!');
$L['cfg_autovalidate'] = array('Автоматическое утверждение страниц', 'Автоматически утверждать публикацию страниц, созданных пользователем с правом администрирования раздела'); // New in 0.0.2
$L['cfg_count_admin'] = array('Считать посещения администраторов', 'Включить посещения администраторов в статистику посещаемости сайта'); // New in 0.0.1
$L['cfg_maxrowsperpage'] = array('Макс. количество записей на страницу списка', ' ');
$L['cfg_maxlistsperpage'] = array('Макс. количество категорий на странице', ' '); // New in 0.0.6

/**
 * page.add.tpl
 */

$L['pagadd_subtitle'] = 'Форма для создания новой страницы';
$L['pagadd_title'] = 'Создать новую страницу';

/**
 * page.edit.tpl
 */

$L['paged_subtitle'] = 'Форма для редактирования страницы';
$L['paged_title'] = 'Свойства страницы';

/**
 * page.tpl
 */

$L['pag_authortooshort'] = 'Имя автора слишком короткое либо отсутствует';
$L['pag_catmissing'] = 'Код категории отсутствует';
$L['pag_desctooshort'] = 'Описание слишком короткое либо отсутствует';
$L['pag_notavailable'] = 'Страница будет опубликована '; // New in N-0.0.2
$L['pag_titletooshort'] = 'Заголовок слишком короткий либо отсутствует';
$L['pag_validation'] = 'Ожидают утверждения';
$L['pag_validation_desc'] = 'Ваши страницы, которые не были ещё утверждены администратором';

/**
 * Moved from theme.lang
 */

$L['pag_linesperpage'] = 'Записей на страницу';
$L['pag_linesinthissection'] = 'Записей в разделе';

$L['pag_file'] = 'Прикрепить файл';
$L['pag_filehint'] = '(при включении модуля загрузок заполните два поля ниже)';
$L['pag_urlhint'] = '(если прикреплен файл)';
$L['pag_filesize'] = 'Размер файла (Кб)';
$L['pag_filesizehint'] = '(если прикреплен файл)';
$L['pag_filehitcount'] = 'Загрузок';
$L['pag_filehitcounthint'] = '(если прикреплен файл)';

$L['pag_formhint'] = 'После заполнения формы страница будет помещена в очередь на утверждение и будет скрыта до тех пор, пока модератор или администратор не утвердят ее публикацию в соответствующем разделе. Внимательно проверьте правильность заполнения полей формы.<br />Если вам понадобится изменить содержание страницы, то вы сможете сделать это позже, но страница вновь будет отправлена на утверждение.';

$L['pag_deletethispage'] = 'Удалить страницу';

?>