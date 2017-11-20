<?php
/**
 * Russian Language File for the Polls Module (polls.ru.lang.php)
 *
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Admin
 */

$L['adm_help_polls'] = 'При создании опроса пустые ответы не учитываются и автоматически удаляются.<br />После создания опроса не рекомендуется редактировать его, так это может повлиять на ход голосования.';
$L['adm_polls_forumpolls'] = 'Опросы в форумах (последние вверху) :';
$L['adm_polls_indexpolls'] = 'Опросы на главной (последние вверху) :';
$L['adm_polls_msg916_bump'] = 'Опрос поднят!';
$L['adm_polls_msg916_deleted'] = 'Опрос удален!';
$L['adm_polls_msg916_reset'] = 'Результаты опроса обнулены!';
$L['adm_polls_on_page'] = 'на странице';
$L['adm_polls_polltopic'] = 'Опрос';
$L['adm_polls_nopolls'] = 'Опросов нет';
$L['adm_polls_bump'] = 'Поднять';

$L['poll'] = 'Опрос';
$L['polls_alreadyvoted'] = 'Вы уже проголосовали в этом опросе';
$L['polls_created'] = 'Опрос создан';
$L['polls_error_count'] = 'Количество вариантов ответа должно быть не менее двух';
$L['polls_error_title'] = 'Название опроса слишком короткое или отсутствует';
$L['polls_locked'] = 'Опрос заблокирован'; // New in 1.0.0
$L['polls_multiple'] = 'Разрешить выбор двух и более вариантов';
$L['polls_notyetvoted'] = 'Вы можете голосовать здесь';
$L['polls_registeredonly'] = 'Только зарегистрированные пользователи могут голосовать.';
$L['polls_since'] = 'с';
$L['polls_updated'] = 'Опрос обновлен';
$L['polls_viewarchives'] = 'Все опросы';
$L['polls_viewresults'] = 'Результаты';
$L['polls_Vote'] = 'Голосовать';
$L['polls_votecasted'] = 'Выполнено, ваш голос записан';
$L['polls_votes'] = 'голосов';

/**
 * Plugin Config
 */

$L['cfg_del_dup_options'] = 'Принудительное удаление дублирующихся ответов';
$L['cfg_del_dup_options_hint'] = 'Удалять дублирующийся ответ даже если он уже внесен в базу данных';
$L['cfg_ip_id_polls'] = 'Способ запоминания голоса';
$L['cfg_ip_id_polls_hint'] = ' ';
$L['cfg_max_options_polls'] = 'Максимальное количество вариантов ответа';
$L['cfg_max_options_polls_hint'] = 'Лишние варианты будут автоматически удаляться при превышении лимита';
$L['cfg_maxpolls'] = 'Количество отображаемых опросов на главной';
$L['cfg_mode'] = 'Режим отображения опросов на главной';
$L['cfg_mode_hint'] = '&laquo;Последние&raquo; &mdash; отображение последнего опроса (опросов)<br />&laquo;Случайные&raquo; &mdash; отображение случайного опроса (опросов)';
$L['cfg_mode_params'] = 'Последние,Случайные';

$L['info_desc'] = 'Размещение и управление опросами на сайте и форуме';

/**
 * Moved from theme.lang
 */

$L['polls_voterssince'] = 'проголосовавших с';
$L['polls_allpolls'] = 'Все опросы';

/**
 * For meta tags
 */
$L['polls_id_stat_result'] = 'Статистика результатов онлайн опроса'; 
$L['polls_id_stat_formed'] = 'формируется на сайте '.$sys['domain'].' динамически с учетом ответов посетителей.';
$L['polls_meta_desc'] = 'Все онлайн опросы веб сайта '.$sys['domain'].' списком. С учетом ответов посетителей формируется и выводится общая статистика в процентном соотношении.';
