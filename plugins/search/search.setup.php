<?PHP
/* ====================
[BEGIN_SED]
File=plugins/search/search.setup.php
Version=3.10
Updated=2009-jul-03
Type=Plugin
Author=Neocrome & Spartan & Boss
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=search
Name=Search
Description=Search with extended features
Version=3.10
Date=2009-jul-03
Author=Neocrome & Spartan & Boss
Copyright=Partial copyright (c) 2008 Cotonti Team
Notes=http://www.hardweb.ru
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
maxwords=01:select:3,5,8,10:5:Maximum search words
maxsigns=02:select:20,30,40,50,60,70,80:40:Maximum signs in query
minsigns=02:select:2,3,4,5:3:Min. signs in query
maxitems=03:select:15,30,50,80,100,150,200:50:Maximum results lines for general search
maxitems_ext=04:select:15,30,50,80,100,150,200,300:100:Maximum results lines for extended search
showtext=05:radio::1:Show text in result for general search
showtext_ext=06:radio::1:Show text in result for extended search
searchurl=07:select:Normal,Single:Normal:Type of forum post link to use, Single uses a Single post view, while Normal uses the traditional thread/jump-to link
[END_SED_EXTPLUGIN_CONFIG]
==================== */


/**
 * Lists the members online
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Neocrome, Boss
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

?>