CREATE TABLE IF NOT EXISTS `cot_contact` (
  `contact_id` int(12) NOT NULL AUTO_INCREMENT,
  `contact_author` varchar(24) NOT NULL,
  `contact_authorid` int(11) DEFAULT '0',
  `contact_date` int(12) NOT NULL,
  `contact_email` varchar(64) NOT NULL,
  `contact_subject` varchar(256) DEFAULT '',
  `contact_text` text NOT NULL,
  `contact_val` tinyint(1) unsigned DEFAULT '0',
  `contact_reply` text DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;