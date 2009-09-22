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
$R['admin_icon_pfs'] = '<img src="images/admin/pfs.gif" alt="" />';
$R['admin_icon_polls'] = '<img src="images/admin/polls.gif" alt="" />';

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
