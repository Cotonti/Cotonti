/**
 * PM DB install
 */

CREATE TABLE IF NOT EXISTS `cot_pm` (
  `pm_id` int(11) unsigned NOT NULL auto_increment,
  `pm_date` int(11) NOT NULL default '0',
  `pm_fromuserid` int(11) NOT NULL default '0',
  `pm_fromuser` varchar(100) collate utf8_unicode_ci NOT NULL,
  `pm_touserid` int(11) NOT NULL default '0',
  `pm_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pm_text` text collate utf8_unicode_ci NOT NULL,
  `pm_fromstate` tinyint(2) NOT NULL default '0',
  `pm_tostate` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`pm_id`),
  KEY `pm_fromuserid` (`pm_fromuserid`),
  KEY `pm_touserid` (`pm_touserid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;