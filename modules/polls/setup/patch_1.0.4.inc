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

global $db_polls, $db_polls_options, $db_polls_voters;

if (empty($db_polls)) {
    // Registering tables
    cot::$db->registerTable('polls');
    cot::$db->registerTable('polls_options');
    cot::$db->registerTable('polls_voters');
}

$changeCollation = false;
if (
    cot::$cfg['mysqlcharset'] == 'utf8mb4' &&
    (empty(cot::$cfg['mysqlcollate']) || cot::$cfg['mysqlcollate'] == 'utf8mb4_unicode_ci')
) {
    $changeCollation = true;
}

cot::$db->query("
    UPDATE `{$db_polls}` SET `poll_text`='' WHERE `poll_text` IS NULL;
    ALTER TABLE `{$db_polls}` 
        MODIFY `poll_id` mediumint UNSIGNED NOT NULL auto_increment,
        MODIFY `poll_code` varchar(255) NOT NULL default '',
        MODIFY `poll_state` tinyint UNSIGNED NOT NULL default '0',
        MODIFY `poll_creationdate` int UNSIGNED NOT NULL default '0',
        MODIFY `poll_text` varchar(255) NOT NULL,
        MODIFY `poll_multiple` tinyint UNSIGNED NOT NULL default '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_polls}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}

cot::$db->query("
    UPDATE `{$db_polls_options}` SET `po_text`='' WHERE `po_text` IS NULL;
    ALTER TABLE `{$db_polls_options}` 
        MODIFY `po_id` mediumint UNSIGNED NOT NULL auto_increment,
        MODIFY `po_pollid` mediumint UNSIGNED NOT NULL,
        MODIFY `po_text` varchar(128) NOT NULL,
        MODIFY `po_count` mediumint UNSIGNED NOT NULL default '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_polls_options}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}

cot::$db->query("
    UPDATE `{$db_polls_voters}` SET `pv_userip`='' WHERE `pv_userip` IS NULL;
    ALTER TABLE `{$db_polls_voters}` 
        MODIFY `pv_id` int UNSIGNED NOT NULL auto_increment,
        MODIFY `pv_pollid` mediumint UNSIGNED NOT NULL,
        MODIFY `pv_userid` int UNSIGNED NOT NULL,
        MODIFY `pv_userip` varchar(15) NOT NULL default '',
        ENGINE=InnoDB;");
if ($changeCollation) {
    cot::$db->query("ALTER TABLE `{$db_polls_voters}`
    CONVERT TO CHARACTER SET '" . cot::$cfg['mysqlcharset'] . "' COLLATE '" . cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . cot::$cfg['mysqlcharset'] . "' COLLATE='" . cot::$cfg['mysqlcollate'] . "'");
}
