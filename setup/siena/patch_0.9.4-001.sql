/* Remove obsolete option */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'locale' AND `config_name` = 'servertimezone';