<?php
/**
 * Russian Language File for Comments Plugin
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$L['comments_title'] = 'Comments';
$L['comments_description'] = 'Комментарии с API и интеграцией со страницами, списками, опросами, RSS и другими расширениями';

/**
 * Plugin Config
 */
$L['cfg_adminHomeCount'] = 'Кол-во последних комментариев на главной странице панели администратора';
$L['cfg_adminHomeCount_hint'] = 'пусто - отключено';
$L['cfg_commentsize'] = 'Макс. размер комментария, байт';
$L['cfg_commentsize_hint'] = '0 - без ограничения размера';
$L['cfg_countcomments'] = 'Считать комментарии';
$L['cfg_countcomments_hint'] = 'Показывать количество комментариев рядом с иконкой';
$L['cfg_enable_comments'] = 'Включить комментарии';
$L['cfg_expand_comments'] = 'Открыть комментарии';
$L['cfg_expand_comments_hint'] = 'По умолчанию показывать комментарии на странице';
$L['cfg_mail'] = 'Отсылать email-уведомления администратору о новых комментариях';
$L['cfg_markup'] = 'Включить разметку';
$L['cfg_maxcommentsperpage'] = 'Макс. количество комментариев на странице';
$L['cfg_maxcommentsperpage_hint'] = ' ';
$L['cfg_minsize'] = 'Мин. длина комментария';
$L['cfg_order'] = 'Порядок сортировки';
$L['cfg_order_hint'] = 'Хронологический или самые последние вверху';
$L['cfg_order_params'] = 'Хронологический,Cамые последние вверху';
$L['cfg_rss_commentMaxSymbols'] = 'Макс. количество символов комментария в RSS ленте';
$L['cfg_rss_commentMaxSymbols_hint'] = 'По умолчанию отключено';
$L['cfg_time'] = 'Пользователи могут редактировать комментарии в течение';
$L['cfg_time_hint'] = 'минут';

/**
 * Plugin Body
 */
$L['comments_added'] = 'Комментарий добавлен';
$L['comments_authorTooShort'] = 'Имя автора слишком короткое';
$L['comments_comment'] = 'Комментарий';
$L['comments_comments'] = 'Комментарии';
$L['comments_commentOn'] = 'Комментарий к';
$L['comments_commentOnCategory'] = 'Комментарий к категории';
$L['comments_commentOnPage'] = 'Комментарий к странице';
$L['comments_commentOnPoll'] = 'Комментарий к опросу';
$L['comments_confirm_delete'] = 'Вы действительно хотите удалить этот комментарий?';
$L['comments_closed'] = 'Для этого элемента нельзя добавлять комментарии';
$L['comments_editTimeExpired'] = 'Время редактирования комментария истекло';
$L['comments_editTitle'] = 'Редактировать {$title}';
$L['comments_deleted'] = 'Комментарий удален';
$L['comments_editHint'] = 'Ваш комментарий будет доступен для редактирования {$time}';
$L['comments_newComment'] = 'Новый комментарий';
$L['comments_noRights'] = 'Вы не можете оставить комментарий';
$L['comments_noYet'] = 'Комментарии отсутствуют';
$L['comments_recent'] = 'Новые комментарии';
$L['comments_registeredOnly'] = 'Добавление комментариев доступно только зарегистрированным пользователям';
$L['comments_saveError'] = 'Ошибка при сохранении комментария';
$L['comments_saved'] = 'Комментарий сохранен';
$L['comments_timeLeft'] = '{$time} осталось';
$L['comments_tooLong'] = 'Комментарий слишком длинный';
$L['comments_tooShort'] = 'Комментарий слишком короткий либо отсутствует';

$L['comments_newCommentNotificationSubject'] = 'Новый комментарий на сайте';
$L['comments_newCommentNotification'] = 'Пользователь {$user} оставил {$commentTo}:<br><br>{$text}<br><br>{$url}';
$L['comments_editedCommentNotificationSubject'] = 'Отредактирован комментарий на сайте';
$L['comments_editedCommentNotification'] = 'Пользователь {$user} отредактировал {$commentTo}:<br><br>{$text}<br><br>{$url}';

$L['comments_rssCommentsOnPage'] = 'Комментарии к странице';
$L['comments_rssForPage'] = 'Лента комментариев страницы';
$L['comments_rssForPages'] = 'Лента комментариев к страницам';
$L['comments_rssFrom'] = 'от';
$L['comments_rssFromUser'] = 'Комментарий от пользователя';
$L['comments_rssOriginal'] = 'Комментируемая страница';

$L['plu_tabs_com'] = 'Комментарии';
$L['plu_area_polls'] = 'Опросы';
$L['plu_area_category'] = 'Разделы';

/**
 * cot_declension arrays
 */
$Ls['Comments'] = "комментарий,комментария,комментариев";

