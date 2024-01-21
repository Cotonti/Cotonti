<?php
/* ====================
[BEGIN_COT_EXT]
Code=recentitems
Name=Recent items
Category=publications-events
Description=Recent pages, topics in forums, users, comments
Version=1.0.4
Date=2024-01-16
Author=Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2008-2024
Notes=BSD License
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Recommends_modules=page,forums
Recommends_plugins=comments
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
recentpages=01:radio::1:Recent pages on index
pagesOrder=04:select:date,begin,updated:date:
maxpages=07:string::5:Recent pages displayed
pagesPeriod=10:select:all,1D,2D,4D,1W,2W,3W,1M,2M,3M,4M,5M,6M,7M,8M,9M,1Y:all:
recentpagestitle=13:string:::Recent pages title length limit
recentpagestext=16:string:::Recent pages text length limit
recentforums=19:radio::1:Recent forums on index
maxtopics=22:string::5:Recent topics in forums displayed
forumsPeriod=24:select:all,1D,2D,4D,1W,2W,3W,1M,2M,3M,4M,5M,6M,7M,8M,9M,1Y:all:
recentforumstitle=27:string:::Recent forums title length limit
newpages=30:radio::1:Recent pages in standalone module
newpagestext=33:string:::New pages text length limit
newforums=36:radio::1:Recent forums in standalone module
itemsperpage=39:string::10:Elements per page in standalone module
rightscan=42:radio::1:Enable prescanning category rights
whitelist=45:text:::White list of categories
blacklist=48:text:::Black list of categories
cache_ttl=80:select:0,60,180,300,600,1800,3600:0:Cache lifetime in seconds, 0 disables cache
[END_COT_EXT_CONFIG]
==================== */

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package RecentItems
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');
