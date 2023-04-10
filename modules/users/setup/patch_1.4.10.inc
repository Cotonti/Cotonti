<?php
/**
 * Update to v. 1.4.10
 *
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from 'utf8mb4'
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */
defined('COT_CODE') or die('Wrong URL');

global $db_users;

if (empty($db_users)) {
    // Registering tables
    Cot::$db->registerTable('users');
}

$changeCollation = false;
if (
    Cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(Cot::$cfg['mysqlcollate']) || Cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

Cot::$db->query("
    UPDATE `{$db_users}` SET `user_banexpire`='0' WHERE `user_banexpire` IS NULL;
    UPDATE `{$db_users}` SET `user_password`='' WHERE `user_password` IS NULL;
    UPDATE `{$db_users}` SET `user_passfunc`='' WHERE `user_passfunc` IS NULL;
    UPDATE `{$db_users}` SET `user_passsalt`='' WHERE `user_passsalt` IS NULL;
    UPDATE `{$db_users}` SET `user_maingrp`='4' WHERE `user_maingrp` IS NULL;
    UPDATE `{$db_users}` SET `user_email`='' WHERE `user_email` IS NULL;
    UPDATE `{$db_users}` SET `user_hideemail`='1' WHERE `user_hideemail` IS NULL;
    UPDATE `{$db_users}` SET `user_regdate`='0' WHERE `user_regdate` IS NULL;
    UPDATE `{$db_users}` SET `user_lastlog`='0' WHERE `user_lastlog` IS NULL;
    UPDATE `{$db_users}` SET `user_lastvisit`='0' WHERE `user_lastvisit` IS NULL;
    UPDATE `{$db_users}` SET `user_logcount`='0' WHERE `user_lastvisit` IS NULL;
    UPDATE `{$db_users}` SET `user_sidtime`='0' WHERE `user_sidtime` IS NULL;
    ALTER TABLE `{$db_users}` 
        MODIFY `user_id` int UNSIGNED NOT NULL auto_increment,
        MODIFY `user_banexpire` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `user_password` varchar(224) NOT NULL DEFAULT '',
        MODIFY `user_passfunc` VARCHAR(32) NOT NULL DEFAULT 'sha256',
        MODIFY `user_passsalt` VARCHAR(16) NOT NULL DEFAULT '',
        MODIFY `user_maingrp` mediumint UNSIGNED NOT NULL DEFAULT '4',
        MODIFY `user_country` char(2) NULL DEFAULT '',
        MODIFY `user_text` text NULL DEFAULT NULL,
        MODIFY `user_email` varchar(64) NOT NULL DEFAULT '',
        MODIFY `user_hideemail` tinyint UNSIGNED NOT NULL DEFAULT '1',
        MODIFY `user_theme` varchar(32) NULL DEFAULT '',
        MODIFY `user_scheme` varchar(32) NULL DEFAULT '',
        MODIFY `user_lang` varchar(16) NULL DEFAULT '',
        MODIFY `user_regdate` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `user_lastlog` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `user_lastvisit` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `user_lastip` varchar(16) NULL DEFAULT '',
        MODIFY `user_logcount` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `user_sid` char(64) NULL DEFAULT '',
        MODIFY `user_sidtime` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `user_lostpass` char(32) NULL DEFAULT '',
        MODIFY `user_auth` MEDIUMTEXT NULL DEFAULT NULL,
        MODIFY `user_token` char(16) DEFAULT '',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_users}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}
