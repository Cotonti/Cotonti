/* Internationalization Data */

-- Structure i18n
CREATE TABLE IF NOT EXISTS `cot_i18n_structure` (
	`istructure_code` VARCHAR(255) NOT NULL REFERENCES `cot_structure` (`structure_code`),
	`istructure_locale` VARCHAR(8) NOT NULL DEFAULT 'en',
	`istructure_title` VARCHAR(128) NOT NULL,
	`istructure_desc` VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`istructure_code`, `istructure_locale`),
	KEY `istructure_code` (`istructure_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Page translations
CREATE TABLE IF NOT EXISTS `cot_i18n_pages` (
	`ipage_id` INT UNSIGNED NOT NULL REFERENCES `cot_pages` (`page_id`),
	`ipage_locale` VARCHAR(8) NOT NULL DEFAULT 'en',
	`ipage_translatorid` INT UNSIGNED NOT NULL REFERENCES `cot_users` (`user_id`),
	`ipage_translatorname` VARCHAR(100) NOT NULL REFERENCES `cot_users` (`user_name`),
	`ipage_date` INT NOT NULL DEFAULT 0,
	`ipage_title` VARCHAR(128) NOT NULL DEFAULT '',
	`ipage_desc` VARCHAR(255) NOT NULL DEFAULT '',
	`ipage_text` MEDIUMTEXT NOT NULL,
	PRIMARY KEY (`ipage_id`, `ipage_locale`),
	KEY `ipage_id` (`ipage_id`),
	KEY `ipage_translatorid` (`ipage_translatorid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
