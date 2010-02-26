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

/* Common */
$R['code_noindex'] = '<meta name="robots" content="noindex" />';
$R['icon_down'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-down.png" alt="" />';
$R['icon_follow'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-follow.png" alt="" />';
$R['icon_left'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-left.png" alt="" />';
$R['icon_rating_stars'] = '<img class="icon" src="skins/'.$skin.'/img/system/vote{$val}.gif" alt="{$val}" />';
$R['icon_right'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-right.png" alt="" />';
$R['icon_rss'] = '<img class="icon" src="skins/'.$skin.'/img/system/rss.png" alt="" />';
$R['icon_stars'] = '<img class="icon" src="skins/'.$skin.'/img/system/stars{$val}.gif" alt="{$val}" />';
$R['icon_unread'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-unread.gif" alt="" />';
$R['icon_up'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-up.png" alt="" />';
$R['img_pixel'] = '<img src="images/pixel.gif" width="{$x}" height="{$y}" alt="" />';
$R['link_catpath'] = '<a href="{$url}" title="{$title}">{$title}</a>';
$R['link_pagenav_current'] = '<span class="pagenav_current"><a href="{$url}"{$event}{$rel}>{$num}</a></span>';
$R['link_pagenav_first'] = '<span class="pagenav_first"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_first'].'</a></span>';
$R['link_pagenav_gap'] = '<span class="pagenav_pages">..</span>';
$R['link_pagenav_last'] = '<span class="pagenav_last"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_last'].'</a></span>';
$R['link_pagenav_main'] = '<span class="pagenav_pages"><a href="{$url}"{$event}{$rel}>{$num}</a></span>';
$R['link_pagenav_next'] = '<span class="pagenav_next"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_next'].'</a></span>';
$R['link_pagenav_prev'] = '<span class="pagenav_prev"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_prev'].'</a></span>';

/* Header */
$R['code_basehref'] = '<base href="'.$cfg['mainurl'].'/" />';
$R['form_guest_remember'] = '<input type="checkbox" name="rremember" />';
$R['form_guest_password'] = '<input type="password" name="rpassword" size="12" maxlength="32" />';
$R['form_guest_username'] = '<input type="text" name="rusername" size="12" maxlength="100" />';

?>