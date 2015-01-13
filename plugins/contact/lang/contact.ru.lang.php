<?php

/**
 * Contact Plugin for Cotonti CMF (Russian Localization)
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Plugin Config
 */

$L['cfg_email'] = 'E-mail';
$L['cfg_email_hint'] = '(оставить пустым для использования E-mail\'а администратора)';
$L['cfg_minchars'] = 'Минимальное количество символов в сообщении';
$L['cfg_map'] = 'Код карты';
$L['cfg_about'] = 'О сайте';
$L['cfg_save'] = 'Метод хранения сообщений';
$L['cfg_save_params'] = 'e-mail,база данных,e-mail + база данных';
$L['cfg_template'] = 'Шаблон письма';
$L['cfg_template_hint'] = 'Используемые переменные: {$sitetitle}, {$siteurl}, {$author}, {$email}, {$subject}, {$text}, {$extra}, {$extraXXXX}, {$extraXXXX_title}';
$L['info_desc'] = 'Форма обратной связи с отправкой на E-mail и записью сообщений в базу данных';

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
$Ls['contact_headercontact'] = "контакт-сообщение,контакт-сообщения,контакт-сообщений";
$L['contact_entrytooshort'] = 'Сообщение слишком короткое или отсутствует';
$L['contact_noname'] = 'Вы не указали имя';
$L['contact_emailnotvalid'] = 'Некорректно указан E-mail';
$L['contact_message_sent'] = 'Сообщение отправлено';
