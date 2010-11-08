<?php

defined('COT_CODE') or die('Wrong URL');

$R['userimg_existing'] = '<div class="userimg_existing"><img src="{$url_file}" alt="" /><br /><a href="{$url_delete}">'.$L['Delete'].'</a></div>';
$R['userimg_selectfile'] = '{$form_input}';
$R['userimg_html'] = '<div class="userimg_{$code}">{$existing}{$selectfile}</div>';

?>