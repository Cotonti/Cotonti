/* Ratings SQL schema */

-- Individual votes
CREATE TABLE IF NOT EXISTS `cot_rated` (
  `rated_id` int UNSIGNED NOT NULL auto_increment,
  `rated_code` varchar(255) default NULL,
  `rated_area` varchar(64) NOT NULL default '',
  `rated_userid` int UNSIGNED NOT NULL default '0',
  `rated_value` tinyint UNSIGNED NOT NULL default '0',
  `rated_date` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`rated_id`),
  KEY (`rated_area`, `rated_code`)
);

-- Item ratings
CREATE TABLE IF NOT EXISTS `cot_ratings` (
  `rating_id` int UNSIGNED NOT NULL auto_increment,
  `rating_code` varchar(255) NOT NULL default '',
  `rating_area` varchar(64) NOT NULL default '',
  `rating_state` tinyint NOT NULL default '0',
  `rating_average` decimal(5,2) NOT NULL default '0.00',
  `rating_creationdate` int UNSIGNED NOT NULL default '0',
  `rating_text` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`rating_id`),
  KEY (`rating_area`, `rating_code`)
);
