/* r1370 Remove obsolete parser configs */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'parser';