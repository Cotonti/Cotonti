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

/* Comments */
$R['icon_comments'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-comment.gif" alt="'.$L['Comments'].'" />';

/* Common */
$R['icon_up'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-up.gif" alt="" />';
$R['icon_down'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-down.gif" alt="" />';
$R['icon_left'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-left.gif" alt="" />';
$R['icon_right'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-right.gif" alt="" />';

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
$R['icon_pm'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-pm.gif"  alt="'.$L['pm_sendnew'].'" />';

/* Ratings and Stars */
$R['icon_rating_stars'] = '<img class="icon" src="skins/'.$skin.'/img/system/vote{$val}.gif" alt="{$val}" />';
$R['icon_stars'] = '<img class="icon" src="skins/'.$skin.'/img/system/stars{$val}.gif" alt="{$val}" />';
?>
