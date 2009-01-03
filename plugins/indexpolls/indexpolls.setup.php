<?PHP
/* ====================
[BEGIN_SED]
File=plugins/indexpolls/indexpolls.setup.php
Version=110
Updated=2006-jun-27
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=indexpolls
Name=Indexpolls
Description=Polls (recent or random) on index with jQuery
Version=0.0.2
Date=2009-jan-03
Author=Cotonti Team
Copyright=
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
maxpolls=01:select:0,1,2,3,4,5:1:Polls displayed
mode=02:select:Recent polls,Random polls:Recent polls:Mode polls displayed
[END_SED_EXTPLUGIN_CONFIG]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

?>
