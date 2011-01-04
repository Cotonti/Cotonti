/**
 * Forums module database installation
 */

-- Install auth for default forums
INSERT INTO `cot_auth` (`auth_groupid`, `auth_code`, `auth_option`,
	`auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES
(1, 'forums', 'pub', 1, 254, 1),
(2, 'forums', 'pub', 1, 254, 1),
(3, 'forums', 'pub', 0, 255, 1),
(4, 'forums', 'pub', 3, 128, 1),
(5, 'forums', 'pub', 255, 255, 1),
(6, 'forums', 'pub', 131, 0, 1),
(1, 'forums', 'general', 1, 254, 1),
(2, 'forums', 'general', 1, 254, 1),
(3, 'forums', 'general', 0, 255, 1),
(4, 'forums', 'general', 3, 128, 1),
(5, 'forums', 'general', 255, 255, 1),
(6, 'forums', 'general', 131, 0, 1),
(1, 'forums', 'offtopic', 1, 254, 1),
(2, 'forums', 'offtopic', 1, 254, 1),
(3, 'forums', 'offtopic', 0, 255, 1),
(4, 'forums', 'offtopic', 3, 128, 1),
(5, 'forums', 'offtopic', 255, 255, 1),
(6, 'forums', 'offtopic', 131, 0, 1);

-- Forum posts
CREATE TABLE IF NOT EXISTS `cot_forum_posts` (
  `fp_id` mediumint(8) unsigned NOT NULL auto_increment,
  `fp_topicid` mediumint(8) NOT NULL default '0',
  `fp_cat` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `fp_posterid` int(11) NOT NULL default '-1',
  `fp_postername` varchar(100) collate utf8_unicode_ci NOT NULL,
  `fp_creation` int(11) NOT NULL default '0',
  `fp_updated` int(11) NOT NULL default '0',
  `fp_updater` varchar(100) collate utf8_unicode_ci NOT NULL DEFAULT '',
  `fp_text` text collate utf8_unicode_ci NOT NULL,
  `fp_posterip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`fp_id`),
  UNIQUE KEY `fp_topicid` (`fp_topicid`,`fp_id`),
  KEY `fp_updated` (`fp_creation`),
  KEY `fp_posterid` (`fp_posterid`),
  KEY `fp_cat` (`fp_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Forum stats
CREATE TABLE IF NOT EXISTS `cot_forum_stats` (
  `fs_cat` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `fs_lt_id` int(11) NOT NULL default '0',
  `fs_lt_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fs_lt_date` int(11) NOT NULL default '0',
  `fs_lt_posterid` int(11) NOT NULL default '-1',
  `fs_lt_postername` varchar(100) collate utf8_unicode_ci NOT NULL,
  `fs_topiccount` int(11) NOT NULL default '0',
  `fs_postcount` int(11) NOT NULL default '0',
  `fs_viewcount` int(11) NOT NULL default '0',
  PRIMARY KEY  (`fs_cat`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Forum topics
CREATE TABLE IF NOT EXISTS `cot_forum_topics` (
  `ft_id` mediumint(8) unsigned NOT NULL auto_increment,
  `ft_mode` tinyint(1) unsigned NOT NULL default '0',
  `ft_state` tinyint(1) unsigned NOT NULL default '0',
  `ft_sticky` tinyint(1) unsigned NOT NULL default '0',
  `ft_tag` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `ft_cat` varchar(255) collate utf8_unicode_ci NOT NULL default '',
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
  `ft_movedto` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `ft_preview` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`ft_id`),
  KEY `ft_updated` (`ft_updated`),
  KEY `ft_mode` (`ft_mode`),
  KEY `ft_state` (`ft_state`),
  KEY `ft_sticky` (`ft_sticky`),
  KEY `ft_cat` (`ft_cat`),
  KEY `ft_movedto` (`ft_movedto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Default forums categories
INSERT INTO `cot_structure` (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
   `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
('forums', 'pub', '1', '', 'Public', '', 'images/icons/default/forums.png', 0, 0),
('forums', 'general', '1.1', '', 'General discussion', 'General discussion', 'images/icons/default/forums.png', 0, 0),
('forums', 'offtopic', '1.2', '', 'Off-topic', 'Various and off-topic', 'images/icons/default/forums.png', 0, 0);