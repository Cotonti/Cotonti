<?php
/* ====================
[BEGIN_COT_EXT]
Code=tags
Name=Tags
Category=navigation-structure
Description=Provides tags - site content keywords, tag clouds, tag search and API
Version=0.7.8
Date=2012-02-19
Author=Trustmaster
Copyright=All rights reserved (c) Vladimir Sibirov 2008-2012
Notes=BSD License.
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345
Recommends_modules=page,forums
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
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
noindex=23:radio::1:Exclude from search engine index
sort=31:select:ID,Title,Date,Category:ID:Default sorting column for tag search results
css=99:radio:0,1:1:Enable plugin CSS
[END_COT_EXT_CONFIG]
==================== */

/**
 * Basic Tags implementation
 *
 * @package tags
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

?>