/* r1620 Remove unused options */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'performance' AND `config_name` = 'theme_consolidate';