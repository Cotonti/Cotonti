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
  `fp_id` int UNSIGNED NOT NULL auto_increment,
  `fp_topicid` mediumint NOT NULL,
  `fp_cat` varchar(255) NOT NULL,
  `fp_posterid` int UNSIGNED NOT NULL,
  `fp_postername` varchar(100) NOT NULL,
  `fp_creation` int UNSIGNED NOT NULL DEFAULT '0',
  `fp_updated` int UNSIGNED NOT NULL DEFAULT '0',
  `fp_updater` varchar(100) NOT NULL DEFAULT '',
  `fp_text` mediumtext NOT NULL,
  `fp_posterip` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY  (`fp_id`),
  UNIQUE KEY `fp_topicid_id_idx` (`fp_topicid`,`fp_id`),
  KEY `fp_created_idx` (`fp_creation`),
  KEY `fp_updated_idx` (`fp_updated`),
  KEY `fp_posterid` (`fp_posterid`),
  KEY `fp_cat_idx` (`fp_cat`),
  KEY `fp_topicid_idx` (`fp_topicid`)
);

-- Forum stats
CREATE TABLE IF NOT EXISTS `cot_forum_stats` (
  `fs_cat` varchar(255) NOT NULL,
  `fs_lt_id` int UNSIGNED NOT NULL DEFAULT '0',
  `fs_lt_title` varchar(255) NOT NULL DEFAULT '',
  `fs_lt_date` int UNSIGNED NOT NULL DEFAULT '0',
  `fs_lt_posterid` int UNSIGNED NOT NULL,
  `fs_lt_postername` varchar(100) NOT NULL,
  `fs_topiccount` mediumint UNSIGNED NOT NULL DEFAULT '0',
  `fs_postcount` int UNSIGNED NOT NULL DEFAULT '0',
  `fs_viewcount` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`fs_cat`)
);

-- Forum topics
CREATE TABLE IF NOT EXISTS `cot_forum_topics` (
  `ft_id` mediumint UNSIGNED NOT NULL auto_increment,
  `ft_mode` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `ft_state` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `ft_sticky` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `ft_tag` varchar(16) NOT NULL DEFAULT '',
  `ft_cat` varchar(255) NOT NULL,
  `ft_title` varchar(255) NOT NULL,
  `ft_desc` varchar(255) NOT NULL DEFAULT '',
  `ft_creationdate` int UNSIGNED NOT NULL,
  `ft_updated` int UNSIGNED NOT NULL DEFAULT '0',
  `ft_postcount` mediumint UNSIGNED NOT NULL default '0',
  `ft_viewcount` mediumint UNSIGNED NOT NULL DEFAULT '0',
  `ft_lastposterid` int UNSIGNED NOT NULL DEFAULT '0',
  `ft_lastpostername` varchar(100) NOT NULL DEFAULT '',
  `ft_firstposterid` int UNSIGNED NOT NULL,
  `ft_firstpostername` varchar(100) NOT NULL,
  `ft_poll` int UNSIGNED default '0',
  `ft_movedto` mediumint UNSIGNED NOT NULL default 0,
  `ft_preview` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`ft_id`),
  KEY `ft_updated` (`ft_updated`),
  KEY `ft_mode` (`ft_mode`),
  KEY `ft_state` (`ft_state`),
  KEY `ft_sticky` (`ft_sticky`),
  KEY `ft_cat` (`ft_cat`),
  KEY `ft_movedto` (`ft_movedto`)
);

-- Default forums categories
INSERT INTO `cot_structure` (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
   `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
('forums', 'pub', '1', '', 'Public', '', 'images/icons/default/modules/forums/forum.png', 0, 0),
('forums', 'general', '1.1', '', 'General discussion', 'General discussion', 'images/icons/default/modules/forums/forum.png', 0, 0),
('forums', 'offtopic', '1.2', '', 'Off-topic', 'Various and off-topic', 'images/icons/default/modules/forums/forum.png', 0, 0);