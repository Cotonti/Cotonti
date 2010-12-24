CREATE TABLE IF NOT EXISTS `cot_contact` (
	`contact_id` INT(12) NOT NULL AUTO_INCREMENT,
	`contact_author` VARCHAR(24) NOT NULL,
	`contact_authorid` INT(12),
	`contact_date` INT(12) NOT NULL,
	`contact_email` VARCHAR(64) NOT NULL,
	`contact_subject` VARCHAR(256) NOT NULL,
	`contact_text` TEXT NOT NULL,
	`contact_val` tinyint(1) unsigned NOT NULL default '0',
	`contact_reply` TEXT NOT NULL,
	PRIMARY KEY (`contact_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;