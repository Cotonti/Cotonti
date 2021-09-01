<?php
/**
 * Является ли $a кратным $b
 * @param $a
 * @param $b
 * @return int
 */
function isMultiple($a, $b) {
    if ($a % $b == 0) return 1;

    return 0;
}

/**
 * Возраст пользователя со словами
 *
 * @param int $birthdate age or Timestamp or a string according to format 'YYYY-MM-DD'
 * @return string Age in years or NULL on failure
 */
function cot_friendlyAge($birthdate){
    global $Ls;

    if(mb_strpos($birthdate, '-') !== false) $birthdate = strtotime($birthdate);

    $age = (int)$birthdate;
    if($age > 300)  $age = cot_build_age($age);

    if(empty($age)) return '';

    $ret = cot_declension($age, $Ls['Years'], false, true);

    return $ret;

}

/**
 * Количество комментариев конкретного пользователя
 * @param int $id
 * @return string
 */
function cot_userCommentsCount($id = 0){
    global $db, $db_com, $urr;

    if(!cot_plugin_active('comments')) return 0;

    if(empty($db_com)) require_once cot_incfile('comments', 'plug');

    $id = (int)$id;

    if(empty($id) && !empty($urr)) $id = $urr['user_id'];

    $sql = $db->query("SELECT COUNT(*) FROM $db_com WHERE com_authorid=".$id);
    $user_comments = $sql->fetchColumn();

    return $user_comments;
}

/**
 * Creates image thumbnail
 *
 * @param string $img_big Original image path
 * @param string $img_small Thumbnail path
 * @param int $small_x Thumbnail width
 * @param int $small_y Thumbnail height
 * @param string $extension Image type
 * @param string $bgcolor Background color
 * @param int $bordersize Border thickness
 * @param int $jpegquality JPEG quality in %
 * @param boolean $thumb_filled Превью полность заполняет указанные размеры?
 * @param string $dim_priority Resize priority dimension
 * @param res $source рессурс, указывающий на загруженное изображение
 *    использовать только если это изображение было загружено ранее
 *    чтобы не грузить его вновь в память из файла
 */
/**
 * Use 4 thumb creation modes:
1 - Height priority
2 - Width priority
3 - Stay within specified thumb dimensions
4 - Crop to enable common-size thumbs
 */
function portal30_createthumb($img_big, $img_small, $small_x, $small_y, $extension, $bgcolor, $bordersize, $jpegquality, $dim_priority="Width", $thumb_filled = false, &$source = NULL){
    global $cfg;

    $dim_priority = 3;		// by Alex - сохраняем соотношение строн

    if (!function_exists('gd_info'))
    { return; }

    $gd_supported = array('jpg', 'jpeg', 'png', 'gif');

    $todestroy = false;

    if (!$source){
        switch($extension)
        {
            case 'gif':
                $source = imagecreatefromgif($img_big);
                break;

            case 'png':
                $source = imagecreatefrompng($img_big);
                break;

            default:
                $source = imagecreatefromjpeg($img_big);
                break;
        }
        $todestroy = true;
    }

    $big_x = imagesx($source);
    $big_y = imagesy($source);

    // расчет размеров превьюхи
    if ($dim_priority=="Width")
    {
        $thumb_x = $small_x;
        $thumb_y = floor($big_y * ($small_x / $big_x));
    }
    elseif ($dim_priority=="Height")
    {
        $thumb_x = floor($big_x * ($small_y / $big_y));
        $thumb_y = $small_y;
    }
    elseif ($dim_priority==3) 		// by Alex
    {
        if ($big_x == $big_y){
            $thumb_x = $small_x - $bordersize*2;
            $thumb_y = $small_y - $bordersize*2;
        }elseif ($big_x > $big_y){
            $thumb_x = $small_x - $bordersize*2;
            $thumb_y = floor($big_y * ($small_x / $big_x)) - $bordersize*2;
        }elseif ($big_x < $big_y){
            $thumb_x = floor($big_x * ($small_y / $big_y))+4 - $bordersize*2;
            $thumb_y = $small_y - $bordersize*2;
        }
    }

    // Создаем превьюху
    if ($thumb_filled){
        if ($cfg['th_amode']=='GD1'){
            $new = imagecreate($small_x, $small_y);
        }else{
            $new = imagecreatetruecolor($small_x, $small_y);
        }
        $border_color = imagecolorallocate ($new, 153, 153, 153);
        imagefilledrectangle ($new, 0,0, $small_x, $small_y, $border_color);

        $background_color = imagecolorallocate ($new, $bgcolor[0], $bgcolor[1] ,$bgcolor[2]);
        imagefilledrectangle ($new, $bordersize, $bordersize, $small_x - $bordersize -1, $small_y - $bordersize - 1, $background_color);
        $dst_x = ( $small_x - $thumb_x ) / 2;
        $dst_y = ( $small_y - $thumb_y ) / 2;
        if ($cfg['th_amode']=='GD1'){
            imagecopyresized($new, $source, $dst_x ,$dst_y, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y);
        }else{
            imagecopyresampled($new, $source, $dst_x ,$dst_y, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y);
        }
    }else{
        if ($cfg['th_amode']=='GD1')
        { $new = imagecreate($thumb_x+$bordersize*2, $thumb_y+$bordersize*2); }
        else
        { $new = imagecreatetruecolor($thumb_x+$bordersize*2, $thumb_y+$bordersize*2); }
        $background_color = imagecolorallocate ($new, $bgcolor[0], $bgcolor[1] ,$bgcolor[2]);
        imagefilledrectangle ($new, 0,0, $thumb_x+$bordersize*2, $thumb_y+$bordersize*2, $background_color);
        if ($cfg['th_amode']=='GD1')
        { imagecopyresized($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y); }
        else
        { imagecopyresampled($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y); }
    }

    switch($extension)
    {
        case 'gif':
            imagegif($new, $img_small);
            break;

        case 'png':
            imagepng($new, $img_small);
            break;

        default:
            imagejpeg($new, $img_small, $jpegquality);
            break;
    }

    imagedestroy($new);
    if ($todestroy) imagedestroy($source);
}

$p30_img_width = 1920;
$p30_img_heght = 1080;
$p30_watermark = "{$cfg["themes_dir"]}/{$cfg["defaulttheme"]}/img/watermark.png";	// пока только PNG
$p30_jpegquality = 80;
$p30_thumb_filled = true;	// Превью полность заполняет указанные размеры?
/**
 * Уменьшаем изображение и ставим водяной знак
 * @param string $source_f Путь и имя файла исходного изображения
 * @param int $width Предельная ширина изображения
 * @param int $height предельная высота изображения. Она имеет приоретет
 * @param int $extension тип изображения (расширение) 'jpg', 'jpeg', 'png', 'gif'
 * @param string $watermark Путь и имя файла водяного знака
 * @param boolean $create_thumb Создавать ли превьюху. Это действие заменит стандартный процесс
 *      создания превьюх
 * @param string $thumb_f Путь и имя файла создаваемой превьюхи
 */
function process_uploaded_imgage($source_f, $width, $height, $extension, $jpegquality = 80, $watermark = '', $create_thumb = false, $thumb_f = ''){

    global $cfg, $p30_thumb_filled;

    if (!function_exists('gd_info'))
    { return; }

    $gd_supported = array('jpg', 'jpeg', 'png', 'gif');

    switch($extension)
    {
        case 'gif':
            $source = imagecreatefromgif($source_f);
            break;

        case 'png':
            $source = imagecreatefrompng($source_f);
            break;

        default:
            $source = imagecreatefromjpeg($source_f);
            break;
    }

    // Создаем превью
    if($create_thumb){
        $th_colorbg = array(hexdec(substr($cfg['pfs']['th_colorbg'],0,2)), hexdec(substr($cfg['pfs']['th_colorbg'],2,2)),
            hexdec(substr($cfg['pfs']['th_colorbg'],4,2)));
        portal30_createthumb($source_f, $thumb_f, $cfg['pfs']['th_x'], $cfg['pfs']['th_y'], $extension, $th_colorbg,
            $cfg['pfs']['th_border'], $cfg['pfs']['th_jpeg_quality'], $cfg['pfs']['th_dimpriority'], $p30_thumb_filled, $source);
    }

    $big_x = imagesx($source);
    $big_y = imagesy($source);

    if ( ($big_x > $width) || ($big_y > $height)){
        if ($big_x == $big_y){
            $new_x = $height;
            $new_y = $height;
        }else{
            $new_x = floor($big_x * ($height / $big_y))+4;
            $new_y = $height;
        }
        if ($new_y > $height){
            $new_x = floor($new_x*($height / $new_y));
            $new_y = $height;
        }
        if ($new_x > $width){
            $new_y = floor($new_y*($width / $new_x));
            $new_x = $width;
        }

    }else{
        $new_x = $big_x;
        $new_y = $big_y;
    }

    $new_img = imagecreatetruecolor($new_x, $new_y);
    imagecopyresampled($new_img, $source, 0 ,0, 0, 0, $new_x, $new_y, $big_x, $big_y);

    if ($watermark != ''){
        list($watm_x, $watm_y) = getimagesize($watermark);
        if ( ($watm_x + 60) < $new_x && ($watm_y + 40) < $new_y ){
            $watm = imagecreatefrompng($watermark);
            imagecopy($new_img, $watm, $new_x - $watm_x - 30 , $new_y - $watm_y - 20, 0, 0, $watm_x, $watm_y);
            imagedestroy($watm);
        }
    }

    unlink($source_f);
    switch($extension)
    {
        case 'gif':
            imagegif($new_img, $source_f);
            break;

        case 'png':
            imagepng($new_img, $source_f);
            break;

        default:
            imagejpeg($new_img, $source_f, $jpegquality);
            break;
    }

    imagedestroy($new_img);
    imagedestroy($source);
} // process_uploaded_imgage


function p30_waterMark($source, $target, $watermark = '', $jpegquality = 85){

    if (empty($watermark)) return false;

    $sourceExt = att_get_ext($source);
    $targetExt = att_get_ext($target);

    $is_img = (int)in_array($sourceExt, array('gif', 'jpg', 'jpeg', 'png'));
    if (!$is_img) return false;

    // Load the image
    $image = imagecreatefromstring(file_get_contents($source));
    $w = imagesx($image);
    $h = imagesy($image);

    // Load the watermark
    $watermark = imagecreatefrompng($watermark);
    $ww = imagesx($watermark);
    $wh = imagesy($watermark);

    $wmAdded = false;
    if ( ($ww + 60) < $w && ($wh + 40) < $h ){
        // Insert watermark to the right bottom corner
        imagecopy($image, $watermark, intval(($w-$ww)/2), $h-$wh-20, 0, 0, $ww, $wh);
        unlink($target);
        switch($targetExt)
        {
            case 'gif':
                imagegif($image, $target);
                break;

            case 'png':
                imagepng($image, $target);
                break;

            default:
                imagejpeg($image, $target, $jpegquality);
                break;
        }
        $wmAdded = true;

    }

    imagedestroy($watermark);
    imagedestroy($image);
   return $wmAdded;
}

/**
 * Для большого файла изображения пытаемся выделить необходимую память
 * @param $file_path
 * @return bool
 */
function checkMemoryForImageProcess($file_path){
    // Получить память, занятую скриптом
    $usedMem = memory_get_usage(true);
    // В мегабайтах
    $usedMem = round($usedMem / 1048576);

    $haveMem = ini_get("memory_limit");
    preg_match('/(\d+)(\w+)/', $haveMem, $mtch);
    // Получаем объем доступной памяти в мегабайтах
    if(!empty($mtch[2])){
        if($mtch[2] == 'M'){
            $haveMem =  $mtch[1];
        }elseif($mtch[2] == 'G'){
            $haveMem =  $mtch[1] * 1024;
        }elseif($mtch[2] == 'K'){
            $haveMem =  $mtch[1] / 1024;
        }
    }

    // Получаем объем памяти, необходимый для обработки изображения
    list($width_orig, $height_orig) = getimagesize($file_path);
    // Тоже в мегабайтах
    $needMem = $width_orig * $height_orig * 4 / 1048576;    // для truecolor
    // Добросим пару мегабайт на ватермарк(ему по идее надо 20K) и на данные для выполнения скрипта
    // на меньших значениях скрипт падает
    $needMem = intval($needMem + $usedMem + 15);
    // Пробуем выделить нужную память
    if( $haveMem < $needMem){
        try{
            ini_set("memory_limit", $needMem."M");
        }catch (Exception $e){
            // Не удалось выделить память
        }
    }else{
        return true;
    }
    // Проверяем что получилось
    $haveMem = ini_get("memory_limit");
    preg_match('/(\d+)(\w+)/', $haveMem, $mtch);
    // Получаем объем доступной памяти в мегабайтах
    if(!empty($mtch[2])){
        if($mtch[2] == 'M'){
            $haveMem =  $mtch[1];
        }elseif($mtch[2] == 'G'){
            $haveMem =  $mtch[1] * 1024;
        }elseif($mtch[2] == 'K'){
            $haveMem =  $mtch[1] / 1024;
        }
    }
    if( $haveMem < $needMem){
        // Не удалось выделить память, выдаем ошибку
        return false;
    }
    return true;
}

// Кастыль для IE
//if(!empty($_SERVER['HTTP_REFERER'])){
//    $_SERVER['HTTP_REFERER'] = str_replace('свадебка.рф', 'xn--80aacclg0b0c.xn--p1ai', $_SERVER['HTTP_REFERER']);
//}