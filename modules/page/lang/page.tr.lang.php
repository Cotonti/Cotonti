<?php
/**
 * Sayfa Modülü için Türkçe Dil Dosyası (page.tr.lang.php)
 *
 * @package Sayfa
 * @copyright (c) Cotonti Ekibi
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Yanlış URL.');

// eğer lang/en/main.en.lang.php yüklenmediyse
if (!isset($L['Ascending'])) {
    $mainLangFile = cot_langfile('main', 'core');
    if (file_exists($mainLangFile)) {
        include $mainLangFile;
    }
}

/**
 * Modül Yapılandırması
 */

$L['cfg_autovalidate'] = 'Sayfayı otomatik onayla';
$L['cfg_autovalidate_hint'] = 'Eğer gönderici sayfa kategorisi için yönetici yetkisine sahipse sayfayı otomatik onayla';
$L['cfg_count_admin'] = 'Yöneticilerin tıklamalarını say';
$L['cfg_count_admin_hint'] = '';
$L['cfg_maxlistsperpage'] = 'Sayfa başına maksimum liste';
$L['cfg_maxlistsperpage_hint'] = '';
$L['cfg_order'] = 'Sıralama sütunu';
$L['cfg_title_page'] = 'Sayfa başlık etiketi formatı';
$L['cfg_title_page_hint'] = 'Seçenekler: {TITLE}, {CATEGORY}';
$L['cfg_way'] = 'Sıralama yönü';
$L['cfg_truncatetext'] = 'Listede kısaltılmış sayfa metin uzunluğunu ayarla';
$L['cfg_truncatetext_hint'] = 'Bu özelliği devre dışı bırakmak için sıfır';
$L['cfg_allowemptytext'] = 'Boş sayfa metnine izin ver';
$L['cfg_keywords'] = 'Anahtar kelimeler';

$L['info_desc'] = 'Sayfalar ve sayfa kategorileri aracılığıyla site içeriğini etkinleştirir';

/**
 * Yapılandırma Yapısı
 */

$L['cfg_order_params'] = array(); // cot_page_config_order() içinde yeniden tanımlandı
$L['cfg_way_params'] = array($L['Ascending'], $L['Descending']);
$L['cfg_metatitle'] = 'Meta başlık';
$L['cfg_metadesc'] = 'Meta açıklama';

/**
 * Yönetici Sayfa Bölümü
 */

$L['adm_valqueue'] = 'Onay bekliyor';
$L['adm_validated'] = 'Zaten onaylanmış';
$L['adm_expired'] = 'Süresi dolmuş';
$L['adm_structure'] = 'Sayfaların yapısı (kategoriler)';
$L['adm_sort'] = 'Sırala';
$L['adm_sortingorder'] = 'Kategoriler için varsayılan sıralama düzenini ayarla';
$L['adm_showall'] = 'Hepsini göster';
$L['adm_help_page'] = '&quot;system&quot; kategorisine ait sayfalar, bağımsız sayfalar oluşturmak için halka açık listelerde gösterilmez.';
$L['adm_fileyesno'] = 'Dosya (evet/hayır)';
$L['adm_fileurl'] = 'Dosya URL\'si';
$L['adm_filecount'] = 'Dosya tıklama sayısı';
$L['adm_filesize'] = 'Dosya boyutu';

/**
 * Sayfa ekleme ve düzenleme
 */

$L['page_addtitle'] = 'Yeni sayfa gönder';
$L['page_addsubtitle'] = 'Tüm gerekli alanları doldurun ve devam etmek için formu gönderin';
$L['page_edittitle'] = 'Sayfa özellikleri';
$L['page_editsubtitle'] = 'Tüm gerekli alanları düzenleyin ve devam etmek için "Gönder"e tıklayın';

$L['page_aliascharacters'] = 'Aliaslarda \'+\', \'/\', \'?\', \'%\', \'#\', \'&\' karakterlerine izin verilmez';
$L['page_catmissing'] = 'Kategori kodu eksik';
$L['page_clone'] = 'Sayfayı klonla';
$L['page_confirm_delete'] = 'Bu sayfayı silmek istediğinizden emin misiniz?';
$L['page_confirm_validate'] = 'Bu sayfayı onaylamak istiyor musunuz?';
$L['page_confirm_unvalidate'] = 'Bu sayfayı onay kuyruğuna geri almak istediğinizden emin misiniz?';
$L['page_date_now'] = 'Sayfa tarihini güncele';
$L['page_deleted'] = 'Sayfa silindi';
$L['page_deletedToTrash'] = 'Sayfa çöp kutusuna taşındı';
$L['page_drafts'] = 'Taslaklar';
$L['page_drafts_desc'] = 'Taslaklarınıza kaydedilmiş sayfalar';
$L['page_notavailable'] = 'Bu sayfa şu tarihte yayımlanacaktır: ';
$L['page_textmissing'] = 'Sayfa metni boş olmamalıdır';
$L['page_titletooshort'] = 'Başlık çok kısa veya eksik';
$L['page_validation'] = 'Onay bekliyor';
$L['page_validation_desc'] = 'Henüz yönetici tarafından onaylanmamış sayfalarınız';

$L['page_file'] = 'Dosya indir';
$L['page_filehint'] = '(Sayfanın alt kısmında indirme modülünü etkinleştirmek için "Evet" olarak ayarlayın ve aşağıdaki iki alanı doldurun)';
$L['page_urlhint'] = '(Dosya indir etkinse)';
$L['page_filesize'] = 'Dosya boyutu, kB';
$L['page_filesizehint'] = '(Dosya indir etkinse)';
$L['page_filehitcount'] = 'Dosya tıklama sayısı';
$L['page_filehitcounthint'] = '(Dosya indir etkinse)';
$L['page_metakeywords'] = 'Meta anahtar kelimeler';
$L['page_metatitle'] = 'Meta başlık';
$L['page_metadesc'] = 'Meta açıklama';

$L['page_formhint'] = 'Gönderiniz tamamlandığında, sayfa onay kuyruğuna yerleştirilecek ve bir site yöneticisi veya genel moderatörden onay bekleyecektir. Tüm alanları dikkatlice kontrol edin. Eğer bir değişiklik yapmanız gerekirse, daha sonra yapabilirsiniz. Ancak, değişikliklerin gönderilmesi sayfayı tekrar onay kuyruğuna alır.';

$L['page_pageid'] = 'Sayfa ID';
$L['page_deletepage'] = 'Bu sayfayı sil';

$L['page_savedasdraft'] = 'Sayfa taslak olarak kaydedildi.';

/**
 * Sayfa Durumları
 */

$L['page_status_draft'] = 'Taslak';
$L['page_status_pending'] = 'Beklemede';
$L['page_status_approved'] = 'Onaylandı';
$L['page_status_published'] = 'Yayınlandı';
$L['page_status_expired'] = 'Süresi dolmuş';
$L['page_linesperpage'] = 'Sayfa başına satır sayısı';
$L['page_linesinthissection'] = 'Bu bölümdeki satır sayısı';

$Ls['pages'] = "sayfalar,sayfa";
$Ls['unvalidated_pages'] = "onaylanmamış sayfalar,onaylanmamış sayfa";
$Ls['pages_in_drafts'] = "taslaklarda sayfalar,taslaklarda sayfa";
