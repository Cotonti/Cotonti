<?php
/**
 * Banlist için Türkçe Dil Dosyası
 *
 * @package Banlist
 * @copyright (c) Cotonti Ekibi
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Yanlış URL.');

/**
 * Eklenti Bilgisi
 */

$L['info_desc'] = 'Kullanıcıları IP/E-posta/giriş üzerinden yasaklamak için yönetim aracı';

/**
 * Eklenti Gövdesi
 */

$L['banlist_title'] = 'Yasak Listesi';
$L['banlist_ipmask'] = 'IP maskesi';
$L['banlist_emailmask'] = 'E-posta maskesi veya kullanıcı girişi';
$L['banlist_reason'] = 'Sebep';
$L['banlist_duration'] = 'Süre';
$L['banlist_neverexpire'] = 'Asla sona erme';

$L['banlist_help'] = 'IP maskesi örnekleri: 194.31.13.41, 194.31.13.*, 194.31.*.*, 194.*.*.*<br />E-posta maskesi örnekleri: @hotmail.com, @yahoo (Joker karakterler desteklenmez)<br />Tek bir giriş, bir IP maskesi veya bir e-posta maskesi veya her ikisini de içerebilir.<br />IP\'ler her görüntülenen sayfa için filtrelenir, e-posta maskeleri yalnızca kullanıcı kaydı sırasında kontrol edilir.';
$L['aut_emailbanned'] = 'Bu giriş yasaklanmış, nedeni: ';

$L['banlist_blocked_ip'] = 'IP adresiniz yasaklanmış';
$L['banlist_blocked_email'] = 'E-posta adresiniz yasaklanmış';
$L['banlist_blocked_login'] = 'Girişiniz yasaklanmış';

$L['banlist_banned'] = '{$0}.<br />Sebep: {$1}<br />Sona Erme: {$2}';
$L['banlist_foreverbanned'] = 'Asla sona ermeyecek.';
