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

/**
 * Arrow Icons
 */

$R['icon_down'] = 
	'<img class="icon" src="images/iconpacks/default/arrow-down.png" alt="" />';
$R['icon_right'] = 
	'<img class="icon" src="images/iconpacks/default/arrow-right.png" alt="" />';
$R['icon_left'] = 
	'<img class="icon" src="images/iconpacks/default/arrow-left.png" alt="" />';
$R['icon_up'] = 
	'<img class="icon" src="images/iconpacks/default/arrow-up.png" alt="" />';

$R['icon_follow'] = 
	'<img class="icon" src="images/iconpacks/default/arrow-follow.png" alt="" />';
$R['icon_unread'] = 
	'<img class="icon" src="images/iconpacks/default/arrow-unread.png" alt="" />';

/**
 * Stars / Votes Icons
 */

$R['icon_rating_stars'] = '<img class="icon" src="skins/'.$skin.'/img/system/vote{$val}.gif" alt="{$val}" />';
$R['icon_stars'] = '<img class="icon" src="skins/'.$skin.'/img/system/stars{$val}.gif" alt="{$val}" />';

/**
 * Pagination
 */

$R['link_pagenav_current'] = 
	'<span class="pagenav_current"><a href="{$url}"{$event}{$rel}>{$num}</a></span>';
$R['link_pagenav_first'] = 
	'<span class="pagenav_first"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_first'].'</a></span>';
$R['link_pagenav_gap'] = 
	'<span class="pagenav_pages">...</span>';
$R['link_pagenav_last'] = 
	'<span class="pagenav_last"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_last'].'</a></span>';
$R['link_pagenav_main'] = 
	'<span class="pagenav_pages"><a href="{$url}"{$event}{$rel}>{$num}</a></span>';
$R['link_pagenav_next'] = 
	'<span class="pagenav_next"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_next'].'</a></span>';
$R['link_pagenav_prev'] = 
	'<span class="pagenav_prev"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_prev'].'</a></span>';

/**
 * Header
 */

$R['code_basehref'] = '<base href="'.$cfg['mainurl'].'/" />';
$R['code_noindex'] = '<meta name="robots" content="noindex" />';

$R['form_guest_remember'] = '<input type="checkbox" name="rremember" />';
$R['form_guest_password'] = '<input type="password" name="rpassword" size="12" maxlength="32" />';
$R['form_guest_username'] = '<input type="text" name="rusername" size="12" maxlength="100" />';

/**
 * Misc
 */

$R['icon_rss'] = '<img class="icon" src="images/iconpacks/default/rss.png" alt="" />';
$R['icon_twitter'] = '<img class="icon" src="images/iconpacks/default/twitter.png" alt="" />';
$R['img_pixel'] = '<img src="images/pixel.gif" width="{$x}" height="{$y}" alt="" />';
$R['link_catpath'] = '<a href="{$url}" title="{$title}">{$title}</a>';

/**
 * Temporary
 */

$R['icon_error'] = 
	'<img class="icon" src="images/iconpacks/default/error.png" alt="" />';
$R['icon_forums'] = 
	'<img class="icon" src="images/iconpacks/default/forums.png" alt="" />';
$R['icon_help'] = 
	'<img class="icon" src="images/iconpacks/default/help.png" alt="" />';
$R['icon_info'] = 
	'<img class="icon" src="images/iconpacks/default/info.png" alt="" />';
$R['icon_news'] = 
	'<img class="icon" src="images/iconpacks/default/news.png" alt="" />';
$R['icon_online'] = 
	'<img class="icon" src="images/iconpacks/default/online.png" alt="" />';
$R['icon_pfs'] = 
	'<img class="icon" src="images/iconpacks/default/pfs.png" alt="" />';
$R['icon_plugin'] = 
	'<img class="icon" src="images/iconpacks/default/plugin.png" alt="" />';
$R['icon_polls'] = 
	'<img class="icon" src="images/iconpacks/default/polls.png" alt="" />';
$R['icon_prefs'] = 
	'<img class="icon" src="images/iconpacks/default/prefs.png" alt="" />';
$R['icon_search'] = 
	'<img class="icon" src="images/iconpacks/default/search.png" alt="" />';
$R['icon_stats'] = 
	'<img class="icon" src="images/iconpacks/default/stats.png" alt="" />';
$R['icon_tags'] = 
	'<img class="icon" src="images/iconpacks/default/tags.png" alt="" />';
$R['icon_update'] = 
	'<img class="icon" src="images/iconpacks/default/update.png" alt="" />';
$R['icon_users'] = 
	'<img class="icon" src="images/iconpacks/default/users.png" alt="" />';
$R['icon_warning'] = 
	'<img class="icon" src="images/iconpacks/default/warning.png" alt="" />';

?>