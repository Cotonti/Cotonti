<?php
/**
 * Update to v. 1.0.12
 *
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from 'utf8mb4'
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */
defined('COT_CODE') or die('Wrong URL');

global $db_pm;

if (empty($db_pm)) {
    // Registering tables
    cot::$db->registerTable('pm');
}

$changeCollation = false;
if (
    cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(cot::$cfg['mysqlcollate']) || cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

cot::$db->query("
    UPDATE `{$db_pm}` SET `pm_text`='' WHERE `pm_text` IS NULL;
    ALTER TABLE `{$db_pm}` 
        MODIFY `pm_id` int UNSIGNED NOT NULL auto_increment,
        MODIFY `pm_date` int UNSIGNED NOT NULL default '0',
        MODIFY `pm_fromuserid` int UNSIGNED NOT NULL,
        MODIFY `pm_fromuser` varchar(100) NOT NULL DEFAULT '',
        MODIFY `pm_touserid` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `pm_text` text NOT NULL,
        MODIFY `pm_fromstate` tinyint NOT NULL DEFAULT '0',
        MODIFY `pm_tostate` tinyint NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_pm}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}
