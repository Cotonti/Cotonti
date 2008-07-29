<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=plugins/textboxer2/tb2.users.edit.php
Version=1A0
Updated=2006-sep-01
Type=Plugin
Author=Arkkimaagi
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=textboxer2
Part=users.edit
File=tb2.users.edit
Hooks=users.edit.tags
Tags=users.edit.tpl:{USERS_EDIT_FORM_TEXTBOXER}
Order=10
[END_SED_EXTPLUGIN]

=============S======= */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

require_once("plugins/textboxer2/inc/textboxer2.lang.php");
require_once("plugins/textboxer2/inc/textboxer2.inc.php");

$tb2DropdownIcons = array(-1,49,1,7,10,15,19,23,35);
$tb2MaxSmilieDropdownHeight = 300; 	// Height in px for smilie dropdown
$tb2InitialSmilieLimit = 20;		// Smilies loaded by default to dropdown
$tb2TextareaRows = 6;				// Rows of the textarea

// Do not edit below this line !

$tb2ParseBBcodes = TRUE;
$tb2ParseSmilies = TRUE;
$tb2ParseBR = TRUE;

$t->assign("USERS_EDIT_FORM_TEXTBOXER",
			sed_textboxer2('rusertext',
			'useredit',
			sed_cc($urr['user_text']),
			$tb2TextareaRows,
			$tb2TextareaCols,
			'usersedit',
			$tb2ParseBBcodes,
			$tb2ParseSmilies,
			$tb2ParseBR,
			$tb2Buttons,
			$tb2DropdownIcons,
			$tb2MaxSmilieDropdownHeight,
			$tb2InitialSmilieLimit).$pfs);

?>
