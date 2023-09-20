<?php
/* ====================
[BEGIN_COT_THEME]
Name=Default
Version=1.00b
[END_COT_THEME]
==================== */

/**
 * Resource strings for the default icon pack
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Arrow Icons
 */

$R['icon_down'] = '<img src="'.$cfg['icons_dir'].'/default/16/arrow-down.png" alt="" />';
$R['icon_right'] = '<img src="'.$cfg['icons_dir'].'/default/16/arrow-right.png" alt="" />';
$R['icon_left'] = '<img src="'.$cfg['icons_dir'].'/default/16/arrow-left.png" alt="" />';
$R['icon_up'] = '<img src="'.$cfg['icons_dir'].'/default/16/arrow-up.png" alt="" />';
$R['icon_vert_active']['desc'] = '<img src="'.$cfg['icons_dir'].'/default/16/arrow-up-active.png" alt="" />';
$R['icon_vert_active']['asc'] = '<img src="'.$cfg['icons_dir'].'/default/16/arrow-down-active.png" alt="" />';;

/**
 * Main Icons
 */

$R['icon_comments'] = '<img src="'.$cfg['icons_dir'].'/default/16/comments.png" alt="" />';
$R['icon_delete'] = '<img src="'.$cfg['icons_dir'].'/default/16/delete.png" alt="" />';
$R['icon_folder'] = '<img src="'.$cfg['icons_dir'].'/default/16/folder.png" alt="" />';
$R['icon_follow'] = '<img src="'.$cfg['icons_dir'].'/default/16/arrow-follow.png" alt="" />';
$R['icon_forums'] = '<img src="'.$cfg['icons_dir'].'/default/16/forums.png" alt="" />';
$R['icon_lock'] = '<img src="'.$cfg['icons_dir'].'/default/16/lock.png" alt="" />';
$R['icon_page'] = '<img src="'.$cfg['icons_dir'].'/default/16/page.png" alt="" />';
$R['icon_prefs'] = '<img src="'.$cfg['icons_dir'].'/default/16/prefs.png" alt="" />';
$R['icon_subfolder'] = '<img src="'.$cfg['icons_dir'].'/default/16/subfolder.png" alt="" />';
$R['icon_undo'] = '<img src="'.$cfg['icons_dir'].'/default/16/undo.png" alt="" />';
$R['icon_unread'] = '<img src="'.$cfg['icons_dir'].'/default/16/arrow-unread.png" alt="" />';

/**
 * Stars / Votes Icons
 */

$R['icon_rating_stars'] = '<img src="' . $cfg['icons_dir'] . '/default/stars/vote{$val}.png" alt="{$val}" />';
$R['icon_stars'] = '<img src="' . $cfg['icons_dir'] . '/default/stars/stars{$val}.png" alt="{$val}" />';

/**
 * Admin Icons
 */

// Icons 16x16
$R['admin_icon_blank'] = '<img src="'.$cfg['icons_dir'].'/default/16/blank.png" alt="" />';
$R['admin_icon_comments'] = '<img src="'.$cfg['icons_dir'].'/default/16/comments2.png" alt="" />'; // Match
$R['admin_icon_delete'] = '<img src="'.$cfg['icons_dir'].'/default/16/bin.png" alt="" />'; // JUST 1 CASE
$R['admin_icon_forums'] = '<img src="'.$cfg['icons_dir'].'/default/16/forums2.png" alt="" />'; // Match
$R['admin_icon_forums_posts'] = '<img src="'.$cfg['icons_dir'].'/default/16/forums2.png" alt="" />'; // Match
$R['admin_icon_forums_topics'] = '<img src="'.$cfg['icons_dir'].'/default/16/forums2.png" alt="" />'; // Match
$R['admin_icon_join1'] = '<img src="'.$cfg['icons_dir'].'/default/16/join1.png" alt="" />'; // OUT?
$R['admin_icon_join2'] = '<img src="'.$cfg['icons_dir'].'/default/16/join2.png" alt="" />'; // OUT?
$R['admin_icon_page'] = '<img src="'.$cfg['icons_dir'].'/default/16/page.png" alt="" />'; // Match
$R['admin_icon_tools'] = '<img src="'.$cfg['icons_dir'].'/default/16/yools.png" alt="" />';
$R['admin_icon_user'] = '<img src="'.$cfg['icons_dir'].'/default/16/user.png" alt="" />';

// Auth Icons
$R['admin_icon_auth_1'] = '<img src="'.$cfg['icons_dir'].'/default/16/auth_1.png" alt="" />';
$R['admin_icon_auth_2'] = '<img src="'.$cfg['icons_dir'].'/default/16/auth_2.png" alt="" />';
$R['admin_icon_auth_3'] = '<img src="'.$cfg['icons_dir'].'/default/16/auth_3.png" alt="" />';
$R['admin_icon_auth_4'] = '<img src="'.$cfg['icons_dir'].'/default/16/auth_4.png" alt="" />';
$R['admin_icon_auth_5'] = '<img src="'.$cfg['icons_dir'].'/default/16/auth_5.png" alt="" />';
$R['admin_icon_auth_a'] = '<img src="'.$cfg['icons_dir'].'/default/16/auth_a.png" alt="" />';
$R['admin_icon_auth_r'] = '<img src="'.$cfg['icons_dir'].'/default/16/auth_r.png" alt="" />';
$R['admin_icon_auth_w'] = '<img src="'.$cfg['icons_dir'].'/default/16/auth_w.png" alt="" />';
$R['admin_icon_discheck0'] = '<img src="'.$cfg['icons_dir'].'/default/16/discheck0.png" alt="" />';
$R['admin_icon_discheck1'] = '<img src="'.$cfg['icons_dir'].'/default/16/discheck1.png" alt="" />';

// Icons 32x32
$R['admin_icon_core'] = '<img src="'.$cfg['icons_dir'].'/default/32/core.png" alt="" />';
$R['admin_icon_plugin'] = '<img src="'.$cfg['icons_dir'].'/default/32/extension.png" alt="" />';
$R['admin_icon_users'] = '<img src="'.$cfg['icons_dir'].'/default/32/users.png" alt="" />';
$R['admin_icon_usergroup0'] = '<img src="'.$cfg['icons_dir'].'/default/32/users-off.png" title="'.$L['Group0'].'" alt="'.$L['Group0'].'" />';
$R['admin_icon_usergroup1'] = '<img src="'.$cfg['icons_dir'].'/default/32/users.png" title="'.$L['Group1'].'" alt="'.$L['Group1'].'" />';

// Default Icon (to be used in custom iconpacks)
// $R['admin_icon_extension_default'] = '<img src="'.$cfg['icons_dir'].'/default/default.png" alt="" />';

// Core Config

$R['icon_cfg_info'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/info.png" alt="" />';
$R['icon_cfg_locale'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/locale.png" alt="" />';
$R['icon_cfg_main'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/main.png" alt="" />';
$R['icon_cfg_menus'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/menus.png" alt="" />';
$R['icon_cfg_performance'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/performance.png" alt="" />';
$R['icon_cfg_phpinfo'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/info.png" alt="" />';
$R['icon_cfg_security'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/security.png" alt="" />';
$R['icon_cfg_sessions'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/sessions.png" alt="" />';
$R['icon_cfg_theme'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/theme.png" alt="" />';
$R['icon_cfg_title'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/title.png" alt="" />';


$R['admin_icon_cache'] = '<img src="'.$cfg['icons_dir'].'/default/default.png" alt="" />';
$R['admin_icon_cache_disk'] = '<img src="'.$cfg['icons_dir'].'/default/default.png" alt="" />';
$R['admin_icon_log'] = '<img src="'.$cfg['icons_dir'].'/default/cfg/info.png" alt="" />';

// 1. Modules (uncomment to use custom icon)
// $R['icon_module_forums'] = '<img src="'.$cfg['icons_dir'].'/default/modules/forums.png" alt="" />';
// $R['icon_module_index'] = '<img src="'.$cfg['icons_dir'].'/default/modules/index.png" alt="" />';
// $R['icon_module_page'] = '<img src="'.$cfg['icons_dir'].'/default/modules/page.png" alt="" />';
// $R['icon_module_pfs'] = '<img src="'.$cfg['icons_dir'].'/default/modules/pfs.png" alt="" />';
// $R['icon_module_pm'] = '<img src="'.$cfg['icons_dir'].'/default/modules/pm.png" alt="" />';
// $R['icon_module_polls'] = '<img src="'.$cfg['icons_dir'].'/default/modules/polls.png" alt="" />';
// $R['icon_module_rss'] = '<img src="'.$cfg['icons_dir'].'/default/modules/rss.png" alt="" />';
// $R['icon_module_users'] = '<img src="'.$cfg['icons_dir'].'/default/modules/users.png" alt="" />';

// 2. Plugins (uncomment to use custom icon)
// $R['icon_plug_autoalias2'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/autoalias2.png" alt="" />';
// $R['icon_plug_autocomplete'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/autocomplete.png" alt="" />';
// $R['icon_plug_banlist'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/banlist.png" alt="" />';
// $R['icon_plug_bbcode'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/bbcode.png" alt="" />';
// $R['icon_plug_ckeditor'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/ckeditor.png" alt="" />';
// $R['icon_plug_cleaner'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/cleaner.png" alt="" />';
// $R['icon_plug_comments'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/comments.png" alt="" />';
// $R['icon_plug_contact'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/contact.png" alt="" />';
// $R['icon_plug_hiddengroups'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/hiddengroups.png" alt="" />';
// $R['icon_plug_hits'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/hits.png" alt="" />';
// $R['icon_plug_html'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/html.png" alt="" />';
// $R['icon_plug_htmlpurifier'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/htmlpurifier.png" alt="" />';
// $R['icon_plug_i18n'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/i18n.png" alt="" />';
// $R['icon_plug_indexnews'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/indexnews.png" alt="" />';
// $R['icon_plug_ipsearch'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/ipsearch.png" alt="" />';
// $R['icon_plug_markup'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/markup.png" alt="" />';
// $R['icon_plug_mcaptcha'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/mcaptcha.png" alt="" />';
// $R['icon_plug_ratings'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/ratings.png" alt="" />';
// $R['icon_plug_recentitems'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/recentitems.png" alt="" />';
// $R['icon_plug_referers'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/referers.png" alt="" />';
// $R['icon_plug_search'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/search.png" alt="" />';
// $R['icon_plug_sitemap'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/sitemap.png" alt="" />';
// $R['icon_plug_statistics'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/statistics.png" alt="" />';
// $R['icon_plug_tags'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/tags.png" alt="" />';
// $R['icon_plug_trashcan'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/trashcan.png" alt="" />';
// $R['icon_plug_urleditor'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/urleditor.png" alt="" />';
// $R['icon_plug_userimages'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/userimages.png" alt="" />';
// $R['icon_plug_whosonline'] = '<img src="'.$cfg['icons_dir'].'/analogue/plugins/whosonline.png" alt="" />';
