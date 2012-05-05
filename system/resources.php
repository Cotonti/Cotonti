<?php
/**
 * Static and dynamic resource (e.g. HTML) strings. Can be overriden by skin files and other code.
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Form generation
 */
$R['code_option_empty'] = '---';
$R['code_time_separator'] = ':';
$R['input_checkbox'] = '<input type="hidden" name="{$name}" value="{$value_off}" /><label><input type="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
$R['input_default'] = '<input type="{$type}" name="{$name}" value="{$value}"{$attrs} />{$error}';
$R['input_option'] = '<option value="{$value}"{$selected}>{$title}</option>';
$R['input_radio'] = '<label><input type="radio" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
$R['input_radio_separator'] = ' ';
$R['input_select'] = '<select name="{$name}"{$attrs}>{$options}</select>{$error}';
$R['input_text'] = '<input type="text" name="{$name}" value="{$value}" {$attrs} />{$error}';
$R['input_textarea'] = '<textarea name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_editor'] =  '<textarea class="editor" name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_medieditor'] =  '<textarea class="medieditor" name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_minieditor'] =  '<textarea class="minieditor" name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';
$R['input_filebox'] = '<a href="{$filepath}">{$value}</a><br /><input type="file" name="{$name}" {$attrs} /><br /><label><input type="checkbox" name="{$delname}" value="1" />'.$L['Delete'].'</label>{$error}';
$R['input_filebox_empty'] = '<input type="file" name="{$name}" {$attrs} />{$error}';

$R['input_date'] =  '{$day} {$month} {$year} {$hour}: {$minute}';
$R['input_date_short'] =  '{$day} {$month} {$year}';

/**
 * Stars / Votes Icons
 */

$R['icon_rating_stars'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/vote{$val}.png" alt="{$val}" />';
$R['icon_stars'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/stars{$val}.png" alt="{$val}" />';

/**
 * Pagination
 */

$R['code_title_page_num'] = ' (' . $L['Page'] . ' {$num})';
$R['link_pagenav_current'] = '<span class="pagenav pagenav_current"><a href="{$url}"{$event}{$rel}>{$num}</a></span>';
$R['link_pagenav_first'] = '<span class="pagenav pagenav_first"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_first'].'</a></span>';
$R['link_pagenav_gap'] = '<span class="pagenav pagenav_gap">...</span>';
$R['link_pagenav_last'] = '<span class="pagenav pagenav pagenav_last"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_last'].'</a></span>';
$R['link_pagenav_main'] = '<span class="pagenav pagenav_pages"><a href="{$url}"{$event}{$rel}>{$num}</a></span>';
$R['link_pagenav_next'] = '<span class="pagenav pagenav_next"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_next'].'</a></span>';
$R['link_pagenav_prev'] = '<span class="pagenav pagenav_prev"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_prev'].'</a></span>';

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
 * Messages
 */
$R['msg_code_153_date'] = '<br />(-&gt; {$date}GMT)';
$R['msg_code_redir_head'] = '<meta http-equiv="refresh" content="{$delay};url={$url}" />';

/**
 * Error handling
 */

$R['code_error_separator'] = '<br />';
$R['code_msg_begin'] = '<ul class="{$class}">';
$R['code_msg_end'] = '</ul>';
$R['code_msg_line'] = '<li><span class="{$class}">{$text}</span></li>';
$R['code_msg_inline'] = '<span class="{$class}">{$text}</span>';

/**
 * Header/footer resources
 */
$R['code_rc_css_embed'] = '<style type="text/css">
/*<![CDATA[*/
{$code}
/*]]>*/
</style>';
$R['code_rc_css_file'] = '<link href="{$url}" type="text/css" rel="stylesheet" />';
$R['code_rc_js_embed'] = '<script type="text/javascript">
//<![CDATA[
{$code}
//]]>
</script>';
$R['code_rc_js_file'] = '<script src="{$url}" type="text/javascript"></script>';

/**
 * Misc
 */

$R['icon_flag'] = '<img class="flag" src="images/flags/{$code}.png" alt="{$alt}" />';
$R['icon_group'] = '<img src="{$src}" alt="'.$L['Group'].'" />';
$R['img_none'] = '<img src="{$src}" alt="'.$L['Image'].'" />';
$R['img_pixel'] = '<img src="images/pixel.gif" width="{$x}" height="{$y}" alt="" />';
$R['img_smilie'] = '<img src="{$src}" alt="{$name}" class="icon" />';
$R['link_catpath'] = '<a href="{$url}" title="{$title}">{$title}</a>';
$R['string_catpath'] = '<span>{$title}</span>';
$R['link_email'] = '<a href="mailto:{$email}">{$email}</a>';


/**
 * Structure
 */
$R['img_structure_cat'] = '<img src="{$icon}" alt="{$title}" title="{$desc}" />';

?>