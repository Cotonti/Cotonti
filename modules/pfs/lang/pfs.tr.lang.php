<?php
/**
 * PFS Modülü için Türkçe Dil Dosyası (pfs.tr.lang.php)
 *
 * @package PFS
 * @copyright (c) Cotonti Ekibi
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Yanlış URL.');

/**
 * Modül Yapılandırması
 */

$L['cfg_maxpfsperpage'] = 'Sayfa başına maksimum öğe';
$L['cfg_maxpfsperpage_hint'] = ' ';
$L['cfg_pfsfilecheck'] = 'Dosya Kontrolü';
$L['cfg_pfsfilecheck_hint'] = 'Etkinleştirilirse, '.$L['PFS'].' aracılığıyla yüklenen tüm dosyaları veya profil üzerinden yüklenen resimleri kontrol eder. Dosyaların geçerli olduğundan emin olun. Güvenlik nedeniyle "Evet" önerilir.';
$L['cfg_pfsmaxuploads'] = 'Aynı anda maksimum yükleme sayısı';
$L['cfg_pfsmaxuploads_hint'] = '';
$L['cfg_pfsnomimepass'] = 'Mime Tipi Olmadan Geçiş';
$L['cfg_pfsnomimepass_hint'] = 'Etkinleştirilirse, yapılandırma dosyasında mimetype bulunmasa bile yüklenen dosyaların geçmesine izin verir.';
$L['cfg_pfstimename'] = 'Zaman temelli dosya adları';
$L['cfg_pfstimename_hint'] = 'Geçerli zaman damgasına göre dosya adları oluşturur. Varsayılan olarak, gerekli karakter dönüşümleriyle orijinal dosya adı kullanılır.';
$L['cfg_pfsuserfolder'] = 'Klasör depolama modu';
$L['cfg_pfsuserfolder_hint'] = 'Etkinleştirilirse, kullanıcı dosyaları /datas/users/USERID/FOLDERNAME/... alt klasörlerinde depolanır. Sitenin İLK kurulumunda AYARLANMALIDIR. Bir dosya yüklendikten sonra, bu ayarı değiştirmek için çok geçtir.';
$L['cfg_flashupload'] = 'Flash yükleyici kullan';
$L['cfg_flashupload_hint'] = 'Birden fazla dosyanın aynı anda yüklenmesine izin verir.';
$L['cfg_pfs_winclose'] = 'BBcode eklemeden sonra açılır pencereyi kapat';
$L['cfg_th_amode'] = 'Küçük resim oluşturma';
$L['cfg_th_amode_hint'] = '';
$L['cfg_th_border'] = 'Küçük resim, kenar boyutu';
$L['cfg_th_border_hint'] = 'Varsayılan: 4 piksel';
$L['cfg_th_colorbg'] = 'Küçük resim, kenar rengi';
$L['cfg_th_colorbg_hint'] = 'Varsayılan: 000000, hex renk kodu';
$L['cfg_th_colortext'] = 'Küçük resim, metin rengi';
$L['cfg_th_colortext_hint'] = 'Varsayılan: FFFFFF, hex renk kodu';
$L['cfg_th_dimpriority'] = 'Küçük resim, yeniden boyutlandırma önceliği';
$L['cfg_th_dimpriority_hint'] = '';
$L['cfg_th_jpeg_quality'] = 'Küçük resim, Jpeg kalitesi';
$L['cfg_th_jpeg_quality_hint'] = 'Varsayılan: 85';
$L['cfg_th_keepratio'] = 'Küçük resim, oranı koru?';
$L['cfg_th_keepratio_hint'] = '';
$L['cfg_th_separator'] = 'Küçük Resim Seçenekleri';
$L['cfg_th_textsize'] = 'Küçük resim, metin boyutu';
$L['cfg_th_textsize_hint'] = '';
$L['cfg_th_x'] = 'Küçük resim, genişlik';
$L['cfg_th_x_hint'] = 'Varsayılan: 112 piksel';
$L['cfg_th_y'] = 'Küçük resim, yükseklik';
$L['cfg_th_y_hint'] = 'Varsayılan: 84 piksel, önerilen: Genişlik x 0.75';

/**
 * Diğer
 */

$L['adm_gd'] = 'GD grafik kütüphanesi';
$L['adm_allpfs'] = 'Tüm PFS';
$L['adm_allfiles'] = 'Tüm dosyalar';
$L['adm_thumbnails'] = 'Küçük resimler';
$L['adm_orphandbentries'] = 'Yetim veritabanı girdileri';
$L['adm_orphanfiles'] = 'Yetim dosyalar';
$L['adm_delallthumbs'] = 'Tüm küçük resimleri sil';
$L['adm_rebuildallthumbs'] = 'Tüm küçük resimleri sil ve yeniden oluştur';
$L['adm_help_allpfs'] = 'Tüm kayıtlı kullanıcıların '.$L['PFS'].'\'si';
$L['adm_nogd'] = 'Bu sunucu GD grafik kütüphanesini desteklemiyor, Cotonti resimler için küçük resimler oluşturamayacak. '.$L['Configuration'].' &gt; '.$L['PFS'].' bölümüne gidin ve "Küçük resim oluşturma"yı "'.$L['Disabled'].'" olarak ayarlayın.';
$L['adm_help_pfsfiles'] = 'Kullanılamıyor';
$L['adm_help_pfsthumbs'] = 'Kullanılamıyor';
$L['info_desc'] = 'Kişisel (PFS) ve ortak (SFS) dosya depolama alanı';

/**
 * Ana
 */

$L['pfs_cancelall'] = 'Hepsini iptal et';
$L['pfs_direxists'] = 'Böyle bir klasör zaten var.<br />Eski yol: %1$s<br />Yeni yol: %2$s';
$L['pfs_extallowed'] = 'İzin verilen uzantılar';
$L['pfs_filecheckfail'] = 'Uyarı: Dosya Kontrolü Başarısız - Uzantı: %1$s Dosya Adı - %2$s';
$L['pfs_filechecknomime'] = 'Uyarı: Uzantı için Mime Tipi verisi bulunamadı: %1$s Dosya Adı - %2$s';
$L['pfs_fileexists'] = 'Yükleme başarısız oldu, bu isimde zaten bir dosya var mı?';
$L['pfs_filelistempty'] = 'Liste boş.';
$L['pfs_filemimemissing'] = '%1$s için mime tipi eksik. Yükleme Başarısız';
$L['pfs_filenotmoved'] = 'Yükleme başarısız oldu, geçici dosya taşınamıyor.';
$L['pfs_filenotvalid'] = 'Bu geçerli bir %1$s dosyası değil.';
$L['pfs_filesintheroot'] = 'Kökteki dosyalar';
$L['pfs_filesinthisfolder'] = 'Bu klasördeki dosyalar';
$L['pfs_filetoobigorext'] = 'Yükleme başarısız oldu, bu dosya çok büyük veya uzantıya izin verilmiyor mu?';
$L['pfs_folderistempty'] = 'Bu klasör boş.';
$L['pfs_foldertitlemissing'] = 'Bir klasör başlığı gerekli.';
$L['pfs_isgallery'] = 'Galeri mi?';
$L['pfs_ispublic'] = 'Herkese açık mı?';
$L['pfs_maxsize'] = 'Bir dosya için maksimum boyut';
$L['pfs_maxspace'] = 'İzin verilen maksimum alan';
$L['pfs_newfile'] = 'Dosya yükle:';
$L['pfs_newfolder'] = 'Yeni bir klasör oluştur:';
$L['pfs_onpage'] = 'Bu sayfada';
$L['pfs_parentfolder'] = 'Ana klasör';
$L['pfs_pastefile'] = 'Dosya bağlantısı olarak yapıştır';
$L['pfs_pasteimage'] = 'Resim olarak yapıştır';
$L['pfs_pastethumb'] = 'Küçük resim olarak yapıştır';
$L['pfs_resizeimages'] = 'Görseli ölçeklendir?';
$L['pfs_title'] = 'Kişisel Dosya Alanım';
$L['pfs_totalsize'] = 'Toplam boyut';
$L['pfs_uploadfiles'] = 'Dosya Yükle';

$L['pfs_insertasthumbnail'] = 'Küçük resim olarak ekle';
$L['pfs_insertasimage'] = 'Tam boyutlu resim olarak ekle';
$L['pfs_insertaslink'] = 'Dosyaya bağlantı olarak ekle';
$L['pfs_dimensions'] = 'Boyutlar';

$L['pfs_confirm_delete_file'] = 'Bu dosyayı silmek istediğinizden emin misiniz?';
$L['pfs_confirm_delete_folder'] = 'Bu klasörü ve tüm içeriğini silmek istediğinizden emin misiniz?';
