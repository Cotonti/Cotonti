/* r1311 charset option is obsolete */
DELETE FROM `sed_config` WHERE `config_owner` = 'core' AND `config_cat` = 'skin' AND `config_name` = 'charset';