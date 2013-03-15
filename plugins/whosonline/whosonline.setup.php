<?php
/* ====================
[BEGIN_COT_EXT]
Code=whosonline
Name=Who's online
Category=community-social
Description=Lists the members online
Version=1.3.1
Date=2013-01-22
Author=Neocrome & Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2008-2013
Notes=BSD License
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=users
Recommends_plugins=hits
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
disable_guests=01:radio::0:Disable guest tracking
maxusersperpage=02:select:0,5,10,15,25,50,100:25:Users per page in whosonline table
[END_COT_EXT_CONFIG]
==================== */

defined('COT_CODE') or die('Wrong URL');
