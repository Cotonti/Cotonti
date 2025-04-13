/**
 * Comments system for Cotonti
 * Database schema
 */

-- Main comments table
CREATE TABLE IF NOT EXISTS `cot_com` (
    `com_id` INT UNSIGNED NOT NULL auto_increment,
    `com_area` VARCHAR(64) NOT NULL,
    `com_code` VARCHAR(255) NOT NULL,
    `com_author` VARCHAR(100) NOT NULL DEFAULT '',
    `com_authorid` INT UNSIGNED NOT NULL,
    `com_authorip` VARCHAR(64) NOT NULL DEFAULT '',
    `com_text` TEXT NOT NULL,
    `com_date` INT UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`com_id`),
    KEY (`com_area`, `com_code`)
);