<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.page.delete.php
Version=0.0.2
Updated=2008-dec-22
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.delete
File=tags.page.delete
Hooks=page.edit.delete.done
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	sed_tag_remove_all($id);
}
?>