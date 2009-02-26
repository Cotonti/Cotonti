<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=indexpolls
Name=Indexpolls
Description=Polls (recent or random) on index with jQuery
Version=0.0.3
Date=2009-jan-03
Author=Cotonti Team
Copyright=Partial copyright (c) 2008-2009 Cotonti Team
Notes=BSD License
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
maxpolls=01:select:1,2,3,4,5:1:Polls displayed
mode=02:select:Recent polls,Random polls:Recent polls:Mode polls displayed
[END_SED_EXTPLUGIN_CONFIG]
==================== */

/**
 * Polls (recent or random) on index with jQuery
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if(!defined('SED_CODE')){die('Wrong URL.');}

?>