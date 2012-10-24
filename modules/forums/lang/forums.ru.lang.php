<?php
/**
 * Russian Language File for the Forums Module (forums.ru.lang.php)
 *
 * @package forums
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Forums Config
 */

$L['cfg_antibumpforums'] = array('Защита от &laquo;поднятия&raquo; сообщений (anti-bump)', 'Запретить пользователям создавать 2 сообщения подряд в теме');
$L['cfg_hideprivateforums'] = array('Скрывать приватные форумы', ' ');
$L['cfg_hottopictrigger'] = array('Количество сообщений для &laquo;популярной&raquo; темы', ' ');
$L['cfg_maxpostsperpage'] = array('Макс. количество сообщений на странице', ' ');
$L['cfg_maxtopicsperpage'] = array('Максимальное количество тем на странице', ' ');
$L['cfg_mergeforumposts'] = array('Объединять сообщения', 'Объединять идущие подряд сообщения одного пользователя (антибамповая защита должна быть отключена)');
$L['cfg_mergetimeout'] = array('Время ожидания для объединения сообщений', 'Последовательно опубликованные сообщения одного пользователя не будут объединены при превышении указанного времени (в часах), требует включения установки \'Объединять сообщения\' (0 для отключения)');
$L['cfg_minpostlength'] = array('Мин. длина сообщения', ' ');
$L['cfg_mintitlelength'] = array('Мин. длина заголовка темы', ' ');
$L['cfg_title_posts'] = array('Формат заголовка темы форума', 'Опции: {FORUM}, {SECTION}, {TITLE}');
$L['cfg_title_topics'] = array('Формат заголовка раздела форума', 'Опции: {FORUM}, {SECTION}');
$L['cfg_enablereplyform'] = array('Отображать форму ответа на всех страницах', '');
$L['cfg_edittimeout'] = array('Тайм-аут редактирования', 'Не позволяет пользователям редактировать или удалять собственные сообщения по истечении тайм-аута (в часах, 0 отключает тайм-аут)');

$L['cfg_allowusertext'] = array('Показывать подписи');
$L['cfg_allowbbcodes'] = array('Разрешить BBCodes');
$L['cfg_allowsmilies'] = array('Разрешить смайлики');
$L['cfg_allowprvtopics'] = array('Разрешить приватные темы');
$L['cfg_allowviewers'] = array('Включить отображение просматривающих раздел');
$L['cfg_allowpolls'] = array('Включить опросы');
$L['cfg_countposts'] = array('Считать сообщения');
$L['cfg_autoprune'] = array('Автоочистка тем через * дней');
$L['cfg_defstate'] = array('По умолчанию');
$L['cfg_defstate_params'] = array('Свернут', 'Развернут');

$L['info_desc'] = 'Модуль форумов для сайтов с сообществом или поддержкой';

/**
 * Main
 */

$L['forums_post'] = 'Сообщение';
$L['forums_posts'] = 'Сообщения';
$L['forums_topic'] = 'Тема';
$L['forums_topics'] = 'Темы';

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
$L['forums_edittimeoutnote'] = 'Время для редактирования или удаления собственного сообщения: ';

$L['forums_privatetopic1'] = '&laquo;Частная&raquo; тема';
$L['forums_privatetopic2'] = 'просмотр и ответы в теме будут доступны только модераторам форумов и вам как автору темы';
$L['forums_privatetopic'] = 'Это частная тема: доступ к просмотру и ответам только для модераторов и автора темы.';

$L['forums_searchinforums'] = 'Поиск в форумах';
$L['forums_markasread'] = 'Отметить все как прочитанные';
$L['forums_foldall'] = 'Свернуть все';
$L['forums_unfoldall'] = 'Развернуть все';
$L['forums_viewers'] = 'Просматривают';

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

$L['forums_announcement'] = 'Объявление';
$L['forums_bump'] = 'Поднять';
$L['forums_makesticky'] = 'Прикрепить тему';
$L['forums_private'] = 'Приватная тема';

$L['forums_explainbump'] = 'Поднять тему (до обновления другой темы)';
$L['forums_explainlock'] = 'Заблокировать тему (запретить новые сообщения)';
$L['forums_explainsticky'] = 'Закрепить тему (до сброса статуса в значение по умолчанию)';
$L['forums_explainannounce'] = 'Пометить тему как объявление';
$L['forums_explainprivate'] = 'Частная тема (доступ только для модераторов и автора темы)';
$L['forums_explaindefault'] = 'Сбросить статус в значение по умолчанию';
$L['forums_explaindelete'] = 'Удалить тему';

$L['forums_confirm_delete_topic'] = 'Вы действительно хотите удалить эту тему?';
$L['forums_confirm_delete_post'] = 'Вы действительно хотите удалить это сообщение?';

/**
 * Unused?
 */

$L['forums_polltooshort'] = 'Количество вариантов ответа должно быть не менее двух';
$L['for_onlinestatus0'] = 'не в сети';
$L['for_onlinestatus1'] = 'в сети';

?>