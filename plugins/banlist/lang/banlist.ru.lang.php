<?php
/**
 * Russian Language File for Banlist
 *
 * @package Banlist
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$L['info_desc'] = 'Блокировка пользователей администратором по IP или E-mail';

/**
 * Plugin Body
 */

$L['banlist_title'] = 'Банлист';
$L['banlist_ipmask'] = 'IP маска';
$L['banlist_emailmask'] = 'E-mail маска';
$L['banlist_reason'] = 'Причина';
$L['banlist_duration'] = 'Срок';
$L['banlist_neverexpire'] = 'Без срока';

$L['banlist_help'] = 'Образцы IP-масок: 194.31.13.41, 194.31.13.*, 194.31.*.*, 194.*.*.*<br />Образцы e-mail масок: @hotmail.com, @yahoo (шаблоны (wildcards) не поддерживаются)<br />Запись может содержать одну IP-маску, одну e-mail маску или обе маски.<br />IP-адреса фильтруются для всех без исключения страниц, e-mail маски применяются только при регистрации пользователей.';

?>