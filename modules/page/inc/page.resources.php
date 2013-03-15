<?php

/**
 * List link
 */

$R['page_submitnewpage'] = '<a href="{$sub_url}" rel="nofollow">'.$L['Submitnew'].'</a>';

$R['list_link_title'] = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_title'].= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> '.$L['Title'];
$R['list_link_key'] = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_key'].= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> '.$L['Key'];
$R['list_link_date'] = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_date'].= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> '.$L['Date'];
$R['list_link_author'] = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_author'].= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> '.$L['Author'];
$R['list_link_owner'] = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_owner'].= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> '.$L['Owner'];
$R['list_link_count'] = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_count'].= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> '.$L['Hits'];
$R['list_link_filecount'] = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_filecount'].= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> '.$L['Hits'];
$R['list_link_field_name'] = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_field_name'].= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a>&nbsp;{$extratitle}';

$R['list_row_admin'] = '<a href="{$unvalidate_url}">'.$L['Putinvalidationqueue'].'</a> <a href="{$edit_url}">'.$L['Edit'].'</a>';

$R['list_more'] =' <span class="readmore"><a href="{$page_url}" title="'.$L['ReadMore'].'">'.$L['ReadMore'].'</a></span>';

/**
 * Page Icons
 */

$R['page_code_redir'] = '<script type="text/javascript">location.href="{$redir}"</script>Redirecting...';
$R['page_icon_file'] = '<img class="icon" src="{$icon}" alt="'.$L['File'].'" />';
$R['page_icon_file_default'] = 'images/icons/'.$cfg['defaulticons'].'/page.png';
$R['page_icon_file_path'] = 'images/filetypes/default/{$type}.png';
