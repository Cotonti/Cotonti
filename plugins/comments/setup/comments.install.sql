/* Comments schema */

-- Main comments table
CREATE TABLE IF NOT EXISTS `cot_com` (
	`com_id` int UNSIGNED NOT NULL auto_increment,
	`com_code` varchar(255) NOT NULL,
	`com_area` varchar(64) NOT NULL,
	`com_author` varchar(100) NOT NULL DEFAULT '',
	`com_authorid` int UNSIGNED NOT NULL,
	`com_authorip` varchar(64) NOT NULL DEFAULT '',
	`com_text` text NOT NULL,
	`com_date` int UNSIGNED NOT NULL DEFAULT '0',
	`com_count` mediumint UNSIGNED NOT NULL DEFAULT '0',  -- Not using anywhere
	`com_isspecial` tinyint UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`com_id`),
	KEY (`com_area`, `com_code`)
);