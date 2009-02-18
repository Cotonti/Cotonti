<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=news
Name=News
Description=Pick up pages from a category and display the newest in the home page
Version=0.0.3
Date=2009-jan-03
Author=Neocrome & Cotonti Team
Copyright=Partial copyright (c) 2008-2009 Cotonti Team
Notes=BSD License
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
category=01:string::news:Category code of the parent category
maxpages=02:select:0,1,2,3,4,5,6,7,8,9,10,15,20,25,30,50,100:10:Recent pages displayed
[END_SED_EXTPLUGIN_CONFIG]
==================== */

/**
 * Pick up pages from a category and display the newest in the home page
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

?>