/* Internationalization Data */

-- Structure i18n
CREATE TABLE IF NOT EXISTS `cot_i18n_structure` (
	`istructure_code` VARCHAR(255) NOT NULL,
	`istructure_locale` VARCHAR(8) NOT NULL DEFAULT 'en',
	`istructure_title` VARCHAR(128) NOT NULL,
	`istructure_desc` VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`istructure_code`, `istructure_locale`),
	KEY `istructure_code` (`istructure_code`)
);

-- Page translations
CREATE TABLE IF NOT EXISTS `cot_i18n_pages` (
	`ipage_id` INT UNSIGNED NOT NULL,
	`ipage_locale` VARCHAR(8) NOT NULL DEFAULT 'en',
	`ipage_translatorid` INT UNSIGNED NOT NULL,
	`ipage_translatorname` VARCHAR(100) NOT NULL,
	`ipage_date` INT UNSIGNED NOT NULL DEFAULT 0,
	`ipage_title` VARCHAR(128) NOT NULL DEFAULT '',
	`ipage_desc` VARCHAR(255) NOT NULL DEFAULT '',
	`ipage_text` MEDIUMTEXT NULL DEFAULT NULL,
	PRIMARY KEY (`ipage_id`, `ipage_locale`),
	KEY `ipage_id` (`ipage_id`),
	KEY `ipage_translatorid` (`ipage_translatorid`),
    CONSTRAINT fk_translation_pages FOREIGN KEY (ipage_id) REFERENCES `cot_pages` (`page_id`) ON DELETE RESTRICT
);
