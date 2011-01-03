<?php
/**
 * Russian Language File for the PM Module (pm.ru.lang.php)
 *
 * @package pm
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Admin
 */

$L['adm_pm_totaldb'] = 'Личных сообщений в базе данных';
$L['adm_pm_totalsent'] = 'Всего отправлено личных сообщений';

/**
 * Config
 */

$L['cfg_pm_allownotifications'] = array('E-mail уведомления', 'Отсылать на пользовательский e-mail уведомления о поступивших личных сообщениях');
$L['cfg_pm_maxsize'] = array('Максимальное количество символов в личном сообщении', 'По умолчанию: 10000 символов');
$L['cfg_maxpmperpage'] = array('Макс. количество сообщений на странице', ' ');

$L['info_desc'] = 'Система личных сообщений - это общение пользователей на сайте, подобное e-mail';

/**
 * Main
 */

$L['pmsend_subtitle'] = 'Форма для создания нового сообщения';
$L['pmsend_title'] = 'Новое личное сообщение';

$L['pm_archives'] = 'Архив';
$L['pm_arcsubtitle'] = 'Старые сообщения, последние вверху';
$L['pm_bodytoolong'] = 'Текст сообщения превышает установленные '.$cfg['pm']['pm_maxsize'].' символов';
$L['pm_bodytooshort'] = 'Текст сообщения слишком короткий либо отсутствует';
$L['pm_inbox'] = 'Входящие сообщения';
$L['pm_inboxsubtitle'] = 'Личные сообщения, новые вверху';
$L['pm_multiplerecipients'] = 'Других получателей данного сообщения: %1\$s';
$L['pm_norecipient'] = 'Не указан получатель';
$L['pm_notifytitle'] = 'Новое сообщение';
$L['pm_putinarchives'] = 'Переместить в архив';
$L['pm_deletefromarchives'] = 'Удалить из архива';
$L['pm_replyto'] = 'Ответить данному пользователю';
$L['pm_sendnew'] = 'Создать новое сообщение';
$L['pm_sentbox'] = 'Отправленные сообщения';
$L['pm_sentboxsubtitle'] = 'Отправленные, но еще не просмотренные получателем сообщения';
$L['pm_titletooshort'] = 'Заголовок слишком короткий либо отсутствует';
$L['pm_toomanyrecipients'] = 'Ошибка: количество получателей не должно превышать %1\$s';
$L['pm_wrongname'] = 'Ошибка в имени одного или более получателей: имя удалено из списка получателей';
$L['pm_messagehistory'] = 'История сообщений';
$L['pm_notmovetosentbox'] = 'Не сохранять в исходящих';

$L['pm_filter'] = 'Фильтр';
$L['pm_all'] = 'Все';
$L['pm_starred'] = 'Избранное';
$L['pm_unread'] = 'Непрочитанные';
$L['pm_deletefromstarred'] = 'Удалить из избранного';
$L['pm_putinstarred'] = 'Добавить в избранное';
$L['pm_read'] = 'Прочитанное';
$L['pm_selected'] = 'Отмеченные';


/**
 * Private messages: notification
 */

$L['pm_notify'] = 'Здравствуйте, %1$s,
Вам отправлено новое личное сообщение от пользователя %2$s. Ссылка для чтения сообщения:
%3$s';

/**
 * Moved from theme.lang
 */

$L['pm_sendmessagetohint'] = '(до 10 адресатов, через запятую)';
$L['pm_newmessage'] = 'Новое сообщение';
$L['pm_sendtoarchives'] = 'Переместить в архив';
$L['pm_selectall'] = 'Выделить все';
$L['pm_unselectall'] = 'Снять выделение';

?>