<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.php
Version=0.0.2
Updated=2008-dec-19
Type=Plugin
Author=Trustmaster
Description=Tag search
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=search
File=tags
Hooks=standalone
Tags=
Order=
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($a == 'pages')
{
	$t = sed_import('t', 'G', 'TXT');
	if(empty($t))
	{
		// Global tag cloud and search form

	}
	else
	{
		// Search results
	}
}
elseif($a == 'forums')
{
	// TODO forum search by tags
}
?>