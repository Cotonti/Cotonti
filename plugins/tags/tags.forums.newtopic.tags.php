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
Tags=forums.newtopic.tpl:{FORUMS_NEWTOPIC_TOP_TAGS},{FORUMS_NEWTOPIC_TOP_TAGS_HINT},{FORUMS_NEWTOPIC_FORM_TAGS}
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W'))
{
	require_once(sed_langfile('tags'));
	$t->assign(array(
	'FORUMS_NEWTOPIC_TOP_TAGS' => $L['Tags'],
	'FORUMS_NEWTOPIC_TOP_TAGS_HINT' => $L['comma_separated'],
	'FORUMS_NEWTOPIC_FORM_TAGS' => '<input type="text" name="rtags" />'
	));
	$t->parse('MAIN.FORUMS_NEWTOPIC_TAGS');
}
?>