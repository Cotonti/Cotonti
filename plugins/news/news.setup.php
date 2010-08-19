<?php
/* ====================
[BEGIN_COT_EXT]
Code=news
Name=News
Description=Pick up pages from a category and display the newest in the home page
Version=0.7.0
Date=2010-jan-03
Author=Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2008-2010
Notes=BSD License
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=index
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
category=01:string::news:News category codes, comma separated
maxpages=03:select:1,2,3,4,5,6,7,8,9,10,15,20,25,30,50,100:10:Recent pages displayed
syncpagination=04:radio::0:Enable pagination for additional categories
[END_COT_EXT_CONFIG]
==================== */

/**
 * Pick up pages from a category and display the newest in the home page
 *
 * @package news
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

?>