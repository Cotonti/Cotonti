<?php
/* ====================
Copyright (c) 2008-2009, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.

[BEGIN_SED_EXTPLUGIN]
Code=tags
Name=Tags
Description=Basic Tags implementation
Version=0.0.3
Date=2008-feb-16
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
order=05:select:Alphabetical,Frequency,Random:Alphabetical:Cloud output order - alphabetical, descending frequency or random
limit=06:string::0:Max. tags per items, 0 is unlimited
lim_pages=07:string::0:Limit of tags in a cloud displayed for pages, 0 is unlimited
lim_forums=08:string::0:Limit of tags in a cloud displayed in forums, 0 is unlimited
lim_index=09:string::0:Limit of tags in a cloud displayed on index, 0 is unlimited
more=10:radio::1:Show 'All tags' link in tag clouds
[END_SED_EXTPLUGIN_CONFIG]
==================== */

// TODO AJAX autocomplete
?>