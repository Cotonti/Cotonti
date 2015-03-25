<?php
/**
 * Russian Language File for Comments Plugin
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_commentsize'] = 'Макс. размер комментария, байт';
$L['cfg_commentsize_hint'] = '0 - без ограничения размера';
$L['cfg_countcomments'] = 'Считать комментарии';
$L['cfg_countcomments_hint'] = 'Показывать количество комментариев рядом с иконкой';
$L['cfg_enable_comments'] = 'Включить комментарии';
$L['cfg_expand_comments'] = 'Открыть комментарии';
$L['cfg_expand_comments_hint'] = 'По умолчанию показывать комментарии на странице';
$L['cfg_mail'] = 'Отсылать email-уведомления администратору о новых комментариях';
$L['cfg_markitup'] = 'Использовать markitup';
$L['cfg_markup'] = 'Включить разметку';
$L['cfg_maxcommentsperpage'] = 'Макс. количество комментариев на странице';
$L['cfg_maxcommentsperpage_hint'] = ' ';
$L['cfg_minsize'] = 'Мин. длина комментария';
$L['cfg_order'] = 'Порядок сортировки';
$L['cfg_order_hint'] = 'Хронологический или самые последние вверху';
$L['cfg_order_params'] = 'Хронологический,Cамые последние вверху';
$L['cfg_parsebbcodecom'] = 'Парсинг BBCode в комментариях';
$L['cfg_parsebbcodecom_hint'] = ' ';
$L['cfg_parsesmiliescom'] = 'Парсинг смайликов в комментариях';
$L['cfg_parsesmiliescom_hint'] = ' ';
$L['cfg_rss_commentmaxsymbols'] = 'Макс. количество символов для комментариев';
$L['cfg_rss_commentmaxsymbols_hint'] = 'По умолчанию отключено';
$L['cfg_time'] = 'Пользователи могут редактировать комментарии в течение';
$L['cfg_time_hint'] = 'минут';

$L['info_desc'] = 'Комментарии с API и интеграцией со страницами, списками, опросами, RSS и другими расширениями';

/**
 * Plugin Body
 */

$L['comments_comment'] = 'Комментарий';
$L['comments_comments'] = 'Комментарии';
$L['comments_confirm_delete'] = 'Вы действительно хотите удалить этот комментарий?';
$L['Newcomment'] = 'Новый комментарий';

$L['comm_on_page'] = 'на странице';

$L['com_closed'] = 'Для этого элемента нельзя добавлять комментарии';
$L['com_commentadded'] = 'Комментарий добавлен';
$L['com_commenttoolong'] = 'Комментарий слишком длинный';
$L['com_commenttooshort'] = 'Комментарий слишком короткий либо отсутствует';
$L['com_nocommentsyet'] = 'Комментарии отсутствуют';
$L['com_authortooshort'] = 'Имя автора слишком короткое';
$L['com_regonly'] = 'Добавление комментариев доступно только зарегистрированным пользователям';

$L['plu_comgup'] = ' осталось';
$L['com_edithint'] = 'Для редактирования комментария осталось {$time}';

$L['plu_comlive'] = 'Новый коментарий на сайте ';
$L['plu_comlive1'] = 'Отредактирован коментарий на сайте ';
$L['plu_comlive2'] = 'оставил комментарий: ';
$L['plu_comlive3'] = 'отредактировал свой комментарий: ';
$L['rss_comments'] = 'Комментарии для';
$L['rss_comment_of_user'] = 'Комментарий пользователя';
$L['rss_comments_item_desc'] = 'Лента комментариев страницы';
$L['rss_original'] = 'Комментируемая страница';

/**
 * Admin Section
 */

$L['home_newcomments'] = 'Новые комментарии';
$L['core_comments'] = &$L['Comments'];
$L['adm_comm_already_del'] = 'Комментарий удален';

/**
 * cot_declension arrays
 */

$Ls['Comments'] = "комментарий,комментария,комментариев";

/**
 * Comedit
 */

$L['plu_title'] = 'Редактирование комментария';
