<?PHP
/**
 * Russian Language File for Search Plugin
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Neocrome, Spartan, Boss
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

// Настройки плагина.
$L['cfg_maxwords']= array('Максимум поисковых слов');
$L['cfg_maxsigns']= array('Максимум знаков в поиске');
$L['cfg_maxitems']= array('Максимум результатов в обычном поиске');
$L['cfg_maxitems_ext']= array('Максимум результатов в расширенном поиске');
$L['cfg_showtext']= array('Отображение текста в результатах обычного поиска');
$L['cfg_showtext_ext']= array('Отображение текста в результатах расширенного поиска');

// Общие - залоговок, инфо, запрос.
$L['plu_title_all'] = "Поиск по сайту";
$L['plu_subtitle_all'] = "Вы можете конкретизировать поиск, отметив ниже лишь необходимые разделы и параметры. Обратите внимание, что общий поиск предоставляет не полные возможности. Выберите поиск по форуму или публикациям, чтобы получить доступ к дополнительным параметрам.";
$L['plu_search_req'] = "Запрос";
$L['plu_search_key'] = "Найти";
$L['plu_search_example'] = "Например, aver 307 vista";

// Дополнения в заголовок.
$L['plu_title_frmtab'] = "Форум";
$L['plu_title_pagtab'] = "Публикации";
$L['plu_title_usetab'] = "Пользователи";

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
$L['plu_need_dd'] = 'дд';
$L['plu_need_mm'] = 'мм';
$L['plu_need_yy'] = 'гггг';

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
$L['plu_notseltopmes'] = "Вы не выбрали область поиска для форума в дополнительных параметрах.";
$L['plu_notseloption'] = "Вы не выбрали область поиска для публикаций в дополнительных параметрах.";
$L['plu_noneresult'] = "Ничего не найдено, попробуйте упростить запрос.";

// Результаты.
$L['plu_result'] = "Результаты поиска";
$L['plu_found'] = "Найдено";
$L['plu_moreres'] = "более";
$L['plu_match'] = "совпадений";
$L['plu_section'] = "Раздел";
$L['plu_last_date'] = "Дата обновления";
?>