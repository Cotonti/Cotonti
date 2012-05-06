<?php
/**
 * Russian Language File for Comments Plugin
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_commentsize'] = array('Макс. размер комментария, байт', '0 - без ограничения размера');
$L['cfg_countcomments'] = array('Считать комментарии', 'Показывать количество комментариев рядом с иконкой');
$L['cfg_enable_comments'] = array('Включить комментарии');
$L['cfg_expand_comments'] = array('Открыть комментарии', 'По умолчанию показывать комментарии на странице');
$L['cfg_mail'] = array('Отсылать email-уведомления администратору о новых комментариях');
$L['cfg_markitup'] = array('Использовать markitup');
$L['cfg_markup'] = array('Включить разметку');
$L['cfg_maxcommentsperpage'] = array('Макс. количество комментариев на странице', ' ');
$L['cfg_minsize'] = array('Мин. длина комментария');
$L['cfg_order'] = array('Порядок сортировки', 'Хронологический или самые последние вверху');
$L['cfg_order_params'] = array('Хронологический', 'Cамые последние вверху');
$L['cfg_parsebbcodecom'] = array('Парсинг BBCode в комментариях', ' ');
$L['cfg_parsesmiliescom'] = array('Парсинг смайликов в комментариях', ' ');
$L['cfg_rss_commentmaxsymbols'] = array('Макс. количество символов для комментариев', 'По умолчанию отключено');
$L['cfg_time'] = array('Пользователи могут редактировать комментарии в течение', 'минут');

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
$L['plu_comlive2'] = 'оставил комментарий:';
$L['plu_comlive3'] = 'отредактировал свой комментарий:';
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

$Ls['Comments'] = array('комментарий','комментария','комментариев');

/**
 * Comedit
 */

$L['plu_title'] = 'Редактирование комментария';

?>