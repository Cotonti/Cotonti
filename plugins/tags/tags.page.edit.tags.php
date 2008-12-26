<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.page.edit.tags.php
Version=0.0.2
Updated=2008-dec-19
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.edit.tags
File=tags.page.edit.tags
Hooks=page.edit.tags
Tags=page.edit.tpl:{PAGEEDIT_FORM_TAGS},{PAGEEDIT_TOP_TAGS},{PAGEEDIT_TOP_TAGS_HINT}
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	require_once(sed_langfile('tags'));
	$tags = sed_tag_list($id);
	$tags = implode(', ', $tags);
	$t->assign(array(
	'PAGEEDIT_TOP_TAGS' => $L['Tags'],
	'PAGEEDIT_TOP_TAGS_HINT' => $L['comma_separated'],
	'PAGEEDIT_FORM_TAGS' => '<input type="text" name="rtags" value="' . $tags . '" />'
	));
}
?>