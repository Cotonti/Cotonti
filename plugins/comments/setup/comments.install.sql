/* Comments schema */

-- Main comments table
CREATE TABLE IF NOT EXISTS `cot_com` (
	`com_id` int(11) NOT NULL auto_increment,
	`com_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
	`com_area` varchar(64) collate utf8_unicode_ci NOT NULL default '',
	`com_author` varchar(100) collate utf8_unicode_ci NOT NULL,
	`com_authorid` int(11) default NULL,
	`com_authorip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
	`com_text` text collate utf8_unicode_ci NOT NULL,
	`com_date` int(11) NOT NULL default '0',
	`com_count` int(11) NOT NULL default '0',
	`com_isspecial` tinyint(1) NOT NULL default '0',
	PRIMARY KEY (`com_id`),
	KEY (`com_area`, `com_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;