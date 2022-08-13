<?php
/**
 * Update to v. 1.0.6
 *
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from 'utf8mb4'
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */
defined('COT_CODE') or die('Wrong URL');

global $db_i18n_structure, $db_i18n_pages;

if (empty($db_i18n_structure)) {
    // Registering tables
    cot::$db->registerTable('i18n_structure');
    cot::$db->registerTable('i18n_pages');
}

$changeCollation = false;
if (
    cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(cot::$cfg['mysqlcollate']) || cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

cot::$db->query("ALTER TABLE `{$db_i18n_structure}` ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_i18n_structure}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}


cot::$db->query("ALTER TABLE `{$db_i18n_pages}` 
    MODIFY `ipage_date` INT UNSIGNED NOT NULL DEFAULT 0,
    MODIFY `ipage_text` MEDIUMTEXT NULL DEFAULT NULL,
    ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_i18n_pages}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}
