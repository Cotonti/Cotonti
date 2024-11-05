<?php
/**
 * Install Modülü için Türkçe Dil Dosyası (install.tr.lang.php)
 *
 * @package Install
 * @copyright (c) Cotonti Ekibi
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Yanlış URL.');

$L['Complete'] = 'Tamamla';
$L['Finish'] = 'Bitir';
$L['Install'] = 'Kur';
$L['Next'] = 'İleri';

$L['install_another_process'] = 'Başka bir kurulum işlemi çalışıyor';
$L['install_another_process2'] = '%s UTC tarihinde başka bir kurulum işlemi başlatıldı. Lütfen bitene kadar bekleyin';
$L['install_adminacc'] = 'Yönetici Hesabı';
$L['install_body_title'] = 'Cotonti Web Kurulumu';
$L['install_body_message1'] = 'Bu komut dosyası, Cotonti temel kurulum ve yapılandırmasını sizin için kuracaktır.';
$L['install_body_message2'] = 'Bu komut dosyasını çalıştırmadan önce datas/config-sample.php dosyasını datas/config.php olarak kopyalamanız ve datas/config.php dosyasına CHMOD 666 ayarlamanız önerilir.';
$L['install_body_message3'] = 'Bu kullanıcı yeni veritabanı oluşturma yetkisine sahip değilse, önce sunucunuzda yukarıdaki adla boş bir veritabanı <strong>oluşturmanız</strong> gerekir.';
$L['install_chmod_value'] = 'CHMOD {$chmod}';
$L['install_complete'] = 'Kurulum başarıyla tamamlandı!';
$L['install_complete_note'] = 'install.php dosyasını kaldırabilir ve datas/config.php dosyasına CHMOD 644 ayarlayabilirsiniz, bu site güvenliğini artıracaktır';
$L['install_db'] = 'MySQL Veritabanı Ayarları';
$L['install_db_host'] = 'Veritabanı sunucusu';
$L['install_db_user'] = 'Veritabanı kullanıcısı';
$L['install_db_pass'] = 'Veritabanı parolası';
$L['install_db_port'] = 'Veritabanı portu';
$L['install_db_port_hint'] = 'Varsayılan dışında bir port ise';
$L['install_db_name'] = 'Veritabanı adı';
$L['install_db_x'] = 'Tablo ön eki';
$L['install_dir_not_found'] = 'Kurulum dizini bulunamadı';
$L['install_error_config'] = 'Yapılandırma dosyası oluşturulamadı veya düzenlenemedi. Lütfen config-sample.php dosyasını config.php olarak kaydedin ve CHMOD 777 olarak ayarlayın';
$L['install_error_sql'] = 'MySQL veritabanına bağlanılamıyor. Lütfen ayarlarınızı kontrol edin.';
$L['install_error_sql_host'] = 'Veritabanı sunucusu eksik';
$L['install_error_sql_user'] = 'Veritabanı kullanıcısı eksik';
$L['install_error_sql_db_name'] = 'Veritabanı adı eksik';
$L['install_error_sql_db'] = 'MySQL veritabanı seçilemedi. Lütfen ayarlarınızı kontrol edin.';
$L['install_error_sql_ext'] = 'Cotonti, PHP pdo_mysql eklentisinin yüklü olmasını gerektirir';
$L['install_error_sql_script'] = 'SQL betiği çalıştırılamadı: {$msg}';
$L['install_error_sql_ver'] = 'Cotonti, MySQL 5.0.7 veya üstü bir sürüm gerektirir. Sürümünüz: {$ver}';
$L['install_error_mainurl'] = 'Site için ana URL’yi belirtmelisiniz.';
$L['install_error_mbstring'] = 'Cotonti, PHP mbstring eklentisinin yüklü olmasını gerektirir';
$L['install_error_missing_file'] = '{$file} eksik. Devam etmek için bu dosyayı yeniden yükleyin.';
$L['install_error_php_ver'] = 'Cotonti, PHP 7.3 veya üstü bir sürüm gerektirir. Sürümünüz: {$ver}';
$L['install_misc'] = 'Diğer Ayarlar';
$L['install_misc_lng'] = 'Varsayılan dil';
$L['install_misc_theme'] = 'Varsayılan tema';
$L['install_misc_url'] = 'Ana site URL\'si (sonunda eğik çizgi olmadan)';
$L['install_parsing'] = 'Ayrıştırma modu';
$L['install_parsing_hint'] = 'Ayrıştırma modu sitenizde genel olarak uygulanacaktır. HTML seçerseniz, mevcut öğeler otomatik olarak HTML\'ye dönüştürülür. Bu işlem geri alınamaz.';
$L['install_permissions'] = 'Dosya/Klasör İzinleri';
$L['install_recommends'] = 'Önerir';
$L['install_requires'] = 'Gerektirir';
$L['install_retype_password'] = 'Parolayı tekrar yazın';
$L['install_step'] = '{$total} adımda adım {$step}';
$L['install_title'] = 'Cotonti Web Kurulumu';
$L['install_update'] = 'Cotonti Güncelleniyor';
$L['install_update_config_error'] = 'datas/config.php güncellenemiyor. Lütfen CHMOD 664 veya 666 olarak ayarlayın ve tekrar deneyin. Eğer yardımcı olmazsa, datas/config-sample.php dosyasının mevcut olduğundan emin olun.';
$L['install_update_config_success'] = 'datas/config.php başarıyla güncellendi';
$L['install_update_error'] = 'Güncelleme Başarısız';
$L['install_update_nothing'] = 'Güncellenecek bir şey yok';
$L['install_update_patch_applied'] = '{$f} yaması uygulandı: {$msg}';
$L['install_update_patch_error'] = '{$f} yaması uygulanırken hata oluştu: {$msg}';
$L['install_update_patches'] = 'Uygulanan yamalar:';
$L['install_update_success'] = '{$rev} revizyonuna başarıyla güncellendi';
$L['install_update_template_not_found'] = 'Güncelleme şablon dosyası bulunamadı';
$L['install_upgrade'] = 'Sistem genel yükseltme yapmaya hazır...';
$L['install_upgrade_error'] = 'Cotonti {$ver} sürümüne yükseltme başarısız oldu';
$L['install_upgrade_success'] = 'Cotonti {$ver} sürümüne başarıyla yükseltildi';
$L['install_upgrade_success_note'] = 'Uyumluluk sorunlarından kaçınmak için tüm Genoa eklentileri kaldırıldı. Bunları daha sonra manuel olarak güncelleyebilirsiniz.';
$L['install_ver'] = 'Sunucu Bilgisi';
$L['install_ver_invalid'] = '{$ver} &mdash; geçersiz!';
$L['install_ver_valid'] = '{$ver} &mdash; geçerli!';
$L['install_view_site'] = 'Siteyi görüntüle';
$L['install_writable'] = 'Yazılabilir';
