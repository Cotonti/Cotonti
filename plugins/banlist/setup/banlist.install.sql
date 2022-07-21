CREATE TABLE IF NOT EXISTS `cot_banlist` (
  `banlist_id` mediumint UNSIGNED NOT NULL auto_increment,
  `banlist_ip` varchar(15) NOT NULL default '',
  `banlist_email` varchar(128) NOT NULL default '',
  `banlist_reason` varchar(128) NOT NULL default '',
  `banlist_expire` int UNSIGNED NOT NULL default '0',
  PRIMARY KEY  (`banlist_id`),
  KEY `banlist_ip` (`banlist_ip`)
);