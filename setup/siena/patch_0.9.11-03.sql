/* 0.9.11-03 Disable gzip by default */
UPDATE `cot_config` SET `config_value` = '0', `config_default` = '0' WHERE `config_owner` = 'core' AND `config_name` = 'gzip';
