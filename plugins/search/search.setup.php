<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=search
Name=Search
Description=Search for words in pages, links, forums, etc
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
searchurl=01:select:Normal,Single:Normal:Type of forum post link to use, Single uses a Single post view, while Normal uses the traditional thread/jump-to link
results=01:select:5,10,15,20,25,50,100:25:Results listed in a single page
[END_SED_EXTPLUGIN_CONFIG]
==================== */

/**
 * Lists the members online
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

?>