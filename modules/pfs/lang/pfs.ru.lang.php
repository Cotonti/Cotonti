<?php
/**
 * Russian Language File for the PFS Module (pfs.ru.lang.php)
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Module Config
 */

$L['cfg_maxpfsperpage'] = 'Макс. количество элементов на странице';
$L['cfg_maxpfsperpage_hint'] = ' ';
$L['cfg_pfsfilecheck'] = 'Проверка файлов';
$L['cfg_pfsfilecheck_hint'] = 'Проверять загружаемые файлы (&laquo;'.$L['PFS'].'&raquo; и профиль) на соответствие их формата используемому расширению. Рекомендуется включить в целях безопасности.';
$L['cfg_pfsmaxuploads'] = 'Макс. число параллельных закачек за раз';
$L['cfg_pfsmaxuploads_hint'] = '';
$L['cfg_pfsnomimepass'] = 'Игнорировать MIME-типы';
$L['cfg_pfsnomimepass_hint'] = 'Разрешить закачку файлов, MIME-тип которых не указан в конфигурации.';
$L['cfg_pfstimename'] = 'Имена файлов на основе шаблона времени';
$L['cfg_pfstimename_hint'] = 'Генерировать имена файлов по шаблону времени. По умолчанию используется маска ИМЯФАЙЛА_USERID.';
$L['cfg_pfsuserfolder'] = 'Режим хранения по каталогам';
$L['cfg_pfsuserfolder_hint'] = 'Пользовательские файлы будут храниться в каталогах /datas/users/USERID/ вместо /datas/users/ и добавления USERID к имени файла. Устанавливается <u>только при начальной настройке сайта</u>. Менять значение после первой загрузки любого файла не рекомендуется!';
$L['cfg_flashupload'] = 'Использовать flash-загрузку файлов';
$L['cfg_flashupload_hint'] = 'Позволяет одновременно загружать несколько файлов.';
$L['cfg_pfs_winclose'] = 'Закрывать всплывающее окно после вставки ббкода';
$L['cfg_th_amode'] = 'Метод создания миниатюр изображений (thumbnails)';
$L['cfg_th_amode_hint'] = ' ';
$L['cfg_th_border'] = 'Ширина рамки миниатюры, px';
$L['cfg_th_border_hint'] = 'По умолчанию: 4px';
$L['cfg_th_colorbg'] = 'Цвет рамки миниатюры';
$L['cfg_th_colorbg_hint'] = 'По умолчанию: #000000';
$L['cfg_th_colortext'] = 'Цвет текста миниатюры';
$L['cfg_th_colortext_hint'] = 'По умолчанию: #FFFFFF';
$L['cfg_th_dimpriority'] = 'Приоритет размеров миниатюр (thumbnails)';
$L['cfg_th_dimpriority_hint'] = ' ';
$L['cfg_th_jpeg_quality'] = 'Коэффициент JPEG-сжатия миниатюры';
$L['cfg_th_jpeg_quality_hint'] = 'По умолчанию: 85';
$L['cfg_th_keepratio'] = 'Сохранять пропорции изображения в миниатюре';
$L['cfg_th_keepratio_hint'] = ' ';
$L['cfg_th_separator'] = 'Настройки миниатюр';
$L['cfg_th_textsize'] = 'Размер шрифта миниатюры';
$L['cfg_th_textsize_hint'] = ' ';
$L['cfg_th_x'] = 'Ширина миниатюры, px';
$L['cfg_th_x_hint'] = 'По умолчанию: 112px';
$L['cfg_th_y'] = 'Высота миниатюры, px';
$L['cfg_th_y_hint'] = 'По умолчанию: 84px (рекомендуется: ширина x 0.75)';

/**
 * Other
 */

$L['adm_gd'] = 'Графическая библиотека GD';
$L['adm_allpfs'] = 'Разделы &laquo;'.$L['PFS'].'&raquo; всех пользователей';
$L['adm_allfiles'] = 'Все файлы';
$L['adm_thumbnails'] = 'Миниатюры';
$L['adm_orphandbentries'] = 'Потерянные записи БД';
$L['adm_orphanfiles'] = 'Потерянные файлы';
$L['adm_delallthumbs'] = 'Удалить все миниатюры';
$L['adm_rebuildallthumbs']= 'Удалить и сгенерировать все миниатюры';
$L['adm_help_allpfs'] = 'Разделы &laquo;'.$L['PFS'].'&raquo; всех зарегистрированных пользователей';
$L['adm_nogd'] = 'Графическая библиотека GD не поддерживается данным хостом. Создание миниатюр для изображений невозможно. Установите переменную &laquo;Метод создания миниатюр&raquo; ('.$L['Configuration'].' &gt; '.$L['PFS'].') в значение &laquo;'.$L['Disabled'].'&raquo;';
$L['adm_help_pfsfiles'] = 'Недоступно';
$L['adm_help_pfsthumbs'] = 'Недоступно';
$L['info_desc'] = 'Хранение файлов: персональное (PFS) и общее (SFS)';

/**
 * Main
 */

$L['pfs_cancelall'] = 'Отменить все';
$L['pfs_direxists'] = 'Такой каталог уже существует.<br />Старый путь: %1$s<br />Новый путь: %2$s';
$L['pfs_extallowed'] = 'Разрешенные расширения';
$L['pfs_filecheckfail'] = 'Внимание: ошибка расширения файла 2$s.%1$s';
$L['pfs_filechecknomime'] = 'Внимание: не найден MIME-тип для файла 2$s.%1$s';
$L['pfs_fileexists'] = 'Ошибка загрузки: файл с таким именем уже существует';
$L['pfs_filelistempty'] = 'Список пуст';
$L['pfs_filemimemissing'] = 'Ошибка загрузки: отсутствует MIME-тип для расширения %1$s';
$L['pfs_filenotmoved'] = 'Ошибка загрузки: временный файл не может быть перемещен.';
$L['pfs_filenotvalid'] = 'Ошибка проверки %1$s-файла';
$L['pfs_filesintheroot'] = 'Файлов в корневом каталоге';
$L['pfs_filesinthisfolder'] = 'Файлов в текущем каталоге';
$L['pfs_filetoobigorext'] = 'Ошибка загрузки: файл слишком велик или недопустимое расширение';
$L['pfs_folderistempty'] = 'Каталог пуст';
$L['pfs_foldertitlemissing'] = 'Отсутствует заголовок каталога';
$L['pfs_isgallery'] = 'Галерея?';
$L['pfs_ispublic'] = 'Открытый доступ?';
$L['pfs_maxsize'] = 'Максимальный размер файла';
$L['pfs_maxspace'] = 'Максимальный разрешенный объем';
$L['pfs_newfile'] = 'Загрузить файл';
$L['pfs_newfolder'] = 'Создать новый каталог';
$L['pfs_onpage'] = 'На этой странице';
$L['pfs_parentfolder'] = 'Родительский каталог';
$L['pfs_pastefile'] = 'Вставить как ссылку на файл';
$L['pfs_pasteimage'] = 'Вставить как изображение';
$L['pfs_pastethumb'] = 'Вставить миниатюру';
$L['pfs_resizeimages'] = 'Масштабировать изображение?';
$L['pfs_title'] = 'Мои файлы';
$L['pfs_totalsize'] = 'Общий объем';
$L['pfs_uploadfiles'] = 'Загрузить файлы';

$L['pfs_insertasthumbnail'] = 'Вставить миниатюру';
$L['pfs_insertasimage'] = 'Вставить полноразмерное изображение';
$L['pfs_insertaslink'] = 'Вставить в виде ссылки на файл';
$L['pfs_dimensions'] = 'Размеры';

$L['pfs_confirm_delete_file'] = 'Вы действительно хотите удалить этот файл?';
$L['pfs_confirm_delete_folder'] = 'Вы действительно хотите удалить эту папку и всё её содержимое?';
