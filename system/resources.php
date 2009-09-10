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
$out['icon_comments'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-comment.gif" alt="'.$L['Comments'].'" />';

/* Common */
$out['img_up'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-up.gif" alt="" />';
$out['img_down'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-down.gif" alt="" />';
$out['img_left'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-left.gif" alt="" />';
$out['img_right'] = '<img class="icon" src="skins/'.$skin.'/img/system/arrow-right.gif" alt="" />';

/* PFS */
$out['icon_gallery'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-gallery.gif" alt="'.$L['Gallery'].'" />';
$out['icon_folder'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-folder.gif" alt="'.$L['Folder'].'" />';
$out['icon_pastefile'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-pastefile.gif" title="'.$L['pfs_pastefile'].'" />';
$out['icon_pasteimage'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-pasteimage.gif" title="'.$L['pfs_pasteimage'].'" />';
$out['icon_pastethumb'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-pastethumb.gif" title="'.$L['pfs_pastethumb'].'" />';
function sed_out_pfs_header($c1 = NULL, $c2 = '', $winclose = '', $addthumb = '', $addpix = '', $addfile = '')
{
	global $cfg;
	$res = $cfg['doctype'].'<html><head>
<title>'.$cfg['maintitle'].'</title>'.sed_htmlmetas();

	if (!is_null($c1)) $res .= sed_javascript()."
<script type=\"text/javascript\">
//<![CDATA[
function help(rcode,c1,c2) {
	window.open('plug.php?h='+rcode+'&amp;c1='+c1+'&amp;c2='+c2,'Help','toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=512,top=16');
}
function addthumb(gfile,c1,c2) {
	insertText(opener.document, '$c1', '$c2', $addthumb);$winclose
}
function addpix(gfile,c1,c2) {
	insertText(opener.document, '$c1', '$c2', $addpix);$winclose
}
function addfile(gfile,c1,c2) {
	insertText(opener.document, '$c1', '$c2', $addfile);$winclose
}
function picture(url,sx,sy) {
	window.open('pfs.php?m=view&amp;id='+url,'Picture','toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width='+sx+',height='+sy+',left=0,top=0');
}
//]]>
</script>
";
}
$out['pfs_header_end'] = '</head><body>';
$out['pfs_header_javascript'] = <<<JS
function addthumb(gfile,c1,c2) {
	insertText(opener.document, '%1\$s', '%2\$s', '[img={$cfg['pfs_path']}'+gfile+']{$cfg['pfs_thumbpath']}'+gfile+'[/img]');
}
function addpix(gfile,c1,c2) {
	insertText(opener.document, '%1\$s', '%2\$s', '[img]'+gfile+'[/img]');
}
JS;
$out['pfs_footer'] = '</body></html>';
$out['pfs_type_icon'] = '<img class="icon" src="images/pfs/%1$s.gif" alt="%2$s />';

/* Private messages */
$out['icon_pm'] = '<img class="icon" src="skins/'.$skin.'/img/system/icon-pm.gif"  alt="'.$L['pm_sendnew'].'" />';

/* Ratings and Stars */
for ($i = 0; $i <= 10; $i++)
{
	$out['ratings_vote'][$i] = '<img class="icon" src="skins/'.$skin.'/img/system/vote'.$i.'.gif" alt="'.$i.'" />';
	$out['stars'][$i] = '<img class="icon" src="skins/'.$skin.'/img/system/stars'.$i.'.gif" alt="'.$i.'" />';
}
?>
