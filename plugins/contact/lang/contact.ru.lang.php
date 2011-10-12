<?php

/**
 * Contact Plugin for Cotonti CMF (Russian Localization)
 * @version 2.00
 * @author Cotonti Team
 * @copyright (c) 2008-2011 Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Plugin Config
 */

$L['cfg_email'] = array('E-mail', '(оставить пустым для использования E-mail\'а администратора)');
$L['cfg_minchars'] = array('Минимальное количество символов в сообщении');

$L['info_desc'] = 'Форма для связи, отправляемая по e-mail и записываемая в базу данных';

/**
 * Plugin Admin
 */

$L['contact_view'] = 'Просмотр сообщения';
$L['contact_markread'] = 'Отметить как прочитанное';
$L['contact_read'] = 'Прочитано';
$L['contact_markunread'] = 'Снять отметку о прочтении';
$L['contact_unread'] = 'Не прочитано';
$L['contact_new'] = 'новое сообщение';
$L['contact_shortnew'] = 'новое';
$L['contact_sent'] = 'Последний ответ';
$L['contact_nosubject'] = 'Без темы';

/**
 * Plugin Title & Subtitle
 */

$L['contact_title'] = 'Обратная связь';
$L['contact_subtitle'] = 'Контактная информация';

/**
 * Plugin Body
 */

$L['contact_headercontact'] = 'Обратная связь';
$Ls['contact_headercontact'] = array('сообщение', 'сообщения', 'сообщений');
$L['contact_entrytooshort'] = 'Сообщение слишком короткое или отсутствует';
$L['contact_noname'] = 'Вы не указали имя';
$L['contact_emailnotvalid'] = 'Некорректно указан E-mail';
$L['contact_message_sent'] = 'Сообщение отправлено';

?>