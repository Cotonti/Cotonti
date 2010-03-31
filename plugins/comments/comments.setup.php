<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Name=Comments system
Description=Comments system for Cotonti
Version=0.7.0
Date=2010-mar-28
Author=Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2008-2010
Notes=BSD License
Auth_guests=R
Lock_guests=12345A
Auth_members=RW
Lock_members=
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
time=01:select:1,2,3,4,5,6,7,8,9,10,15,30,60,90,120,180:10:Comments editable timeout for users, minutes
mail=02:radio:0,1:0:Notify about new comments by email?
markitup=03:select:No,Yes:Yes:Use markitup?
trash_comment=04:radio:0,1:1:Use the trash can for the comments
rss_commentmaxsymbols=05:string:::Comments. Cut element description longer than N symbols, Disabled by default
expand_comments=06:radio:0,1:1:Expand comments, Show comments expanded by default
maxcommentsperpage=07:select:5,10,15,20,25,30,40,50,60,70,100,200,500:15:Max. comments on page
commentsize=08:select:0,1024,2048,4096,8192,16384,32768,65536:0:Max. size of comment, In bytes (zero for unlimited size). Default - 0
countcomments=09:radio:0,1:1:Count comments, Display the count of comments near the icon
parsebbcodecom=10:radio:0,1:1:Parse BBcode in comments
parsesmiliescom=11:radio:0,1:1:Parse smilies in comments
[END_SED_EXTPLUGIN_CONFIG]
==================== */

/**
 * Comments system plugins
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($action == 'install')
{
	sed_sql_runscript(file_get_contents($cfg['plugins_dir'] . '/comments/setup/install.sql'));
}
elseif ($action == 'uninstall')
{
	// Uncomment for complete uninstall
	// sed_sql_runscript(file_get_contents($cfg['plugins_dir'] . '/comments/setup/uninstall.sql'));
}

?>