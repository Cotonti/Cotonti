<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=plugins/textboxer2/tb2.forums.editpost.php
Version=101
Updated=2006-mar-15
Type=Plugin
Author=Arkkimaagi
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=textboxer2
Part=forums.editpost
File=tb2.forums.editpost
Hooks=forums.editpost.tags
Tags=forums.editpost.tpl:{FORUMS_EDITPOST_TEXTBOXER}
Order=10
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

require_once($cfg['plugins_dir']."/textboxer2/inc/textboxer2.lang.php");
require_once($cfg['plugins_dir']."/textboxer2/inc/textboxer2.inc.php");

$tb2DropdownIcons = array(-1,49,1,7,10,15,19,23,35);
$tb2MaxSmilieDropdownHeight = 300; 	// Height in px for smilie dropdown
$tb2InitialSmilieLimit = 20;		// Smilies loaded by default to dropdown
$tb2TextareaRows = 16;				// Rows of the textarea

// Do not edit below this line !

$tb2ParseBBcodes=$cfg['parsebbcodeforums'] && $fs_allowbbcodes;
$tb2ParseSmilies=$cfg['parsesmiliesforums'] && $fs_allowsmilies;
$tb2ParseBR=TRUE;

$t->assign("FORUMS_EDITPOST_TEXTBOXER",
			$edittopictitle.sed_textboxer2('rtext',
			'editpost',
			sed_cc($fp_text),
			$tb2TextareaRows,
			$tb2TextareaCols,
			'forumseditpost',
			$tb2ParseBBcodes,
			$tb2ParseSmilies,
			$tb2ParseBR,
			$tb2Buttons,
			$tb2DropdownIcons,
			$tb2MaxSmilieDropdownHeight,
			$tb2InitialSmilieLimit).$pfs);

?>
