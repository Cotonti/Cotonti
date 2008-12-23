<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.forums.editpost.tags.php
Version=121
Updated=2008-jan-30
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.editpost.tags
File=tags.forums.editpost.tags
Hooks=forums.editpost.tags
Tags=forums.editpost.tpl:{FORUMS_EDITPOST_FORM_TAGS},{FORUMS_EDITPOST_TOP_TAGS},{FORUMS_EDITPOST_TOP_TAGS_HINT}
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W') && $is_first_post)
{
	file_exists($cfg['plugins_dir']."/tags/lang/tags.$lang.lang.php") ? require_once($cfg['plugins_dir']."/tags/lang/tags.$lang.lang.php") : require_once($cfg['plugins_dir'].'/tags/lang/tags.en.lang.php');
	$tags = sed_tag_list($q, 'forums');
	$tags = implode(', ', $tags);
	$t->assign(array(
	'FORUMS_EDITPOST_TOP_TAGS' => $L['Tags'],
	'FORUMS_EDITPOST_TOP_TAGS_HINT' => $L['comma_separated'],
	'FORUMS_EDITPOST_FORM_TAGS' => '<input type="text" name="rtags" value="' . $tags . '" />'
	));
	$t->parse('MAIN.FORUMS_EDITPOST_TAGS');
}
?>