/**
 * Polls DB install
 */

-- Polls
CREATE TABLE IF NOT EXISTS `cot_polls` (
  `poll_id` mediumint(8) NOT NULL auto_increment,
  `poll_type` varchar(10) collate utf8_unicode_ci NOT NULL default 'index',
  `poll_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `poll_state` tinyint(1) NOT NULL default '0',
  `poll_creationdate` int(11) NOT NULL default '0',
  `poll_text` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `poll_multiple` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`poll_id`),
  KEY `poll_creationdate` (`poll_creationdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- Options
CREATE TABLE IF NOT EXISTS `cot_polls_options` (
  `po_id` mediumint(8) unsigned NOT NULL auto_increment,
  `po_pollid` mediumint(8) unsigned NOT NULL default '0',
  `po_text` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `po_count` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`po_id`),
  KEY `po_pollid` (`po_pollid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- Votes
CREATE TABLE IF NOT EXISTS `cot_polls_voters` (
  `pv_id` mediumint(8) unsigned NOT NULL auto_increment,
  `pv_pollid` mediumint(8) NOT NULL default '0',
  `pv_userid` mediumint(8) NOT NULL default '0',
  `pv_userip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`pv_id`),
  KEY `pv_pollid` (`pv_pollid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;