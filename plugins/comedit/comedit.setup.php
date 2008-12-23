<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/comedit/comedit.setup.php
Version=101
Updated=2006-mar-15
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=comedit
Name=Comments Edit
Description=Enhance comedit system
Version=100
Date=2006-mar-10
Author=Asmo
Copyright=asmo.org.ru
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
time=01:select:1,2,3,4,5,6,7,8,9,10,15,30,60,90,120,180:10:Comments editable timeout for users, minutes
mail=02:radio:0,1:0:Notify about new comments by email?
[END_SED_EXTPLUGIN_CONFIG]

==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

?>
