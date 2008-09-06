<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/recentitems/recentitems.setup.php
Version=110
Updated=2006-jun-27
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=recentitems
Name=Recent items
Description=Recent pages, polls and topics in forums
Version=100
Date=2008-aug-14
Author=Neocrome
Copyright=
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
maxpages=01:select:0,1,2,3,4,5,6,7,8,9,10,15,20,25,30:5:Recent pages displayed
maxtopics=02:select:0,1,2,3,4,5,6,7,8,9,10,15,20,25,30:5:Recent topics in forums displayed
fd=03:select:Standard, Parent only, Subforums with Master Forums, Just Topics:Standard:Topic path display
maxpolls=04:select:0,1,2,3,4,5:1:Recent polls displayed
redundancy=05:select:1,2,3,4,5:2:Redundancy to come over "private topics" problem
[END_SED_EXTPLUGIN_CONFIG]

==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

?>
