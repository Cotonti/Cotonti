<?php
/**
 * Russian Language File for the PM Module (pm.ru.lang.php)
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

$L['pmsend_subtitle'] = 'Форма для создания нового сообщения';
$L['pmsend_title'] = 'Новое личное сообщение';

$L['pm_archives'] = 'Архив';
$L['pm_arcsubtitle'] = 'Старые сообщения, последние вверху';
$L['pm_bodytoolong'] = 'Текст сообщения превышает установленные '.$cfg['pm_maxsize'].' символов';
$L['pm_bodytooshort'] = 'Текст сообщения слишком короткий либо отсутствует';
$L['pm_inbox'] = 'Входящие сообщения';
$L['pm_inboxsubtitle'] = 'Личные сообщения, новые вверху';
$L['pm_multiplerecipients'] = 'Других получателей данного сообщения: %1\$s';
$L['pm_norecipient'] = 'Не указан получатель';
$L['pm_notifytitle'] = 'Новое сообщение';
$L['pm_putinarchives'] = 'Переместить в архив';
$L['pm_deletefromarchives'] = 'Удалить из архива'; // New in N-0.7.0
$L['pm_replyto'] = 'Ответить данному пользователю';
$L['pm_sendnew'] = 'Создать новое сообщение';
$L['pm_sentbox'] = 'Отправленные сообщения';
$L['pm_sentboxsubtitle'] = 'Отправленные, но еще не просмотренные получателем сообщения';
$L['pm_titletooshort'] = 'Заголовок слишком короткий либо отсутствует';
$L['pm_toomanyrecipients'] = 'Ошибка: количество получателей не должно превышать %1\$s';
$L['pm_wrongname'] = 'Ошибка в имени одного или более получателей: имя удалено из списка получателей';
$L['pm_messagehistory'] = 'История сообщений'; // New in N-0.7.0
$L['pm_notmovetosentbox'] = 'Не сохранять в исходящих'; // New in N-0.7.0

$L['pm_filter'] = 'Фильтр'; // New in N-0.7.0
$L['pm_all'] = 'Все'; // New in N-0.7.0
$L['pm_starred'] = 'Избранное'; // New in N-0.7.0
$L['pm_unread'] = 'Непрочитанные'; // New in N-0.7.0
$L['pm_deletefromstarred'] = 'Удалить из избранного'; // New in N-0.7.0
$L['pm_putinstarred'] = 'Добавить в избранное'; // New in N-0.7.0
$L['pm_read'] = 'Прочитанное'; // New in N-0.7.0
$L['pm_selected'] = 'Отмеченные'; // New in N-0.7.0


/**
 * Private messages: notification
 */

$L['pm_notify'] = 'Здравствуйте, %1$s,
Вам отправлено новое личное сообщение от пользователя %2$s. Ссылка для чтения сообщения:
%3$s';

?>