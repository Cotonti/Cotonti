/**
 * PM DB install
 */

CREATE TABLE IF NOT EXISTS `cot_pm` (
  `pm_id` int UNSIGNED NOT NULL auto_increment,
  `pm_date` int UNSIGNED NOT NULL default '0',
  `pm_fromuserid` int UNSIGNED NOT NULL,
  `pm_fromuser` varchar(100) NOT NULL DEFAULT '',
  `pm_touserid` int UNSIGNED NOT NULL DEFAULT '0',
  `pm_title` VARCHAR(255) NOT NULL DEFAULT '',
  `pm_text` text NOT NULL,
  `pm_fromstate` tinyint NOT NULL DEFAULT '0',
  `pm_tostate` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY  (`pm_id`),
  KEY `pm_fromuserid` (`pm_fromuserid`),
  KEY `pm_touserid` (`pm_touserid`)
);