<?php
/**
 * Static and dynamic resource (e.g. HTML) strings. Can be overriden by skin files and other code.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009
 * @license BSD
 */

/* Common */
$R['icon_comments'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-comment.gif" alt="'.$L['Comments'].'" />';
$R['icon_comments_cnt'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-comment.gif" alt="'.$L['Comments'].'" /> ({$cnt})';
$R['icon_rating_stars'] = '<img class="icon" src="skins/'.$skin.'/img/system/vote{$val}.gif" alt="{$val}" />';
$R['icon_stars'] = '<img class="icon" src="skins/'.$skin.'/img/system/stars{$val}.gif" alt="{$val}" />';
$R['icon_up'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-up.gif" alt="" />';
$R['icon_down'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-down.gif" alt="" />';
$R['icon_left'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-left.gif" alt="" />';
$R['icon_right'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-right.gif" alt="" />';
$R['img_pixel'] = '<img src="images/pixel.gif" width="{$x}" height="{$y}" alt="" />';

/* Administration */
$R['admin_icon_admin'] = '<img src="images/admin/admin.gif" alt="" />';
$R['admin_icon_auth_1'] = '<img src="images/admin/auth_1.gif" alt="" />';
$R['admin_icon_auth_2'] = '<img src="images/admin/auth_2.gif" alt="" />';
$R['admin_icon_auth_3'] = '<img src="images/admin/auth_3.gif" alt="" />';
$R['admin_icon_auth_4'] = '<img src="images/admin/auth_4.gif" alt="" />';
$R['admin_icon_auth_5'] = '<img src="images/admin/auth_5.gif" alt="" />';
$R['admin_icon_auth_a'] = '<img src="images/admin/auth_a.gif" alt="" />';
$R['admin_icon_auth_r'] = '<img src="images/admin/auth_r.gif" alt="" />';
$R['admin_icon_auth_w'] = '<img src="images/admin/auth_w.gif" alt="" />';
$R['admin_icon_banlist'] = '<img src="images/admin/banlist.gif" alt="" />';
$R['admin_icon_comments'] = '<img src="images/admin/comments.gif" alt="" />';
$R['admin_icon_config'] = '<img src="images/admin/config.gif" alt="" />';
$R['admin_icon_delete'] = '<img src="images/admin/delete.gif" alt="" />';
$R['admin_icon_discheck0'] = '<img src="images/admin/discheck0.gif" alt="" />';
$R['admin_icon_discheck1'] = '<img src="images/admin/discheck1.gif" alt="" />';
$R['admin_icon_events'] = '<img src="images/admin/events.gif" alt="" />';
$R['admin_icon_folder'] = '<img src="images/admin/folder.gif" alt="" />';
$R['admin_icon_forums'] = '<img src="images/admin/forums.gif" alt="" />';
$R['admin_icon_forums_posts'] = '<img src="images/admin/forums.gif" alt="" />';
$R['admin_icon_forums_topics'] = '<img src="images/admin/forums.gif" alt="" />';
$R['admin_icon_gallery'] = '<img src="images/admin/gallery.gif" alt="" />';
$R['admin_icon_groups'] = '<img src="images/admin/groups.gif" alt="" />';
$R['admin_icon_home'] = '<img src="images/admin/admin.gif" alt="" />';
$R['admin_icon_index'] = '<img src="images/admin/index.gif" alt="" />';
$R['admin_icon_info'] = '<img src="images/admin/info.gif" alt="" />';
$R['admin_icon_ipsearch'] = '<img src="images/admin/ipsearch.gif" alt="" />';
$R['admin_icon_join1'] = '<img src="images/admin/join1.gif" alt="" />';
$R['admin_icon_join2'] = '<img src="images/admin/join2.gif" alt="" />';
$R['admin_icon_journals'] = '<img src="images/admin/journals.gif" alt="" />';
$R['admin_icon_jumpto'] = '<img src="images/admin/jumpto.gif" alt="" />';
$R['admin_icon_links'] = '<img src="images/admin/links.gif" alt="" />';
$R['admin_icon_main'] = '<img src="images/admin/main.gif" alt="" />';
$R['admin_icon_manual'] = '<img src="images/admin/manual.gif" alt="" />';
$R['admin_icon_message'] = '<img src="images/admin/message.gif" alt="" />';
$R['admin_icon_news'] = '<img src="images/admin/news.gif" alt="" />';
$R['admin_icon_other'] = '<img src="images/admin/folder.gif" alt="" />';
$R['admin_icon_page'] = '<img src="images/admin/page.gif" alt="" />';
$R['admin_icon_pages'] = '<img src="images/admin/pages.gif" alt="" />';
$R['admin_icon_pfs'] = '<img src="images/admin/pfs.gif" alt="" />';
$R['admin_icon_plug'] = '<img src="images/admin/plug.gif" alt="" />';
$R['admin_icon_plugins'] = '<img src="images/admin/plugins.gif" alt="" />';
$R['admin_icon_pm'] = '<img src="images/admin/pm.gif" alt="" />';
$R['admin_icon_polls'] = '<img src="images/admin/polls.gif" alt="" />';
$R['admin_icon_ratings'] = '<img src="images/admin/ratings.gif" alt="" />';
$R['admin_icon_rights'] = '<img src="images/admin/rights.gif" alt="" />';
$R['admin_icon_rights2'] = '<img src="images/admin/rights2.gif" alt="" />';
$R['admin_icon_skins'] = '<img src="images/admin/skins.gif" alt="" />';
$R['admin_icon_smilies'] = '<img src="images/admin/smilies.gif" alt="" />';
$R['admin_icon_statistics'] = '<img src="images/admin/statistics.gif" alt="" />';
$R['admin_icon_structure'] = '<img src="images/admin/.gif" alt="" />';
$R['admin_icon_tools'] = '<img src="images/admin/tools.gif" alt="" />';
$R['admin_icon_trash'] = '<img src="images/admin/trash.gif" alt="" />';
$R['admin_icon_user'] = '<img src="images/admin/user.gif" alt="" />';
$R['admin_icon_users'] = '<img src="images/admin/users.gif" alt="" />';
$R['admin_icon_versions'] = '<img src="images/admin/versions.gif" alt="" />';

/* Page */
$R['page_icon_file'] = '<img src="{$icon}" alt="" />';
$R['page_icon_file_default'] = 'images/admin/page.gif';
$R['page_icon_file_path'] = 'images/pfs/{$type}.gif';

/* PFS */
$R['pfs_code_header_javascript'] = '
function addthumb(gfile,c1,c2) {
	insertText(opener.document, "{$c1}", "{$c2}", "[img='.$cfg['pfs_path'].'"+gfile+"]'.$cfg['pfs_thumbpath'].'"+gfile+"[/img]");
}
function addpix(gfile,c1,c2) {
	insertText(opener.document, "{$c1}", "{$c2}", "[img]"+gfile+"[/img]");
}';

$R['pfs_icon_gallery'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-gallery.gif" alt="'.$L['Gallery'].'" />';
$R['pfs_icon_folder'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-folder.gif" alt="'.$L['Folder'].'" />';
$R['pfs_icon_pastefile'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-pastefile.gif" title="'.$L['pfs_pastefile'].'" />';
$R['pfs_icon_pasteimage'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-pasteimage.gif" title="'.$L['pfs_pasteimage'].'" />';
$R['pfs_icon_pastethumb'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-pastethumb.gif" title="'.$L['pfs_pastethumb'].'" />';
$R['pfs_icon_type'] = '<img class="icon" src="images/pfs/{$type}.gif" alt="{$name}" />';

$R['pfs_link_addfile'] = '<a href="javascript:addfile(\'{$pfs_file}\',\'{$c1}\',\'{$c2}\')">'.$R['pfs_icon_pastefile'].'</a>';
$R['pfs_link_addpix'] = '<a href="javascript:addpix(\''.$cfg['pfs_path'].'{$pfs_file}\',\'{$c1}\',\'{$c2}\')\">'.$R['pfs_icon_pasteimage'].'</a>';
$R['pfs_link_addthumb'] = '<a href="javascript:addthumb(\'{$pfs_file}\',\'{$c1}\',\'{$c2}\')">'.$R['pfs_icon_pastethumb'].'</a>';
$R['pfs_link_thumbnail'] = '<a href="{$pfs_fullfile}"><img src="{$thumbpath}{$pfs_file}" title="{$pfs_file}"></a>';

/* Private messages */
$R['pm_icon'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-pm.gif"  alt="'.$L['pm_sendnew'].'" />';
$R['pm_icon_archive'] = '<img src="skins/'.$skin.'/img/system/icon-pm-archive.gif" alt="'.$L['pm_putinarchives'].'" />';
$R['pm_icon_new'] = '<img src="skins/'.$skin.'/img/system/icon-pm-new.gif" alt="" />';
$R['pm_icon_trashcan'] = '<img src="skins/'.$skin.'/img/system/icon-pm-trashcan.gif" alt="'.$L['Delete'].'" />';

?>
