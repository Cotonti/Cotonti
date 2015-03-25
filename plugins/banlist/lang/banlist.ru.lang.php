<?php
/**
 * Russian Language File for Banlist
 *
 * @package Banlist
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$L['info_desc'] = 'Блокировка пользователей администратором по IP/адресу эл.почты/имени учетной записи';

/**
 * Plugin Body
 */

$L['banlist_title'] = 'Банлист';
$L['banlist_ipmask'] = 'IP маска';
$L['banlist_emailmask'] = 'E-mail маска или имя пользователя (login)';
$L['banlist_reason'] = 'Причина';
$L['banlist_duration'] = 'Срок действия';
$L['banlist_neverexpire'] = 'Постоянный';

$L['banlist_help'] = 'Образцы IP-масок: 194.31.13.41, 194.31.13.*, 194.31.*.*, 194.*.*.*<br />Образцы e-mail масок: @hotmail.com, @yahoo (шаблоны (wildcards) не поддерживаются)<br />Запись может содержать одну IP-маску, одну e-mail маску или обе маски.<br />IP-адреса фильтруются для всех без исключения страниц, e-mail маски применяются только при регистрации пользователей.';
$L['aut_emailbanned'] = 'Учетная запись заблокирована. Причина: ';

$L['banlist_blocked_ip'] = 'Ваш IP-адрес заблокирован';
$L['banlist_blocked_email'] = 'Ваш эл.адрес заблокирован';
$L['banlist_blocked_login'] = 'Ваша учетная запись заблокирована';

$L['banlist_banned'] = '{$0}.<br />Причина: {$1}<br />Срок действия блокировки: {$2}';
$L['banlist_foreverbanned'] = 'пожизненно.';
