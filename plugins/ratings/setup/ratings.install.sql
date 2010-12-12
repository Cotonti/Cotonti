/* Ratings SQL schema */

CREATE TABLE `cot_rated` (
  `rated_id` int unsigned NOT NULL auto_increment,
  `rated_code` varchar(32) collate utf8_unicode_ci default NULL,
  `rated_userid` int default NULL,
  `rated_value` tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (`rated_id`),
  KEY `rated_code` (`rated_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `cot_ratings` (
  `rating_id` int NOT NULL auto_increment,
  `rating_code` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `rating_state` tinyint NOT NULL default '0',
  `rating_average` decimal(5,2) NOT NULL default '0.00',
  `rating_creationdate` int NOT NULL default '0',
  `rating_text` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`rating_id`),
  KEY `rating_code` (`rating_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
