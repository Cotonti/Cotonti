<?php
/* ====================
[BEGIN_COT_EXT]
Code=news
Name=News
Category=publications-events
Description=Pick up pages from a category and display the newest in the home page
Version=0.9
Date=2012-05-28
Author=Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2008-2012
Notes=BSD License
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=page
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
category=01:string::news:News category codes, comma separated
maxpages=02:string::10:Recent pages displayed
syncpagination=03:radio::0:Enable pagination for additional categories
cache_ttl=04:select:0,60,180,300,600,1800,3600:0:Cache lifetime in seconds, 0 disables cache
[END_COT_EXT_CONFIG]
==================== */

/**
 * Pick up pages from a category and display the newest in the home page
 *
 * @package news
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

?>