<?php
/**
 * Update to v. 1.1.19
 *
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from 'utf8mb4'
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */
defined('COT_CODE') or die('Wrong URL');

global $db_rated, $db_ratings;

if (empty($db_rated)) {
    // Registering tables
    cot::$db->registerTable('rated');
    cot::$db->registerTable('ratings');
}

$changeCollation = false;
if (
    cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(cot::$cfg['mysqlcollate']) || cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

cot::$db->query("
    UPDATE `{$db_rated}` SET `rated_userid`='0' WHERE `rated_userid` IS NULL;
    ALTER TABLE `{$db_rated}` 
        MODIFY `rated_id` int UNSIGNED NOT NULL auto_increment,
        MODIFY `rated_userid` int UNSIGNED NOT NULL default '0',
        MODIFY `rated_date` int UNSIGNED NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_rated}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}


cot::$db->query("ALTER TABLE `{$db_ratings}` 
    MODIFY `rating_id` int UNSIGNED NOT NULL auto_increment,
    MODIFY `rating_creationdate` int UNSIGNED NOT NULL default '0',
    ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_ratings}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}
