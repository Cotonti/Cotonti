/* r1971 Configuration cleanup */
UPDATE `cot_config` SET `config_cat` = 'users' WHERE `config_owner` = 'core' AND `config_name` = 'timedout';
UPDATE `cot_config` SET `config_cat` = 'locale' WHERE `config_owner` = 'core' AND `config_name` IN ('forcedefaultlang', 'defaulttimezone');
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` IN ('lang', 'time');

UPDATE `cot_config` SET `config_type` = 1, `config_value` = '', `config_default` = '' WHERE `config_owner` = 'core' AND `config_name` = 'jquery_cdn';