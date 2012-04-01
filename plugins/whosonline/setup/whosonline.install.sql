/* Online user tracking schema */
DROP TABLE IF EXISTS `cot_online`;
CREATE TABLE `cot_online` (
  `online_id` int NOT NULL auto_increment,
  `online_ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `online_name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `online_lastseen` int NOT NULL default '0',
  `online_location` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `online_subloc` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `online_userid` int NOT NULL default '0',
  `online_shield` int NOT NULL default '0',
  `online_action` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `online_hammer` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`online_id`),
  KEY `online_lastseen` (`online_lastseen`),
  KEY `online_userid` (`online_userid`),
  KEY `online_name` (`online_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
