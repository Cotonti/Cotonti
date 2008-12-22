<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.forums.newtopic.tags.php
Version=121
Updated=2008-dec-22
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.newtopic.tags
File=tags.forums.newtopic.tags
Hooks=forums.newtopic.tags
Tags=forums.newtopic.tpl:{NEWTOPIC_ATTACH_PERSURL},{NEWTOPIC_ATTACH_ROW_FILE},{NEWTOPIC_ATTACH_ROW_CAPTION},{NEWTOPIC_ATTACH_MAXFILESIZE},{NEWTOPIC_ATTACH_TOTALSPACE},{NEWTOPIC_ATTACH_USEDSPACE},{NEWTOPIC_ATTACH_LEFTSPACE}
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W'))
{
	file_exists($cfg['plugins_dir']."/tags/lang/tags.$lang.lang.php") ? require_once($cfg['plugins_dir']."/tags/lang/tags.$lang.lang.php") : require_once($cfg['plugins_dir'].'/tags/lang/tags.en.lang.php');
	$t->assign(array(
	'NEWTOPIC_TOP_TAGS' => $L['Tags'],
	'NEWTOPIC_TOP_TAGS_HINT' => $L['comma_separated'],
	'NEWTOPIC_FORM_TAGS' => '<input type="text" name="rtags" />'
	));
	$t->parse('MAIN.NEWTOPIC_TAGS');
}
?>