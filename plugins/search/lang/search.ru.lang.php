<?php
/**
 * Russian Language File for Search Plugin
 *
 * @package search
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Title & Subtitle
 */

$L['plu_search'] = 'Поиск';

/**
 * Plugin Body
 */

// Общие - залоговок, инфо, запрос.
$L['plu_search_req'] = 'Запрос';
$L['plu_search_key'] = 'Найти';
$L['plu_search_forums'] = 'Поиск по форумам';
$L['plu_search_pages'] = 'Поиск по страницам';

// Переключатели режимов и заголовки результатов.
$L['plu_tabs_all'] = 'Везде';
$L['plu_tabs_frm'] = 'Форумы';
$L['plu_tabs_pag'] = 'Страницы';

// Параметры - Общие.
$L['plu_ctrl_list'] = 'Удерживайте CTRL чтобы выделить несколько разделов';
$L['plu_allsections'] = 'Все разделы';
$L['plu_allcategories'] = 'Все разделы';
$L['plu_res_sort'] = 'Сортировать результаты по';
$L['plu_sort_desc'] = 'Убывание';
$L['plu_sort_asc'] = 'Возрастание';
$L['plu_other_opt'] = 'Параметры поиска';
$L['plu_other_date'] = 'Учитывать дату';
$L['plu_other_userfilter'] = 'Фильтр по пользователям';

// Параметры - Даты.
$L['plu_any_date'] = 'Любая дата';
$L['plu_last_2_weeks'] = 'Последние 2 недели';
$L['plu_last_1_month'] = 'Последний месяц';
$L['plu_last_3_month'] = 'Последние 3 месяца';
$L['plu_last_1_year'] = 'Последний год';
$L['plu_need_datas'] = 'Произвольный диапазон';

// Параметры - Форум.
$L['plu_frm_set_sec'] = 'Разделы форума';
$L['plu_frm_res_sort1'] = 'Дате обновления тем';
$L['plu_frm_res_sort2'] = 'Дате создания тем';
$L['plu_frm_res_sort3'] = 'Названию тем';
$L['plu_frm_res_sort4'] = 'Числу ответов';
$L['plu_frm_res_sort5'] = 'Числу просмотров';
$L['plu_frm_res_sort6'] = 'Разделу';
$L['plu_frm_search_names'] = 'Поиск в названиях тем';
$L['plu_frm_search_post'] = 'Поиск в теле сообщений';
$L['plu_frm_search_answ'] = 'Только темы с ответами';
$L['plu_frm_set_subsec'] = 'Поиск в подразделах';

// Параметры - Страницы.
$L['plu_pag_set_sec'] = 'Разделы сайта';
$L['plu_pag_res_sort1'] = 'Дате публикации';
$L['plu_pag_res_sort2'] = 'Названию';
$L['plu_pag_res_sort3'] = 'Популярности';
$L['plu_pag_res_sort3'] = 'Категории';
$L['plu_pag_search_names'] = 'Поиск в названиях публикаций';
$L['plu_pag_search_desc'] = 'Поиск в описании публикаций';
$L['plu_pag_search_text'] = 'Поиск в самих публикациях';
$L['plu_pag_search_file'] = 'Публикации только с файлами';
$L['plu_pag_set_subsec'] = 'Поиск в подразделах';

// Ошибки.
$L['plu_querytooshort'] = 'Поисковый запрос слишком короткий';
$L['plu_toomanywords'] = 'Слишком много слов, должно быть не больше';
$L['plu_noneresult'] = 'Ничего не найдено, попробуйте упростить запрос';
$L['plu_usernotexist'] = 'Данный пользователь не существует';

// Результаты.
$L['plu_result'] = 'Результаты поиска';
$L['plu_found'] = 'Найдено';
$L['plu_match'] = 'совпадений';
$L['plu_section'] = 'Раздел';
$L['plu_last_date'] = 'Дата обновления';

/**
 * Plugin Config
 */

$L['cfg_maxwords']= array('Максимум поисковых слов');
$L['cfg_maxsigns']= array('Максимум символов в поиске');
$L['cfg_maxitems']= array('Максимум результатов в обычном поиске');
$L['cfg_minsigns'] = array('Минимум символов в запросе');
$L['cfg_pagesearch'] = array('Влючить поиск по страницам');
$L['cfg_forumsearch'] = array('Включить поиск по форумам');
$L['cfg_searchurl'] = array('Вид отображения сообщений в форумах', 'Single &ndash; отдельное сообщение на странице<br />Normal &ndash; переход к сообщению при отображении всей темы');
$L['cfg_addfields']= array('Дополнительные поля страниц для поиска, разделенные запятыми', 'Например page_extra1, page_extra2, page_key');
$L['cfg_extrafilters']= array('Отображать дополнительные фильтры на главной странице поиска');

$L['info_desc'] = 'Расширенный поиск по страницам, форумам и другим локациям сайта';

?>