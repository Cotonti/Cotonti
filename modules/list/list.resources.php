<?php

/**
 * List icon
 */

$R['page_icon_file'] = '<img class="icon" src="{$icon}" alt="" />';
$R['page_icon_file_default'] = 'images/icons/default/page.png';
$R['page_icon_file_path'] = 'images/filetypes/default/{$type}.png';

/**
 * List link
 */

$R['page_submitnewpage'] = '<a href="{$sub_url}" />'.$L['Submitnew'].'</a>';

$R['list_top_title'] = '<a href="{$list_top_url_down}" />{$sed_img_down}</a>';
$R['list_top_title'].= '<a href="{$list_top_url_up}" />{$sed_img_up}</a> '.$L['Title'];
$R['list_top_key'] = '<a href="{$list_top_key_url_down}" />{$sed_img_down}</a>';
$R['list_top_key'].= '<a href="{$list_top_key_url_up}" />{$sed_img_up}</a> '.$L['Key'];
$R['list_top_date'] = '<a href="{$list_top_date_url_down}" />{$sed_img_down}</a>';
$R['list_top_date'].= '<a href="{$list_top_date_url_up}" />{$sed_img_up}</a> '.$L['Date'];
$R['list_top_author'] = '<a href="{$list_top_author_url_down}" />{$sed_img_down}</a>';
$R['list_top_author'].= '<a href="{$list_top_author_url_up}" />{$sed_img_up}</a> '.$L['Author'];
$R['list_top_owner'] = '<a href="{$list_top_owner_url_down}" />{$sed_img_down}</a>';
$R['list_top_owner'].= '<a href="{$list_top_owner_url_up}" />{$sed_img_up}</a> '.$L['Owner'];
$R['list_top_count'] = '<a href="{$list_top_count_url_down}" />{$sed_img_down}</a>';
$R['list_top_count'].= '<a href="{$list_top_count_url_up}" />{$sed_img_up}</a> '.$L['Hits'];
$R['list_top_filecount'] = '<a href="{$list_top_filecount_url_down}" />{$sed_img_down}</a>';
$R['list_top_filecount'].= '<a href="{$list_top_filecount_url_up}" />{$sed_img_up}</a> '.$L['Hits'];
$R['list_top_field_name'] = '<a href="{$list_top_field_name_url_down}" />{$sed_img_down}</a>';
$R['list_top_field_name'].= '<a href="{$list_top_field_name_url_up}" />{$sed_img_up}</a> '.$extratitle;

$R['list_row_admin'] = '<a href="{$unvalidate_url}" />'.$L['Putinvalidationqueue'].'</a> &nbsp;<a href="{$edit_url}" />'.$L['Edit'].'</a>';

$R['list_page_html'] =' <span class="readmore" ><a href="{$page_url}" /">'.$L['ReadMore'].'</a></span>';
$R['list_page_text'] =' <span class="readmore" ><a href="{$page_url}" />'.$L['ReadMore'].'</a></span>';

?>