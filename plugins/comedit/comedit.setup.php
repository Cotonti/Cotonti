<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comedit
Name=Comments Edit
Description=Enhance comedit system
Version=0.0.2
Date=2009-jan-03
Author=Asmo (Edited by motor2hg)
Copyright=asmo.org.ru
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
time=01:select:1,2,3,4,5,6,7,8,9,10,15,30,60,90,120,180:10:Comments editable timeout for users, minutes
mail=02:radio:0,1:0:Notify about new comments by email?
markitup=03:select:No,Yes:Yes:Use markitup?
[END_SED_EXTPLUGIN_CONFIG]
==================== */

/**
 * Enhance comedit system
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if(!defined('SED_CODE')){die('Wrong URL.');}

?>