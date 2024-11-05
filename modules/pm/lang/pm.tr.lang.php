<?php
/**
 * PM Modülü için Türkçe Dil Dosyası (pm.tr.lang.php)
 *
 * @package PM
 * @copyright (c) Cotonti Ekibi
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Yanlış URL.');

/**
 * Modül Yapılandırması
 */

$L['cfg_allownotifications'] = 'PM bildirimlerine e-posta ile izin ver';
$L['cfg_allownotifications_hint'] = '';
$L['cfg_maxsize'] = 'Mesajlar için maksimum uzunluk';
$L['cfg_maxsize_hint'] = '';
$L['cfg_maxpmperpage'] = 'Sayfa başına maksimum mesaj';
$L['cfg_maxpmperpage_hint'] = ' ';
$L['cfg_showlast'] = 'Yeni mesaj sayısı';
$L['cfg_showlast_hint'] = 'Yeni kullanıcının <strong>Header</strong> bölümünde ve ' .
    '<strong>Cot::$out[\'pm_lastMessages\']</strong> içerisinde gösterilecek mesaj sayısı.<br>0 - gösterme';
$L['info_desc'] = 'Site içi kullanıcı iletişimi için özel mesajlaşma sistemi';

/**
 * Diğer
 */

$L['adm_pm_totaldb'] = 'Veritabanındaki özel mesajlar';
$L['adm_pm_totalsent'] = 'Şimdiye kadar gönderilen toplam özel mesajlar';

/**
 * Ana
 */

$L['pmsend_title'] = 'Yeni özel mesaj gönder';
$L['pmsend_subtitle'] = 'Yeni özel mesaj gönderme formu';

$L['pm_bodytoolong'] = 'Özel mesajın içeriği çok uzun, maksimum {$size} karakter';
$L['pm_bodytooshort'] = 'Özel mesajın içeriği çok kısa veya eksik';
$L['pm_inbox'] = 'Gelen Kutusu';
$L['pm_inboxsubtitle'] = 'Özel mesajlar, en yeni en üstte';
$L['pm_norecipient'] = 'Alıcı belirtilmemiş';
$L['pm_notifytitle'] = 'Yeni özel mesaj';
$Ls['Privatemessages'] = "yeni özel mesajlar,yeni özel mesaj";
$L['pm_replyto'] = 'Bu kullanıcıya yanıtla';
$L['pm_sendnew'] = 'Yeni özel mesaj gönder';
$L['pm_sendpm'] = 'Özel mesaj gönder';
$L['pm_sendmessagetohint'] = 'en fazla 10 alıcı, virgülle ayırarak';
$L['pm_sentbox'] = 'Gönderilenler';
$L['pm_sentboxsubtitle'] = 'Gönderilen mesajlar';
$L['pm_titletooshort'] = 'Başlık çok kısa veya eksik';
$L['pm_toomanyrecipients'] = 'En fazla %1$s alıcı olabilir';
$L['pm_wrongname'] = 'En az bir alıcı hatalıydı ve listeden çıkarıldı';
$L['pm_messagehistory'] = 'Mesaj geçmişi';
$L['pm_notmovetosentbox'] = '"Gönderilenler"e taşıma';

$L['pm_filter'] = 'Filtre';
$L['pm_all'] = 'Tümünü görüntüle';
$L['pm_starred'] = 'Yıldızlı';
$L['pm_unread'] = 'Okunmamış';
$L['pm_deletefromstarred'] = 'Yıldızlılardan çıkar';
$L['pm_putinstarred'] = 'Yıldızlılara ekle';
$L['pm_read'] = 'Okundu';
$L['pm_selected'] = 'Seçili';

/**
 * Özel mesajlar: bildirim
 */

$L['pm_notify'] = "Merhaba %1\$s,\nBu e-postayı alıyorsunuz çünkü gelen kutunuzda %2\$s tarafından gönderilmiş yeni bir özel mesaj var.\nOkumak için bu bağlantıya tıklayın: %3\$s";
