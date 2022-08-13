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

global $db_pfs, $db_pfs_folders;

if (empty($db_pfs)) {
    // Registering tables
    cot::$db->registerTable('pfs');
    cot::$db->registerTable('pfs_folders');
}

$changeCollation = false;
if (
    cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(cot::$cfg['mysqlcollate']) || cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

cot::$db->query("UPDATE `{$db_pfs}` SET `pfs_folderid` = 0 WHERE `pfs_folderid` < 0;
ALTER TABLE `{$db_pfs}` 
    MODIFY `pfs_id` int UNSIGNED NOT NULL auto_increment,
    MODIFY `pfs_userid` int UNSIGNED NOT NULL DEFAULT '0',
    MODIFY `pfs_date` int UNSIGNED NOT NULL DEFAULT '0',
    MODIFY `pfs_folderid` int UNSIGNED NOT NULL DEFAULT '0',
    MODIFY `pfs_size` int UNSIGNED NOT NULL DEFAULT '0',
    MODIFY `pfs_count` int UNSIGNED NOT NULL DEFAULT '0',
    ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_pfs}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}


cot::$db->query("ALTER TABLE `{$db_pfs_folders}` 
    MODIFY `pff_id` int UNSIGNED NOT NULL auto_increment,
    MODIFY `pff_userid` int UNSIGNED NOT NULL DEFAULT '0',
    MODIFY `pff_date` int UNSIGNED NOT NULL DEFAULT '0',
    MODIFY `pff_updated` int UNSIGNED NOT NULL DEFAULT '0',
    MODIFY `pff_ispublic` tinyint UNSIGNED NOT NULL DEFAULT '0',
    MODIFY `pff_isgallery` tinyint UNSIGNED NOT NULL DEFAULT '0',
    MODIFY `pff_count` int UNSIGNED NOT NULL DEFAULT '0',
    ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_pfs_folders}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}
