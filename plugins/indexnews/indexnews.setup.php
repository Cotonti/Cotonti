<?php
/* ====================
[BEGIN_COT_EXT]
Code=indexnews
Name=Index News
Category=publications-events
Description=Pick up pages from a category and display the newest in the home page
Version=1.0.0
Date=2015-11-01
Author=esclkm
Copyright=(c) Cotonti Team 2016
Auth_guests=R
Lock_guests=12345A
Auth_members=RW
Requires_modules=page
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
category=01:callback:cot_pagecat_list():news:News category codes, comma separated
maxpages=02:string::10:Recent pages displayed
cache_ttl=03:select:0,60,180,300,600,1800,3600:0:Cache lifetime in seconds, 0 disables cache
[END_COT_EXT_CONFIG]
==================== */

/**
 * plugin Index News for Cotonti Siena
 * 
 * @package Index News
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');
