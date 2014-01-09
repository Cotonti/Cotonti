<?php
/* ====================
[BEGIN_COT_EXT]
Code=bbcode
Name=BBcode Parser
Category=editor-parser
Description=Adds BBcode parser support to the contents
Version=0.9.16
Date=2013-11-24
Author=Cotonti Team
Copyright=Copyright (c) Cotonti Team 2008-2014
Notes=BSD License
Auth_guests=R
Lock_guests=12345A
Auth_members=RW
Lock_members=
Recommends_modules=page,forums
Recommends_plugins=markitup
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
smilies=01:radio::1:Enable smilies
editor=02:callback:cot_get_editors():markitup:
parse_autourls=03:radio::1:
[END_COT_EXT_CONFIG]
==================== */

defined('COT_CODE') or die('Wrong URL');
