CREATE TABLE IF NOT EXISTS `cot_banlist` (
  `banlist_id` int NOT NULL auto_increment,
  `banlist_ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `banlist_email` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `banlist_reason` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `banlist_expire` int default '0',
  PRIMARY KEY  (`banlist_id`),
  KEY `banlist_ip` (`banlist_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;