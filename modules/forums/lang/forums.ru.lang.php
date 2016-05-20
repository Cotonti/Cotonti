<?php
/**
 * Russian Language File for the Forums Module (forums.ru.lang.php)
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Forums Config
 */

$L['cfg_antibumpforums'] = 'Защита от &laquo;поднятия&raquo; сообщений (anti-bump)';
$L['cfg_antibumpforums_hint'] = 'Запретить пользователям создавать 2 сообщения подряд в теме';
$L['cfg_hideprivateforums'] = 'Скрывать приватные форумы';
$L['cfg_hideprivateforums_hint'] = ' ';
$L['cfg_hottopictrigger'] = 'Количество сообщений для &laquo;популярной&raquo; темы';
$L['cfg_hottopictrigger_hint'] = ' ';
$L['cfg_maxpostsperpage'] = 'Макс. количество сообщений на странице';
$L['cfg_maxpostsperpage_hint'] = ' ';
$L['cfg_maxtopicsperpage'] = 'Максимальное количество тем на странице';
$L['cfg_maxtopicsperpage_hint'] = ' ';
$L['cfg_mergeforumposts'] = 'Объединять сообщения';
$L['cfg_mergeforumposts_hint'] = 'Объединять идущие подряд сообщения одного пользователя (антибамповая защита должна быть отключена)';
$L['cfg_mergetimeout'] = 'Время ожидания для объединения сообщений';
$L['cfg_mergetimeout_hint'] = 'Последовательно опубликованные сообщения одного пользователя не будут объединены при превышении указанного времени (в часах), требует включения установки \'Объединять сообщения\' (0 для отключения)';
$L['cfg_minpostlength'] = 'Мин. длина сообщения';
$L['cfg_minpostlength_hint'] = ' ';
$L['cfg_mintitlelength'] = 'Мин. длина заголовка темы';
$L['cfg_mintitlelength_hint'] = ' ';
$L['cfg_title_posts'] = 'Формат заголовка темы форума';
$L['cfg_title_posts_hint'] = 'Опции: {FORUM}, {SECTION}, {TITLE}';
$L['cfg_title_topics'] = 'Формат заголовка раздела форума';
$L['cfg_title_topics_hint'] = 'Опции: {FORUM}, {SECTION}';
$L['cfg_enablereplyform'] = 'Отображать форму ответа на всех страницах';
$L['cfg_enablereplyform_hint'] = '';
$L['cfg_edittimeout'] = 'Тайм-аут редактирования';
$L['cfg_edittimeout_hint'] = 'Не позволяет пользователям редактировать или удалять собственные сообщения по истечении тайм-аута (в часах, 0 отключает тайм-аут)';
$L['cfg_minimaxieditor'] = 'Выбор конфигурации визуального редактора';
$L['cfg_minimaxieditor_params'] = 'Минимальный набор кнопок,Стандартный набор кнопок,Расширенный набор кнопок'; 

$L['cfg_allowusertext'] = 'Показывать подписи';
$L['cfg_allowbbcodes'] = 'Разрешить BBCodes';
$L['cfg_allowsmilies'] = 'Разрешить смайлики';
$L['cfg_allowprvtopics'] = 'Разрешить приватные темы';
$L['cfg_allowviewers'] = 'Включить отображение просматривающих раздел';
$L['cfg_allowpolls'] = 'Включить опросы';
$L['cfg_countposts'] = 'Считать сообщения';
$L['cfg_autoprune'] = 'Автоочистка тем через * дней';
$L['cfg_defstate'] = 'По умолчанию';
$L['cfg_defstate_params'] = 'Свернут,Развернут';
$L['cfg_keywords'] = 'Ключевые слова';
$L['cfg_metatitle'] = 'Meta-заголовок';
$L['cfg_metadesc'] = 'Meta-описание';

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
$L['forums_updatedby'] = 'Отредактировано: %1$s (%2$s, %3$s назад)';
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
$L['forums_announcment'] = 'Объявление';
$L['forums_newannouncment'] = 'Новые объявления';
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
