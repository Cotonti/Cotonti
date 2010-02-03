/* r910 Change config for redirect back on logout */
UPDATE `sed_config` SET `config_value` = '0' WHERE `config_owner` = 'core' AND `config_cat` = 'main' AND `config_name` = 'redirbkonlogout' LIMIT 1;