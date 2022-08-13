<?php
/**
 * Update to v. 1.3.4
 *
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from 'utf8mb4'
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */
defined('COT_CODE') or die('Wrong URL');

global $db_online;

if (empty($db_online)) {
    // Registering tables
    cot::$db->registerTable('online');
}

$changeCollation = false;
if (
    cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(cot::$cfg['mysqlcollate']) || cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

cot::$db->query("
    UPDATE `{$db_online}` SET `online_ip`='' WHERE `online_ip` IS NULL;
    UPDATE `{$db_online}` SET `online_location`='' WHERE `online_location` IS NULL;
    UPDATE `{$db_online}` SET `online_subloc`='' WHERE `online_subloc` IS NULL;
    UPDATE `{$db_online}` SET `online_action`='' WHERE `online_action` IS NULL;
    ALTER TABLE `{$db_online}` 
        MODIFY `online_id` int UNSIGNED NOT NULL auto_increment,
        MODIFY `online_ip` varchar(15) NOT NULL default '',
        MODIFY `online_lastseen` int UNSIGNED NOT NULL default '0',
        MODIFY `online_location` varchar(128) NOT NULL default '',
        MODIFY `online_subloc` varchar(255) NOT NULL default '',
        MODIFY `online_userid` int NOT NULL default '0',
        MODIFY `online_action` varchar(64) NOT NULL default '',
        MODIFY `online_hammer` tinyint NOT NULL default '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_online}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}