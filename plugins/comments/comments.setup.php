<?php
/* ====================
[BEGIN_COT_EXT]
Code=comments
Name=Comments system
Category=community-social
Description=Comments system for Cotonti
Version=0.9.7
Date=2011-09-23
Author=Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2008-2012
Notes=BSD License
Auth_guests=R
Lock_guests=12345A
Auth_members=RW
Lock_members=
Recommends_modules=page,polls,rss
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
time=01:select:1,2,3,4,5,6,7,8,9,10,15,30,60,90,120,180:10:Comments editable timeout for users, minutes
mail=02:radio:0,1:0:Notify about new comments by email?
rss_commentmaxsymbols=05:string:::Comments. Cut element description longer than N symbols, Disabled by default
expand_comments=06:radio:0,1:1:Expand comments, Show comments expanded by default
maxcommentsperpage=07:string::15:Max. comments on page
commentsize=08:string::0:Max. size of comment, In bytes (zero for unlimited size). Default - 0
countcomments=09:radio:0,1:1:Count comments, Display the count of comments near the icon
parsebbcodecom=10:radio:0,1:1:Parse BBcode in comments
parsesmiliescom=11:radio:0,1:1:Parse smilies in comments
markup=12:radio::1:Enable markup in comments
minsize=13:string::2:Min. comment size
order=14:select:Chronological,Recent:Recent:Comment sorting order
[END_COT_EXT_CONFIG]
==================== */

/**
 * Comments system plugins
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

?>