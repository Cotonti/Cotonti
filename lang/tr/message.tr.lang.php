<?php
/**
 * Mesaj Modülü için Türkçe Dil Dosyası (message.tr.lang.php)
 *
 * @package Cotonti
 */

defined('COT_CODE') or die('Hatalı URL.');

// Genel Mesajlar
$L['msg_Message'] = 'Mesaj';
$L['msg_Error'] = 'Hata';
$L['msg_Warning'] = 'Uyarı';
$L['msg_Security'] = 'Güvenlik';
$L['msg_System'] = 'Sistem';

$L['msgredir'] = 'Yönlendiriliyor...';

/**
 * Hesap İlgili Mesajlar
 */

$L['msg100_title'] = 'Kullanıcı giriş yapmadı, profile erişim engellendi';
$L['msg100_body'] = 'Profilinizi yalnızca kayıtlı ve giriş yapmış kullanıcılar görüntüleyebilir!';

$L['msg101_title'] = 'Kullanıcı giriş yapmadı';
$L['msg101_body'] = 'Giriş yapmanıza gerek yok, zaten giriş yapmadınız.';

$L['msg102_title'] = 'Kullanıcı çıkış yaptı';
$L['msg102_body'] = 'Başarılı, çıkış yaptınız.';

$L['msg105_title'] = 'Kayıt tamamlandı (1. adım)';
$L['msg105_body'] = 'Lütfen birkaç dakika içinde posta kutunuzu kontrol edin.<br />Kayıt işlemini tamamlamak için<br />mesajdaki URL’ye tıklayın.<br />Bu işlem tamamlanana kadar hesabınız kullanıcı listesinde "pasif" olarak işaretlenecek.';

$L['msg106_title'] = 'Kayıt tamamlandı';
$L['msg106_body'] = 'Hoş geldiniz, hesabınız artık geçerli ve aktif.<br />Şifrenizle giriş yapabilirsiniz.';

$L['msg109_title'] = 'Kullanıcı silindi';
$L['msg109_body'] = 'Tamamlandı, kullanıcı silindi.';

$L['msg117_title'] = 'Kayıt devre dışı bırakıldı';
$L['msg117_body'] = 'Yeni kullanıcılar için kayıt işlemi devre dışı bırakılmıştır.';

$L['msg118_title'] = 'Kayıt tamamlandı (1. adım)';
$L['msg118_body'] = 'Hesabınız şu anda pasif durumda,<br />giriş yapabilmeniz için site yöneticisinin hesabınızı aktif hale getirmesi gerekmektedir.<br />Bu işlem tamamlandığında size bir e-posta gönderilecektir.';

$L['msg151_title'] = 'Giriş başarısız (yanlış isim veya şifre)';
$L['msg151_body'] = 'Hata, sağladığınız kullanıcı adı veritabanında bulunamadı veya şifre uyuşmuyor!';

$L['msg152_title'] = 'Giriş başarısız (hesap aktif değil)';
$L['msg152_body'] = 'Hata, hesabınız kayıtlı ancak henüz aktif değil.';

$L['msg153_title'] = 'Giriş başarısız (kullanıcı yasaklandı)';
$L['msg153_body'] = 'Hata, hesabınız yasaklandı.';

$L['msg154_title'] = 'Şifre kurtarma başarısız (e-posta bulunamadı)';
$L['msg154_body'] = 'Hata, sağladığınız e-posta adresi veritabanında bulunamadı!';

$L['msg157_title'] = 'Geçersiz doğrulama URL’si';
$L['msg157_body'] = 'Bu doğrulama URL’si geçerli değil.';

/**
 * Yönlendirme Mesajları
 */

$L['msg300_title'] = 'Yeni gönderi';
$L['msg300_body'] = 'Tamam, bu öğe veritabanına kaydedildi.<br />Bir moderatör en kısa sürede inceleyecektir.<br />Teşekkürler!';

/**
 * İstemci Hata Mesajları
 */

$L['msg400_title'] = '400 - Geçersiz Dosya';
$L['msg400_body'] = 'Tarayıcınız (veya vekil sunucunuz) bu sunucunun anlayamayacağı bir istek gönderdi.';

$L['msg401_title'] = '401 - Yetkilendirme Gerekli';
$L['msg401_body'] = 'Bu sunucu, belirtilen URL’ye erişim izniniz olduğunu doğrulayamıyor.<br />Yanlış kimlik bilgileri sağladınız (örneğin, hatalı şifre) veya tarayıcınız gerekli kimlik bilgilerini nasıl sağlayacağını bilmiyor olabilir.';

$L['msg403_title'] = '403 - Yasaklandı';
$L['msg403_body'] = 'İstediğiniz dizine veya URL’ye erişim izniniz yok.<br />Bu bir hata olduğunu düşünüyorsanız, lütfen ilgili sayfanın yöneticisini bilgilendirin.';

$L['msg404_title'] = '404 - Bulunamadı';
$L['msg404_body'] = 'İstediğiniz nesne veya URL bu sunucuda bulunamadı.<br />Takip ettiğiniz bağlantı eski, yanlış veya sunucu size erişim izni vermiyor olabilir.';

/**
 * Sunucu Hata Mesajları
 */

$L['msg500_title'] = '500 İç Sunucu Hatası';
$L['msg500_body'] = 'Sunucu, isteğinizi tamamlayamadı.<br />Lütfen hatanın oluştuğu zamanı ve muhtemelen hataya neden olan işlemi belirterek yöneticiyi bilgilendirin.';

$L['msg503_title'] = '503 Hizmet Geçici Olarak Kullanılamıyor';
$L['msg503_body'] = 'İstediğiniz sayfa teknik nedenlerden dolayı geçici olarak kullanılamıyor.<br />Lütfen daha sonra tekrar deneyin veya site yöneticisiyle iletişime geçin.';

/**
 * Forum Mesajları
 */

$L['msg602_title'] = 'Bölüm kilitli';
$L['msg602_body'] = 'Bu bölüm kilitli';

$L['msg603_title'] = 'Konu kilitli';
$L['msg603_body'] = 'Bu konu kilitli';

/**
 * Sistem Mesajları
 */

$L['msg900_title'] = 'Yapım aşamasında';
$L['msg900_body'] = 'Sayfa henüz tamamlanmadı, lütfen daha sonra tekrar gelin.';

$L['msg904_title'] = 'Yalnızca yöneticiye özel sistem sayfaları';
$L['msg904_body'] = 'Bu sistem sayfalarını görüntüleme izniniz yok.';

$L['msg907_title'] = 'Eklenti yüklenmedi';
$L['msg907_body'] = 'Bu eklentiyi yüklemeye çalışırken bir hata oluştu, dosya(lar) eksik olabilir mi?';

$L['msg911_title'] = 'Dil dosyası eksik';
$L['msg911_body'] = 'Bu dil paketini kontrol ederken bir hata oluştu.';

$L['msg915_title'] = 'Hata!';
$L['msg915_body'] = 'En az bir alan boş bırakılmış.';

$L['msg916_title'] = 'Veritabanı güncellendi';
$L['msg916_body'] = 'Tamamlandı, veritabanı başarıyla güncellendi.<br />Etkilenen girişler: $num';

$L['msg920_title'] = 'Onay gerekli';
$L['msg920_body'] = 'Bu işlemi gerçekleştirmek istediğinizden emin misiniz?';

$L['msg930_title'] = 'Erişim reddedildi';
$L['msg930_body'] = 'Bu işlemi yapmaya yetkiniz yok.';

$L['msg940_title'] = 'Bölüm devre dışı';
$L['msg940_body'] = 'Web sitesinin bu bölümü devre dışı bırakılmıştır.';

$L['msg950_title'] = 'İstek parametreleri hatası';
$L['msg950_body'] = 'İstek parametrelerinden biri geçersiz veya süresi dolmuş. Lütfen geri dönüp formu tekrar gönderin.';

$L['msg951_title'] = 'Oturum süresi doldu';
$L['msg951_body'] = 'Oturumunuz artık geçerli değil. Lütfen tekrar deneyin.';
