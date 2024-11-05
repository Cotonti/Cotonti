<?php
/**
 * Anket Modülü için Türkçe Dil Dosyası (polls.tr.lang.php)
 *
 * @package Polls
 * @copyright (c) Cotonti Ekibi
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Yanlış URL.');

/**
 * Yönetici
 */

$L['adm_help_polls'] = 'Yeni bir anket başlatmak için formu doldurun ve &quot;Oluştur&quot; butonuna basın. Boş seçenekler yok sayılacak ve kaldırılacaktır. Anket başladıktan sonra anketi düzenlemek, sonuçları etkileyebileceğinden tavsiye edilmez.';
$L['adm_polls_forumpolls'] = 'Forum anketleri (en yenisi üstte):';
$L['adm_polls_indexpolls'] = 'Anasayfa anketleri (en yenisi üstte):';
$L['adm_polls_msg916_bump'] = 'Başarıyla güncellendi!';
$L['adm_polls_msg916_deleted'] = 'Başarıyla silindi!';
$L['adm_polls_msg916_reset'] = 'Başarıyla sıfırlandı!';
$L['adm_polls_polltopic'] = 'Anket konusu';
$L['adm_polls_nopolls'] = 'Anket yok';
$L['adm_polls_bump'] = 'Yenile';

$L['poll'] = 'Anket';
$L['polls_alreadyvoted'] = 'Bu ankette zaten oy kullandınız.';
$L['polls_created'] = 'Anket başarıyla oluşturuldu';
$L['polls_error_count'] = 'Bir ankette iki veya daha fazla seçenek olmalıdır';
$L['polls_error_title'] = 'Anket adı çok kısa veya boş';
$L['polls_locked'] = 'Anket kilitli';
$L['polls_multiple'] = 'Birden çok seçeneğe izin ver';
$L['polls_notyetvoted'] = 'Yukarıdaki bir satıra tıklayarak oy kullanabilirsiniz.';
$L['polls_registeredonly'] = 'Sadece kayıtlı üyeler oy kullanabilir.';
$L['polls_since'] = 'başlangıcından beri';
$L['polls_updated'] = 'Anket başarıyla güncellendi';
$L['polls_viewarchives'] = 'Tüm anketleri görüntüle';
$L['polls_viewresults'] = 'Sonuçları görüntüle';
$L['polls_Vote'] = 'Oy ver';
//$L['polls_votecasted'] = 'Tamamlandı, oy başarıyla kaydedildi';
$L['polls_votes'] = 'oylar';

/**
 * Yapılandırma
 */
$L['cfg_del_dup_options'] = 'Çift seçenekleri kaldırmayı zorunlu yap';
$L['cfg_del_dup_options_hint'] = 'Veritabanında mevcut olsa bile çift seçenekleri kaldırır';
$L['cfg_ip_id_polls'] = 'Oy sayma yöntemi';
$L['cfg_ip_id_polls_hint'] = '';
$L['cfg_max_options_polls'] = 'Maksimum seçenek sayısı';
$L['cfg_max_options_polls_hint'] = 'Bu sınırı aşan seçenekler otomatik olarak kaldırılacaktır';
$L['cfg_maxpolls'] = 'Anasayfada görüntülenen anket sayısı';
$L['cfg_mode'] = 'Anketlerin anasayfada görüntülenme modu';
$L['cfg_mode_hint'] = '&quot;En son anketler&quot; son anket(ler)i gösterir<br />&quot;Rastgele anketler&quot; rastgele anket(ler)i gösterir';
$L['cfg_mode_params'] = 'En son anketler,Rastgele anketler';

$L['info_desc'] = 'Sayfalar ve forumlar için yapılandırılabilir oylama sistemi';

/**
 * Tema dosyasından taşındı
 */

$L['polls_voterssince'] = 'seçmenler';
$L['polls_allpolls'] = 'Tüm anketler';

/**
 * Meta etiketleri için
 */
$L['polls_id_stat_result'] = 'Çevrimiçi anket sonuçlarının istatistikleri';
$L['polls_id_stat_formed'] = Cot::$sys['domain'].' sitesinde ziyaretçilerin yanıtları dikkate alınarak dinamik olarak oluşturulmuştur.';
$L['polls_meta_desc'] = Cot::$sys['domain'].' sitesindeki tüm çevrimiçi anketlerin listesi. Ziyaretçilerin yanıtları dikkate alınarak genel istatistikler oluşturulur ve yüzde olarak görüntülenir.';
