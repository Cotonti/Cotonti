/* 0.9.8-05 whosonline and shield cleanup */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_name` IN ('disablewhosonline', 'shieldenabled', 'shieldtadjust', 'shieldzhammer');
