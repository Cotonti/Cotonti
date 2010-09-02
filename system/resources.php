<?php
/**
 * Static and dynamic resource (e.g. HTML) strings. Can be overriden by skin files and other code.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

/**
 * Form generation
 */
$R['code_option_empty'] = '---';
$R['code_time_separator'] = ':';
$R['input_checkbox'] = '<label><input type="checkbox" class="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
$R['input_default'] = '<input type="{$type}" name="{$name}" value="{$value}"{$attrs} />{$error}';
$R['input_option'] = '<option value="{$value}"{$selected}>{$title}</option>';
$R['input_radio'] = '<label><input type="radio" class="radio" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
$R['input_radio_separator'] = ' ';
$R['input_select'] = '<select name="{$name}"{$attrs}>{$options}</select>{$error}';
$R['input_text'] = '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}';
$R['input_textarea'] = '<textarea name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_editor'] =  '<textarea class="editor" name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_minieditor'] =  '<textarea class="minieditor" name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';

$R['input_date'] =  '{$day} {$month} {$year} {$hour} : {$minute}';
$R['input_date_short'] =  '{$day} {$month} {$year}';

/**
 * Stars / Votes Icons
 */

$R['icon_rating_stars'] = '<img class="icon" src="skins/'.$skin.'/img/system/vote{$val}.gif" alt="{$val}" />';
$R['icon_stars'] = '<img class="icon" src="skins/'.$skin.'/img/system/stars{$val}.gif" alt="{$val}" />';

/**
 * Pagination
 */

$R['link_pagenav_current'] = '<span class="pagenav_current"><a href="{$url}"{$event}{$rel}>{$num}</a></span>';
$R['link_pagenav_first'] = '<span class="pagenav_first"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_first'].'</a></span>';
$R['link_pagenav_gap'] = '<span class="pagenav_pages">...</span>';
$R['link_pagenav_last'] = '<span class="pagenav_last"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_last'].'</a></span>';
$R['link_pagenav_main'] = '<span class="pagenav_pages"><a href="{$url}"{$event}{$rel}>{$num}</a></span>';
$R['link_pagenav_next'] = '<span class="pagenav_next"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_next'].'</a></span>';
$R['link_pagenav_prev'] = '<span class="pagenav_prev"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_prev'].'</a></span>';

/**
 * Header
 */

$R['code_basehref'] = '<base href="'.$cfg['mainurl'].'/" />';
$R['code_noindex'] = '<meta name="robots" content="noindex" />';

$R['form_guest_remember'] = '<input type="checkbox" name="rremember" />';
$R['form_guest_remember_forced'] = '<input type="checkbox" name="rremember" checked="checked" disabled="disabled" />';
$R['form_guest_password'] = '<input type="password" name="rpassword" size="12" maxlength="32" />';
$R['form_guest_username'] = '<input type="text" name="rusername" size="12" maxlength="100" />';

/**
 * Misc
 */

$R['code_error_separator'] = '<br />';
$R['code_msg_begin'] = '<ul class="{$class}">';
$R['code_msg_end'] = '</ul>';
$R['code_msg_line'] = '<li><span class="{$class}">{$text}</span></li>';
$R['code_msg_inline'] = '<span class="{$class}">{$text}</span>';
$R['icon_flag'] = '<img class="flag" src="images/flags/{$code}.png" alt="{$alt}" />';
$R['img_avatar'] = '<img src="{$src}" alt="'.$L['Avatar'].'" class="avatar" />';
$R['img_avatar_default'] = '<img src="datas/defaultav/blank.png" alt="'.$L['Avatar'].'" class="avatar" />';
$R['img_none'] = '<img src="{$src}" alt="'.$L['Image'].'" />';
$R['img_pixel'] = '<img src="images/pixel.gif" width="{$x}" height="{$y}" alt="" />';
$R['img_photo'] = '<img src="{$src}" alt="'.$L['Photo'].'" class="photo" />';
$R['img_sig'] = '<img src="{$src}" alt="'.$L['Signature'].'" class="signature" />';
$R['link_catpath'] = '<a href="{$url}" title="{$title}">{$title}</a>';

/**
 * Plugin Title
 */

$R['plug_code_homebreadcrumb'] = '<a href="'.$cfg['mainurl'].'">'.htmlspecialchars($cfg['maintitle']).'</a> '.$cfg['separator'].' ';
$R['plug_code_title'] = '{$bhome}<a href="{$url}">{$plugin_title}</a>';

?>