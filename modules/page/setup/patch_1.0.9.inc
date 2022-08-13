<?php
/**
 * Update to v. 1.0.9
 *
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from 'utf8mb4'
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */
defined('COT_CODE') or die('Wrong URL');

global $db_pages;

if (empty($db_pages)) {
    // Registering tables
    cot::$db->registerTable('pages');
}

$changeCollation = false;
if (
    cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(cot::$cfg['mysqlcollate']) || cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

cot::$db->query("
    UPDATE `{$db_pages}` SET `page_alias`='' WHERE `page_alias` IS NULL;
    UPDATE `{$db_pages}` SET `page_state`='0' WHERE `page_state` IS NULL;
    UPDATE `{$db_pages}` SET `page_ownerid`='0' WHERE `page_ownerid` IS NULL;
    UPDATE `{$db_pages}` SET `page_date`='0' WHERE `page_date` IS NULL;
    UPDATE `{$db_pages}` SET `page_begin`='0' WHERE `page_begin` IS NULL;
    UPDATE `{$db_pages}` SET `page_expire`='0' WHERE `page_expire` IS NULL;
    UPDATE `{$db_pages}` SET `page_updated`='0' WHERE `page_updated` IS NULL;
    UPDATE `{$db_pages}` SET `page_file`='0' WHERE `page_file` IS NULL;
    UPDATE `{$db_pages}` SET `page_size`='0' WHERE `page_size` IS NULL;
    UPDATE `{$db_pages}` SET `page_count`='0' WHERE `page_count` IS NULL;
    UPDATE `{$db_pages}` SET `page_rating`='0.00' WHERE `page_rating` IS NULL;
    UPDATE `{$db_pages}` SET `page_filecount`='0' WHERE `page_filecount` IS NULL;
    ALTER TABLE `{$db_pages}` 
        MODIFY `page_id` int UNSIGNED NOT NULL auto_increment,
        MODIFY `page_alias` varchar(255) NOT NULL DEFAULT '',
        MODIFY `page_state` tinyint UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `page_cat` varchar(255) NOT NULL,
        MODIFY `page_title` varchar(255) NOT NULL,
        MODIFY `page_text` MEDIUMTEXT NULL DEFAULT NULL,
        MODIFY `page_ownerid` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `page_date` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `page_begin` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `page_expire` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `page_updated` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `page_file` tinyint UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `page_size` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `page_count` mediumint UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `page_rating` decimal(5,2) NOT NULL DEFAULT '0.00',
        MODIFY `page_filecount` mediumint UNSIGNED NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_pages}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}
