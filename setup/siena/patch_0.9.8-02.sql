/* 0.9.8-02 remove obsolete plugin config */
DELETE FROM `cot_config` WHERE config_owner = 'core' AND config_cat = 'plug';