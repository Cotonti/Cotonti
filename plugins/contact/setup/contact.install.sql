CREATE TABLE IF NOT EXISTS `cot_contact` (
  `contact_id` int UNSIGNED NOT NULL auto_increment,
  `contact_author` varchar(24) NOT NULL,
  `contact_authorid` int UNSIGNED NOT NULL DEFAULT '0',
  `contact_date` int UNSIGNED NOT NULL,
  `contact_email` varchar(64) NOT NULL,
  `contact_subject` varchar(255) DEFAULT '',
  `contact_text` text NOT NULL,
  `contact_val` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `contact_reply` text DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
);