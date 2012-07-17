<?php
/**
 * Russian Language File for RecentItems Plugin
 *
 * @package recentitems
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Info
 */

$L['info_desc'] = 'Вывод обновленного контента (страницы, темы) на главной странице сайта';

/**
 * Plugin Config
 */

$L['cfg_recentpages'] = array('Отображать новые страницы на главной странице');
$L['cfg_maxpages'] = array('Количество новых страниц на главной странице');
$L['cfg_recentforums'] = array('Отображать новые сообщения на форумах на главной странице');
$L['cfg_maxtopics'] = array('Количество новых сообщений на форумах на главной');
$L['cfg_newpages'] = array('Отображать новые страницы на странице "Новое на сайте"');
$L['cfg_newforums'] = array('Отображать новые сообщения на форумах на странице "Новое на сайте"');
$L['cfg_newadditional'] = array('Включить поддержку дополнительных модулей на странице "Новое на сайте"');
$L['cfg_itemsperpage'] = array('Количество элементов на странице "новое на сайте"');
$L['cfg_rightscan'] = array('Включить проверку доступа к элементам', 'Используйте, если на сайте большое количество категорий страниц или разделов форума с разделением прав');
$L['cfg_recentpagestitle'] = array('Автоматическое ограничение длины заголовка новых страниц на главной', 'По умолчанию отключено.');
$L['cfg_recentpagestext'] = array('Автоматическое ограничение длины текста новых страниц на главной', 'По умолчанию отключено.');
$L['cfg_recentforumstitle'] = array('Автоматическое ограничение длины заголовка новых тем на форуме на главной', 'По умолчанию отключено.');
$L['cfg_newpagestext'] = array('Автоматическое ограничение длины текста страниц на странице "Новое на сайте"', 'По умолчанию отключено.');
$L['cfg_whitelist'] = array('Белый список категорий', 'По одному коду на строку. Если не пуст, только эти ветви будут отображены.');
$L['cfg_blacklist'] = array('Черный список категорий', 'По одному коду на строку. Если не пуст, только эти ветви будут исключены из вывода.');
$L['cfg_cache_ttl'] = array('Время жизни кеша в секундах', '0 - кеш отключен');

/**
 * Plugin Body
 */

$L['recentitems_title'] = 'Новое на сайте';

$L['recentitems_forums'] = 'Новое на форумах';
$L['recentitems_pages'] = 'Новое в разделах';

$L['recentitems_nonewpages'] = 'Нет совых страниц';
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
$L['recentitems_posts_sticky_locked'] = 'Обьявление';
$L['recentitems_posts_new_sticky_locked'] = 'Новые обьявления';
$L['recentitems_posts_moved'] = 'Перенесена в другой раздел';

?>