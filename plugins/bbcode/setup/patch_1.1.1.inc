<?php
/**
 * Update to v. 1.1.1
 *
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from 'utf8mb4'
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */
defined('COT_CODE') or die('Wrong URL');

global $db_bbcode;

if (empty($db_bbcode)) {
    // Registering tables
    Cot::$db->registerTable('bbcode');
}

$changeCollation = false;
if (
    Cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(Cot::$cfg['mysqlcollate']) || Cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

Cot::$db->query("ALTER TABLE `{$db_bbcode}` 
    MODIFY `bbc_id` smallint UNSIGNED NOT NULL auto_increment,
    MODIFY `bbc_container` tinyint UNSIGNED NOT NULL DEFAULT '1',
    MODIFY `bbc_enabled` tinyint UNSIGNED NOT NULL DEFAULT '1',
    MODIFY `bbc_plug` varchar(100) NOT NULL DEFAULT '',
    MODIFY `bbc_postrender` tinyint NOT NULL DEFAULT '0',
    ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_bbcode}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}
