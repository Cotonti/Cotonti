<?php
/* ====================
[BEGIN_COT_EXT]
Code=search
Name=Search
Category=navigation-structure
Description=Search with extended features
Version=4.0.9
Date=2016-dec-18
Author=Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2008-2016
Notes=BSD License
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Recommends_modules=page,forums
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
maxwords=01:select:3,5,8,10:5:Maximum search words
maxsigns=02:select:20,30,40,50,60,70,80:40:Maximum signs in query
minsigns=03:select:2,3,4,5:3:Min. signs in query
maxitems=04:string::50:Maximum results lines for general search
pagesearch=05:radio::1:Enable pages search
forumsearch=06:radio::1:Enable forums search
searchurl=07:select:Normal,Single:Normal:Type of forum post link to use, Single uses a Single post view, while Normal uses the traditional thread/jump-to link
addfields=08:string:::Additional pages fields for search, separated by commas. Example "page_extra1,page_extra2,page_key"
extrafilters=09:radio::1:Show extrafilters on main search page
[END_COT_EXT_CONFIG]
==================== */

/**
 * Search plugin
 *
 * @package Search
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');
