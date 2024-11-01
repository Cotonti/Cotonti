<?php

/**
 * Kullanıcı Modülü için Türkçe Dil Dosyası (users.tr.lang.php)
 *
 * @package Cotonti
 */

defined('COT_CODE') or die('Hatalı URL.');

/**
 * Kullanıcı kimlik doğrulama
 */
$L['users_nameormail'] = 'Kullanıcı adı veya e-posta';
$L['users_rememberme'] = 'Beni hatırla';
$L['users_lostpass'] = 'Şifrenizi mi unuttunuz?';
$L['users_maintenance1'] = 'Site bakım modunda';
$L['users_maintenance2'] = 'Yalnızca yöneticiler ve yetkili gruplar erişebilir';
$L['users_loggedinas'] = 'Şu kullanıcı olarak giriş yaptınız';
$L['users_logoutfirst'] = 'Başka bir hesapla giriş yapmak istiyorsanız önce çıkış yapmanız gerekir.';

/**
 * Kullanıcı kaydı
 */
$L['users_validemail'] = 'Geçerli e-posta';
$L['users_validemailhint'] = '(Kayıt işlemini tamamlamak için geçerli bir e-posta gereklidir)';
$L['users_confirmpass'] = 'Şifreyi doğrula';

$L['aut_contactadmin'] = 'Sorun yaşarsanız bir yönetici ile iletişime geçin';
$L['aut_emailalreadyindb'] = 'Girdiğiniz e-posta zaten veritabanında mevcut';
$L['aut_emailbanned'] = 'Bu e-posta (veya bu alan adı) yasaklanmış, sebebi: ';
$L['aut_emailtooshort'] = 'E-posta geçerli değil!';
$L['aut_invalidloginchars'] = 'Giriş geçersiz karakterler içeriyor';
$L['aut_logintitle'] = 'Giriş formu';
$L['aut_mailnoticetitle'] = 'E-posta geçişi';
$L['aut_passwordmismatch'] = 'Şifre alanları uyuşmuyor!';
$L['aut_passwordtooshort'] = 'Şifre en az 4 karakter uzunluğunda olmalı ve yalnızca harf, sayı ve alt çizgi içermelidir.';
$L['aut_registersubtitle'] = '';
$L['aut_registertitle'] = 'Yeni üye hesabı kaydı';
$L['aut_regreqnoticetitle'] = 'Yeni hesap isteği';
$L['aut_regrequesttitle'] = 'Kayıt isteği';
$L['aut_usernamealreadyindb'] = 'Girdiğiniz kullanıcı adı veritabanında zaten mevcut';
$L['aut_usernametooshort']= 'Kullanıcı adı en az 2 karakter uzunluğunda olmalıdır';

/**
 * Kullanıcı kaydı: mesajlar
 */
$L['aut_regrequest'] = "Merhaba %1\$s,\n\nHesabınız şu anda pasif, giriş yapabilmek için bir yöneticinin hesabınızı etkinleştirmesi gerekmektedir. Bu işlem tamamlandığında başka bir e-posta alacaksınız.";

$L['aut_regreqnotice'] = "Bu e-postayı almanızın nedeni %1\$s tarafından yeni bir hesap talep edilmesidir.\nBu kullanıcı, hesabı elle 'aktif' olarak ayarlanana kadar giriş yapamayacaktır:\n%2\$s";

$L['aut_emailreg'] = "Merhaba %1\$s,\n\nHesabınızı kullanabilmek için bu bağlantı ile etkinleştirmeniz gerekmektedir:\n%2\$s\n\nKısa süre önce aktif olmayan üyeliği iptal etmek için bu bağlantıyı kullanın:\n%3\$s";

$L['aut_emailchange'] = "Merhaba %1\$s,\nE-postanızı değiştirmek için lütfen bu etkinleştirme bağlantısını kullanın:\n%2\$s";

/**
 * Kullanıcı listesi
 */
$L['users_usersperpage'] = 'Sayfa başına kullanıcı';
$L['users_usersinthissection'] = 'Toplam kullanıcı';

$L['pro_emailandpass'] = 'E-posta ve şifreyi aynı anda değiştiremezsiniz';
$L['pro_passdiffer'] = 'Şifre alanları uyuşmuyor';
$L['pro_passtoshort'] = 'Şifre en az 4 karakter uzunluğunda olmalı ve yalnızca harf, sayı ve alt çizgi içermelidir.';
$L['pro_subtitle'] = 'Kişisel hesabınız';
$L['pro_title'] = 'Profil';
$L['pro_wrongpass'] = 'Mevcut şifrenizi girmediniz veya yanlış';
$L['pro_invalidbirthdate'] = 'Doğum tarihi geçersiz.';

$L['useed_accountactivated'] = 'Hesap etkinleştirildi';
$L['useed_email'] = 'Bu e-postayı almanızın nedeni, bir yöneticinin hesabınızı etkinleştirmesidir. Artık kullanıcı adınız ve şifrenizle giriş yapabilirsiniz.';
$L['useed_subtitle'] = '&nbsp;';
$L['useed_title'] = 'Düzenle';

$L['use_allbannedusers'] = 'Yasaklanan kullanıcılar';
$L['use_allinactiveusers'] = 'Pasif kullanıcılar';
$L['use_allusers'] = 'Tüm kullanıcılar';
$L['use_byfirstletter'] = 'Ad ile başlayan';
$L['use_subtitle'] = 'Kayıtlı üyeler';
$L['use_title'] = 'Kullanıcılar';

$L['pasrec_title'] = 'Şifre kurtarma';
$L['pasrec_email1'] = "Merhaba %1\$s,\nŞifrenizi sıfırlamak için aşağıdaki bağlantıyı kullanabilirsiniz:\n%2\$s\n\nDikkat: Bu şifre kurtarma e-postasını talep etmediyseniz lütfen dikkate almayın. Talep edenin IP adresi %3\$s, talep zamanı %4\$s.";
$L['pasrec_email2'] = 'İsteğiniz üzerine şifreniz sıfırlandı. Lütfen şifrenizi mümkün olan en kısa sürede değiştirin ve bu e-postayı silin. Yeni şifreniz';
$L['pasrec_explain1'] = 'E-posta adresinizi girin.';
$L['pasrec_explain2'] = 'Acil bağlantıyı içeren bir e-posta alacaksınız. Şifrenizi sıfırlamak için üzerine tıklayın.';
$L['pasrec_explain3'] = 'Şifre sıfırlama isteğinizi iki kez onayladığınızda, sistem tarafından oluşturulan rastgele bir şifre alacaksınız.';
$L['pasrec_explain4'] = 'Profilinizde e-posta alanını temizlediyseniz şifrenizi kurtaramazsınız. Bu durumda bir yöneticiye e-posta ile ulaşın.';
$L['pasrec_mailsent'] = 'Tamamlandı, lütfen birkaç dakika içinde posta kutunuzu kontrol edin ve acil bağlantıya tıklayın. Ardından talimatları izleyin.';
$L['pasrec_mailsent2'] = 'Şifre sıfırlama tamamlandı. Lütfen posta kutunuzu kontrol edin ve yeni şifrenizi alın.';
$L['pasrec_request'] = 'İstek';
$L['pasrec_youremail'] = 'E-posta adresiniz: ';

/**
 * Kullanıcı detayları
 */
$L['users_sendpm'] = 'Özel mesaj gönder';

/**
 * Kullanıcı profili & düzenleme
 */
$L['users_id'] = 'Kullanıcı ID';
$L['users_hideemail'] = 'E-posta adresini her zaman gizle';
$L['users_myProfile'] = 'Profilim';
$L['users_pmnotify'] = 'ÖM bildirimi';
$L['users_pmnotifyhint'] = '(Yeni özel mesaj olduğunda e-posta ile bildir)';
$L['users_profileSettings'] = 'Profil ayarları';
$L['users_newpass'] = 'Yeni bir şifre belirle';
$L['users_newpasshint1'] = '(Mevcut şifreyi korumak için boş bırakın)';
$L['users_newpasshint2'] = '(Yeni şifrenizi iki kez girin)';
$L['users_oldpasshint'] = '(Yeni bir şifre belirlemek için mevcut olanı girin)';
$L['users_lastip'] = 'Son bilinen IP';
$L['users_logcounter'] = 'Giriş sayacı';
$L['users_deleteuser'] = 'Bu kullanıcıyı sil';
$L['users_changeemail'] = 'E-posta değiştir';

/**
 * Çeşitli
 */
$L['users_group_not_found'] = 'Grup bulunamadı';

/**
 * Temadan taşınan öğeler
 */

$themelang['usersprofile_Emailpassword'] = 'Mevcut şifreniz';
$themelang['usersprofile_Emailnotes'] = '<p><b>E-posta geçiş süreci:</b></p><ol><li>Mevcut e-posta adresinizi kullanamazsınız</li><li>Güvenlik nedeniyle mevcut şifrenizi girmeniz gerekir</li><li>Yeni e-posta adresinin geçerli olduğunu kanıtlamak için hesabınızı tekrar etkinleştirmeniz gerekmektedir</li><li>Hesabınız doğrulama bağlantısını kullanana kadar askıya alınacaktır</li><li>E-posta doğrulaması tamamlandıktan sonra hesabınız hemen aktif hale gelecektir</li><li>Yeni e-posta adresinizi dikkatlice yazın; daha sonra değiştirme şansınız olmayacaktır</li><li>Bir hata yaptıysanız bir yöneticiye e-posta ile ulaşın.</li></ol><p><b>E-posta doğrulaması gerekmezse, yeni e-posta adresi hemen geçerli olur.</b></p>';

/**
 * Kullanıcı tam adı gösterim formatı
 */
$R['users_full_name'] = '{$firstname} {$lastname}';
