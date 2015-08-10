<?php
/**
 * Russian Language File for the PM Module (pm.ru.lang.php)
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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

$L['cfg_allownotifications'] = 'E-mail уведомления';
$L['cfg_allownotifications_hint'] = 'Отсылать на пользовательский e-mail уведомления о поступивших личных сообщениях';
$L['cfg_maxsize'] = 'Максимальное количество символов в личном сообщении';
$L['cfg_maxsize_hint'] = 'По умолчанию: 10000 символов';
$L['cfg_maxpmperpage'] = 'Макс. количество сообщений на странице';
$L['cfg_maxpmperpage_hint'] = ' ';
$L['info_desc'] = 'Общение пользователей сайта через систему отправки сообщений';

/**
 * Main
 */

$L['pmsend_subtitle'] = 'Форма для создания нового сообщения';
$L['pmsend_title'] = 'Новое личное сообщение';

$L['pm_bodytoolong'] = 'Текст сообщения превышает установленные {$size} символов';
$L['pm_bodytooshort'] = 'Текст сообщения слишком короткий либо отсутствует';
$L['pm_inbox'] = 'Входящие сообщения';
$L['pm_inboxsubtitle'] = 'Личные сообщения, новые вверху';
$L['pm_norecipient'] = 'Не указан получатель';
$L['pm_notifytitle'] = 'Новое сообщение';
$Ls['Privatemessages'] = "новое сообщение,новых сообщения,новых сообщений";
$L['pm_replyto'] = 'Ответить данному пользователю';
$L['pm_sendnew'] = 'Создать новое сообщение';
$L['pm_sendpm'] = 'Отправить личное сообщение';
$L['pm_sendmessagetohint'] = 'до 10 получателей, разделенных запятыми';
$L['pm_sentbox'] = 'Отправленные сообщения';
$L['pm_sentboxsubtitle'] = 'Отправленные Вами личные сообщения';
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

$L['pm_notify'] = "Здравствуйте, %1\$s,\nВам отправлено новое личное сообщение от пользователя %2\$s. Ссылка для чтения сообщения:\n%3\$s";
