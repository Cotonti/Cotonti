<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Name=Comments system
Description=Comments system for Cotonti
Version=0.7.0
Date=2010-jan-03
Author=Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2008-2010
Notes=BSD License
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
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
	require_once $cfg['plugins_dir'] . '/comments/comments.global.php';

	sed_sql_query("CREATE TABLE IF NOT EXISTS $db_com (
	  `com_id` int(11) NOT NULL auto_increment,
	  `com_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
	  `com_code_prefix` varchar(30) collate utf8_unicode_ci NOT NULL default '',
	  `com_author` varchar(100) collate utf8_unicode_ci NOT NULL,
	  `com_authorid` int(11) default NULL,
	  `com_authorip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
	  `com_text` text collate utf8_unicode_ci NOT NULL,
	  `com_html` text collate utf8_unicode_ci,
	  `com_date` int(11) NOT NULL default '0',
	  `com_count` int(11) NOT NULL default '0',
	  `com_isspecial` tinyint(1) NOT NULL default '0',
	  PRIMARY KEY (`com_id`),
	  KEY `com_code` (`com_code`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

	if (!mysql_query("SELECT page_comcount FROM $db_pages"))
	{
		sed_sql_query("ALTER TABLE $db_pages ADD COLUMN page_comcount mediumint(8) unsigned default '0'");
	}
	if (!mysql_query("SELECT poll_comcount FROM $db_polls"))
	{
		sed_sql_query("ALTER TABLE $db_polls ADD COLUMN poll_comcount mediumint(8) unsigned default '0'");
	}
	if (!mysql_query("SELECT poll_comments FROM $db_polls"))
	{
		sed_sql_query("ALTER TABLE $db_polls ADD COLUMN poll_comments tinyint(1) NOT NULL default 1");
	}
	if (!mysql_query("SELECT structure_comments FROM $db_structure"))
	{
		sed_sql_query("ALTER TABLE $db_structure ADD COLUMN structure_comments tinyint(1) NOT NULL default 1");
	}
}

?>