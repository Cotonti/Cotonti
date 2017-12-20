<?php
/* ====================
[BEGIN_COT_EXT]
Code=i18n
Name=Content Internationalization
Category=customization-i18n
Description=Enables site contents translation into multiple languages
Version=1.0.5
Date=2017-12-20
Author=Trustmaster
Copyright=Copyright (c) Cotonti Team 2010-2018
Notes=BSD License
Auth_guests=R
Lock_guests=12345A
Auth_members=RW
Lock_members=
Requires_modules=page
Recommends_plugins=search,tags
Order=50
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
cats=01:string:::Category codes
locales=02:text::en|English:Site locales
omitmain=03:radio::1:Omit language parameter in the URL if pointing to main language
rewrite=04:radio::0:Enable URL overwrite for language parameter
cookie=05:radio::0:Remember language selection in cookie
[END_COT_EXT_CONFIG]
==================== */

defined('COT_CODE') or die('Wrong URL');
