<?php
/**
 * Russian Language File for the Forums Module (forums.ru.lang.php)
 *
 * @package forums
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Admin Forums Section
 */

$L['adm_forum_structure'] = 'Структура форумов (разделы)';
$L['adm_forum_emptytitle'] = 'Ошибка: пустой заголовок';	// New in 0.1.0

/**
  * Forums Section
  * Structure Subsection
 */

$L['adm_defstate'] = 'По умолчанию';
$L['adm_defstate_0'] = 'Свернут';
$L['adm_defstate_1'] = 'Развернут';

/**
  * Forums Section
  * Forum Edit Subsection
 */

$L['adm_forums_master'] = 'Родительский раздел';	// New in 0.0.1
$L['adm_diplaysignatures'] = 'Показывать подписи';
$L['adm_enablebbcodes'] = 'Разрешить BBCodes';
$L['adm_enablesmilies'] = 'Разрешить смайлики';
$L['adm_enableprvtopics'] = 'Разрешить приватные темы';
$L['adm_enableviewers'] = 'Включить отображение просматривающих раздел';  // New in 0.0.2
$L['adm_enablepolls'] = 'Включить опросы';  // New in 0.0.2
$L['adm_countposts'] = 'Считать сообщения';
$L['adm_autoprune'] = 'Автоочистка тем через * дней';
$L['adm_postcounters'] = 'Проверка счетчиков';

$L['adm_help_forums'] = 'Недоступно';
$L['adm_help_forums_structure'] = 'Недоступно';

/**
 * Config Section
 * Forums Subsection
 */

$L['cfg_antibumpforums'] = array('Защита от &laquo;поднятия&raquo; сообщений (anti-bump)', 'Запретить пользователям создавать 2 сообщения подряд в теме');
$L['cfg_hideprivateforums'] = array('Скрывать приватные форумы', ' ');
$L['cfg_hottopictrigger'] = array('Количество сообщений для &laquo;популярной&raquo; темы', ' ');
$L['cfg_maxtopicsperpage'] = array('Максимальное количество тем на странице', ' ');
$L['cfg_mergeforumposts'] = array('Объединять сообщения', 'Объединять идущие подряд сообщения одного пользователя (антибамповая защита должна быть отключена)');	// New in 0.1.0
$L['cfg_mergetimeout'] = array('Время ожидания для объединения сообщений', 'Последовательно опубликованные сообщения одного пользователя не будут объединены при превышении указанного времени (в часах), требует включения установки \'Объединять сообщения\' (0 для отключения)');	// New in 0.1.0
$L['cfg_maxpostsperpage'] = array('Макс. количество сообщений на странице', ' '); // New in 0.0.6

/**
 * Main
 */

$L['for_antibump'] = 'Активирована система защиты от поднятия тем: вы не можете создавать несколько сообщений подряд';	// 0.0.2
$L['for_keepmovedlink'] = 'Оставить ссылку в старом разделе'; // 0.6.6
$L['for_markallasread'] = 'Отметить все сообщения как прочитанные';
$L['for_mergetime'] = 'Добавлено %1$s спустя:'; // 0.0.6
$L['for_messagetooshort'] = 'Сообщение слишком короткое';	// 0.0.2
$L['for_newtopic'] = 'Новая тема';
$L['for_polltooshort'] = 'Количество вариантов ответа должно быть не менее двух';	// 0.0.2
$L['for_titletooshort'] = 'Название темы слишком короткое или отсутствует';	// 0.0.2
$L['for_updatedby'] = '<br /><em>Отредактировано: %1$s (%2$s, %3$s назад)</em>';

$L['for_postedby'] = 'Опубликовал(а)';

/**
 * Moved from theme.lang
 */

$L['for_privatetopic1'] = '&laquo;Частная&raquo; тема';
$L['for_privatetopic2'] = 'просмотр и ответы в теме будут доступны только модераторам форумов и вам как автору темы';

$L['for_privatetopic'] = 'Это частная тема: доступ к просмотру и ответам только для модераторов и автора темы.';
$L['for_onlinestatus0'] = 'не в сети';
$L['for_onlinestatus1'] = 'в сети';

$L['for_searchinforums'] = 'Поиск в форумах';
$L['for_markasread'] = 'Отметить все как прочитанные';
$L['for_foldall'] = 'Свернуть все';
$L['for_unfoldall'] = 'Развернуть все';

$L['for_newpoll'] = 'Новый опрос';
$L['for_viewers'] = 'Просматривают';
$L['for_nonewposts'] = 'Нет новых сообщений';
$L['for_newposts'] = 'Есть новые сообщения';
$L['for_nonewpostspopular'] = 'Популярная (нет новых)';
$L['for_newpostspopular'] = 'Популярная (есть новые)';
$L['for_sticky'] = 'Тема закреплена (нет новых)';
$L['for_newpostssticky'] = 'Тема закреплена (есть новые)';
$L['for_locked'] = 'Тема закрыта (нет новых)';
$L['for_newpostslocked'] = 'Тема закрыта (есть новые)';
$L['for_announcment'] = 'Обьявление';
$L['for_newannouncment'] = 'Новые обьявления';
$L['for_movedoutofthissection'] = 'Перенесена в другой раздел';

?>