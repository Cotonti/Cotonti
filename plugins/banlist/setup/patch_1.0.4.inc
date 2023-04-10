<?php
/**
 * Update to v. 1.0.4
 *
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from 'utf8mb4'
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */
defined('COT_CODE') or die('Wrong URL');

global $db_banlist;

if (empty($db_banlist)) {
    // Registering tables
    Cot::$db->registerTable('banlist');
}

$changeCollation = false;
if (
    Cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(Cot::$cfg['mysqlcollate']) || Cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

Cot::$db->query("
    UPDATE `{$db_banlist}` SET `banlist_id`='' WHERE `banlist_id` IS NULL;
    UPDATE `{$db_banlist}` SET `banlist_email`='' WHERE `banlist_email` IS NULL;
    UPDATE `{$db_banlist}` SET `banlist_reason`='' WHERE `banlist_reason` IS NULL;
    UPDATE `{$db_banlist}` SET `banlist_expire`='0' WHERE `banlist_expire` IS NULL;
    ALTER TABLE `{$db_banlist}` 
        MODIFY `banlist_id` mediumint UNSIGNED NOT NULL auto_increment,
        MODIFY `banlist_ip` varchar(15) NOT NULL default '',
        MODIFY `banlist_email` varchar(128) NOT NULL default '',
        MODIFY `banlist_reason` varchar(128) NOT NULL default '',
        MODIFY `banlist_expire` int UNSIGNED NOT NULL default '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_banlist}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}
