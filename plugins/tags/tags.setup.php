<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.setup.php
Version=0.0.2
Updated=2008-dec-28
Type=Plugin
Author=Trustmaster
Description=Tags
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Name=Tags
Description=Basic Tags implementation
Version=0.0.2
Date=2008-dec-28
Author=Trustmaster
Copyright=
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
pages=01:radio::1:Enable Tags for Pages
forums=02:radio::1:Enable Tags for Forums
title=03:radio::1:Capitalize first latters of keywords
translit=04:radio::0:Transliterate Tags in URLs
order=06:select:tag,cnt:tag:Cloud output order - alphabetical or descending frequency
limit=07:string::0:Max. tags per items, 0 is unlimited
lim_pages=08:string::0:Limit of tags in a cloud displayed for pages, 0 is unlimited
lim_forums=09:string::0:Limit of tags in a cloud displayed in forums, 0 is unlimited
[END_SED_EXTPLUGIN_CONFIG]
==================== */

// TODO AJAX autocomplete
?>