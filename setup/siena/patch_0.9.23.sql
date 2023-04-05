/* 0.9.23 */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','main','08','loggerlevel',2,'sec+adm+ext','sec+adm+ext','none,sec,adm,ext,sec+adm,sec+ext,adm+ext,sec+adm+ext,all','');

ALTER TABLE `cot_logger` MODIFY `log_group` varchar(64) DEFAULT 'adm';
ALTER TABLE `cot_logger` ADD `log_uid` int UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `cot_logger` ADD `log_type` varchar(32) DEFAULT '';
ALTER TABLE `cot_logger` ADD `log_status` varchar(24) DEFAULT '';
ALTER TABLE `cot_logger` ADD `log_uri` varchar(255) DEFAULT '';

UPDATE `cot_logger` SET `log_group` = 'forums' WHERE `log_group` = 'for';
UPDATE `cot_logger` SET `log_group` = 'users' WHERE `log_group` = 'usr';
UPDATE `cot_logger` SET `log_group` = 'page' WHERE `log_group` = 'pag';

UPDATE `cot_config` SET `config_default` = 'UTC' WHERE `config_owner` = 'core' AND `config_cat` = 'locale' AND `config_name` = 'defaulttimezone';
UPDATE `cot_config` SET `config_value` = 'UTC'
    WHERE `config_owner` = 'core' AND `config_cat` = 'locale' AND `config_name` = 'defaulttimezone'
      AND (`config_value` = 'GMT' OR `config_value` = '0' OR `config_value` IS NULL);

UPDATE `cot_config` SET `config_value` = 'Europe/Kiev'
    WHERE `config_owner` = 'core' AND `config_cat` = 'locale' AND `config_name` = 'defaulttimezone'  AND `config_value` = 'Europe/Kyiv';