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
    Cot::$db->registerTable('rated');
    Cot::$db->registerTable('ratings');
}

$changeCollation = false;
if (
    Cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(Cot::$cfg['mysqlcollate']) || Cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

Cot::$db->query("
    UPDATE `{$db_rated}` SET `rated_userid`='0' WHERE `rated_userid` IS NULL;
    ALTER TABLE `{$db_rated}` 
        MODIFY `rated_id` int UNSIGNED NOT NULL auto_increment,
        MODIFY `rated_userid` int UNSIGNED NOT NULL default '0',
        MODIFY `rated_date` int UNSIGNED NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_rated}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}


Cot::$db->query("ALTER TABLE `{$db_ratings}` 
    MODIFY `rating_id` int UNSIGNED NOT NULL auto_increment,
    MODIFY `rating_creationdate` int UNSIGNED NOT NULL default '0',
    ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_ratings}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}
