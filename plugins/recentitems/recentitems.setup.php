<?php
/* ====================
[BEGIN_COT_EXT]
Code=recentitems
Name=Recent items
Description=Recent pages, topics in forums, users, comments
Version=0.7.0
Date=2010-jan-03
Author=esclkm & Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2008-2010
Notes=BSD License
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=index
Recommends_modules=page,forums
Recommends_plugins=comments
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
recentpages=11:radio::1:Recent pages on index
maxpages=12:select:1,2,3,4,5,6,7,8,9,10,15,20,25,30:5:Recent pages displayed
recentpagestitle=13:string:::Recent pages title length limit
recentpagestext=14:string:::Recent pages text length limit
recentforums=15:radio::1:Recent forums on index
maxtopics=16:select:1,2,3,4,5,6,7,8,9,10,15,20,25,30:5:Recent topics in forums displayed
recentforumstitle=17:string:::Recent forums title length limit
newpages=18:radio::1:Recent pages in standalone module
newpagestext=19:string:::New pages text length limit
newforums=20:radio::1:Recent forums in standalone module
newadditional=21:radio::0:Additional modules in standalone module
itemsperpage=22:select:1,2,3,5,10,20,30,50,100,150,200,300,500:10:Elements per page in standalone module
rightscan=23:radio::1:Enable prescanning category rights
[END_COT_EXT_CONFIG]
==================== */

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package recentitems
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

?>