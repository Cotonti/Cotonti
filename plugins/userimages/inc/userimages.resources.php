<?php

defined('COT_CODE') or die('Wrong URL');

$R['userimg_existing'] = '<div class="userimg_existing"><img src="{$url_file}" alt="" /><br /><a href="{$url_delete}">'.$L['Delete'].'</a></div>';
$R['userimg_selectfile'] = '{$form_input}';
$R['userimg_html'] = '<div class="userimg_{$code}">{$existing}{$selectfile}</div>';
$R['userimg_remove'] = '<a href="{$url}" class="button">'.$L['Delete'].'</a>';
$R['userimg_img'] = '<img src="{$src}" alt="{$alt}" class="userimg {$class}" />';

$R['userimg_default_avatar'] = '<img src="datas/defaultav/blank.png" alt="'.$L['Avatar'].'" class="avatar" />';
