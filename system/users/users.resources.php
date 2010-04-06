<?php

/*
 * Group list layout
 */
$R['users_code_grplist_begin'] = '<ul>';
$R['users_code_grplist_end'] = '</ul>';
$R['users_code_grplist_item'] = '<li>{$item}</li>';
$R['users_code_grplist_item_main'] = '<li><strong>{$item}</strong></li>';
$R['users_input_grplist_checkbox'] = '<input type="checkbox" class="checkbox" name="{$name}" value="1"{$checked}{$attrs} />';
$R['users_input_grplist_radio'] = '<input type="radio" class="radio" name="{$name}" value="{$value}"{$checked}{$attrs} />';

/*
 * Users main resources
 */

$R['users_link_sort'] = '<a href="{$asc_url}">'.$R['icon_down'].'</a> <a href="{$desc_url}">'.$R['icon_up'].'</a> {$text}';

/*
 * User's profile resources
 */

$R['users_code_avatar'] = '{$avatar_existing}'.$L['pro_avatarsupload'].' ('.$cfg['av_maxx'].'x'.$cfg['av_maxy'].'x'
	.$cfg['av_maxsize'].$L['b'].')<br />{$input_maxsize}{$input_file}<br />';
$R['users_code_avatar_existing'] = '<img src="{$avatar_url}" alt="" /><br />'.$L['Delete']
	.' [<a href="{$delete_url}">x</a>]<br />&nbsp;<br />';

$R['users_code_avatarchoose_title'] = '<a name="list" id="list"></a><h4>'.$L['pro_avatarschoose'].' :</h4>';

$R['users_code_photo'] = '{$photo_existing}'.$L['pro_photoupload'].' ('.$cfg['ph_maxx'].'x'.$cfg['ph_maxy'].'x'
	.$cfg['ph_maxsize'].$L['b'].')<br />{$input_maxsize}{$input_file}<br />';
$R['users_code_photo_existing'] = '<img src="{$photo_url}" alt="" /><br />'.$L['Delete']
	.' [<a href="{$delete_url}">x</a>]<br />&nbsp;<br />';

$R['users_code_signature'] = '{$signature_existing}'.$L['pro_sigupload'].' ('.$cfg['sig_maxx'].'x'.$cfg['sig_maxy'].'x'
	.$cfg['sig_maxsize'].$L['b'].')<br />{$input_maxsize}{$input_file}<br />';
$R['users_code_signature_existing'] = '<img src="{$signature_url}" alt="" /><br />'.$L['Delete']
	.' [<a href="{$delete_url}">x</a>]<br />&nbsp;<br />';

$R['users_link_avatar'] = '<a name="avatar" id="avatar"></a>';
$L['users_link_avatarchoose'] = '<a href="{$url}">'.$L['pro_avatarspreset'].'</a>';
$R['users_link_avatarselect'] = '<a href="{$url}"><img src="'.$cfg['defav_dir'].'{$f}" alt="" /></a>';
$R['users_link_photo'] = '<a name="photo" id="photo"></a>';
$R['users_link_signature'] = '<a name="signature" id="signature"></a>';
?>
