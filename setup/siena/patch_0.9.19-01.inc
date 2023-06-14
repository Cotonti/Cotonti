<?php
/**
 * Update to v. 0.9.19-01
 */
defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

global $file, $db_auth, $db_cache, $db_cache_bindings, $db_core, $db_config, $db_groups, $db_groups_users, $db_logger,
        $db_online, $db_extra_fields, $db_plugins, $db_structure, $db_updates, $db_users;

if (!function_exists('cot_installConfigReplace')) {
    /**
     * Replaces a sample config with its actual value
     *
     * @param string $file_contents Config file contents
     * @param string $config_name Config option name
     * @param string $config_value Config value to set
     * @return string Modified file contents
     */
    function cot_installConfigReplace(&$file_contents, $config_name, $config_value)
    {
        $file_contents = preg_replace(
            "#^\\\$cfg\['$config_name'\]\s*=\s*'.*?';#m",
            "\$cfg['$config_name'] = '$config_value';",
            $file_contents
        );
    }
}
/**
 * Migrate to MySql collation 'utf8mb4_unicode_ci' from 'utf8_unicode_ci'
 * Will not change collation if user using his own $cfg['mysqlcharset'] and $cfg['mysqlcharset'] settings different
 * from old default 'utf8_unicode_ci' settings
 *
 * Replace table engine MyISAM to InnoDB
 * Some DB tables updates
 */

$changeCollation = false;
$mySqlVersion = Cot::$db->getConnection()->getAttribute(PDO::ATTR_SERVER_VERSION);
if (
    version_compare($mySqlVersion, '5.7', '>=') &&
    version_compare(PHP_VERSION, '5.5', '>=') &&
    (empty(Cot::$cfg['mysqlcharset']) || in_array(Cot::$cfg['mysqlcharset'], ['utf8', 'utf8mb4']))  //&&
    // Not sure about this condition. May be 'mysqlcharset' is enough
    //(empty(Cot::$cfg['mysqlcollate']) || in_array(Cot::$cfg['mysqlcollate'], ['utf8_unicode_ci', 'utf8mb4_unicode_ci']))
) {
    $changeCollation = true;

    // Update config
    if (Cot::$cfg['mysqlcharset'] != 'utf8mb4' || Cot::$cfg['mysqlcollate'] != 'utf8mb4_unicode_ci') {
        $configContent = file_get_contents($file['config']);
        if (Cot::$cfg['mysqlcharset'] != 'utf8mb4') {
            cot_installConfigReplace($configContent, 'mysqlcharset', 'utf8mb4');
            Cot::$cfg['mysqlcharset'] = 'utf8mb4';
        }
        if (Cot::$cfg['mysqlcollate'] != 'utf8mb4_unicode_ci') {
            cot_installConfigReplace($configContent, 'mysqlcollate', 'utf8mb4_unicode_ci');
            Cot::$cfg['mysqlcollate'] = 'utf8mb4_unicode_ci';
        }
        file_put_contents($file['config'], $configContent);
        unset($configContent);

        Cot::$db->query(
            "SET NAMES '" . Cot::$cfg['mysqlcharset'] . "'  COLLATE '" .
            Cot::$cfg['mysqlcollate'] . "'"
        );
    }
}

// Changing character set and collation database wide:
if ($changeCollation) {
    Cot::$db->query( "ALTER DATABASE `" . Cot::$cfg['mysqldb'] . "` CHARACTER SET '" . Cot::$cfg['mysqlcharset'] .
        "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "'");
}

Cot::$db->query("
    UPDATE `{$db_auth}` SET `auth_setbyuserid`=0 WHERE `auth_setbyuserid` IS NULL;
    ALTER TABLE `{$db_auth}`  
        MODIFY `auth_id` int UNSIGNED NOT NULL auto_increment,
        MODIFY `auth_groupid` mediumint UNSIGNED NOT NULL,
        MODIFY `auth_code` varchar(255) NOT NULL,
        MODIFY `auth_option` varchar(255) NOT NULL default '',
        MODIFY `auth_rights` TINYINT UNSIGNED NULL DEFAULT '0',
        MODIFY `auth_rights_lock` TINYINT UNSIGNED NULL DEFAULT '0',
        MODIFY `auth_setbyuserid` INT UNSIGNED NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_auth}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}


Cot::$db->query("
    UPDATE `{$db_cache}` SET `c_expire`=0 WHERE `c_expire` IS NULL;
    UPDATE `{$db_cache}` SET `c_auto`=0 WHERE `c_auto` IS NULL;
    ALTER TABLE `{$db_cache}`
        MODIFY `c_realm` varchar(64) NOT NULL default 'cot',
        MODIFY `c_expire` INT UNSIGNED NOT NULL default '0',
        MODIFY `c_auto` TINYINT UNSIGNED NOT NULL default '0',
        MODIFY `c_value` MEDIUMTEXT NULL DEFAULT NULL,
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_cache}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}


Cot::$db->query("
    UPDATE `{$db_cache_bindings}` SET `c_type`=0 WHERE `c_type` IS NULL;
    ALTER TABLE `{$db_cache_bindings}` 
        MODIFY `c_event` VARCHAR(64) NOT NULL,
        MODIFY `c_realm` VARCHAR(64) NOT NULL DEFAULT 'cot',
        MODIFY `c_type` TINYINT NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_cache_bindings}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}

Cot::$db->query("
    UPDATE `{$db_config}` SET `config_owner`='core' WHERE `config_owner` IS NULL;
    UPDATE `{$db_config}` SET `config_subcat`='' WHERE `config_subcat` IS NULL;
    UPDATE `{$db_config}` SET `config_type`='0' WHERE `config_type` IS NULL;
    UPDATE `{$db_config}` SET `config_default`='' WHERE `config_default` IS NULL;
    UPDATE `{$db_config}` SET `config_variants`='' WHERE `config_variants` IS NULL;
    UPDATE `{$db_config}` SET `config_text`='' WHERE `config_text` IS NULL;
    UPDATE `{$db_config}` SET `config_donor`='' WHERE `config_donor` IS NULL;
    ALTER TABLE `{$db_config}` 
        MODIFY `config_owner` varchar(24) NOT NULL default 'core',
        MODIFY `config_cat` varchar(64) NOT NULL,
        MODIFY `config_subcat` varchar(255) NOT NULL default '',
        MODIFY `config_name` varchar(64) NOT NULL,
        MODIFY `config_type` tinyint NOT NULL default '0',
        MODIFY `config_default` varchar(255) NOT NULL default '',
        MODIFY `config_variants` varchar(255) NOT NULL default '',
        MODIFY `config_text` varchar(255) NOT NULL default '',
        MODIFY `config_donor` varchar(64) NOT NULL default '',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_config}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}

Cot::$db->query("
    UPDATE `{$db_core}` SET `ct_code`='' WHERE `ct_code` IS NULL;
    UPDATE `{$db_core}` SET `ct_title`='' WHERE `ct_title` IS NULL;
    UPDATE `{$db_core}` SET `ct_version`='' WHERE `ct_version` IS NULL;
    UPDATE `{$db_core}` SET `ct_state`='1' WHERE `ct_state` IS NULL;
    UPDATE `{$db_core}` SET `ct_lock`='0' WHERE `ct_lock` IS NULL;
    UPDATE `{$db_core}` SET `ct_plug`='0' WHERE `ct_plug` IS NULL;
    ALTER TABLE `{$db_core}` 
        MODIFY `ct_id` mediumint UNSIGNED NOT NULL auto_increment,
        MODIFY `ct_code` varchar(64) NOT NULL,
        MODIFY `ct_title` varchar(64) NOT NULL,
        MODIFY `ct_version` varchar(32) NOT NULL default '',
        MODIFY `ct_state` tinyint UNSIGNED NOT NULL default '1',
        MODIFY `ct_lock` tinyint UNSIGNED NOT NULL default '0',
        MODIFY `ct_plug` tinyint UNSIGNED NOT NULL default '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_core}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}


Cot::$db->query("
    UPDATE `{$db_extra_fields}` SET `field_required`='0' WHERE `field_required` IS NULL;
    UPDATE `{$db_extra_fields}` SET `field_enabled`='1' WHERE `field_enabled` IS NULL;
    UPDATE `{$db_extra_fields}` SET `field_parse`='HTML' WHERE `field_parse` IS NULL;
    ALTER TABLE `{$db_extra_fields}` 
        MODIFY `field_html` text NULL default NULL,
        MODIFY `field_variants` text NULL DEFAULT NULL,
        MODIFY `field_params` text NULL DEFAULT NULL,
        MODIFY `field_default` text NULL DEFAULT NULL,
        MODIFY `field_required` tinyint UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `field_enabled` tinyint UNSIGNED NOT NULL DEFAULT '1',
        MODIFY `field_parse` varchar(32) NOT NULL DEFAULT 'HTML',
        MODIFY `field_description` text NULL DEFAULT NULL,
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_extra_fields}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}


Cot::$db->query("
    UPDATE `{$db_groups}` SET `grp_alias`='' WHERE `grp_alias` IS NULL;
    UPDATE `{$db_groups}` SET `grp_level`='1' WHERE `grp_level` IS NULL;
    UPDATE `{$db_groups}` SET `grp_disabled`='0' WHERE `grp_disabled` IS NULL;
    UPDATE `{$db_groups}` SET `grp_title`='' WHERE `grp_title` IS NULL;
    UPDATE `{$db_groups}` SET `grp_desc`='' WHERE `grp_desc` IS NULL;
    UPDATE `{$db_groups}` SET `grp_icon`='' WHERE `grp_icon` IS NULL;
    UPDATE `{$db_groups}` SET `grp_ownerid`='0' WHERE `grp_ownerid` IS NULL;
    UPDATE `{$db_groups}` SET `grp_maintenance`='0' WHERE `grp_maintenance` IS NULL;
    UPDATE `{$db_groups}` SET `grp_skiprights`='0' WHERE `grp_skiprights` IS NULL;
    ALTER TABLE `{$db_groups}` 
        MODIFY `grp_id` mediumint UNSIGNED NOT NULL auto_increment,
        MODIFY `grp_alias` varchar(32) NOT NULL,
        MODIFY `grp_level` tinyint NOT NULL DEFAULT '1',
        MODIFY `grp_disabled` tinyint NOT NULL DEFAULT '0',
        MODIFY `grp_name` varchar(64) NOT NULL,
        MODIFY `grp_title` varchar(64) NOT NULL DEFAULT '',
        MODIFY `grp_desc` varchar(255) NOT NULL DEFAULT '',
        MODIFY `grp_icon` varchar(128) NOT NULL DEFAULT '',
        MODIFY `grp_ownerid` int UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `grp_maintenance` tinyint NOT NULL DEFAULT '0',
        MODIFY `grp_skiprights` tinyint NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_groups}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}


Cot::$db->query("
    UPDATE `{$db_groups_users}` SET `gru_state`='0' WHERE `gru_state` IS NULL;
    ALTER TABLE `{$db_groups_users}` 
        MODIFY `gru_userid` int UNSIGNED NOT NULL,
        MODIFY `gru_groupid` mediumint UNSIGNED NOT NULL,
        MODIFY `gru_state` tinyint NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_groups_users}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}


Cot::$db->query("
    UPDATE `{$db_logger}` SET `log_text`='' WHERE `log_text` IS NULL;
    ALTER TABLE `{$db_logger}` 
        MODIFY `log_id` int UNSIGNED NOT NULL auto_increment,
        MODIFY `log_date` int UNSIGNED NOT NULL,
        MODIFY `log_text` VARCHAR(255) NOT NULL,
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_logger}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}


Cot::$db->query("
    UPDATE `{$db_plugins}` SET `pl_part`='' WHERE `pl_part` IS NULL;
    UPDATE `{$db_plugins}` SET `pl_title`='' WHERE `pl_title` IS NULL;
    UPDATE `{$db_plugins}` SET `pl_file`='' WHERE `pl_file` IS NULL;
    UPDATE `{$db_plugins}` SET `pl_order`='10' WHERE `pl_order` IS NULL;
    UPDATE `{$db_plugins}` SET `pl_active`='1' WHERE `pl_active` IS NULL;
    UPDATE `{$db_plugins}` SET `pl_module`='0' WHERE `pl_module` IS NULL;
    ALTER TABLE `{$db_plugins}` 
        MODIFY `pl_id` mediumint UNSIGNED NOT NULL auto_increment,
        MODIFY `pl_hook` varchar(255) NOT NULL,
        MODIFY `pl_code` varchar(255) NOT NULL,
        MODIFY `pl_part` varchar(255) NOT NULL DEFAULT '',
        MODIFY `pl_title` varchar(255) NOT NULL DEFAULT '',
        MODIFY `pl_file` varchar(255) NOT NULL,
        MODIFY `pl_order` tinyint UNSIGNED NOT NULL DEFAULT '10',
        MODIFY `pl_active` tinyint UNSIGNED NOT NULL DEFAULT '1',
        MODIFY `pl_module` tinyint UNSIGNED NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_plugins}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}


Cot::$db->query("
    UPDATE `{$db_structure}` SET `structure_locked`='0' WHERE `structure_locked` IS NULL;
    UPDATE `{$db_structure}` SET `structure_count`='0' WHERE `structure_count` IS NULL;
    ALTER TABLE `{$db_structure}` 
        MODIFY `structure_id` mediumint UNSIGNED NOT NULL auto_increment,
        MODIFY `structure_code` varchar(255) NOT NULL,
        MODIFY `structure_path` varchar(255) NOT NULL,
        MODIFY `structure_title` varchar(128) NOT NULL,
        MODIFY `structure_locked` tinyint UNSIGNED NOT NULL DEFAULT '0',
        MODIFY `structure_count` mediumint UNSIGNED NOT NULL DEFAULT '0',
        ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_structure}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}


Cot::$db->query("ALTER TABLE `{$db_updates}` ENGINE=InnoDB;");
if ($changeCollation) {
    Cot::$db->query("ALTER TABLE `{$db_updates}`
    CONVERT TO CHARACTER SET '" . Cot::$cfg['mysqlcharset'] . "' COLLATE '" . Cot::$cfg['mysqlcollate'] . "', 
    DEFAULT CHARACTER SET='" . Cot::$cfg['mysqlcharset'] . "' COLLATE='" . Cot::$cfg['mysqlcollate'] . "'");
}
