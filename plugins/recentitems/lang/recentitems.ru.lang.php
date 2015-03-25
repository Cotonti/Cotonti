<?php
/**
 * Russian Language File for RecentItems Plugin
 *
 * @package RecentItems
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Info
 */

$L['info_desc'] = 'Вывод обновленного контента (страницы, темы) на главной странице сайта';

/**
 * Plugin Config
 */

$L['cfg_recentpages'] = 'Отображать новые страницы на главной странице';
$L['cfg_maxpages'] = 'Количество новых страниц на главной странице';
$L['cfg_recentforums'] = 'Отображать новые сообщения на форумах на главной странице';
$L['cfg_maxtopics'] = 'Количество новых сообщений на форумах на главной';
$L['cfg_newpages'] = 'Отображать новые страницы на странице "Новое на сайте"';
$L['cfg_newforums'] = 'Отображать новые сообщения на форумах на странице "Новое на сайте"';
$L['cfg_newadditional'] = 'Включить поддержку дополнительных модулей на странице "Новое на сайте"';
$L['cfg_itemsperpage'] = 'Количество элементов на странице "новое на сайте"';
$L['cfg_rightscan'] = 'Включить проверку доступа к элементам';
$L['cfg_rightscan_hint'] = 'Используйте, если на сайте большое количество категорий страниц или разделов форума с разделением прав';
$L['cfg_recentpagestitle'] = 'Автоматическое ограничение длины заголовка новых страниц на главной';
$L['cfg_recentpagestitle_hint'] = 'По умолчанию отключено.';
$L['cfg_recentpagestext'] = 'Автоматическое ограничение длины текста новых страниц на главной';
$L['cfg_recentpagestext_hint'] = 'По умолчанию отключено.';
$L['cfg_recentforumstitle'] = 'Автоматическое ограничение длины заголовка новых тем на форуме на главной';
$L['cfg_recentforumstitle_hint'] = 'По умолчанию отключено.';
$L['cfg_newpagestext'] = 'Автоматическое ограничение длины текста страниц на странице "Новое на сайте"';
$L['cfg_newpagestext_hint'] = 'По умолчанию отключено.';
$L['cfg_whitelist'] = 'Белый список категорий';
$L['cfg_whitelist_hint'] = 'По одному коду на строку. Если не пуст, только эти ветви будут отображены.';
$L['cfg_blacklist'] = 'Черный список категорий';
$L['cfg_blacklist_hint'] = 'По одному коду на строку. Если не пуст, только эти ветви будут исключены из вывода.';
$L['cfg_cache_ttl'] = 'Время жизни кеша в секундах';
$L['cfg_cache_ttl_hint'] = '0 - кеш отключен';

/**
 * Plugin Body
 */

$L['recentitems_title'] = 'Новое на сайте';

$L['recentitems_forums'] = 'Новое на форумах';
$L['recentitems_pages'] = 'Новое в разделах';

$L['recentitems_nonewpages'] = 'Нет новых страниц';
$L['recentitems_nonewposts'] = 'Нет новых сообщений';

$L['recentitems_shownew'] = 'Показать новое на сайте';
$L['recentitems_fromlastvisit'] = 'с последнего моего посещения';
$L['recentitems_1day'] = 'за сутки';
$L['recentitems_2days'] = 'за 2 суток';
$L['recentitems_3days'] = 'за 3 суток';
$L['recentitems_1week'] = 'за неделю';
$L['recentitems_2weeks'] = 'за 2 недели';
$L['recentitems_1month'] = 'за месяц';

$L['recentitems_posts'] = 'Нет новых сообщений';
$L['recentitems_posts_new'] = 'Есть новые сообщения';
$L['recentitems_posts_hot'] = 'Популярная (нет новых)';
$L['recentitems_posts_new_hot'] = 'Популярная (есть новые)';
$L['recentitems_posts_sticky'] = 'Тема закреплена (нет новых)';
$L['recentitems_posts_new_sticky'] = 'Тема закреплена (есть новые)';
$L['recentitems_posts_locked'] = 'Тема закрыта (нет новых)';
$L['recentitems_posts_new_locked'] = 'Тема закрыта (есть новые)';
$L['recentitems_posts_sticky_locked'] = 'Объявление';
$L['recentitems_posts_new_sticky_locked'] = 'Новые объявления';
$L['recentitems_posts_moved'] = 'Перенесена в другой раздел';
