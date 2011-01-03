<?php
/**
 * Russian Language File for Comments Plugin
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_enable_comments'] = array('Включить комментарии');
$L['cfg_mail'] = array('Отсылать email-уведомления администратору о новых комментариях');
$L['cfg_markitup'] = array('Использовать markitup'); // New in N-0.1.0
$L['cfg_markup'] = array('Включить разметку в комментариях');
$L['cfg_time'] = array('Пользователи могут редактировать комментарии в течение', 'минут');
$L['cfg_rss_commentmaxsymbols'] = array('Макс. количество символов для комментариев', 'По умолчанию отключено'); // New in N-0.7.0
$L['cfg_expand_comments'] = array('Открыть комментарии', 'По умолчанию показывать комментарии на странице'); // New in N-0.0.2
$L['cfg_maxcommentsperpage'] = array('Макс. количество комментариев на странице', ' '); // New in N-0.0.6
$L['cfg_commentsize'] = array('Макс. размер комментария, байт', '0 - без ограничения размера'); // New in N-0.0.6
$L['cfg_countcomments'] = array('Считать комментарии', 'Показывать количество комментариев рядом с иконкой');
$L['cfg_parsebbcodecom'] = array('Парсинг BBCode в комментариях', ' ');
$L['cfg_parsesmiliescom'] = array('Парсинг смайликов в комментариях', ' ');

$L['info_desc'] = 'Система комментариев для Cotonti с API и интеграцией со страницами, списками, опросами, RSS и другими расширениями.';

/**
 * Plugin Body
 */

$L['Comment'] = 'Комментарий';
$L['Comments'] = 'Комментарии';
$L['Newcomment'] = 'Новый комментарии';

$L['comm_on_page'] = 'на странице'; // New in N-0.0.2

$L['com_closed'] = 'Для этого элемента нельзя добавлять комментарии'; // New in 0.1.0
$L['com_commentadded'] = 'Комментарий добавлен';
$L['com_commenttoolong'] = 'Комментарий слишком длинный';
$L['com_commenttooshort'] = 'Комментарий слишком короткий либо отсутствует';
$L['com_nocommentsyet'] = 'Комментарии отсутствуют';
$L['com_regonly'] = 'Добавление комментариев доступно только зарегистрированным пользователям';

$L['plu_comgup'] = ' осталось';
$L['plu_comhint'] = 'Ваш комментарий будет доступен для редактирования в течение %1$s';

$L['plu_comlive'] = 'Новый коментарий на сайте '; // New in N-0.1.0
$L['plu_comlive1'] = 'Отредактирован коментарий на сайте '; // New in N-0.1.0
$L['plu_comlive2'] = 'оставил комментарий:'; // New in N-0.1.0
$L['plu_comlive3'] = 'отредактировал свой комментарий:'; // New in N-0.1.0
$L['plu_comtooshort'] = 'Текст комментария не может быть пустым';
$L['rss_comments'] = 'Комментарии для'; // New in N-0.7.0
$L['rss_comment_of_user'] = 'Комментарий пользователя'; // New in N-0.0.2
$L['rss_comments_item_desc'] = 'Лента комментариев страницы'; // New in N-0.0.2
$L['rss_original'] = 'Комментируемая страница'; // New in N-0.0.2

/**
 * Admin Section
 */

$L['home_newcomments'] = 'Новые комментарии';
$L['core_comments'] = &$L['Comments'];
$L['adm_comm_already_del'] = 'Комментарий удален'; // New in N-0.0.2

/**
 * cot_declension arrays
 */

$Ls['Comments'] = array('комментарий','комментария','комментариев');

/**
 * Comedit
 */

$L['plu_title'] = 'Редактирование комментария';

?>