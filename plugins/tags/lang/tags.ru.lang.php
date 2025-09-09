<?php
/**
 * Russian Language File for Tags Plugin
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Title & Subtitle
 */

$L['tags_title'] = 'Tags';
$L['tags_description'] = 'Теги &mdash; метки для контента с облаком, поиском и API';

/**
 * Plugin Body
 */
$L['tags_All'] = 'Все теги';
$L['tags_delete_confirm'] = 'К тегу "{$tag}" привязано: {$count}. Подтвердите удаление.';
$L['tags_tag_deleted'] = 'Тег "{$tag}" удален';
$L['tags_Found_in_forums'] = 'Найдено в форумах';
$L['tags_Found_in_pages'] = 'Найдено на страницах';
$L['tags_Keyword'] = 'Ключевое слово';
$L['tags_Keywords'] = 'Ключевые слова';
$L['tags_Orderby'] = 'Сортировка результатов по';
$L['tags_Query_hint'] = 'Несколько тегов, разделённых запятой, означают логическое И между ними. Вы также можете использовать точку с запятой в качестве логического ИЛИ. И имеет высший приоритет над ИЛИ. Вы не можете использовать скобки для группировки условий. Звёздочка (*) внутри тега используется в качестве маски для &quot;подстроки&quot;.';
$L['tags_Search_results'] = 'Результаты поиска';
$L['tags_Search_tags'] = 'Поиск тегов';
$L['tags_Tag_cloud'] = 'Облако тегов';
$L['tags_Tag_cloud_none'] = 'Нет тегов';
$L['tags_tag_edited'] = 'Тег отредактирован';
$L['tags_tag_exists'] = 'Такой тег уже есть';
$L['tags_length'] = 'Длина';
$L['adm_tag_item_area'] = 'Элементы тега';

/**
 * Plugin Config
 */
$L['cfg_forums'] = 'Включить теги для форумов';
$L['cfg_index'] = 'Раздел тегов для главной страницы';
$L['cfg_index_params'] = 'pages: Страницы, forums: Форумы, all: Все';
$L['cfg_limit'] = 'Максимальное количество тегов';
$L['cfg_limit_hint'] = '0 &mdash; неограничено';
$L['cfg_lim_forums'] = 'Лимит количества тегов в облаке на форумах';
$L['cfg_lim_forums_hint'] = '0 &mdash; неограничено';
$L['cfg_lim_index'] = 'Лимит количества тегов в облаке на главной странице';
$L['cfg_lim_index_hint'] = '0 &mdash; неограничено';
$L['cfg_lim_pages'] = 'Лимит количества тегов в облаке на страницах';
$L['cfg_lim_pages_hint'] = '0 &mdash; неограничено';
$L['cfg_more'] = 'Показывать в облаке тегов ссылку &laquo;Все теги&raquo;';
$L['cfg_noindex'] = 'Исключить из индекса поисковых систем';
$L['cfg_order'] = 'Сортировка облака тегов';
$L['cfg_order_params'] = 'Alphabetical: По алфавиту, Frequency: По убыванию частотности, Random: Случайным образом';
$L['cfg_order_hint'] = 'по алфавиту, по убыванию частотности, случайным образом';
$L['cfg_pages'] = 'Включить теги для страниц';
$L['cfg_perpage'] = 'Тегов на странице в облаке всех тегов';
$L['cfg_perpage_hint'] = '0 &mdash; неограничено';
$L['cfg_sort'] = 'Сортировка по умолчанию в результатах поиска по тегам';
$L['cfg_sort_params'] = 'ID: По ID, Title: По заголовку, Date: По дате, Category: По категории';
$L['cfg_title'] = 'Первые буквы тегов прописными';
$L['cfg_translit'] = 'Транслитерировать теги в URL-адресах';
$L['cfg_css'] = 'Использовать CSS стили плагина';
