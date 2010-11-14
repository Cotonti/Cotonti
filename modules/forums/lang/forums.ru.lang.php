<?php
/**
 * Russian Language File for the Forums Module (forums.ru.lang.php)
 *
 * @package forums
 * @version 0.9.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Forums Config
 */

$L['cfg_antibumpforums'] = array('Защита от &laquo;поднятия&raquo; сообщений (anti-bump)', 'Запретить пользователям создавать 2 сообщения подряд в теме');
$L['cfg_hideprivateforums'] = array('Скрывать приватные форумы', ' ');
$L['cfg_hottopictrigger'] = array('Количество сообщений для &laquo;популярной&raquo; темы', ' ');
$L['cfg_maxtopicsperpage'] = array('Максимальное количество тем на странице', ' ');
$L['cfg_mergeforumposts'] = array('Объединять сообщения', 'Объединять идущие подряд сообщения одного пользователя (антибамповая защита должна быть отключена)');
$L['cfg_mergetimeout'] = array('Время ожидания для объединения сообщений', 'Последовательно опубликованные сообщения одного пользователя не будут объединены при превышении указанного времени (в часах), требует включения установки \'Объединять сообщения\' (0 для отключения)');
$L['cfg_maxpostsperpage'] = array('Макс. количество сообщений на странице', ' ');

$L['cfg_allowusertext'] = array('Показывать подписи');
$L['cfg_allowbbcodes'] = array('Разрешить BBCodes');
$L['cfg_allowsmilies'] = array('Разрешить смайлики');
$L['cfg_allowprvtopics'] = array('Разрешить приватные темы');
$L['cfg_allowviewers'] = array('Включить отображение просматривающих раздел');
$L['cfg_allowpolls'] = array('Включить опросы');
$L['cfg_countposts'] = array('Считать сообщения');
$L['cfg_autoprune'] = array('Автоочистка тем через * дней');
$L['cfg_defstate'] = array('Проверка счетчиков');

/**
 * Forums Administration
 */

$L['forums_defstate'] = 'По умолчанию';
$L['forums_defstate_0'] = 'Свернут';
$L['forums_defstate_1'] = 'Развернут';

/**
 * Main
 */

$L['forum_topic'] = 'Тема';
$L['forum_topics'] = 'Темы';

$L['forums_antibump'] = 'Активирована система защиты от поднятия тем: вы не можете создавать несколько сообщений подряд';
$L['forums_keepmovedlink'] = 'Оставить ссылку в старом разделе';
$L['forums_markallasread'] = 'Отметить все сообщения как прочитанные';
$L['forums_mergetime'] = 'Добавлено %1$s спустя:';
$L['forums_messagetooshort'] = 'Сообщение слишком короткое';
$L['forums_newtopic'] = 'Новая тема';
$L['forums_newpoll'] = 'Новый опрос';
$L['forums_titletooshort'] = 'Название темы слишком короткое или отсутствует';
$L['forums_topiclocked'] = 'Тема заблокирована, новые сообщения запрещены';
$L['forums_topicoptions'] = 'Опции темы';
$L['forums_updatedby'] = '<br /><em>Отредактировано: %1$s (%2$s, %3$s назад)</em>';
$L['forums_postedby'] = 'Опубликовал(а)';

$L['forums_privatetopic1'] = '&laquo;Частная&raquo; тема';
$L['forums_privatetopic2'] = 'просмотр и ответы в теме будут доступны только модераторам форумов и вам как автору темы';
$L['forums_privatetopic'] = 'Это частная тема: доступ к просмотру и ответам только для модераторов и автора темы.';

$L['forums_searchinforums'] = 'Поиск в форумах';
$L['forums_markasread'] = 'Отметить все как прочитанные';
$L['forums_foldall'] = 'Свернуть все';
$L['forums_unfoldall'] = 'Развернуть все';

$L['forums_nonewposts'] = 'Нет новых сообщений';
$L['forums_newposts'] = 'Есть новые сообщения';
$L['forums_nonewpostspopular'] = 'Популярная (нет новых)';
$L['forums_newpostspopular'] = 'Популярная (есть новые)';
$L['forums_sticky'] = 'Тема закреплена (нет новых)';
$L['forums_newpostssticky'] = 'Тема закреплена (есть новые)';
$L['forums_locked'] = 'Тема закрыта (нет новых)';
$L['forums_newpostslocked'] = 'Тема закрыта (есть новые)';
$L['forums_announcment'] = 'Обьявление';
$L['forums_newannouncment'] = 'Новые обьявления';
$L['forums_movedoutofthissection'] = 'Перенесена в другой раздел';

$L['forums_explain1'] = 'Поднять тему (до обновления другой темы)';
$L['forums_explain2'] = 'Заблокировать тему (запретить новые сообщения)';
$L['forums_explain3'] = 'Закрепить тему (до сброса статуса в значение по умолчанию)';
$L['forums_explain4'] = 'Пометить тему как объявление';
$L['forums_explain5'] = 'Частная тема (доступ только для модераторов и автора темы)';
$L['forums_explain6'] = 'Сбросить статус в значение по умолчанию';
$L['forums_explain7'] = 'Удалить тему';

/**
 * Unused?
 */

$L['adm_help_forums'] = 'Недоступно';
$L['adm_help_forums_structure'] = 'Недоступно';
$L['forums_polltooshort'] = 'Количество вариантов ответа должно быть не менее двух';
$L['for_onlinestatus0'] = 'не в сети';
$L['for_onlinestatus1'] = 'в сети';

?>