<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.forums.delete.php
Version=121
Updated=2008-dec-22
Type=Plugin
Author=Trustmaster
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.delete
File=tags.forums.delete
Hooks=forums.topics.delete.done
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W'))
{
	sed_tag_remove_all($q, 'forums');
}
?>