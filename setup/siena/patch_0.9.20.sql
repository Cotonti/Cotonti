/* 0.9.20 */
ALTER TABLE `cot_logger`
  CHANGE `log_ip` `log_ip` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  CHANGE `log_name` `log_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  CHANGE `log_group` `log_group` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'def',
  CHANGE `log_text` `log_text` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '';

ALTER TABLE `cot_plugins`
  CHANGE `pl_order` `pl_order` TINYINT UNSIGNED NULL DEFAULT '10',
  CHANGE `pl_active` `pl_active` TINYINT(1) UNSIGNED NULL DEFAULT '1',
  CHANGE `pl_module` `pl_module` TINYINT(1) UNSIGNED NULL DEFAULT '0';

ALTER TABLE `cot_structure`
  CHANGE `structure_area` `structure_area` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  CHANGE `structure_path` `structure_path` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  CHANGE `structure_tpl` `structure_tpl` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  CHANGE `structure_desc` `structure_desc` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  CHANGE `structure_icon` `structure_icon` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  CHANGE `structure_locked` `structure_locked` TINYINT(1) NULL DEFAULT '0',
  CHANGE `structure_count` `structure_count` MEDIUMINT NULL DEFAULT '0';

ALTER TABLE `cot_auth`
  CHANGE `auth_rights` `auth_rights` TINYINT(1) UNSIGNED NULL DEFAULT '0',
  CHANGE `auth_rights_lock` `auth_rights_lock` TINYINT(1) UNSIGNED NULL DEFAULT '0',
  CHANGE `auth_setbyuserid` `auth_setbyuserid` INT(11) UNSIGNED NULL DEFAULT '0';