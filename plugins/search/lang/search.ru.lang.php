<?php
/**
 * Russian Language File for Search Plugin
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL.');

// Настройки плагина.
$L['cfg_maxwords']= array('Максимум поисковых слов');
$L['cfg_maxsigns']= array('Максимум знаков в поиске');
$L['cfg_maxitems']= array('Максимум результатов в обычном поиске');
$L['cfg_minsigns'] = array('Минимум знаков в запросе');
$L['cfg_pageseach'] = array('Влючить поиск по страницам');
$L['cfg_forumsearch'] = array('Включить поиск по форумам');
$L['cfg_searchurl'] = array('Вид отображения сообщений в форумах, Single - отдельное сообщение на странице, Normal - переход к сообщению при отображении всей темы');
$L['cfg_addfields']= array('Дополнительные поля страниц для поиска, разделенные запятыми. Например "page_extra1,page_extra2,page_key"');

// Общие - залоговок, инфо, запрос.
$L['plu_title_all'] = "Поиск по сайту";
$L['plu_subtitle_all'] = "Вы можете конкретизировать поиск, отметив ниже лишь необходимые разделы и параметры.";
$L['plu_search_req'] = "Запрос";
$L['plu_search_key'] = "Найти";
$L['plu_search_example'] = "Например, cotonti 7 genesis";

// Дополнения в заголовок.
$L['plu_title_frmtab'] = "Форум";
$L['plu_title_pagtab'] = "Публикации";

// Переключатели режимов и заголовки результатов.
$L['plu_tabs_all'] = "Общий поиск";
$L['plu_tabs_frm'] = "Форум";
$L['plu_tabs_pag'] = "Публикации";

// Параметры - Общие.
$L['plu_ctrl_list'] = "Удерживайте CTRL, чтобы выделить несколько разделов.";
$L['plu_allsections'] = "Все разделы";
$L['plu_allcategories'] = "Все разделы";
$L['plu_res_sort'] = "Сортировать результаты по";
$L['plu_sort_desc'] = "Убывание";
$L['plu_sort_asc'] = "Возрастание";
$L['plu_other_opt'] = "Дополнительные параметры";
$L['plu_other_date'] = "Учитывать дату";

// Параметры - Даты.
$L['plu_any_date'] = 'Любая дата';
$L['plu_last_2_weeks'] = 'Последние 2 недели';
$L['plu_last_1_month'] = 'Последний месяц';
$L['plu_last_3_month'] = 'Последние 3 месяца';
$L['plu_last_1_year'] = 'Последний год';
$L['plu_need_datas'] = 'Произвольный диапазон';

// Параметры - Форум.
$L['plu_frm_set_sec'] = "Выберите разделы форума";
$L['plu_frm_res_sort1'] = "Дате обновления тем";
$L['plu_frm_res_sort2'] = "Дате создания тем";
$L['plu_frm_res_sort3'] = "Названию тем";
$L['plu_frm_res_sort4'] = "Числу ответов";
$L['plu_frm_res_sort5'] = "Числу просмотров";
$L['plu_frm_search_names'] = "Поиск в названиях тем";
$L['plu_frm_search_post'] = "Поиск в теле сообщений";
$L['plu_frm_search_answ'] = "Только темы с ответами";

// Параметры - Страницы.
$L['plu_pag_set_sec'] = "Выберите разделы сайта";
$L['plu_pag_res_sort1'] = "Дате публикации";
$L['plu_pag_res_sort2'] = "Названию";
$L['plu_pag_res_sort3'] = "Популярности";
$L['plu_pag_search_names'] = "Поиск в названиях публикаций";
$L['plu_pag_search_desc'] = "Поиск в описании публикаций";
$L['plu_pag_search_text'] = "Поиск в самих публикациях";
$L['plu_pag_search_file'] = "Публикации только с файлами";

// Ошибки.
$L['plu_querytooshort'] = "Поисковый запрос слишком короткий.";
$L['plu_toomanywords'] = "Слишком много слов, должно быть не больше";
$L['plu_noneresult'] = "Ничего не найдено, попробуйте упростить запрос.";

// Результаты.
$L['plu_result'] = "Результаты поиска";
$L['plu_found'] = "Найдено";
$L['plu_match'] = "совпадений";
$L['plu_section'] = "Раздел";
$L['plu_last_date'] = "Дата обновления";
?>