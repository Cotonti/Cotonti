<?php
/* ====================
[BEGIN_COT_EXT]
Code=autoalias2
Name=AutoAlias 2
Category=navigation-structure
Description=Creates page alias from title if a user leaves it empty
Version=2.1.3
Date=2015-01-13
Author=Trustmaster
Copyright=(c) Cotonti Team 2010-2016
Notes=BSD License
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345
Requires_modules=page
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
translit=01:radio::0:Transliterate non-latinic characters if possible
prepend_id=02:radio::0:Prepend page ID to alias
on_duplicate=03:select:ID,Random:ID:Number appended on duplicate alias (if prepend ID is off)
sep=04:select:-,_,.:-:Word separator
lowercase=05:radio::0:Cast to lower case
[END_COT_EXT_CONFIG]
==================== */

defined('COT_CODE') or die('Wrong URL');
