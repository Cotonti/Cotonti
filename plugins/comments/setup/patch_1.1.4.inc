<?php
/**
 * Update to v. 1.1.4
 *
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from 'utf8mb4'
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */
defined('COT_CODE') or die('Wrong URL');

global $db_com;

if (empty($db_com)) {
    // Registering tables
    Cot::$db->registerTable('com');
}

$changeCollation = false;
if (
    Cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(Cot::$cfg['mysqlcollate']) || Cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

Cot::$db->query("
    UPDATE `{$db_com}` SET `com_authorid`='0' WHERE `com_authorid` IS NULL;
    ALTER TABLE `{$db_com}` 
        MODIFY `com_id` int UNSIGNED NOT NULL auto_increment,
        MODIFY `com_code` varchar(255) NOT NULL,
        MODIFY `com_area` varchar(64) NOT NULL,
        MODIFY `com_author` varchar(100) NOT NULL DEFAULT '',
        MODIFY `com_authorid` int UNSIGNED NOT NULL,
        MODIFY `com_authorip` varchar(15) NOT NULL DEFAULT '',
        MODIFY `com_text` text NOT NULL,
        MODIFY `com_date` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `com_count` mediumint UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `com_isspecial` tinyint UNSIGNED NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_com}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}
