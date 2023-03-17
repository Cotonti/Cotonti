<?php
/* ====================
[BEGIN_COT_EXT]
Code=bbcode
Name=BBcode Parser
Category=editor-parser
Description=Adds BBcode parser support to the contents
Version=1.1.1
Date=2022-07-13
Author=Cotonti Team
Copyright=Copyright (c) Cotonti Team 2008-2023
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
