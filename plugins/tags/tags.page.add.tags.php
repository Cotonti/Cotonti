<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.page.add.tags.php
Version=0.0.2
Updated=2008-dec-18
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.add.tags
File=tags.page.add.tags
Hooks=page.add.tags
Tags=page.add.tpl:{PAGEADD_FORM_TAGS}
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	file_exists($cfg['plugins_dir']."/tags/lang/tags.$lang.lang.php") ? require_once($cfg['plugins_dir']."/tags/lang/tags.$lang.lang.php") : require_once($cfg['plugins_dir'].'/tags/lang/tags.en.lang.php');
	$t->assign(array(
	'PAGEADD_TOP_TAGS' => $L['Tags'],
	'PAGEADD_TOP_TAGS_HINT' => $L['comma_separated'],
	'PAGEADD_FORM_TAGS' => '<input type="text" name="rtags" />'
	));
}
?>