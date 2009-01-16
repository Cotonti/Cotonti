<?PHP
/* ====================
[BEGIN_SED]
File=plugins/pagetextbyidn/pagetextbyidn.setup.php
Version=0.0.2
Updated=2009-jan-03
Type=Plugin
Author=Neocrome & Cotonti Team
Description=Cotonti - Website engine http://www.cotonti.com Copyright (c) Cotonti Team 2009 BSD License
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=pagetextbyidn
Name={PAGE_TEXT_ID_XX} tag
Description={PAGE_TEXT_ID_XX} display text of page with id=XX
Version=0.0.2
Date=20.11.2008
Author=medar
Copyright=
Notes=Version for Cotonti
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
link_to_edit=01:string::[<a {HREF_EDIT}>Edit</a>]:Link for edit page ({HREF_EDIT} - href construction for edit link)
where=02:select:before,after,do not include:before:Where to place link to edit content - before page text, after, or dont include this link
[END_SED_EXTPLUGIN_CONFIG]
==================== */
if ( !defined('SED_CODE') ) { die("Wrong URL."); }
?>