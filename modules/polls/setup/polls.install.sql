/**
 * Polls DB install
 */

-- Polls
CREATE TABLE IF NOT EXISTS `cot_polls` (
  `poll_id` mediumint UNSIGNED NOT NULL auto_increment,
  `poll_type` varchar(10) NOT NULL default 'index',
  `poll_code` varchar(255) NOT NULL default '',
  `poll_state` tinyint UNSIGNED NOT NULL default '0',
  `poll_creationdate` int UNSIGNED NOT NULL default '0',
  `poll_text` varchar(255) NOT NULL,
  `poll_multiple` tinyint UNSIGNED NOT NULL default '0',
  PRIMARY KEY  (`poll_id`),
  KEY `poll_creationdate` (`poll_creationdate`)
);

-- Options
CREATE TABLE IF NOT EXISTS `cot_polls_options` (
  `po_id` mediumint UNSIGNED NOT NULL auto_increment,
  `po_pollid` mediumint UNSIGNED NOT NULL,
  `po_text` varchar(128) NOT NULL,
  `po_count` mediumint UNSIGNED NOT NULL default '0',
  PRIMARY KEY  (`po_id`),
  KEY `po_pollid` (`po_pollid`)
);

-- Votes
CREATE TABLE IF NOT EXISTS `cot_polls_voters` (
  `pv_id` int UNSIGNED NOT NULL auto_increment,
  `pv_pollid` mediumint UNSIGNED NOT NULL,
  `pv_userid` int UNSIGNED NOT NULL,
  `pv_userip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`pv_id`),
  KEY `pv_pollid` (`pv_pollid`)
);