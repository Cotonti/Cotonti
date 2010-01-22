<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Name=Tags
Description=Basic Tags implementation
Version=0.7.0
Date=2010-jan-22
Author=Trustmaster
Copyright=All rights reserved (c) 2008-2010, Vladimir Sibirov.
Notes=BSD License.
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
pages=11:radio::1:Enable Tags for Pages
forums=12:radio::1:Enable Tags for Forums
title=13:radio::1:Capitalize first latters of keywords
translit=14:radio::0:Transliterate Tags in URLs
order=15:select:Alphabetical,Frequency,Random:Alphabetical:Cloud output order - alphabetical, descending frequency or random
limit=16:string::0:Max. tags per items, 0 is unlimited
lim_pages=17:string::0:Limit of tags in a cloud displayed for pages, 0 is unlimited
lim_forums=18:string::0:Limit of tags in a cloud displayed in forums, 0 is unlimited
lim_index=19:string::0:Limit of tags in a cloud displayed on index, 0 is unlimited
more=20:radio::1:Show 'All tags' link in tag clouds
perpage=21:string::0:Tags displayed per page in standalone cloud, 0 is all at once
index=22:select:pages,forums,all:pages:Index page tag cloud area
autocomplete=23:select:0,1,2,3,4,5,6:3:Min. chars for autocomplete
[END_SED_EXTPLUGIN_CONFIG]
==================== */

/**
 * Basic Tags implementation
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');
?>