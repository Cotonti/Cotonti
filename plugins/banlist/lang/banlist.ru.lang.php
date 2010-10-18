<?php
/**
 * Russian Language File for Banlist
 *
 * @package Banlist
 * @version 0.9.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$L['Banlist'] = 'Список заблокированных учетных записей';

/**
 * Other Section
 * Banlist Subsection
 */

$L['adm_ipmask'] = 'IP-маска';
$L['adm_emailmask'] = 'E-mail маска';
$L['adm_neverexpire'] = 'Без срока действия';
$L['adm_help_banlist'] = 'Образцы IP-масок: 194.31.13.41, 194.31.13.*, 194.31.*.*, 194.*.*.*<br />Образцы e-mail масок: @hotmail.com, @yahoo (шаблоны (wildcards) не поддерживаются)<br />Запись может содержать одну IP-маску, одну e-mail маску или обе маски.<br />IP-адреса фильтруются для всех без исключения страниц, e-mail маски применяются только при регистрации пользователей.';

$L['adm_searchthisuser'] = 'Поиск данного IP-адреса в базе данных пользователей';
$L['adm_dnsrecord'] = 'DNS-запись для данного адреса';

$L['alreadyaddnewentry'] = 'Новая запись добавлена';	// New in 0.0.2
$L['alreadyupdatednewentry'] = 'Запись обновлена';	// New in 0.0.2
$L['alreadydeletednewentry'] = 'Запись удалена';	// New in 0.0.2

?>
