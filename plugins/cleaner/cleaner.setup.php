<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/cleaner/cleaner.setup.php
Version=110
Updated=2006-sep-07
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=cleaner
Name=Cleaner
Description=Will clean various things...
Version=1.1
Date=2006-jun-06
Author=Neocrome
Copyright=
Notes=Set a delay to 0 (zero) in the configuration panel to disable a cleaning.
SQL=
Auth_guests=0
Lock_guests=RW12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]


[BEGIN_SED_EXTPLUGIN_CONFIG]
trashcan=01:select:3,5,7,10,15,30,60,120:15:Remove the trashcan items after * days (0 to disable).
userprune=05:select:0,1,2,3,4,5,6,7:2:Delete the user accounts not activated within * days (0 to disable).
logprune=06:select:0,1,2,3,7,15,30,60:15:Delete the log entries older than * days (0 to disable).
refprune=04:select:0,15,30,60,120,180,365:30:Delete the referer entries older than * days (0 to disable).
pmnotread=05:select:0,15,30,60,120,180,365:120:Delete the private messages older than * days and not read by the recipient (0 to disable).
pmnotarchived=06:select:0,15,30,60,120,180,365:180:Delete the private messages older than * days and not archived (0 to disable).
pmold=07:select:0,15,30,60,120,180,365:365:Delete ALL the private messages older than * days (0 to disable).
[END_SED_EXTPLUGIN_CONFIG]

==================== */

if ( !defined('SED_CODE') ) { die("Wrong URL."); }

?>