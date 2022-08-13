<?php
/**
 * Update to v. 1.0.3
 *
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from 'utf8mb4'
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */
defined('COT_CODE') or die('Wrong URL');

global $db_referers;

if (empty($db_referers)) {
    // Registering tables
    cot::$db->registerTable('referers');
}

$changeCollation = false;
if (
    cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(cot::$cfg['mysqlcollate']) || cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

cot::$db->query("ALTER TABLE `{$db_referers}` 
    MODIFY `ref_url` varchar(255) NOT NULL,
    MODIFY `ref_count` mediumint UNSIGNED NOT NULL default '0',
    ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_referers}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}
