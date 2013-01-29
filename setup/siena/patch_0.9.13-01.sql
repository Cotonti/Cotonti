/* 0.9.13-01 remove the obsolete version/revision config */
DELETE FROM `cot_config` WHERE config_owner = 'core' AND `config_cat` = 'version';