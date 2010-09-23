/**
 * Forums module database installation
 */

-- Install auth for default forums
INSERT INTO `cot_auth` (`auth_groupid`, `auth_code`, `auth_option`,
	`auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES
(1, 'forums', '1', 1, 254, 1),
(2, 'forums', '1', 1, 254, 1),
(3, 'forums', '1', 0, 255, 1),
(4, 'forums', '1', 3, 128, 1),
(5, 'forums', '1', 255, 255, 1),
(1, 'forums', '2', 1, 254, 1),
(2, 'forums', '2', 1, 254, 1),
(3, 'forums', '2', 0, 255, 1),
(4, 'forums', '2', 3, 128, 1),
(5, 'forums', '2', 255, 255, 1),
(6, 'forums', '1', 131, 0, 1),
(6, 'forums', '2', 131, 0, 1);

-- Forum posts
CREATE TABLE IF NOT EXISTS `cot_forum_posts` (
  `fp_id` mediumint(8) unsigned NOT NULL auto_increment,
  `fp_topicid` mediumint(8) NOT NULL default '0',
  `fp_sectionid` smallint(5) NOT NULL default '0',
  `fp_posterid` int(11) NOT NULL default '-1',
  `fp_postername` varchar(100) collate utf8_unicode_ci NOT NULL,
  `fp_creation` int(11) NOT NULL default '0',
  `fp_updated` int(11) NOT NULL default '0',
  `fp_updater` varchar(100) collate utf8_unicode_ci NOT NULL DEFAULT '',
  `fp_text` text collate utf8_unicode_ci NOT NULL,
  `fp_posterip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `fp_html` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`fp_id`),
  UNIQUE KEY `fp_topicid` (`fp_topicid`,`fp_id`),
  KEY `fp_updated` (`fp_creation`),
  KEY `fp_posterid` (`fp_posterid`),
  KEY `fp_sectionid` (`fp_sectionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Forum sections
CREATE TABLE IF NOT EXISTS `cot_forum_sections` (
  `fs_id` smallint(5) unsigned NOT NULL auto_increment,
  `fs_state` tinyint(1) unsigned NOT NULL default '0',
  `fs_order` smallint(5) unsigned NOT NULL default '0',
  `fs_title` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `fs_category` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `fs_desc` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `fs_icon` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `fs_lt_id` int(11) NOT NULL default '0',
  `fs_lt_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fs_lt_date` int(11) NOT NULL default '0',
  `fs_lt_posterid` int(11) NOT NULL default '-1',
  `fs_lt_postername` varchar(100) collate utf8_unicode_ci NOT NULL,
  `fs_autoprune` int(11) NOT NULL default '0',
  `fs_allowusertext` tinyint(1) NOT NULL default '1',
  `fs_allowbbcodes` tinyint(1) NOT NULL default '1',
  `fs_allowsmilies` tinyint(1) NOT NULL default '1',
  `fs_allowprvtopics` tinyint(1) NOT NULL default '0',
  `fs_countposts` tinyint(1) NOT NULL default '1',
  `fs_topiccount` mediumint(8) NOT NULL default '0',
  `fs_topiccount_pruned` int(11) default '0',
  `fs_postcount` mediumint(8) NOT NULL default '0',
  `fs_postcount_pruned` int(11) default '0',
  `fs_viewcount` mediumint(8) NOT NULL default '0',
  `fs_masterid` smallint(5) unsigned NOT NULL default '0',
  `fs_mastername` varchar(128) collate utf8_unicode_ci NOT NULL,
  `fs_allowviewers` tinyint(1) NOT NULL default '1',
  `fs_allowpolls` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`fs_id`),
  KEY `fs_order` (`fs_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Default sections
INSERT INTO `cot_forum_sections` (`fs_state`, `fs_order`, `fs_title`, `fs_category`, `fs_desc`, `fs_icon`, `fs_lt_id`, `fs_lt_title`, `fs_lt_date`, `fs_lt_posterid`, `fs_lt_postername`, `fs_autoprune`, `fs_allowusertext`, `fs_allowbbcodes`, `fs_allowsmilies`, `fs_allowprvtopics`, `fs_countposts`, `fs_topiccount`, `fs_topiccount_pruned`, `fs_postcount`, `fs_postcount_pruned`, `fs_viewcount`, `fs_masterid`, `fs_mastername`, `fs_allowviewers`, `fs_allowpolls`) VALUES
(0, 100, 'General discussion', 'pub', 'General chat.', 'images/icons/default/forums.png', 0, '', 0, 0, '', 365, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, '', 1, 0),
(0, 101, 'Off-topic', 'pub', 'Various and off-topic.', 'images/icons/default/forums.png', 0, '', 0, 0, '', 365, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, '', 1, 0);

-- Forum structure
CREATE TABLE IF NOT EXISTS `cot_forum_structure` (
  `fn_id` mediumint(8) NOT NULL auto_increment,
  `fn_path` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `fn_code` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `fn_tpl` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `fn_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fn_desc` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `fn_icon` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `fn_defstate` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`fn_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Default categories
INSERT INTO `cot_forum_structure` (`fn_path`, `fn_code`, `fn_tpl`, `fn_title`, `fn_desc`, `fn_icon`, `fn_defstate`) VALUES
('1', 'pub', '', 'Public', '', '', 1);

-- Forum topics
CREATE TABLE IF NOT EXISTS `cot_forum_topics` (
  `ft_id` mediumint(8) unsigned NOT NULL auto_increment,
  `ft_mode` tinyint(1) unsigned NOT NULL default '0',
  `ft_state` tinyint(1) unsigned NOT NULL default '0',
  `ft_sticky` tinyint(1) unsigned NOT NULL default '0',
  `ft_tag` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `ft_sectionid` mediumint(8) NOT NULL default '0',
  `ft_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ft_desc` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ft_creationdate` int(11) NOT NULL default '0',
  `ft_updated` int(11) NOT NULL default '0',
  `ft_postcount` mediumint(8) NOT NULL default '0',
  `ft_viewcount` mediumint(8) NOT NULL default '0',
  `ft_lastposterid` int(11) NOT NULL default '-1',
  `ft_lastpostername` varchar(100) collate utf8_unicode_ci NOT NULL,
  `ft_firstposterid` int(11) NOT NULL default '-1',
  `ft_firstpostername` varchar(100) collate utf8_unicode_ci NOT NULL,
  `ft_poll` int(11) default '0',
  `ft_movedto` int(11) default '0',
  `ft_preview` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`ft_id`),
  KEY `ft_updated` (`ft_updated`),
  KEY `ft_mode` (`ft_mode`),
  KEY `ft_state` (`ft_state`),
  KEY `ft_sticky` (`ft_sticky`),
  KEY `ft_sectionid` (`ft_sectionid`),
  KEY `ft_movedto` (`ft_movedto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
