<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=recentitems
Name=Recent items
Description=Recent pages, topics in forums, users, comments
Version=0.7.0
Date=2009-jan-03
Author=esclkm & Cotonti Team
Copyright=2008-2009 Cotonti Team
Notes=BSD License
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
recentpages=01:radio::1:Recent pages on index
maxpages=02:select:1,2,3,4,5,6,7,8,9,10,15,20,25,30:5:Recent pages displayed
recentforums=03:radio::1:Recent forums on index
maxtopics=04:select:1,2,3,4,5,6,7,8,9,10,15,20,25,30:5:Recent topics in forums displayed
newpages=05:radio::1:Recent pages in standalone module
newforums=06:radio::1:Recent forums in standalone module
newadditional=06:radio::0:Additional modules in standalone module
itemsperpage=07:select:1,2,3,5,10,20,30,50,100,150,200,300,500:10:Elements per page in standalone module
rightscan=08:radio::1:Enable prescanning category rights
[END_SED_EXTPLUGIN_CONFIG]
==================== */

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package Cotonti
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

?>