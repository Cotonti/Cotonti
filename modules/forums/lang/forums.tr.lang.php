<?php
/**
 * Forums Modülü için Türkçe Dil Dosyası (forums.tr.lang.php)
 *
 * @package Forums
 * @copyright (c) Cotonti Ekibi
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Yanlış URL.');

/**
 * Forums Yapılandırma
 */

$L['cfg_antibumpforums'] = 'Anti-bump koruması';
$L['cfg_antibumpforums_hint'] = 'Kullanıcıların aynı konuda art arda mesaj göndermesini engeller';
$L['cfg_hideprivateforums'] = 'Özel forumları gizle';
$L['cfg_hideprivateforums_hint'] = '';
$L['cfg_hottopictrigger'] = '\'sıcak\' bir konu için gönderi sayısı';
$L['cfg_hottopictrigger_hint'] = '';
$L['cfg_maxpostsperpage'] = 'Sayfa başına maksimum gönderi';
$L['cfg_maxpostsperpage_hint'] = ' ';
$L['cfg_maxtopicsperpage'] = 'Sayfa başına maksimum konu';
$L['cfg_maxtopicsperpage_hint'] = '';
$L['cfg_mergeforumposts'] = 'Gönderi birleştirme özelliği';
$L['cfg_mergeforumposts_hint'] = 'Kullanıcının gönderileri art arda gönderilmişse birleştirir, anti-bump kapalı olmalıdır';
$L['cfg_mergetimeout'] = 'Gönderi birleştirme zaman aşımı';
$L['cfg_mergetimeout_hint'] = 'Zaman aşımı süresinden sonra art arda gönderilmişse kullanıcı gönderilerini birleştirmez (Saat cinsinden), gönderi birleştirme açık olmalıdır (Bu özelliği devre dışı bırakmak için sıfır ayarlayın)';
$L['cfg_minpostlength'] = 'Min. gönderi uzunluğu';
$L['cfg_minpostlength_hint'] = ' ';
$L['cfg_mintitlelength'] = 'Min. konu başlığı uzunluğu';
$L['cfg_mintitlelength_hint'] = ' ';
$L['cfg_title_posts'] = 'Forum Gönderi başlığı formatı';
$L['cfg_title_posts_hint'] = 'Seçenekler: {FORUM}, {BÖLÜM}, {BAŞLIK}';
$L['cfg_title_topics'] = 'Forum Konuları başlık formatı';
$L['cfg_title_topics_hint'] = 'Seçenekler: {FORUM}, {BÖLÜM}';
$L['cfg_enablereplyform'] = 'Her sayfada yanıt formunu göster';
$L['cfg_enablereplyform_hint'] = '';
$L['cfg_edittimeout'] = 'Düzenleme zaman aşımı';
$L['cfg_edittimeout_hint'] = 'Kullanıcıların verilen süre sonrasında kendi gönderilerini düzenlemelerini veya silmelerini engeller (saat cinsinden, 0 zaman aşımını devre dışı bırakır)';
$L['cfg_minimaxieditor'] = 'Yapılandırılabilir görsel düzenleyici';
$L['cfg_minimaxieditor_params'] = 'Minimal düğme seti, Standart düğme seti, Gelişmiş düğme seti';

$L['cfg_allowusertext'] = 'İmzaları göster';
$L['cfg_allowbbcodes'] = 'BBkodları etkinleştir';
$L['cfg_allowsmilies'] = 'Gülümsemeleri etkinleştir';
$L['cfg_allowprvtopics'] = 'Özel konulara izin ver';
$L['cfg_allowviewers'] = 'Görüntüleyicileri etkinleştir';
$L['cfg_allowpolls'] = 'Anketleri etkinleştir';
$L['cfg_countposts'] = 'Gönderileri say';
$L['cfg_countposts_hint'] = 'Bu kategorideki gönderileri kullanıcı gönderi sayımına dahil et';
$L['cfg_autoprune'] = 'Konuları * gün sonra otomatik temizle';
$L['cfg_defstate'] = 'Varsayılan durum';
$L['cfg_defstate_params'] = 'Kapalı, Açık';
$L['cfg_keywords'] = 'Anahtar kelimeler';
$L['cfg_metatitle'] = 'Meta başlık';
$L['cfg_metadesc'] = 'Meta açıklama';

$L['info_desc'] = 'Bölümler, alt bölümler, konular ve gönderilerle topluluk siteleri için temel forumlar';

/**
 * Ana
 */

$L['forums_post'] = 'Gönderi';
$L['forums_posts'] = 'Gönderiler';
$L['forums_topic'] = 'Konu';
$L['forums_topics'] = 'Konular';

$L['forums_antibump'] = 'Anti-bump koruması etkin, aynı konuda art arda gönderi yapamazsınız.';
$L['forums_editPost'] = 'Gönderiyi düzenle';
$L['forums_keepmovedlink'] = 'Taşınan Konu bağlantısını koru';
$L['forums_markallasread'] = 'Tüm gönderileri okundu olarak işaretle';
$L['forums_mergetime'] = '%1$s sonra eklendi:';
$L['forums_messagetooshort'] = 'Konu mesajı çok kısa';
$L['forums_newtopic'] = 'Yeni konu';
$L['forums_newpoll'] = 'Yeni anket';
$L['forums_titletooshort'] = 'Konu başlığı çok kısa veya eksik';
$L['forums_topiclocked'] = 'Bu konu kilitli, yeni gönderilere izin verilmiyor.';
$L['forums_topicoptions'] = 'Konu seçenekleri';
$L['forums_updatedby'] = 'Bu gönderi %1$s tarafından düzenlendi (%2$s, %3$s önce)';
$L['forums_postedby'] = 'Gönderen';
$L['forums_edittimeoutnote'] = 'Kendi gönderisini düzenleme veya silme için zaman aşımı ';
$L['forums_moveToSameSection'] = 'Konuyu aynı bölüme taşıyamazsınız. Lütfen başka bir bölüm seçin.';

$L['forums_privatetopic1'] = 'Bu konuyu özel olarak işaretle';
$L['forums_privatetopic2'] = 'sadece forum moderatörleri ve konuyu başlatan kişi (siz) okuyup yanıtlayabilecektir';
$L['forums_privatetopic'] = 'Bu konu özeldir, sadece moderatörler ve konuyu başlatan kişi burada okuyabilir ve yanıtlayabilir.';

$L['forums_searchinforums'] = 'Forumlarda ara';
$L['forums_markasread'] = 'Tüm gönderileri okundu olarak işaretle';
$L['forums_foldall'] = 'Hepsini daralt';
$L['forums_unfoldall'] = 'Hepsini genişlet';
$L['forums_viewers'] = 'Görüntüleyiciler';

$L['forums_nonewposts'] = 'Yeni gönderi yok';
$L['forums_newposts'] = 'Yeni gönderiler';
$L['forums_nonewpostspopular'] = 'Yeni gönderi yok (popüler)';
$L['forums_newpostspopular'] = 'Yeni gönderiler (popüler)';
$L['forums_sticky'] = 'Sabit';
$L['forums_newpostssticky'] = 'Yeni gönderiler (sabit)';
$L['forums_locked'] = 'Kilitli';
$L['forums_newpostslocked'] = 'Yeni gönderiler (kilitli)';
$L['forums_announcment'] = 'Duyuru';
$L['forums_newannouncment'] = 'Yeni duyuru';
$L['forums_movedoutofthissection'] = 'Bu bölümden taşındı';

$L['forums_announcement'] = 'Duyuru';
$L['forums_bump'] = 'Bump';
$L['forums_makesticky'] = 'Sabit';
$L['forums_private'] = 'Özel';

$L['forums_explainbump'] = 'Konuyu konular listesinde ilk sıraya getir (başka bir konu güncellenene kadar)';
$L['forums_explainlock'] = 'Konuyu kilitle (yeni gönderilere kapalı)';
$L['forums_explainsticky'] = 'Konuyu konular listesinde ilk sırada tut (konu varsayılan duruma geri getirilene kadar)';
$L['forums_explainannounce'] = 'Konuyu duyuru olarak işaretle';
$L['forums_explainprivate'] = 'Konuyu özel olarak işaretle (sadece moderatör(ler) ve konu başlatıcı erişebilir)';
$L['forums_explaindefault'] = 'Konuyu varsayılan duruma geri getir';
$L['forums_explaindelete'] = 'Konuyu sil';

$L['forums_confirm_delete_topic'] = 'Bu konuyu silmek istediğinizden emin misiniz?';
$L['forums_confirm_delete_post'] = 'Bu gönderiyi silmek istediğinizden emin misiniz?';

/**
 * Kullanılmıyor mu?
 */

$L['forums_polltooshort'] = 'Anket seçenekleri en az 2 olmalı veya daha fazla olmalıdır';
$L['for_onlinestatus0'] = 'kullanıcı çevrimdışı';
$L['for_onlinestatus1'] = 'kullanıcı çevrimiçi';
