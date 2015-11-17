/* 0.9.19 */
UPDATE `cot_config` SET `config_default`='15', `config_variants`='cot_config_type_int(1)' WHERE `config_owner`='core' AND `config_cat`='main' AND `config_name`='maxrowsperpage';