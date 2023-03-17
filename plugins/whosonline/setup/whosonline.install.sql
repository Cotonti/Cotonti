/* Online user tracking schema */
DROP TABLE IF EXISTS `cot_online`;
CREATE TABLE `cot_online` (
  `online_id` int UNSIGNED NOT NULL auto_increment,
  `online_ip` varchar(64) NOT NULL default '',
  `online_name` varchar(100) NOT NULL,
  `online_lastseen` int UNSIGNED NOT NULL default '0',
  `online_location` varchar(128) NOT NULL default '',
  `online_subloc` varchar(255) NOT NULL default '',
  `online_userid` int NOT NULL default '0', -- using '-1' value for guests
  `online_shield` int NOT NULL default '0',
  `online_action` varchar(64) NOT NULL default '',
  `online_hammer` tinyint NOT NULL default '0',
  PRIMARY KEY  (`online_id`),
  KEY `online_lastseen` (`online_lastseen`),
  KEY `online_userid` (`online_userid`),
  KEY `online_name` (`online_name`)
);
