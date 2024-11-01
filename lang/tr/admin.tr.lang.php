<?php

/**
 * Admin Modülü için Türkçe Dil Dosyası (admin.tr.lang.php)
 *
 * @package Cotonti
 * @copyright (c) Cotonti Ekibi
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Yanlış URL.');

/**
 * Ana Sayfa Bölümü
 */
$L['home_remove_install'] = 'install.php dosyasını bir sonraki güncellemeye kadar veya en azından config.php dosyasını yazılabilir olmaktan koruyana kadar kaldırın';

$L['home_newusers'] = 'Yeni üyeler';
$L['home_newpages'] = 'Yeni sayfalar';
$L['home_newtopics'] = 'Yeni konular';
$L['home_newposts'] = 'Yeni mesajlar';
$L['home_newpms'] = 'Yeni özel mesajlar';

$L['home_db_rows'] = 'SQL veritabanı, satır sayısı';
$L['home_db_indexsize'] = 'SQL veritabanı, indeks boyutu (KB)';
$L['home_db_datassize'] = 'SQL veritabanı, veri boyutu (KB)';
$L['home_db_totalsize'] = 'SQL veritabanı, toplam boyut (KB)';

$L['home_site_props'] = 'Site özellikleri';
$L['home_extrafields_pages'] = 'Sayfalar için ekstra alanlar';
$L['home_extrafields_users'] = 'Kullanıcılar için ekstra alanlar';
$L['home_extrafields_forums_topics'] = 'Konular için ekstra alanlar';
$L['home_extrafields_forums_posts'] = 'Mesajlar için ekstra alanlar';
$L['home_users_rights'] = 'Kullanıcı hakları';

$L['home_update_notice'] = 'Güncelleme mevcut';
$L['home_update_revision'] = 'Mevcut sürüm: <b>%1$s</b><br />Yeni sürüm: <b>%2$s</b>';

/**
 * Yapılandırma Bölümü
 */
$L['core_locale'] = &$L['Dil ve Saat Ayarları'];
$L['core_locale_desc'] = 'Varsayılan dil ve saat dilimi ayarları';
$L['core_main'] = 'Ana Ayarlar';
$L['core_main_desc'] = 'Web sitesi yapılandırması, genel liste ayarları';
$L['core_menus'] = &$L['Menüler'];
$L['core_menus_desc'] = 'Düz metin bilgisi göndermek için alanlar';
$L['core_performance'] = 'Performans';
$L['core_performance_desc'] = 'Gzip sıkıştırması, kaynak birleştirme, Ajax ve jQuery etkinleştirme';
$L['core_security'] = &$L['Güvenlik'];
$L['core_security_desc'] = 'Koruma, CAPTCHA, hata ayıklama ve bakım modları';
$L['core_sessions'] = 'Oturumlar';
$L['core_sessions_desc'] = 'Çerez ayarları, giriş/çıkış yönlendirmeleri';
$L['core_theme'] = &$L['Temalar'];
$L['core_theme_desc'] = 'Varsayılan tema ve biçimlendirme öğelerini yönetme';
$L['core_title'] = 'Başlıklar ve Metalar';
$L['core_title_desc'] = 'Ana sayfa ve iç sayfalar için META Başlık ayarları';

/**
 * Yapılandırma Bölümü
 * Dil ve Saat Ayarları Alt Bölümü
 */
$L['cfg_forcedefaultlang'] = 'Tüm kullanıcılar için varsayılan dili zorla';
$L['cfg_forcedefaultlang_hint'] = 'Kullanıcı profili ayarlarını geçersiz kılar';
$L['cfg_defaulttimezone'] = 'Varsayılan saat dilimi';
$L['cfg_defaulttimezone_hint'] = 'Misafirler ve yeni üyeler için';

/**
 * Yapılandırma Bölümü
 * Ana Ayarlar Alt Bölümü
 */
$L['cfg_adminemail'] = 'Yöneticinin e-posta adresi';
$L['cfg_adminemail_hint'] = 'Güvenlik nedeniyle gereklidir';
$L['cfg_clustermode'] = 'Sunucu kümesi';
$L['cfg_clustermode_hint'] = 'Evet olarak ayarlayın, eğer yük dengeleme yapılandırması varsa';
$L['cfg_confirmlinks'] = 'Potansiyel olarak tehlikeli işlemleri onaylayın';
$L['cfg_default_show_installed'] = 'Varsayılan olarak yalnızca yüklü eklentileri göster';
$L['cfg_easypagenav'] = 'Kullanıcı dostu sayfalama';
$L['cfg_easypagenav_hint'] = 'URL\'lerde DB ofsetleri yerine sayfa numaraları kullanır';
$L['cfg_hostip'] = 'Sunucu IP\'si';
$L['cfg_hostip_hint'] = 'Sunucunun IP adresi, isteğe bağlı';
$L['cfg_maxrowsperpage'] = 'Sayfa başına maksimum öğe';
$L['cfg_maxrowsperpage_hint'] = 'Sayfalama için varsayılan öğe sınırı';
$L['cfg_parser'] = 'İşaretleme ayrıştırıcı';
$L['cfg_parser_hint'] = 'Varsayılan olarak HTML';
$L['cfg_loggerlevel'] = 'Günlük seviyesi';
$L['cfg_loggerlevel_params'] = 'Devre dışı,'.$L['Güvenlik'].','.$L['Yönetim'].','.$L['Eklentiler'].','.$L['Güvenlik'].'+'.$L['Yönetim'].','.$L['Güvenlik'].'+'.$L['Eklentiler'].','.$L['Yönetim'].'+'.$L['Eklentiler'].','.$L['Güvenlik'].'+'.$L['Yönetim'].'+'.$L['Eklentiler'].',Hepsi';
$L['cfg_loggerlevel_hint'] = 'Hepsi: tüm işlemler günlüğe kaydedilir<br />Devre dışı: tüm kullanıcı seviyeleri için günlük kaydı tamamen devre dışı bırakılır<br />"Devre dışı" ve "Hepsi" modları, eklenti günlüğü için bireysel ayarları geçersiz kılar';

/**
 * Yapılandırma Bölümü
 * Menüler Alt Bölümü
 */
$L['cfg_banner'] = 'Afiş<br />header.tpl dosyasındaki {HEADER_BANNER}';
$L['cfg_banner_hint'] = '';
$L['cfg_bottomline'] = 'Alt satır<br />footer.tpl dosyasındaki {FOOTER_BOTTOMLINE}';
$L['cfg_bottomline_hint'] = '';
$L['cfg_topline'] = 'Üst satır<br />header.tpl dosyasındaki {HEADER_TOPLINE}';
$L['cfg_topline_hint'] = '';

$L['cfg_freetext1'] = 'Boş Metin Alanı #1<br />Tüm tpl dosyalarında {PHP.cfg.freetext1}';
$L['cfg_freetext1_hint'] = '';
$L['cfg_freetext2'] = 'Boş Metin Alanı #2<br />Tüm tpl dosyalarında {PHP.cfg.freetext2}';
$L['cfg_freetext2_hint'] = '';
$L['cfg_freetext3'] = 'Boş Metin Alanı #3<br />Tüm tpl dosyalarında {PHP.cfg.freetext3}';
$L['cfg_freetext3_hint'] = '';
$L['cfg_freetext4'] = 'Boş Metin Alanı #4<br />Tüm tpl dosyalarında {PHP.cfg.freetext4}';
$L['cfg_freetext4_hint'] = '';
$L['cfg_freetext5'] = 'Boş Metin Alanı #5<br />Tüm tpl dosyalarında {PHP.cfg.freetext5}';
$L['cfg_freetext5_hint'] = '';
$L['cfg_freetext6'] = 'Boş Metin Alanı #6<br />Tüm tpl dosyalarında {PHP.cfg.freetext6}';
$L['cfg_freetext6_hint'] = '';
$L['cfg_freetext7'] = 'Boş Metin Alanı #7<br />Tüm tpl dosyalarında {PHP.cfg.freetext7}';
$L['cfg_freetext7_hint'] = '';
$L['cfg_freetext8'] = 'Boş Metin Alanı #8<br />Tüm tpl dosyalarında {PHP.cfg.freetext8}';
$L['cfg_freetext8_hint'] = '';
$L['cfg_freetext9'] = 'Boş Metin Alanı #9<br />Tüm tpl dosyalarında {PHP.cfg.freetext9}';
$L['cfg_freetext9_hint'] = '';

$L['cfg_menu1'] = 'Menü alanı #1<br />Tüm tpl dosyalarında {PHP.cfg.menu1}';
$L['cfg_menu1_hint'] = '';
$L['cfg_menu2'] = 'Menü alanı #2<br />Tüm tpl dosyalarında {PHP.cfg.menu2}';
$L['cfg_menu2_hint'] = '';
$L['cfg_menu3'] = 'Menü alanı #3<br />Tüm tpl dosyalarında {PHP.cfg.menu3}';
$L['cfg_menu3_hint'] = '';
$L['cfg_menu4'] = 'Menü alanı #4<br />Tüm tpl dosyalarında {PHP.cfg.menu4}';
$L['cfg_menu4_hint'] = '';
$L['cfg_menu5'] = 'Menü alanı #5<br />Tüm tpl dosyalarında {PHP.cfg.menu5}';
$L['cfg_menu5_hint'] = '';
$L['cfg_menu6'] = 'Menü alanı #6<br />Tüm tpl dosyalarında {PHP.cfg.menu6}';
$L['cfg_menu6_hint'] = '';
$L['cfg_menu7'] = 'Menü alanı #7<br />Tüm tpl dosyalarında {PHP.cfg.menu7}';
$L['cfg_menu7_hint'] = '';
$L['cfg_menu8'] = 'Menü alanı #8<br />Tüm tpl dosyalarında {PHP.cfg.menu8}';
$L['cfg_menu8_hint'] = '';
$L['cfg_menu9'] = 'Menü alanı #9<br />Tüm tpl dosyalarında {PHP.cfg.menu9}';
$L['cfg_menu9_hint'] = '';

/**
 * Yapılandırma Bölümü
 * Performans Alt Bölümü
 */
$L['cfg_gzip'] = 'Gzip';
$L['cfg_gzip_hint'] = 'HTML çıktısının Gzip sıkıştırması. Sunucunuz zaten sayfalarınıza Gzip uyguluyorsa bunu etkinleştirmeyin. Gzip\'in zaten etkin olup olmadığını kontrol etmek için <a target="_blank" href="http://www.whatsmyip.org/http-compression-test/">HTTP Compression Test</a> kullanabilirsiniz.';
$L['cfg_headrc_consolidate'] = 'Başlık ve alt bilgi kaynaklarını birleştir (JS/CSS)';
$L['cfg_headrc_minify'] = 'Birleştirilmiş JS/CSS\'yi sıkıştır';
$L['cfg_jquery_cdn'] = 'Bu CDN URL\'sinden jQuery kullan';
$L['cfg_jquery_cdn_hint'] = 'Örnek: https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js';
$L['cfg_jquery'] = 'jQuery\'yi etkinleştir';
$L['cfg_jquery_hint'] = '';
$L['cfg_turnajax'] = 'Ajax\'ı etkinleştir';
$L['cfg_turnajax_hint'] = 'Sadece jQuery etkinse çalışır';

/**
 * Yapılandırma Bölümü
 * Güvenlik Alt Bölümü
 */
$L['cfg_captchamain'] = 'Ana captcha';
$L['cfg_captcharandom'] = 'Rastgele captcha';
$L['cfg_hashfunc'] = 'Varsayılan karma fonksiyonu';
$L['cfg_hashfunc_hint'] = 'Şifreleri karma yapmak için kullanılır';
$L['cfg_logWrongInput'] = 'Yanlış girişleri günlüğe kaydet';
$L['cfg_logWrongInput_hint'] = 'Devre dışı bırakıldığında protokol hacmini azaltır, ancak yanlış veri girişini kaydetmeyi durdurur';
$L['cfg_referercheck'] = 'Formlar için yönlendiren kontrolü';
$L['cfg_referercheck_hint'] = 'Çapraz alan gönderimini engeller';
$L['cfg_shieldenabled'] = 'Kalkanı etkinleştir';
$L['cfg_shieldenabled_hint'] = 'Spam engelleme ve fazla istek önleme';
$L['cfg_shieldtadjust'] = 'Kalkan zamanlayıcılarını ayarla (%)';
$L['cfg_shieldtadjust_hint'] = 'Ne kadar yüksekse spam yapmak o kadar zor';
$L['cfg_shieldzhammer'] = '* hızlı vuruştan sonra fazla istek engelleme';
$L['cfg_shieldzhammer_hint'] = 'Ne kadar küçükse, 3 dakikalık otomatik yasaklama o kadar hızlı olur';
$L['cfg_devmode'] = 'Hata ayıklama modu';
$L['cfg_devmode_hint'] = 'Canlı sitelerde devre dışı bırakın';
$L['cfg_maintenance'] = 'Bakım modu';
$L['cfg_maintenance_hint'] = 'Yalnızca yetkili kullanıcı gruplarına siteye erişim sağlar';
$L['cfg_maintenancereason'] = 'Bakım nedeni';
$L['cfg_maintenancereason_hint'] = 'İsteğe bağlı, kısa ve basit tutun';

/**
 * Yapılandırma Bölümü
 * Oturumlar Alt Bölümü
 */
$L['cfg_cookiedomain'] = 'Çerezler için alan adı';
$L['cfg_cookiedomain_hint'] = 'Varsayılan olarak boş';
$L['cfg_cookielifetime'] = 'Maksimum çerez ömrü';
$L['cfg_cookielifetime_hint'] = 'Saniye cinsinden';
$L['cfg_cookiepath'] = 'Çerezler için yol';
$L['cfg_cookiepath_hint'] = 'Varsayılan olarak boş';
$L['cfg_forcerememberme'] = '&quot;Beni hatırla&quot; zorunluluğu';
$L['cfg_forcerememberme_hint'] = 'Çok alanlı sitelerde veya aniden çıkış yapmalarda kullanın';
$L['cfg_timedout'] = 'Boşta kalma süresi, saniye cinsinden';
$L['cfg_timedout_hint'] = 'Bu süreden sonra kullanıcı uzakta sayılır';
$L['cfg_redirbkonlogin'] = 'Girişte geri yönlendir';
$L['cfg_redirbkonlogin_hint'] = 'Kullanıcının giriş yapmadan önce görüntülediği sayfaya geri yönlendirin';
$L['cfg_redirbkonlogout'] = 'Çıkışta geri yönlendir';
$L['cfg_redirbkonlogout_hint'] = 'Kullanıcının çıkış yapmadan önce görüntülediği sayfaya geri yönlendirin';

/**
 * Yapılandırma Bölümü
 * Temalar Alt Bölümü
 */
$L['cfg_disablesysinfos'] = 'Sayfa oluşturma süresini kapat';
$L['cfg_disablesysinfos_hint'] = 'footer.tpl dosyasında kullanılır';
$L['cfg_forcedefaulttheme'] = 'Tüm kullanıcılar için varsayılan temayı zorla';
$L['cfg_forcedefaulttheme_hint'] = 'Kullanıcı profilindeki ayarı geçersiz kılar';
$L['cfg_homebreadcrumb'] = 'Breadcrumbs içinde Anasayfa bağlantısını göster';
$L['cfg_homebreadcrumb_hint'] = 'Anasayfa bağlantısını breadcrumbs içine zorla yerleştir';
$L['cfg_keepcrbottom'] = '{FOOTER_BOTTOMLINE} etiketine telif hakkı ekle';
$L['cfg_keepcrbottom_hint'] = 'footer.tpl dosyasında kullanılır';
$L['cfg_msg_separate'] = 'Mesajları her kaynak için ayrı olarak göster';
$L['cfg_msg_separate_hint'] = '';
$L['cfg_separator'] = 'Genel ayırıcı';
$L['cfg_separator_hint'] = 'Breadcrumbs vb. yerlerde kullanılır';
$L['cfg_showsqlstats'] = 'SQL sorgu istatistiklerini göster';
$L['cfg_showsqlstats_hint'] = 'footer.tpl dosyasında kullanılır';

/**
 * Yapılandırma Bölümü
 * Başlık Alt Bölümü
 */
$L['cfg_maintitle'] = 'Web Sitesi Başlığı';
$L['cfg_metakeywords'] = 'HTML Meta anahtar kelimeleri';
$L['cfg_metakeywords_hint'] = 'Virgülle ayrılmış';
$L['cfg_maintitle_hint'] = 'Web sitesinin ana başlığı, zorunludur';
$L['cfg_subtitle'] = 'Açıklama';
$L['cfg_subtitle_hint'] = 'Opsiyonel, site başlığının ardından gösterilecektir';
$L['cfg_title_header'] = 'Başlık Başlığı';
$L['cfg_title_header_hint'] = 'Seçenekler: {MAINTITLE}, {DESCRIPTION}, {SUBTITLE}';
$L['cfg_title_header_index'] = 'Anasayfa için Başlık Başlığı';
$L['cfg_title_header_index_hint'] = 'Seçenekler: {MAINTITLE}, {DESCRIPTION}, {SUBTITLE}';
$L['cfg_title_users_details'] = 'Kullanıcı Detayları Başlığı';
$L['cfg_title_users_details_hint'] = 'Seçenekler: {USER}, {NAME}';
$L['cfg_subject_mail'] = 'E-posta konusu';
$L['cfg_subject_mail_hint'] = 'Seçenekler: {SITE_TITLE}, {SITE_DESCRIPTION}, {MAIL_SUBJECT}';
$L['cfg_body_mail'] = 'E-posta başlığı';
$L['cfg_body_mail_hint'] = 'Seçenekler: {SITE_TITLE}, {SITE_DESCRIPTION}, {SITE_URL}, {ADMIN_EMAIL}, {MAIL_BODY}, {MAIL_SUBJECT}';

/**
 * Yapılandırma Bölümü
 * Genel Deyimler
 */
$L['cfg_css'] = 'Modül/eklentiyi etkinleştir CSS';
$L['cfg_markup'] = 'İşaretlemeyi etkinleştir';
$L['cfg_markup_hint'] = 'HTML/BBcode veya sisteminizde kurulu diğer ayrıştırıcıları etkinleştirir';

/**
 * Eklenti Yönetimi
 */
$L['ext_already_installed'] = 'Bu eklenti zaten yüklü: {$name}';
$L['ext_auth_installed'] = 'Yüklü yetkilendirme varsayılanları';
$L['ext_auth_locks_updated'] = 'Güncellenmiş yetkilendirme kilitleri';
$L['ext_auth_uninstalled'] = 'Kaldırılmış yetkilendirme seçenekleri';
$L['ext_bindings_installed'] = '{$cnt} kanca bağlantısı yüklendi';
$L['ext_bindings_uninstalled'] = '{$cnt} kanca bağlantısı kaldırıldı';
$L['ext_config_error'] = 'Yapılandırma kurulumu başarısız oldu';
$L['ext_config_installed'] = 'Yüklü yapılandırma';
$L['ext_config_uninstalled'] = 'Kaldırılmış yapılandırma';
$L['ext_config_updated'] = 'Güncellenmiş yapılandırma seçenekleri';
$L['ext_config_struct_error'] = 'Yapı yapılandırması kurulumu başarısız oldu';
$L['ext_config_struct_installed'] = 'Yapı yapılandırması yüklendi';
$L['ext_config_struct_updated'] = 'Yapı yapılandırma seçenekleri güncellendi';
$L['ext_dependency_error'] = '{$dep_type} &quot;{$dep_name}&quot; tarafından gerekli {$type} &quot;{$name}&quot; yüklü değil veya yükleme için seçilmedi';
$L['ext_dependency_uninstall_error'] = '{$type} &quot;{$name}&quot; bu eklentiyi gerektiriyor ve önce kaldırılmalıdır';
$L['ext_executed_php'] = 'Çalıştırılan PHP işleyici parçası: {$ret}';
$L['ext_executed_sql'] = 'Çalıştırılan SQL işleyici parçası: {$ret}';
$L['ext_installing'] = '{$type} &quot;{$name}&quot; yükleniyor';
$L['ext_invalid_format'] = 'Bu geçerli bir Cotonti >= 0.9 eklentisi değil. Lütfen geliştirici ile iletişime geçin';
$L['ext_old_format'] = 'Bu eski Genoa/Seditio eklentisidir. Doğru çalışmayabilir veya hiç çalışmayabilir.';
$L['ext_patch_applied'] = '{$f} yamasına uygulandı: {$msg}';
$L['ext_patch_error'] = '{$f} yaması uygulanırken hata oluştu: {$msg}';
$L['ext_requires_modules'] = 'Modüller gerekli';
$L['ext_requires_plugins'] = 'Eklentiler gerekli';
$L['ext_recommends_modules'] = 'Modüller önerilir';
$L['ext_recommends_plugins'] = 'Eklentiler önerilir';
$L['ext_setup_not_found'] = 'Kurulum dosyası bulunamadı: {$path}';
$L['ext_uninstall_confirm'] = 'Bu eklentiyi kaldırmak istediğinizden emin misiniz? Eklentiye bağlı tüm veriler kaldırılacak ve geri alınamaz.<br/><a href="{$url}">Evet, kaldır ve veriyi sil.</a>';
$L['ext_uninstalling'] = '{$type} &quot;{$name}&quot; kaldırılıyor';
$L['ext_up2date'] = '{$type} &quot;{$name}&quot; güncel';
$L['ext_update_error'] = '{$type} &quot;{$name}&quot; güncellemesi başarısız';
$L['ext_updated'] = '{$type} &quot;{$name}&quot; {$ver} sürümüne güncellendi';
$L['ext_updating'] = '{$type} &quot;{$name}&quot; güncelleniyor';

/**
 * Eklenti Kategorileri
 */
$L['ext_cat_administration-management'] = 'Yönetim &amp; Yönetim';
$L['ext_cat_commerce'] = 'Ticaret &amp; Alışveriş';
$L['ext_cat_community-social'] = 'Topluluk &amp; Sosyal';
$L['ext_cat_customization-i18n'] = 'Özelleştirme &amp; I18n';
$L['ext_cat_data-apis'] = 'Veri Akışları &amp; API\'ler';
$L['ext_cat_development-maintenance'] = 'Geliştirme &amp; Bakım';
$L['ext_cat_editor-parser'] = 'Editörler &amp; İşaretleme';
$L['ext_cat_files-media'] = 'Dosyalar &amp; Medya';
$L['ext_cat_forms-feedback'] = 'Formlar &amp; Geri Bildirim';
$L['ext_cat_gaming-clans'] = 'Oyun &amp; Klanlar';
$L['ext_cat_intranet-groupware'] = 'İntranet &amp; Grup Çalışması';
$L['ext_cat_misc-ext'] = 'Çeşitli';
$L['ext_cat_mobile-geolocation'] = 'Mobil &amp; Coğrafi Konum';
$L['ext_cat_navigation-structure'] = 'Gezinme &amp; Yapı';
$L['ext_cat_performance-seo'] = 'Performans &amp; SEO';
$L['ext_cat_publications-events'] = 'Yayınlar &amp; Etkinlikler';
$L['ext_cat_security-authentication'] = 'Güvenlik &amp; Kimlik Doğrulama';
$L['ext_cat_utilities-tools'] = 'Araçlar &amp; Yardımcı Programlar';
$L['ext_cat_post-install'] = 'Kurulum Sonrası Betikler';

/**
 * Yapı Bölümü
 */
$L['adm_structure_category_not_exists'] = 'Kategori mevcut değil';
$L['adm_structure_category_not_empty'] = 'Kategori boş değil. Önce öğeleri silin.';
$L['adm_structure_code_reserved'] = "Yapı kodu 'all' rezerve edilmiştir.";
$L['adm_structure_code_required'] = 'Gerekli alan eksik: Kod';
$L['adm_structure_defaults'] = 'Yapı Varsayılanları';
$L['adm_structure_path_required'] = 'Gerekli alan eksik: Yol';
$L['adm_structure_title_required'] = 'Gerekli alan eksik: Başlık';
$L['adm_structure_somenotupdated'] = 'Dikkat! Bazı değerler güncellenmedi.';
$L['adm_cat_exists'] = 'Bu kod ile bir kategori zaten mevcut';
$L['adm_tpl_mode'] = 'Şablon modu';
$L['adm_tpl_empty'] = 'Varsayılan';
$L['adm_tpl_forced'] = 'Aynı olarak';
$L['adm_tpl_parent'] = 'Üst kategorinin aynısı';
$L['adm_tpl_code'] = 'Özel kategori veya şablon kodu';
$L['adm_tpl_resyncalltitle'] = 'Tüm sayfa sayacılarını yeniden senkronize et';
$L['adm_tpl_resynctitle'] = 'Kategori sayfa sayacılarını yeniden senkronize et';
$L['adm_help_structure'] = '"system" kategorisine ait sayfalar genel listelerde gösterilmez, bağımsız sayfalar oluşturmak için kullanılır.';

/**
 * Yapı Bölümü
 * Ekstra Alanlar Alt Bölümü
 */
$L['adm_extrafields_desc'] = 'Özel veriler için ekstra alanlar ekle/düzenle';
$L['adm_extrafields_all'] = 'Tüm veritabanı tablolarını göster';
$L['adm_extrafields_table'] = 'Tablo';
$L['adm_extrafields_help_notused'] = 'Kullanılmıyor';
$L['adm_extrafields_help_variants'] = '{seçenek1},{seçenek2},{seçenek3},...';
$L['adm_extrafields_help_range'] = '{min_değer},{maks_değer}';
$L['adm_extrafields_help_data'] = '{min_yıl},{maks_yıl},{tarih_formatı}. Boş bırakılırsa {date_format}, damga kullanılır';
$L['adm_extrafields_help_regex'] = 'Kontrol için Regex';
$L['adm_extrafields_help_file'] = 'Yükleme dizini';
$L['adm_extrafields_help_separator'] = 'Değer ayırıcı';
$L['adm_help_info'] = '<b>Temel HTML</b> alanı boş bırakırsanız otomatik olarak ayarlanır';
$L['adm_help_newtags'] = '<br /><br /><b>Yeni tpl dosyalarındaki etiketler:</b>';

/**
 * Kullanıcılar Bölümü
 */
$L['adm_rightspergroup'] = 'Grup başına haklar';
$L['adm_maxsizesingle'] = 'PFS\'de tek bir dosya için maksimum boyut, KiB';
$L['adm_maxsizeallpfs'] = 'PFS\'de tüm dosyaların toplam boyutu, KiB';
$L['adm_copyrightsfrom'] = 'Grubun haklarını aynı olarak ayarla';
$L['adm_rights_maintenance'] = 'Bakım modu açıkken siteye erişim';
$L['adm_skiprights'] = 'Bu grup için hakları atla';
$L['adm_group_has_no_rights'] = 'Grubun hakları yok';
$L['adm_groups_name_empty'] = 'Grup adı boş olmamalıdır';
$L['adm_groups_title_empty'] = 'Grup üyesi başlığı boş olmamalıdır';
$L['users_grp_5_title'] = 'Yöneticiler';
$L['users_grp_5_desc'] = 'Maksimum yetkiye sahip süper kullanıcılar ve site yöneticileri';
$L['users_grp_6_title'] = 'Moderatörler';
$L['users_grp_6_desc'] = 'İçerik yöneticileri ve güvenilir katkıda bulunanlar';
$L['users_grp_4_title'] = 'Üyeler';
$L['users_grp_4_desc'] = 'Temel haklara sahip kayıtlı kullanıcılar';
$L['users_grp_3_title'] = 'Yasaklılar';
$L['users_grp_3_desc'] = 'Uygunsuz faaliyetlerde bulunan kullanıcı hesapları';
$L['users_grp_2_title'] = 'Aktif Olmayan';
$L['users_grp_2_desc'] = 'Kaydı tamamlanmamış kullanıcı hesapları';
$L['users_grp_1_title'] = 'Misafirler';
$L['users_grp_1_desc'] = 'Kayıtlı olmayan ziyaretçiler veya çıkış yapmış kullanıcılar';

/**
 * Eklenti Bölümü
 */
$L['adm_defauth_guests'] = 'Misafirler için varsayılan haklar';
$L['adm_deflock_guests'] = 'Misafirler için kilit maskesi';
$L['adm_defauth_members'] = 'Üyeler için varsayılan haklar';
$L['adm_deflock_members'] = 'Üyeler için kilit maskesi';

$L['adm_present'] = 'Mevcut';
$L['adm_missing'] = 'Eksik';
$L['adm_paused'] = 'Duraklatıldı';
$L['adm_running'] = 'Çalışıyor';
$L['adm_partrunning'] = 'Kısmen çalışıyor';
$L['adm_partstopped'] = 'Kısmen durdu';
$L['adm_installed'] = 'Yüklü';
$L['adm_notinstalled'] = 'Yüklü değil';

$L['adm_plugsetup'] = 'Eklenti Kurulumu';
$L['adm_override_guests'] = 'Sistem geçersiz kılma, misafirler ve aktif olmayanlar yönetime izin verilmez';
$L['adm_override_banned'] = 'Sistem geçersiz kılma, Yasaklı';
$L['adm_override_admins'] = 'Sistem geçersiz kılma, Yöneticiler';

$L['adm_opt_install'] = 'Kur';
$L['adm_opt_install_explain'] = 'Bu, bu eklentinin yeni bir kurulumunu yapacaktır';
$L['adm_opt_pause'] = 'Duraklat';
$L['adm_opt_pauseall'] = 'Tümünü duraklat';
$L['adm_opt_pauseall_explain'] = 'Bu, eklentinin tüm parçalarını duraklatacaktır (devre dışı bırakacaktır).';
$L['adm_opt_update'] = 'Güncelle';
$L['adm_opt_update_explain'] = 'Bu, eklenti yapılandırmasını ve verisini güncelleyecektir, eğer disk üzerindeki eklenti dosyaları zaten güncellenmişse';
$L['adm_opt_uninstall'] = 'Kaldır';
$L['adm_opt_uninstall_explain'] = 'Bu, eklentinin tüm parçalarını devre dışı bırakır ve tüm veri ve yapılandırmasını siler, ancak dosyaları fiziksel olarak kaldırmaz.';
$L['adm_opt_unpause'] = 'Duraklatmayı kaldır';
$L['adm_opt_unpauseall'] = 'Tüm duraklatmaları kaldır';
$L['adm_opt_unpauseall_explain'] = 'Bu, eklentinin tüm parçalarının duraklatmasını kaldıracaktır (etkinleştirecektir).';

$L['adm_opt_setup_missing'] = 'Hata: kurulum dosyası eksik!';

$L['adm_sort_alphabet'] = 'Alfabetik';
$L['adm_sort_category'] = 'Kategori Görünümü';

$L['adm_only_installed'] = 'Yüklü';

$L['adm_hook_changed'] = 'Uyarı! Bu dosya DB\'de düzgün kayıtlı değil veya kurulumdan sonra değiştirildi.<br />';
$L['adm_hook_notregistered'] = ' — Kanca(lar): <b>{$hooks}</b> kayıtlı değil<br />';
$L['adm_hook_notfound'] = ' — Kanca(lar): <b>{$hooks}</b> dosyada kayıtlı ancak bulunamadı<br />';
$L['adm_hook_filenotfound'] = ' — Dosya: <b>{$file}</b> bulunamadı!<br />';
$L['adm_hook_updatenote'] = 'Lütfen yukarıdaki «<b>güncelle</b>» düğmesiyle eklentiyi güncelleyin.';

/**
 * Araçlar Bölümü
 */
$L['adm_listisempty'] = 'Liste boş';

/**
 * Diğer Bölüm
 * Önbellek Alt Bölümü
 */
$L['adm_delcacheitem'] = 'Önbellek öğesi kaldırıldı';
$L['adm_internalcache'] = 'Dahili önbellek';
$L['adm_internalcache_desc'] = 'Sıkça değişen verileri saklamak için önbellek';
$L['adm_purgeall_done'] = 'Önbellek tamamen temizlendi';
$L['adm_diskcache'] = 'Disk önbelleği';
$L['adm_diskcache_desc'] = 'Dosya verilerini saklamak için önbellek';
$L['adm_cache_showall'] = 'Tümünü göster';

/**
 * Diğer Bölüm
 * Günlük Alt Bölümü
 */
$L['adm_log'] = 'Sistem günlüğü';
$L['adm_log_desc'] = 'Web sitesi kullanıcı faaliyetleri hakkında bilgi';
$L['adm_infos'] = 'Bilgiler';
$L['adm_infos_desc'] = 'PHP/Zend ve işletim sistemi sürümleri, sunucu saat dilimi bilgileri';
$L['adm_phpinfo'] = 'PHP Bilgisi';
$L['adm_phpinfo_desc'] = 'Mevcut PHP yapılandırması hakkında bilgi';
$L['adm_versiondclocks'] = 'Sürümler ve saatler';
$L['adm_checkcorethemes'] = 'Çekirdek dosyaları ve temaları kontrol et';
$L['adm_checkcorenow'] = 'Çekirdek dosyaları şimdi kontrol et!';
$L['adm_checkingcore'] = 'Çekirdek dosyaları kontrol ediliyor...';
$L['adm_checkthemes'] = 'Tüm dosyaların temalarda mevcut olup olmadığını kontrol et';
$L['adm_checktheme'] = 'Tema için TPL dosyalarını kontrol et';
$L['adm_checkingtheme'] = 'Tema kontrol ediliyor...';
$L['adm_check_ok'] = 'Tamam';
$L['adm_check_missing'] = 'Eksik';
$L['adm_ref_prune'] = 'Temizlendi';
$L['adm_log_uri'] = 'URL adresi';

/**
 * Diğer Bölüm
 * Bilgi Alt Bölümü
 */
$L['adm_core_info'] = 'Cotonti bilgisi';
$L['adm_server_info'] = 'Sunucu bilgisi';
$L['adm_phpver'] = 'PHP sürümü';
$L['adm_zendver'] = 'Zend sürümü';
$L['adm_interface'] = 'Web sunucusu ile PHP arasındaki arayüz';
$L['adm_cachedrivers'] = 'Önbellek sürücüleri';
$L['adm_os'] = 'İşletim sistemi';
$L['adm_clocks'] = 'Saatler';
$L['adm_time1'] = '#1: Ham sunucu zamanı';
$L['adm_time2'] = '#2: Sunucu tarafından döndürülen GMT zamanı';
$L['adm_time3'] = '#3: GMT zamanı + sunucu ofseti (Cotonti referansı)';
$L['adm_time4'] = '#4: Profilinizden ayarlanmış yerel zamanınız';
$L['adm_help_versions'] = "Sunucu saat dilimini ayarlayarak saat #3'ü doğru ayarlayın.<br />\nSaat #4 profilinizdeki saat dilimi ayarına bağlıdır.<br />\nSaatler #1 ve #2 Cotonti tarafından dikkate alınmaz.";

/**
 * Genel Girişler
 */
$L['adm_area'] = 'Alan';
$L['adm_clicktoedit'] = '(Düzenlemek için tıklayın)';
$L['adm_confirm'] = 'Onaylamak için bu düğmeye basın:';
$L['adm_done'] = 'Tamamlandı';
$L['adm_failed'] = 'Başarısız';
$L['adm_from'] = 'Kimden';
$L['adm_more'] = 'Daha fazla araç...';
$L['adm_purgeall'] = 'Tümünü temizle';
$L['adm_queue_unvalidated'] = 'Doğrulanmamış';
$L['adm_queue_validated'] = 'Doğrulanmış';
$L['adm_required'] = '(Gerekli)';
$L['adm_setby'] = 'Tarafından ayarlandı';
$L['adm_to'] = 'Kime';
$L['adm_totalsize'] = 'Toplam boyut';
$L['adm_warnings'] = 'Uyarılar';

$L['editdeleteentries'] = 'Girdileri düzenle veya sil';
$L['viewdeleteentries'] = 'Girdileri görüntüle veya sil';

$L['alreadyaddnewentry'] = 'Yeni giriş eklendi';
$L['alreadyupdatednewentry'] = 'Giriş güncellendi';
$L['alreadydeletednewentry'] = 'Giriş silindi';

$L['adm_invalid_input'] = '\'{$field_name}\' değişkeni için geçersiz değer \'{$value}\'';
$L['adm_set_default'] = 'Varsayılan değer kullanıldı';
$L['adm_max'] = 'izin verilen maksimum \'{$value}\'';
$L['adm_min'] = 'izin verilen minimum \'{$value}\'';
$L['adm_set'] = 'Kullanarak';
$L['adm_partially_updated'] = 'Tüm değerler güncellenmedi';
$L['adm_already_updated'] = 'Zaten güncellendi';

/**
 * Ekstra Alanlar (Sayfalar & Yapı & Kullanıcılar için Genel Girişler)
 */
$L['adm_extrafields'] = &$L['Extrafields'];
$L['adm_extrafield_added'] = 'Yeni ekstra alan başarıyla eklendi.';
$L['adm_extrafield_error_name'] = 'Alan adı yanlış doldurulmuş. Sadece Latin harfleri, sayılar ve alt çizgiler kullanılabilir.';
$L['adm_extrafield_error_name_missing'] = 'Alan adı boş olmamalıdır.';
$L['adm_extrafield_not_added'] = 'Hata! Yeni ekstra alan eklenmedi.';
$L['adm_extrafield_updated'] = 'Ekstra alan \'%1$s\' başarıyla güncellendi.';
$L['adm_extrafield_not_updated'] = 'Hata! Ekstra alan \'%1$s\' güncellenmedi.';
$L['adm_extrafield_removed'] = 'Ekstra alan başarıyla kaldırıldı.';
$L['adm_extrafield_not_removed'] = 'Hata! Ekstra alan silinmedi.';
$L['adm_extrafield_confirmdel'] = 'Bu ekstra alanı gerçekten silmek istiyor musunuz? Bu alandaki tüm veriler kaybolacak!';
$L['adm_extrafield_confirmupd'] = 'Bu ekstra alanı gerçekten güncellemek istiyor musunuz? Bu alandaki bazı veriler kaybolabilir!';
$L['adm_extrafield_default'] = 'Varsayılan değer';
$L['adm_extrafield_required'] = 'Gerekli alan';
$L['adm_extrafield_parse'] = 'Ayrıştırıcı';
$L['adm_extrafield_enable'] = 'Alanı etkinleştir';
$L['adm_extrafield_params'] = 'Alan parametreleri';

$L['extf_Name'] = 'Ad';
$L['extf_Type'] = 'Alan türü';
$L['extf_Base_HTML'] = 'Temel HTML';
$L['extf_Page_tags'] = 'Etiketler';
$L['extf_Description'] = 'Açıklama (_TITLE)';

$L['adm_extrafield_new'] = 'Yeni ekstra alan';
$L['adm_extrafield_noalter'] = 'Veritabanında gerçek alan ekleme, sadece ekstra olarak kaydet';
$L['adm_extrafield_selectable_values'] = 'Seçenek, radyo ve kontrol kutusu için seçenekler (virgülle ayırın)';
$L['adm_help_extrafield'] = 'İpucu: "Temel HTML" alanını boş bırakıp Güncelle düğmesine bastığınızda otomatik olarak varsayılana ayarlanır.';

/**
 * Yardım mesajları
 */
$L['adm_help_cache'] = 'Mevcut değil';
$L['adm_help_check1'] = 'Mevcut değil';
$L['adm_help_check2'] = 'Mevcut değil';
$L['adm_help_config'] = 'Mevcut değil';
